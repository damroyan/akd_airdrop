<?php
class FrontendV1Route extends Phalcon\Mvc\Router\Group {
    use TraitFrontendV1Route;

    public function initialize() {
        $this->setPrefix(self::$_FRONTEND_V2_PREFIX);

        $this->add('(.*)', array(
            'namespace'     => self::$_FRONTEND_V2_NAMESPACE,
            'module'        => self::$_FRONTEND_V2_MODULE,
            'controller'    => 'error',
            'action'        => 'notfound',
        ))->setName(self::$_FRONTEND_V2_MODULE . "-error-notfound");

        $this->_initializeFrontendV1();
    }
}