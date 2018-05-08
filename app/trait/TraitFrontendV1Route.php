<?php
trait TraitFrontendV1Route {
    static private $_FRONTEND_V2_MODULE = 'frontend-v1';
    static private $_FRONTEND_V2_NAMESPACE = 'Site\FrontendV1\Controllers';
    static private $_FRONTEND_V2_PREFIX = '';

    private function _initializeFrontendV1($prefix = "") {
        $array = [

            // [
            //      'controller_name',  -- название controller
            //      'action_name',      -- название action
            //      'url',              -- url, если не задан то генерируется из /controller_name/action_name/, если есть namespace /namespace_name/controller_name/action_name/
            //      'prefix_name',      -- setName всегда module_name-(namespace_name-)?controller_name-action_name(-prefix_name)?
            //      'params_array',     -- массив доп. параметров которые передаются в action,
            //]

            // CORE роуты
            ['index', 'index', '/', ],
            ['index', 'setLanguage', '/set/language/', ],
            ['index', 'search', '/search/', ],

            ['user', 'auth', ],
            ['user', 'registration', ],
            ['user', 'forgot', ],
            ['user', 'logout', ],

            ['user', 'authfb', '/user/auth/fb/'],
            ['user', 'authfbcallback', '/user/auth/fb/callback/'],

            ['user', 'authvk', '/user/auth/vk/'],
            ['user', 'authvkcallback', '/user/auth/vk/callback/'],

            ['user', 'index',   '/user/', ],
            ['user', 'accesslog',   '/user/accesslog/', ],
            ['user', 'edit',    '/user/edit/', ],
            ['user', 'profile', '/profile/', ],

            ['token', 'index', '/token/'],

            ['assets', 'js', '/assets/{assets:[a-z0-9\-]+}.js', ],
            ['assets', 'css', '/assets/{assets:[a-z0-9\-]+}.css', ],
            ['assets', 'tmpl', '/assets/{assets:[a-z0-9\-]+}.tmpl.js', ],
            ['assets', 'translate', '/assets/{assets:[a-z0-9\-]+}.trns.js', ],

            ['image', 'index', '/s{path:\/.*\/}{width:[0-9]+}x{height:[0-9]+}_{filename:.*}____{method:[0-9]+}_{crc:[a-f0-9]{8}}.{ext:jpg|png}'],
            ['image', 'index', '/s{path:\/.*\/}{width:[0-9]+}x{height:[0-9]+}_{filename:.*}____{method:[0-9]+}_{watermark:1}_{crc:[a-f0-9]{8}}.{ext:jpg|png}'],
            ['image', 'index', '/s{path:\/.*\/}{width:[0-9]+}x{height:[0-9]+}_{filename:.*}____{method:[0-9]+}_{post_id:p([0-9]+)}_{crc:[a-f0-9]{8}}.{ext:jpg|png}'],
            ['image', 'index', '/s{path:\/.*\/}{width:[0-9]+}x{height:[0-9]+}_{filename:.*}____{method:[0-9]+}_{watermark:1}_{post_id:p([0-9]+)}_{crc:[a-f0-9]{8}}.{ext:jpg|png}'],

            // CUSTOM роуты для разработки под каждый проект
            ['feedback', 'form', '/feedback/'],

            ['offer','one'  ,'/offer/{offer_id:[a-z0-9\-]+}/'],

            //['company', 'my', '/company/my/']


        ];

        foreach($array as $value) {
            $params = [
                'namespace'     => self::$_FRONTEND_V2_NAMESPACE,
                'module'        => self::$_FRONTEND_V2_MODULE,
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
            )->setName(self::$_FRONTEND_V2_MODULE . "-{$value[0]}-{$value[1]}" . ($value[3] ? "-{$value[3]}" : ""));
        }
    }
}