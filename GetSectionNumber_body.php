<?php

class ExtGetSectionNumber {

    static $numHeadings = 0;
    static $sectionNumber;
    private static $hasRun = false;

    # This function is called by the hook ParserBeforeInternalParse.
    # It registers the parser function that processes 'secnum'.
    public static function onPBIParseDoFirst( $parser ) {

        $parser->setFunctionHook( 'secnum', 'ExtGetSectionNumber::secnum' );
        return true;
    }

    # This function is called by the hook ParserBeforeInternalParse.
    # It parses the text to find the headlines and generate their numbering.
    public static function onPBIParseDoSecond( $parser, $text ) {
        if (self::$hasRun) return true;
        self::$hasRun = true;

        $matchArray = array();
        #global $sectionNumber;
        $sectionNumber = array();

        # Taken verbatim from doHeadings in parser.php
        # Replaces all '=' with the right <h> tags
        for ( $i = 6; $i >= 1; --$i ) {
            $h = str_repeat( '=', $i );
            $text = preg_replace( "/^$h(.+)$h\\s*$/m", "<h$i>\\1</h$i>", $text );
        }

        # Taken from formatHeadings in parser.php and modified a bit
        # \start generate numbers
        self::$numHeadings = preg_match_all(
            #'/tocnumber">(?<level>[\d\.]+)<[^<>]+>[\s]<[^<>]+>(?<heading>[^<]+)/',
            '/<H(?P<level>[1-6])(?:.*?>)\s*(?P<header>[\s\S]*?)\s*<\/H[1-6] *>/i',
            $text,
            $matchArray);
        # $headlines is an array of the headings on the wiki page
        $headlines = $numHeadings !== false ? $matchArray['header'] : array();
        # $headlineCount is an iterator used to access $matches
        $headlineCount = 0;
        # $level is the current level *according to* <hx> tags
        # initialize this to first level, so that we can skip special case in loop
        $level = $matchArray['level'][0]; 
        # $numberingArray[0-4] tracks the current level in actual numbers
        # it constructs the number string stored in $sectionNumbers
        $numberingArray = array();
        $numberingArray[0] = 0;
        # $depth tracks how much of $numberingArray is relevant at each level
        $depth = 0;

        foreach ( $headlines as $headline ) {
            # heading is a sibling to the last one
            if ( $matchArray['level'][$headlineCount] == $level ) {
                $numberingArray[$depth] += 1;
            }
            # heading is superior to the last one
            elseif ( $matchArray['level'][$headlineCount] < $level ) {
                $diff = $level - $matchArray['level'][$headlineCount];
                $level -= $diff;
                $depth -= $diff;
                $numberingArray[$depth] += 1;
            } 
            # heading is a subsection of the last one
            elseif ( $matchArray['level'][$headlineCount] > $level ) {
                $level += 1;
                $depth += 1;
                $numberingArray[$depth] = 1;
            }
            
            # generate the number
            for ($i = 0; $i <= $depth; $i++) {
                if ( $i === 0 ) { # we don't need a period at the start of the number
                    $sectionNumber[$headline][0] = $numberingArray[$i];
                } else {
                    $sectionNumber[$headline][0] .= '.' . $numberingArray[$i];
                }
            }

            # initialize the autoinc number to 1
            $sectionNumber[$headline][1] = 1;

            $headlineCount += 1;
        }
        # \end generate numbers

        # transfer the local array to a class static one
        # so we only need to run onPBIParseDoSecond once
        self::$sectionNumber = $sectionNumber;

        return true;
    }

    # This returns the formated number for the requested section heading.
    # It is also capable of formating auto-incrementing "sub" numbers for use in lists, etc.
    public static function secnum( $parser, $autoinc, $heading ) {

        # if the requested heading exists
        if (array_key_exists($heading,self::$sectionNumber)) {

            $autoinc = strtoupper($autoinc);

            if ($autoinc === 'Y' or $autoinc === 'YES') {
                $output = '<strong>' . self::$sectionNumber[$heading][0]
                    . '.' . self::$sectionNumber[$heading][1] . '</strong>';
                self::$sectionNumber[$heading][1]++; # increment for the next call
            } else {
                    $output = '<strong>' . self::$sectionNumber[$heading][0] . '</strong>';
                self::$sectionNumber[$heading][1] = 1; # reset the auto increment
            }

        } else {
            $output = '<strong>' . htmlspecialchars($heading) . '</strong>';
        }

        return $output;
    }
}
