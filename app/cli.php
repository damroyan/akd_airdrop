<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

date_default_timezone_set('Europe/Kiev');

use Phalcon\DI\FactoryDefault\CLI as CliDI,
    Phalcon\CLI\Console as ConsoleApp;

use Phalcon\Mvc\Url as UrlProvider;

define('VERSION', '1.0.0');

// Определяем путь к каталогу приложений

defined('DOCUMENT_ROOT')
|| define('DOCUMENT_ROOT', realpath(dirname(__FILE__) . '/../public/'));

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

defined('ROOT_PATH')
|| define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));

include_once(APPLICATION_PATH . '/function.php');

include_once(ROOT_PATH . "/vendor/autoload.php");

$config = new Phalcon\Config\Adapter\Ini(APPLICATION_PATH . '/config/config.ini');

/**
 * Регистрируем автозагрузчик, и скажем ему, чтобы зарегистрировал каталог задач
 */

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'Helper'    => __DIR__ . '/helper/',
    'Model'     => __DIR__ . '/models/',
));

$loader->registerDirs(
    array(
        __DIR__,
        __DIR__ . '/controllers/',
        __DIR__ . '/tasks/',
        __DIR__ . '/library/',
        __DIR__ . '/models/',
        __DIR__ . '/helper/',
        __DIR__ . '/plugins/',
        __DIR__ . '/trait/',
    )
);

$loader->register();

//Используем стандартный для CLI контейнер зависимостей
$di = new CliDI();

include_once(__DIR__ . '/di.php');

$di->setShared('route', function() use ($config) {
    $router = new CliRoute();

    $router->setRoute();

    $router->handle();

    return $router;
});

$di->setShared('url', function () use ($config, $di) {
    $route = $di->getShared('route');

    $url = new CliUrlPlugin();
    $url->setRouter($route); //this is my router object from my MVC app

    $url->setBaseUri("/");
    return $url;
});

//Создаем консольное приложение
$console = new ConsoleApp();
$console->setDI($di);

$console->registerModules(
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


/**
* Определяем консольные аргументы
*/
$arguments = array();
$params = array();

foreach($argv as $k => $arg) {
    if($k == 1) {
        $arguments['task'] = $arg;
    }
    elseif($k == 2) {
        $arguments['action'] = $arg;
    }
    elseif($k >= 3) {
        $params[] = $arg;
    }
}
if(count($params) > 0) {
    $arguments['params'] = $params;
}

// определяем глобальные константы для текущей задачи и действия
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // обрабатываем входящие аргументы
    $console->handle($arguments);
}
catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}