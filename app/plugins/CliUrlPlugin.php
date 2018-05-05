<?php
class CliUrlPlugin extends \Phalcon\Mvc\Url
{

    public function setRouter(\Phalcon\Mvc\RouterInterface $router)
    {
        $this->_router = $router;
        return $this;
    }
}