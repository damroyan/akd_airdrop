<?php
trait TraitBackendV1Route {
    static private $_BACKEND_V1_MODULE = 'backend-v1';
    static private $_BACKEND_V1_NAMESPACE = 'Site\BackendV1\Controllers';
    static private $_BACKEND_V1_PREFIX = '/admin';

    private function _initializeBackendV1($prefix = "") {
        $array = [

            // [
            //      'controller_name',  -- название controller
            //      'action_name',      -- название action
            //      'url',              -- url, если не задан то генерируется из /controller_name/action_name/, если есть namespace /namespace_name/controller_name/action_name/
            //      'prefix_name',      -- setName всегда module_name-(namespace_name-)?controller_name-action_name(-prefix_name)?
            //      'params_array',     -- массив доп. параметров которые передаются в action,
            //]

            ['index', 'index', '/', ],
            ['index', 'email', '/email/', ],

            ['user', 'index',   '/user/', ],
            ['user', 'accesslog',   '/user/accesslog/', ],
            ['user', 'edit',    '/user/edit/', ],
            ['user', 'profile', '/profile/', ],

            ['token', 'index', '/token/'],

            ['offer', 'list', '/offers/'],
            ['offer', 'edit', '/offer/edit/'],



        ];

        foreach($array as $value) {
            $params = [
                'namespace'     => self::$_BACKEND_V1_NAMESPACE,
                'module'        => self::$_BACKEND_V1_MODULE,
                'controller'    => $value[0],
                'action'        => $value[1],
            ];

            if(is_array($value[4])) {
                $params = array_merge(
                    $params,
                    $value[4]
                );
            }

            $this->add(
                $value[2] ? $value[2] : "/{$value[0]}/$value[1]/",
                $params
            )->setName(self::$_BACKEND_V1_MODULE . "-{$value[0]}-{$value[1]}" . ($value[3] ? "-{$value[3]}" : ""));
        }
    }
}