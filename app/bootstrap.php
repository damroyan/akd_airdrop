<?php
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

defined('ROOT_PATH')
|| define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));

// Определение IP
if(!defined("IP")) {
    if(isset($_SERVER) && isset($_SERVER["REMOTE_ADDR"])) {
        define('IP',            $_SERVER["HTTP_X_REAL_IP"] ? $_SERVER["HTTP_X_REAL_IP"] : $_SERVER["REMOTE_ADDR"]);
    }
    else {
        define('IP',            '127.0.0.1');
    }
}

include_once(__DIR__ . '/function.php');

include_once(ROOT_PATH . "/vendor/autoload.php");

$config = new Phalcon\Config\Adapter\Ini(APPLICATION_PATH . '/config/config.ini');

//Register an autoloader
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'Helper'        => __DIR__ . '/helper/',
    'Model'         => __DIR__ . '/models/',
));

$loader->registerDirs(array(
    __DIR__ . '/controllers/',
    __DIR__ . '/trait/',
    __DIR__ . '/models/',
    __DIR__ . '/library/',
    __DIR__ . '/validators/',
    __DIR__ . '/plugins/',
    __DIR__ . '/helper/',
))->register();

$loader->register();

//Create a DI
$di = new Phalcon\DI\FactoryDefault();

include_once(__DIR__ . '/di.php');

$di->set('router', function() use ($config, $di) {
    $router = new \Phalcon\Mvc\Router(false);
    $router->setDI($di);

    include_once(__DIR__ . '/route.php');

    $router->handle();

    return $router;
});

$di->set('annotations', function() use ($config) {
    switch($config->application->cacheAnnotationsType) {
        case 'apcu':
        case 'apc':
            $crc = substr(md5($config->system->version), 0, 4);
            $annotations = new \Phalcon\Annotations\Adapter\Apc([
                'prefix' => "{$config->application->cacheAnnotationsPrefix}_{$crc}",
                'lifetime' => (
                    $config->application->production ?
                        $config->application->cacheAnnotationsLifetime :
                        10
                ),
            ]);
            break;

        case 'file':
        case 'files':
            $annotations = new \Phalcon\Annotations\Adapter\Files(array(
                'annotationsDir' => ROOT_PATH . $config->application->cacheAnnotationsDir,
            ));
            break;

        case 'memory':
        default:
            $annotations = new \Phalcon\Annotations\Adapter\Memory();
            break;
    }

    return $annotations;
});

//Handle the request
$application = new \Phalcon\Mvc\Application($di);

$application->registerModules(
    array(
        'api-v1' => array(
            'className' => 'Site\ApiV1\Module',
            'path'      => APPLICATION_PATH . '/site/api-v1/Module.php',
        ),

        'backend-v1' => array(
            'className' => 'Site\BackendV1\Module',
            'path'      => APPLICATION_PATH . '/site/backend-v1/Module.php',
        ),

        'frontend-v1' => array(
            'className' => 'Site\FrontendV1\Module',
            'path'      => APPLICATION_PATH . '/site/frontend-v1/Module.php',
        ),
    )
);

echo $application->handle()->getContent();