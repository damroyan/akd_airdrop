<?php
class BackendV1Route extends Phalcon\Mvc\Router\Group {
    use TraitBackendV1Route;

    public function initialize() {
        $this->setPrefix(self::$_BACKEND_V1_PREFIX);

        $this->add('(.*)', array(
            'namespace'     => self::$_BACKEND_V1_NAMESPACE,
            'module'        => self::$_BACKEND_V1_MODULE,
            'controller'    => 'error',
            'action'        => 'notfound',
        ))->setName(self::$_BACKEND_V1_MODULE . "-error-notfound");

        $this->_initializeBackendV1();
    }
}