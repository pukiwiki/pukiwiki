<?php 
require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '__template__'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',  DATA_HOME . 'wiki/'  ); 
define('PKWK_HOME', dirname($_SERVER['PHP_SELF']) .'/');

$router = new \Bramus\Router\Router();
$router->mount('/site', function () use ($router) {
    $router->get('/(\w+)', function ($site) {
        define('DATA_DIR', WIKI_DIR .  $site . '/'); 
        $file = WIKI_DIR .  $site . '/'. SITE_CONFIG_FILE; 
        if (file_exists($file) and is_readable($file)){
            $config = Symfony\Component\Yaml\Yaml::parseFile($file);
            if ($config){
                define('SKIN_DIR', 'skin/' . $config['skin'] . '/');
                define('SITE_CONF', $config);	
            }
        }
    });

});

$router->run();
