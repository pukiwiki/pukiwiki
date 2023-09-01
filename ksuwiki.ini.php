<?php 
require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '_template'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',  DATA_HOME . 'wiki/'  ); 

$router = new \Bramus\Router\Router();
$router->mount('/site', function () use ($router) {
    $router->match('GET|POST', '/(\w+)', function ($site) {
        initialize_site($site);
    });    
});

$router->run();

function initialize_site($site)
{
    define('SITE_ID', $site);// TODO: check validity of $site - is there a full set of site data   
    define('SITE_URL', PKWK_HOME .'site/'. $site . '/'); 
    foreach( [
        'DATA_DIR'=>'wiki/',
        'DIFF_DIR'=>'diff/',
        'BACKUP_DIR'=>'backup/',
        'CACHE_DIR'=>'cache/',
        'UPLOAD_DIR'=>'attach/',
        'COUNTER_DIR'=>'counter/',
    ] as $item=>$dir){
        define($item,  WIKI_DIR .'sites/'. $site .'/'. $dir ); 
    }
    $site_admin = false;
    $file = WIKI_DIR .'sites/'. $site .'/'. SITE_CONFIG_FILE; 
    if (file_exists($file) and is_readable($file)){
        $config = Symfony\Component\Yaml\Yaml::parseFile($file);
        if ($config){
            define('SKIN_DIR', 'skin/' . $config['skin'] . '/');
            define('SITE_TITLE', $config['title']);
            session_start();
            $site_admin = isset($_SESSION['authenticated_site']) 
                and $_SESSION['authenticated_site'] 
                and $_SESSION['authenticated_site']===$site;
        }
    }
    define('SITE_ADMIN', $site_admin);
}