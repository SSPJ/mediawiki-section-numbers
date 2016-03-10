If you have a very long or intricately numbered wiki page, you can use this extension to automatically insert section numbers (for example: 3.4 or 1.2.2.4) into the page using {{#secnum:n|section_title_goes_here}}. For example, you can write "see section {{#secnum:n|Boring Heading}} for a map of this" and it will be replaced with "see section 4.7 for a map of this".

You can also use {{#secnum:Y|title_goes_here}} to add an auto incrementing variable to the end of the section number. Useful in creating numbered lists, like:
* {{#secnum:Y|title #8}} It contains some more items.
* {{#secnum:Y|title #8}} It contains some more items.
* {{#secnum:Y|title #8}} It contains some more items.

Tested it on MediaWiki 1.24.1 with PHP 5.4.42

Installation:

1. Extract these files into [wiki_installation]/extensions/GetSectionNumber:
GetSectionNumber.php
GetSectionNumber_body.php
GetSectionNumber.i18n.php

2. Add this line to the bottom of LocalSettings.php:
require_once "$IP/extensions/GetSectionNumber/GetSectionNumber.php";

3. Run: 
php rebuildLocalisationCache.php --force

Step 3 may not be necessary, but if you get "Fatal exception of type MWException"
it may fix it.

4. (optionally) Create a wiki page with the contents of TestPage.txt
to verify that the extension works.
