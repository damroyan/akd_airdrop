<?php
class CliRoute extends \Phalcon\Mvc\Router {
    use TraitBackendV1Route;
    use TraitFrontendV1Route;
    use TraitApiV1Route;

    public function setRoute() {
        $this->_initializeBackendV1(self::$_BACKEND_V1_PREFIX);
        $this->_initializeFrontendV1(self::$_FRONTEND_V2_PREFIX);
        $this->_initializeApiV1(self::$_API_V1_PREFIX);
    }
}