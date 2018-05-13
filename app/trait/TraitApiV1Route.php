<?php
trait TraitApiV1Route {
    static private $_API_V1_MODULE = 'api-v1';
    static private $_API_V1_NAMESPACE = 'Site\ApiV1\Controllers';
    static private $_API_V1_PREFIX = '/api/v1';

    private function _initializeApiV1($prefix = "") {
        $array = [

            // [
            //      'controller_name',  -- название controller
            //      'action_name',      -- название action
            //      'url',              -- url, если не задан то генерируется из /controller_name/action_name/, если есть namespace /namespace_name/controller_name/action_name/
            //      'prefix_name',      -- setName всегда module_name-(namespace_name-)?controller_name-action_name(-prefix_name)?
            //      'params_array',     -- массив доп. параметров которые передаются в action,
            //]


            //CORE ROUTES [SKELETON]
            ['index', 'index', '/', ],
            ['index', 'search', '/search/' ],

            ['user', 'registration'],
            ['user', 'auth'],
            ['user', 'forgot'],
            ['user', 'logout'],

            ['user', 'toggleban'],
            ['user', 'toggledelete'],
            ['user', 'edit'],
            ['user', 'password'],
            ['user', 'picture'],
            ['user', 'profileEdit','/profile/edit/'],
            ['user', 'delete','/user/{user_id:[0-9]+}/delete/'],

            ['token', 'index', '/token/'],
            ['token', 'cancel', '/token/cancel/'],

            ['upload', 'temp', '/upload/temp/'],
            ['upload', 'index', '/upload/'],

            ['message','close', '/message/close/'],

            ['feedback','send','/feedback/send/'],

            ['company', 'picture'],
            ['company', 'dataEdit'],

            ['offer', 'edit'],
            ['offer', 'toggleban'],
            ['offer', 'toggledelete'],
            ['offer', 'descUpdate'],
            ['offer', 'picture'],



            // NEW PROJECT NAME - ROUTES
        ];

        foreach($array as $value) {
            $params = [
                'namespace'     => self::$_API_V1_NAMESPACE,
                'module'        => self::$_API_V1_MODULE,
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
                $value[2] ? $value[2] : "{$prefix}/{$value[0]}/$value[1]/",
                $params
            )->setName(self::$_API_V1_MODULE . "-{$value[0]}-{$value[1]}" . ($value[3] ? "-{$value[3]}" : ""));
        }
    }
}