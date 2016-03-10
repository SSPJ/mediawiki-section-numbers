<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => 'Get Section Number',
    'descriptionmsg' => 'Parser function to get the section number, given a title.',
    'version' => 0.8,
    'author' => 'Seamus J.',
    'url' => 'none',
);

# Place this line in LocalSettings.php:
#require_once "$IP/extensions/GetSectionNumber/GetSectionNumber.php";

# This indicates where to find the localisation file. It is not optional.
$wgExtensionMessagesFiles['GetSectionNumber'] = __DIR__ . '/GetSectionNumber.i18n.php';

$wgAutoloadClasses['ExtGetSectionNumber'] = __DIR__ . '/GetSectionNumber_body.php';

# This indicates where in parser.php this code will run.
$wgHooks['ParserBeforeInternalParse'][] = 'ExtGetSectionNumber::onPBIParseDoFirst';
$wgHooks['ParserBeforeInternalParse'][] = 'ExtGetSectionNumber::onPBIParseDoSecond';

