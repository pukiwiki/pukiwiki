KsuWiki - A PukiWiki extension supports multiple sites in a single PukiWiki installation

FEATURES
1. Support multiple sites under a single PukiWiki installation

2. Provide 'view' and 'admin' modes 
  - 'view' mode for readonly, hide navigation bar and tool bar
  - 'admin' mode for edit requring login, show navigation bar and tool bar

3. Site administration tool

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

B. New PHP scripts and related files for KsuWiki
  (1) DATA_HOME . 'index.php' (updated, add new definitions and require statement), 
    '.htaccess'(updated, add rewrite rules), 'composer.json'(new)

  (2) DATA_HOME . 'ksuwiki.ini.php'(new, for site initialization)
    'pukiwiki.ini.php' (updated, insert site-related definition)

  (3) LIB . 'ksuwiki.php'(new, functions specially implemented for KsuWiki)

  (4) LIB . 'auth.php' (updated, for site login)

  (5) PLUGIN . 'site.ini.php' (new, plugin for site administration!)
    'snippet.inc.php' (new, plugin for code syntax-highlight)

  (6) SKIN . 'default/' (new, per-site skin files)
 
  (7) New Contants
    PKWK_HOME, SITE_ID


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

