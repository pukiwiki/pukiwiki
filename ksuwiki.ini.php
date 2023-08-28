<?php 

require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '__template__'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',      DATA_HOME . 'wiki/'  ); 

$path = str_replace(DIRECTORY_SEPARATOR,'/', realpath(__DIR__));
$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
if (substr($path, -1)!='/') $path .= '/';
if (substr($path, 0, 1)!='/') $path = '/' . $path;
define('PKWK_HOME', $path);

$router = new \Bramus\Router\Router();

$router->mount('/site', function () use ($router) {
    $router->get('/(\w+)', function ($site) {
        $file = WIKI_DIR .  $site . '/'. SITE_CONFIG_FILE; 
        if (file_exists($file) and is_readable($file)){
            $config = Symfony\Component\Yaml\Yaml::parseFile($file);
            if ($config){
                define('DATA_DIR', WIKI_DIR .  $site . '/'); 
                define('SKIN_DIR', 'skin/' . $config['skin'] . '/');
                define('SITE_CONF', $config);	
            }
        }else{
            define('DATA_DIR', WIKI_DIR .  $site . '/'); 
        }
    });

});

$router->run();
