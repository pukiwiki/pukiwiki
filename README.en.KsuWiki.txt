KsuWiki - A PukiWiki extension that supports multiple sites in a single installation

HOW IT WORK

1. Create 'sites' and '_template' directories under 'wiki/'

2. Move all directories of original wiki site to 'wiki/_template/', including,
   attach/, backup/, cache/, counter/, diff/
   
   used as template when creating a new site

3. Each site has a directory (e.g., site1), under 'wiki/sites/', including, 　 
   attach/, backup/, cache/, counter/, diff/ 

4. Create a config file, named '.site.yaml' under site directory, (e.g., 'wiki/sites/site1/') 
   title: site's title
   skin: which skin to use
   admin: name of the administrator
   passwd: password for site administration, md5 hashed
   toppage: default page of the site
   readonly: is the site is readonly

   For example,
    title: 'Samepl Site'
    skin: default
    admin: hoge
    passwd: 81dc9bdb52d04dc20036dbd8313ed055
    toppage: FrontPage
    readonly: 0

5. New PHP scripts for KsuWiki
 (1) DATA_HOME . 'index.php', 
   '.htaccess', 'composer.json'
 (2) DATA_HOME . 'ksuwiki.ini.php'
 (3) LIB . 'ksuwiki.php'
 (4) LIB . 'auth.php'
 (5) PLUGIN . 'site.ini.php'
   'snippet.inc.php'
 (6) SKIN . 'default/'
 
6. Dependencies
 (1) symfony/yaml
 (2) bramus/router
