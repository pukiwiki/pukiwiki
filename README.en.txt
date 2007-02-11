NOTE: This document become old, needs help
(2004-08-28)
----------------------------------------------
NAME

    PukiWiki - PHP scripts for Web pages which can be edited by anyone, 
               at any time, from anywhere. 

        PukiWiki 1.4.8
        Copyright (C)
          2001-2006 PukiWiki Developers Team
          2001-2002 yu-ji (Based on PukiWiki 1.3 by yu-ji)
        License: GPL version 2 or (at your option) any later version
        http://pukiwiki.sourceforge.jp/

DESCRIPTION

    PukiWiki consists of a series of PHP Scripts designed to build 
    a collection of web pages which can be edited by anyone without any restriction.
    Pukiwiki is more flexible than moderate bulletinboards.
    Since it ,basically, enables everyone change the design and content of any 
    page, while you can only leave messages in bulletinboards.

    PukiWiki is a unique Wiki-Engine which is based on the YukiWiki specification 
     created by Mr. Hiroshi Yuki.
    Mr. yu-ji continued its development until the release of PukiWiki 1.3, and 
    PukiWiki Developers Team inherited the development from him after 1.3.1b.

    PukiWiki is written in PHP, so it's relatively easy to install
    on a web server which supports PHP.

    PukiWiki is distributed under GPL. Thus it is, so to speak, a  Free Software!

REQUIREMENTS

    PukiWiki is only written in PHP, so it needs a PHP environment.
    PHP4.1 or later versions are recommended.

    To use the multibyte features, PHP with multibyte extensions is needed.   

HOW TO MAKE SITE

    1. Download a PukiWiki package.
       The latest package can be obtained from
       http://sourceforge.jp/projects/pukiwiki/.

    2. Extract the package on a local system or a target system.
        The default name for the root directory of the system is "pukiwiki". 
        You may change it to another name if necessary.  

    3. Edit pukiwiki.ini.php to make it fit to your environment.
        Common setting                 : pukiwiki.ini.php
        Settings for each agent:
          I-MODE(NTT),AirH"(DDIPocket) : i_mode.ini.php
          J-PHONE                      : jphone.ini.php
          Default( any other above )   : default.ini.php
        String replace setting         : rules.ini.php

    4. Change the file permission as follows
          Directory      Permission     (more secure (*1)) 
             attach        777               707
             backup        777               707
             cache         777               707
             counter       777               707
             diff          777               707
             face          755               705
             image         755               705
             plugin        755               705
             skin          755               705 
             wiki          777               707
          File
             attach/*      666               606
             backup/*      666               606
             cache/*       666               606
             diff/*        666               606
             face/*        644               604
             image/*       644               604
             plugin/*      644               604
             skin/*        644               604
             wiki/*        666               606
             *.php         644               604
             *.lng         644               604
             *.txt         644               604   

    5.  Now you are ready to access the site !
         Please point your browser to:  
           http://[your domain]/[pukiwiki dir]/pukiwiki.php

HOW TO MAKE A BACKUP OF DATA

    The document data is stored in the "wiki" directory. (default setting)
    Please make a backup of your "wiki" directory. 
    And, if necessary, also make backups of the other directories - namely, 
    "attach" , "backup", "counter", "cache", and "diff" with default setting.

HOW TO CREATE PAGE

    1.  Click on ''New'' menu ( at the top of a page) or ''new icon '' (at the 
        bottom of a page), then a page displaying an input box will  open. 
        Please input whatever name you like for the new page and push the    
        button, then the new page will be created in edit mode.
    2.  Every time you write a WikiName, which includes at least two capitalized
        letters in a word, or BracketName enclosing on a page, the question
        mark, "?", appears at the tail of the written word. If you click the
        mark, a new page with an editable textfield is displayed, and you can
        modify the page as you usually edit a page.

RULES FOR TEXT FORMATTING

    Refer to [[Text Formatting Rule]] page.

INTERWIKI

    The InterWiki feature is supported from version 1.11 on.
    InterWiki originally meant the function that established links among Wiki Sites. 
    The present InterWiki can connect any web site to strings. This expanded feature
    is very convenient even though it is now a conceptually different function.
    This function is ported nearly completely from Tiki.

    Please refer to [[InterWikiTechnical]] page for details.     

RDF/RSS

    Since version 1.2.1, the function to create RDF/RSS from RecentChanges 
    has been supported.
    Since version 1.4.5, RSS 2.0 has been supported.

    * Output example:
      RSS 0.91 http://path/to/pukiwiki/index.php?plugin=rss
      RSS 1.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=1.0
      RSS 2.0  http://path/to/pukiwiki/index.php?plugin=rss&ver=2.0

TODO

    http://pukiwiki.sourceforge.jp/dev/?BugTrack

AUTHOR

    PukiWiki Developers Team http://pukiwiki.sourceforge.jp/dev/
    Based on "PukiWiki" 1.3 by yu-ji

    Please send questions, opinions and bug reports to
    http://pukiwiki.sourceforge.jp/dev/

LICENCE 

    PukiWiki is distributed under GNU GPL/2 (GNU General Public License) .
     (http://www.gnu.org)

SEE ALSO
    "doc" directory for another documentation
    PukiWiki Web Site         http://pukiwiki.sourceforge.jp/
    PukiWiki Developer's Site http://pukiwiki.sourceforge.jp/dev/

ACKNOWLEDGEMENT

    First of all, thanks so much to the users of PukiWiki and the members of
    PukiWiki Developers Team. 
    Special thanks to Mr. yu-ji(aka sng) who developed the first PukiWiki.
    And also thanks to Mr. Hiroshi Yuki who was willing to accept our cloning
    YukiWiki and 
    Cunningham & Cunningham, Inc. who created the original WikiWikiWeb.

    * yu-ji's Web Site          http://factage.com/yu-ji/ 
    * Hiroshi Yuki 's Web Site  http://www.hyuki.com/ 
    * YukiWiki                  http://www.hyuki.com/yukiwiki/ 
    * Tiki                      http://todo.org/cgi-bin/jp/tiki.cgi 
    * Original WikiWikiWeb      http://c2.com/cgi/wiki?WikiWikiWeb 
    * Author of WikiWikiWeb(Cunningham & Cunningham, Inc.) http://c2.com/
