<?php
include_once(APPLICATION_PATH . '/site/backend-v1/route.php');
include_once(APPLICATION_PATH . '/site/api-v1/route.php');
include_once(APPLICATION_PATH . '/site/frontend-v1/route.php');

/**
 * @var $router \Phalcon\Mvc\Router
 */
$router->setDefaultModule('frontend-v1');

//Remove trailing slashes automatically
//$router->removeExtraSlashes(true);

$router->mount(new FrontendV1Route());
$router->mount(new BackendV1Route());
$router->mount(new ApiV1Route());

//Set 404 paths
$router->notFound(array(
    "namespace"     => 'Site\FrontendV1\Controllers',
    "controller"    => "error",
    "action"        => "blank",
));
