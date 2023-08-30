<?php 
require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '_template'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',  DATA_HOME . 'wiki/'  ); 

$router = new \Bramus\Router\Router();
$router->mount('/site', function () use ($router) {
    function setup($site){
        $definitions = array(
            'DATA_DIR'=>'wiki/',
            'DIFF_DIR'=>'diff/',
            'BACKUP_DIR'=>'backup/',
            'CACHE_DIR'=>'cache/',
            'UPLOAD_DIR'=>'attach/',
            'COUNTER_DIR'=>'counter/',
        );
        foreach ($definitions as $item=>$dir){
            define($item,  WIKI_DIR .'sites/'. $site .'/'. $dir ); 
        }
        define('SITE_ID', $site);
        define('SITE_URL', PKWK_HOME .'site/'. $site . '/'); 
        $file = WIKI_DIR .'sites/'. $site .'/'. SITE_CONFIG_FILE; 
        if (file_exists($file) and is_readable($file)){
            $config = Symfony\Component\Yaml\Yaml::parseFile($file);
            if ($config){
                define('SKIN_DIR', 'skin/' . $config['skin'] . '/');
                define('SITE_TITLE', $config['title']);
                session_start();
                $auth_site = isset($_SESSION['authenticated_site']) ? $_SESSION['authenticated_site'] : null;
                define('SITE_ADMIN', $auth_site==$site);
            }
        }
    }

    $router->get('/(\w+)', function ($site) {
        setup($site);
    });
    
    $router->post('/(\w+)', function ($site) {
        setup($site);
    });
});

$router->run();
