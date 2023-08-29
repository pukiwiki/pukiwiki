<?php 
require __DIR__ . '/vendor/autoload.php';

define('SITE_TEMPLATE', '_template'); 
define('SITE_CONFIG_FILE', '.site.yaml'); 
define('WIKI_DIR',  DATA_HOME . 'wiki/'  ); 
define('PKWK_HOME', dirname($_SERVER['PHP_SELF']) .'/');

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
    }
    $router->get('/(\w+)', function ($site) {
        setup($site);

        $file = WIKI_DIR .'sites/'. $site .'/'. SITE_CONFIG_FILE; 
        if (file_exists($file) and is_readable($file)){
            $config = Symfony\Component\Yaml\Yaml::parseFile($file);
            if ($config){
                define('SKIN_DIR', 'skin/' . $config['skin'] . '/');
                define('SITE_CONF', $config);	
            }
        }
    });
    $router->post('/(\w+)', function ($site) {
        setup($site);
    });
});

$router->run();
