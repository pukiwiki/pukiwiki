<?php 

require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '__template__'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',      DATA_HOME . 'wiki/'     ); 

$router = new \Bramus\Router\Router();

// Subrouting
$router->mount('/site', function () use ($router) {
    $router->get('/(\w+)', function ($site) {
        $file = WIKI_DIR .  $site . '/'. SITE_CONFIG_FILE; 
        if (file_exists($file) and is_readable($file)){
            $config = Symfony\Component\Yaml\Yaml::parseFile($file);
            if ($config){
                define('DATA_DIR', WIKI_DIR .  $site . '/'); 
                define('SKIN_DIR', 'skin/' . $config['skin'] . '/');	
            }
        }
    });

});

$router->run();
