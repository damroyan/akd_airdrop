<?php
namespace Helper;

class StaticWrapper {
    /**
     * Враппер для доступа к методам из Volt
     *
     * @param $className
     * @param $methodName
     * @param array $arguments
     * @return mixed
     */
    static public function exec($className, $methodName, $arguments = array()) {
        return call_user_func_array(array($className, $methodName), $arguments);
    }
}