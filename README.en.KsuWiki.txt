KsuWiki - A PukiWiki extension that supports multiple sites in a single installation

HOW DOES IT WORK

A. Save data in separate directories for each site
  A.1. Create 'sites' and '_template' directories under 'wiki/'

  A.2. Move all directories of original wiki site to 'wiki/_template/', including the following directories,
    attach/, backup/, cache/, counter/, diff/
    # used as template for new sites

  A.3. Create a directory for each site (e.g., site1) under 'wiki/sites/', including the following directories, 　 
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
  (1) DATA_HOME . 'index.php' (updated), 
    '.htaccess'(updated), 'composer.json'(new)
  (2) DATA_HOME . 'ksuwiki.ini.php'(new)
    'pukiwiki.ini.php' (updated)
  (3) LIB . 'ksuwiki.php'(new, functions for KsuWiki)
  (4) LIB . 'auth.php' (updated)
  (5) PLUGIN . 'site.ini.php' (new, plugin for site administration!)
    'snippet.inc.php' (new, plugin for code syntax-highlight)
  (6) SKIN . 'default/' (new, per-site skin files)
 
6. Dependencies
 (1) symfony/yaml
 (2) bramus/router
