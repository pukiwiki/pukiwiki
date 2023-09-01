NAME

    KsuWiki - a Mult-Site extension of PukiWiki
    
    PukiWiki - PHP scripts for Web pages which can be edited by anyone, 
               at any time, from anywhere. 

        PukiWiki 1.5.4
        Copyright
          2001-2022 PukiWiki Developement Team
          2001-2002 yu-ji (Based on PukiWiki 1.3 by yu-ji)
        License: GPL version 2 or (at your option) any later version
        https://pukiwiki.osdn.jp/

DESCRIPTION


FEATURES KsuWiki 
1. Support multiple sites under a single PukiWiki installation

2. Provide 'view' and 'admin' modes 
  - 'view' mode for readonly, hide navigation bar and tool bar
  - 'admin' mode for edit requring login, show navigation bar and tool bar

3. Site administration tool

DIRECTORY/FILE LAYOUT
^^^^^^^^^^^^^^^^^^^^^
PKWK_ROOT
- index.php
- INSTALL.txt
- README.txt
- ...
- UPDATING.txt

+ assets/   # NEW !
  + image/  # MOVED FROM PKWK_ROOT!
  + skin/   # MOVED FROM PKWK_ROOT!
    + default/   # NEW !
      + pukiwiki.css
      + pukiwiki.skin.php

    - pukiwiki.css
    - pukiwiki.skin.php
  + snippet/    # NEW ! for snipet plugin

+ config/
  - en.lang.php
  - ja.lang.php
  - default.ini.php
  - ksuwiki.ini.php   # NEW !
  - pukiwiki.ini.php  # UPDATED : update constant definitions

+ lib/
  - auth.php    # UPDATED: enable site login
  - ...
  - init.php    # UPDATED: change path to '*.ini.php', '*.lang.php' 
  - ksuwiki.php # NEW !
  - ...
  - pukiwiki.php  
+ wiki/
  + _template/  # MOVED FROM PKWK_ROOT
    + attach/
    + backup/
    + cache/
    + counter/
    + diff/
    + wiki/
    + wiki.en/
    - .site.yaml
  + sites/  # NEW !
    + site1/
      + attach/
      + ...
      + wiki.en/

      - .site.yaml
    + site2/
      + attach/
      + ...
      + wiki.en/

      - .site.yaml

HOW DOES IT WORK?

A. Store data for different sites in separate directories 
  A.1. Create 'sites' and '_template' directories under 'wiki/'

  A.2. Move a set of directories for original wiki site to 'wiki/_template/', as template for creating new sites 
    attach/, backup/, cache/, counter/, diff/

  A.3. Create a directory for each site (e.g., site1) under 'wiki/sites/' 　 
    attach/, backup/, cache/, counter/, diff/ 

  A.4. Create a config file, named '.site.yaml' under site directory, (e.g., 'wiki/sites/site1/') 
    title: site's title
    skin: which skin to use
    admin: name of the administrator
    passwd: password for site administration, md5 hashed
    toppage: default page of the site
    readonly: is the site is readonly

    For example,
      title: 'Sample Site'
      skin: default
      admin: hoge
      passwd: '{x-php-md5}81dc9bdb52d04dc20036dbd8313ed055'
      toppage: FrontPage
      readonly: 0

B. Other Optimization
  (1) Move all static content to 'assets/' directory
  (2) Move all '*.ini.php' and '*.lang.php' to 'config' directory

C. New PHP scripts and related files for KsuWiki
  (1) DATA_HOME . 'index.php' (updated, add new definitions and require statement), 
    '.htaccess'(updated, add rewrite rules), 'composer.json'(new)

  (2) DATA_HOME . 'ksuwiki.ini.php'(new, for site initialization)
    'pukiwiki.ini.php' (updated, add site-related definition)

  (3) LIB . 'ksuwiki.php'(new, functions specially implemented for KsuWiki)

  (4) LIB . 'auth.php' (updated, allow site login)

  (5) PLUGIN . 'site.ini.php' (new, plugin for site administration!)
    'snippet.inc.php' (new, plugin for code syntax-highlight)

  (6) SKIN . 'default/' (new, per-site skin files)
 
  (7) New Contants
    PKWK_HOME, WIKI_DIR, CONF_DIR,
    SITE_ID, SITE_TITLE, SITE_URL, SITE_ADMIN,
    ALLOW_SHOW_FOOTER (in 'pukiwiki.skin.php') 

C. Dependencies
 (1) symfony/yaml
 (2) bramus/router

D. Site administration
  A new plugin named 'site' is provided for site administration.
  URL                     | Comment
  -------------------------------  
  ?cmd=site                | list all sites 
  ?cmd=site&act=new        | create a new site from template
  ?cmd=site&act=setup&site_id=site1     |  modify config of site1
  ?cmd=site&act=delete&site_id=site1    | delete site1
  ?cmd=site&act=copy&site_id=site1      |  create a new site from site1 
  ?cmd=site&act=passwd&site_id=site1    |  change password of site1
  ?cmd=site&act=login      |  login as administrator
  ?cmd=site&act=logout     |  logout as administrator

