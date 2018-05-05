<?php
class ApiV1Route extends Phalcon\Mvc\Router\Group {
    use TraitApiV1Route;

    public function initialize() {
        $this->setPrefix(self::$_API_V1_PREFIX);

        $this->add('(.*)', array(
            'namespace'     => self::$_API_V1_NAMESPACE,
            'module'        => self::$_API_V1_MODULE,
            'controller'    => 'error',
            'action'        => 'notfound',
        ))->setName(self::$_API_V1_MODULE . "-error-notfound");

        $this->_initializeApiV1();
    }
}