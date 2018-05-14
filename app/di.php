<?php
use Phalcon\Db\Adapter\Pdo\Mysql as Connection;
use Phalcon\Logger;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Logger\Adapter\File as FileLogger;

\Helper\String2Url::setDomain($config->application->domain);

$di->set('config', function() use ($config) {
    return $config;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function() use ($config) {
    try {
        $connection = new Connection(array(
    		"host"     => $config->database->host,
    		"username" => $config->database->username,
    		"password" => $config->database->password,
    		"dbname"   => $config->database->name,
            "charset"  => 'utf8',
            'persistent'    => ($config->database->persistent ? true : false),
//            'dialectClass'  => '\Phalcon\Db\Dialect\MySQL',
    	));
        $connection->query('SET time_zone = ?;', [date('P')]);

        if($config->database->log && !$config->application->production) {
            try {
                $eventsManager = new Manager();

                $logger = new FileLogger(ROOT_PATH . $config->application->directoryLog . "sql.log");

                //Listen all the database events
                $eventsManager->attach('db', function($event, $connection) use ($logger) {
                    if ($event->getType() == 'beforeQuery') {
                        $logger->log($connection->getSQLStatement(), Logger::INFO);
                    }
                });

                $connection->setEventsManager($eventsManager);
            }
            catch(\Exception $e) {
                error_log("{$e->getFile()}:{$e->getLine()}. Can't log SQL beforeQuery. {$e->getMessage()}");
            }
        }

        return $connection;
    }
    catch(\Exception $e) {
        throw new Exception("Can't connect DB");
    }
});

$di->set('crypt', function() use ($config) {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey($config->system->secretKey); // Используйте свой собственный ключ!
    return $crypt;
});

$di->setShared('cookies', function() {
    if($_SERVER['SHELL']) {
        return null;
    }

    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(true);
    return $cookies;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function() use ($config, $di) {
    if($_SERVER['SHELL']) {
        return null;
    }

    $session = new \Phalcon\Session\Adapter\Files();

    if($config->session->name) {
        session_name($config->session->name);
    }

    preg_match("@([^\.]+\.)?(([^\.]+)\.[^\.]+)$@ui", $_SERVER['HTTP_HOST'], $match);
    $ip = (preg_match('@^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$@ui', $_SERVER['HTTP_HOST'])) ? true : false;

    session_set_cookie_params(
        ($config->session->lifetime ? $config->session->lifetime : 0),
        ($config->session->path ? $config->session->path : 0),
        ($config->session->domain ?
            $config->session->domain :
            ($ip ? null : ".{$match[2]}")
        ),
        false,
        ($config->session->http ? true : false)
    );
    $session->start();

	return $session;
});

$di->set('modelsMetadata', function() use ($config) {
    switch($config->application->cacheMetaType) {
        case 'apcu':
        case 'apc':
            $crc = substr(md5($config->system->version), 0, 4);
            $metaData = new \Phalcon\Mvc\Model\MetaData\Apc([
                'prefix' => "{$config->application->cacheMetaPrefix}_{$crc}",
                'lifetime' => (
                    $config->application->production ?
                        $config->application->cacheMetaLifetime :
                        10
                ),
            ]);
            break;

        case 'file':
        case 'files':
            $metaData = new \Phalcon\Mvc\Model\MetaData\Files([
                "metaDataDir" => ROOT_PATH . $config->application->cacheMetaDir,
                "lifetime" => (
                    $config->application->production ?
                        $config->application->cacheMetaLifetime :
                        10
                ),
            ]);
            break;

        case 'memory':
        default:
            $metaData = new \Phalcon\Mvc\Model\MetaData\Memory();
            break;
    }

	return $metaData;
});

/**
 * @param $compiler \Phalcon\Mvc\View\Engine\Volt\Compiler
 */
function add2Compiler($compiler) {
    // default
    $compiler->addFunction('rand',          'rand');
    $compiler->addFunction('array_merge',   'array_merge');
    $compiler->addFunction('in_array',      'in_array');
    $compiler->addFunction('is_array',      'is_array');
    $compiler->addFunction('int',           'intval');
    $compiler->addFunction('preg_match',    'preg_match');
    $compiler->addFunction('is_file',       'is_file');
    $compiler->addFunction('substr',        'mb_substr');

    $compiler->addFilter('strip_tags',      'strip_tags');
    $compiler->addFilter('urlencode',       'urlencode');
    $compiler->addFilter('int',             'intval');
    $compiler->addFilter('timestamp',       function($value) { return "strtotime({$value})"; });
    $compiler->addFilter('var_dump',        function($value) { return "var_export({$value}, true)"; });
    $compiler->addFilter('first_uppercase',        function($value) { return "mb_strtoupper(mb_substr({$value}, 0,1)).mb_substr({$value}, 1)"; });

    // custom
    $compiler->addFunction('static', '\Helper\StaticWrapper::exec');
    $compiler->addFunction('image_path', '\Helper\Image::getInstance()->image');
    $compiler->addFunction('preview', '\Helper\Text::preview');
    $compiler->addFunction('text_substr', '\Helper\Text::substr');
    $compiler->addFunction('plural', '\Helper\Text::plural');
    $compiler->addFunction('platform', '\Helper\UA::Platform');

    $compiler->addFunction('widget', function($resolvedArgs, $exprArgs) use ($compiler, $view) {
        $class = trim($compiler->expression($exprArgs[0]['expr']), "'");
        $params = empty($exprArgs[1]['expr']) ? null : $compiler->expression($exprArgs[1]['expr']);
        return "\\Site\\FrontendV2\\Widgets\\$class::widget($params)";
    });

    $compiler->addFunction('partial_local', function($resolvedArgs, $exprArgs) use ($compiler,$view) {
        $name = trim($compiler->expression($exprArgs[0]['expr']), "'");
        $values = trim($compiler->expression($exprArgs[1]['expr']), "'");
        $lang = trim($compiler->expression($exprArgs[2]['expr']), "'");

        $view = new \Phalcon\Mvc\View();
        $dir = $view->getViewsDir();

        $locale_file = '/languages/'.$lang.'/partials/'.$name;
        //echo '<br>'.$dir.$locale_file.'.volt';
        //exit;
        if (file_exists($dir.$locale_file.'.volt')) {
            /*
                 var_dump($view->re('../'.$locale_file));
                 exit;*/

        }
        //eturn $view->partial($name,$values);
    });

    $compiler->addFilter('hash_number', function($value) { return "\Helper\Text::hashNumber({$value})"; });
    $compiler->addFilter('cut', function($value, $exprArgs) use ($compiler) {
        $value = empty($exprArgs[0]['expr']) ? null : $compiler->expression($exprArgs[0]['expr']);
        $length = empty($exprArgs[1]['expr']) ? null : $compiler->expression($exprArgs[1]['expr']);
        $offset = empty($exprArgs[2]['expr']) ? 0 : $compiler->expression($exprArgs[2]['expr']);

        if(!$length) {
            return "{$value}";
        }

        return "mb_substr({$value}, $offset, $length)";
    });

    $compiler->addFilter('html_remove',     function($value) { return "\Helper\Text::removeHtml({$value})"; });
    $compiler->addFilter('go',              function($value) { return "\Helper\Go::exec({$value})"; });

    $compiler->addFilter('date_utc',        function($value) { return "\Helper\Datetime::exec({$value}, 'utc')"; });
    $compiler->addFilter('time_hm',         function($value) { return "\Helper\Datetime::exec({$value}, 'time_hm')"; });
    $compiler->addFilter('date_dmr',        function($value) { return "\Helper\Datetime::exec({$value}, 'dmr')"; });
    $compiler->addFilter('date_dmr_pretty', function($value) { return "\Helper\Datetime::exec({$value}, 'dmr_pretty')"; });
    $compiler->addFilter('date',            function($value) { return "\Helper\Datetime::exec({$value}, 'date')"; });
    $compiler->addFilter('date_sql',        function($value) { return "\Helper\Datetime::exec({$value}, 'sql_date')"; });
    $compiler->addFilter('date_y',          function($value) { return "\Helper\Datetime::exec({$value}, 'year')"; });
    $compiler->addFilter('time',            function($value) { return "\Helper\Datetime::exec({$value}, 'time')"; });
    $compiler->addFilter('datetime',        function($value) { return "\Helper\Datetime::exec({$value}, 'datetime')"; });
    $compiler->addFilter('datetime_check',  function($value) { return "\Helper\Datetime::exec({$value}, 'check')"; });
    $compiler->addFilter('datetime_pretty', function($value) { return "\Helper\Datetime::exec({$value}, 'pretty')"; });

    $compiler->addFilter('http',            function($value) { return "\Helper\String2Url::url({$value})"; });
    $compiler->addFilter('http_domain',     function($value) { return "\Helper\String2Url::domain({$value})"; });
    $compiler->addFilter('http_unset',      function($value) { return "\Helper\String2Url::unsetHttpProtocol({$value})"; });

    $compiler->addFunction('widget', function($resolvedArgs, $exprArgs) use ($compiler, $view) {
        preg_match('@\/site\/([^\/]+)\/views\/@ui', $exprArgs[0]['file'], $match);
        $module = implode('', array_map(function($value) {
            return ucfirst($value);
        }, explode('-', $match[1])));

        $class = trim($compiler->expression($exprArgs[0]['expr']), "'");
        $params = empty($exprArgs[1]['expr']) ? null : $compiler->expression($exprArgs[1]['expr']);

        return "\\Site\\{$module}\\Widget\\$class::widget($params)";
    });
}

$di->set('view', function() use ($config) {
    $eventsManager = new Phalcon\Events\Manager();

	$view = new \Phalcon\Mvc\View();

    $eventsManager->attach("view:afterRender", function($event, $view) {
        $content = $view->getContent();

        if(!isset($_GET['debug'])) {
            $content = preg_replace('@^\s+@usmi', '', $content);
        }

        if($view->seo && $view->seo['noindex']) {
            header('X-Robots-Tag: noindex');
        }

        $view->setContent(
            $content .
            "<!-- widget complete: " . (microtime(true) - INIT_MICROTIME) . " -->\n\n"
        );
    });

    $view->setLayoutsDir($config->application->viewsLayoutsDir);
    $view->setPartialsDir($config->application->viewsPartialsDir);

    $view->registerEngines(array(
        '.volt' => function($view, $di) use ($config) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                'compiledPath'      => __DIR__ .'/../'. $config->cache->view_directory,
                'compiledSeparator' => '_',
                'compileAlways'     => $config->application->production && !isset($_GET['debug']) ? false : true,
                'stat'              => true, // Если Phalcon должен проверять, существуют ли различия между файлом шаблона и его скомпилированным результатом
                'compiledExtension' => '.php' // Дополнительное расширение, добавляемое к скомпилированным PHP-файлам
            ));

            $compiler = $volt->getCompiler();

            // default
            $compiler->addFunction('rand',          'rand');
            $compiler->addFunction('array_merge',   'array_merge');
            $compiler->addFunction('in_array',      'in_array');
            $compiler->addFunction('int',           'intval');
            $compiler->addFunction('preg_match',    'preg_match');
            $compiler->addFunction('is_file',       'is_file');
            $compiler->addFunction('substr',        'mb_substr');

            $compiler->addFilter('strip_tags',      'strip_tags');
            $compiler->addFilter('urlencode',       'urlencode');
            $compiler->addFilter('int',             'intval');
            $compiler->addFilter('timestamp',       function($value) { return "strtotime({$value})"; });
            $compiler->addFilter('var_dump',        function($value) { return "var_export({$value}, true)"; });

            // custom
            $compiler->addFunction('static', '\Helper\StaticWrapper::exec');
            $compiler->addFunction('image_path', '\Helper\Image::getInstance()->image');
            $compiler->addFunction('preview', '\Helper\Text::preview');
            $compiler->addFunction('text_substr', '\Helper\Text::substr');
            $compiler->addFunction('plural', '\Helper\Text::plural');
            $compiler->addFunction('platform', '\Helper\UA::Platform');

            $compiler->addFunction('widget', function($resolvedArgs, $exprArgs) use ($compiler, $view) {
                $class = trim($compiler->expression($exprArgs[0]['expr']), "'");
                $params = empty($exprArgs[1]['expr']) ? null : $compiler->expression($exprArgs[1]['expr']);
                return "\\Site\\FrontendV1\\Widgets\\$class::widget($params)";
            });


            $compiler->addFunction('partial_local', function($resolvedArgs, $exprArgs) use ($compiler,$view) {
                $name = trim($compiler->expression($exprArgs[0]['expr']), "'");
                $values = trim($compiler->expression($exprArgs[1]['expr']), "'");
                $lang = trim($compiler->expression($exprArgs[2]['expr']), "'");

                $view = new \Phalcon\Mvc\View();
                $dir = $view->getViewsDir();

                $locale_file = '/languages/'.$lang.'/partials/'.$name;
                //echo '<br>'.$dir.$locale_file.'.volt';
                //exit;
                if (file_exists($dir.$locale_file.'.volt')) {
               /*
                    var_dump($view->re('../'.$locale_file));
                    exit;*/

                }
                //eturn $view->partial($name,$values);
            });


            $compiler->addFilter('hash_number', function($value) { return "\Helper\Text::hashNumber({$value})"; });
            $compiler->addFilter('cut', function($value, $exprArgs) use ($compiler) {
                $value = empty($exprArgs[0]['expr']) ? null : $compiler->expression($exprArgs[0]['expr']);
                $length = empty($exprArgs[1]['expr']) ? null : $compiler->expression($exprArgs[1]['expr']);
                $offset = empty($exprArgs[2]['expr']) ? 0 : $compiler->expression($exprArgs[2]['expr']);

                if(!$length) {
                    return "{$value}";
                }

                return "mb_substr({$value}, $offset, $length)";
            });

            $compiler->addFilter('html_remove',     function($value) { return "\Helper\Text::removeHtml({$value})"; });
            $compiler->addFilter('go',              function($value) { return "\Helper\Go::exec({$value})"; });

            $compiler->addFilter('date_utc',        function($value) { return "\Helper\Datetime::exec({$value}, 'utc')"; });
            $compiler->addFilter('time_hm',         function($value) { return "\Helper\Datetime::exec({$value}, 'time_hm')"; });
            $compiler->addFilter('date_dmr',        function($value) { return "\Helper\Datetime::exec({$value}, 'dmr')"; });
            $compiler->addFilter('date_dmr_pretty', function($value) { return "\Helper\Datetime::exec({$value}, 'dmr_pretty')"; });
            $compiler->addFilter('date',            function($value) { return "\Helper\Datetime::exec({$value}, 'date')"; });
            $compiler->addFilter('date_sql',        function($value) { return "\Helper\Datetime::exec({$value}, 'sql_date')"; });
            $compiler->addFilter('date_y',          function($value) { return "\Helper\Datetime::exec({$value}, 'year')"; });
            $compiler->addFilter('time',            function($value) { return "\Helper\Datetime::exec({$value}, 'time')"; });
            $compiler->addFilter('datetime',        function($value) { return "\Helper\Datetime::exec({$value}, 'datetime')"; });
            $compiler->addFilter('datetime_check',  function($value) { return "\Helper\Datetime::exec({$value}, 'check')"; });
            $compiler->addFilter('datetime_pretty', function($value) { return "\Helper\Datetime::exec({$value}, 'pretty')"; });

            return $volt;
        },
        '.phtml'    => 'Phalcon\Mvc\View\Engine\Php',
        '.php'      => 'Phalcon\Mvc\View\Engine\Php',
    ));

    $view->setVars([
        'global_var' => [
            'production'            => $config->application->production,
            'domain'                => $config->application->domain,
            'version'               => $config->system->version,
            'recaptchaPublicKey'    => $config->recaptcha->public_key,
            'imageBlank'            => '/front/img/blank.png',
//            'ua'                    => \Helper\UA::Parse(),
        ],
        'seo'               => [],
        'js_modules_enabled' => [],
    ]);

    $view->setEventsManager($eventsManager);

	return $view;
});

$di->set('filter', function() use ($di)  {
    $filter = new \Phalcon\Filter();

    $filter->add('md5', function($value) {
        preg_match('/[0-9a-f]{32}/ui', $value, $match);

        if($match[0]) {
            return $match[0];
        }

        return null;
    });

    $filter->add('ip', function($value) {
        if(preg_match('@^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$@ui', $value, $match)) {

            return sprintf(
                '%03d.%03d.%03d.%03d',
                $match[1],
                $match[2],
                $match[3],
                $match[4]
            );
        }
        elseif(preg_match('@^([a-f0-9]{8})$@ui', $value, $match)) {
            $ip = str_split($value, 2);

            return sprintf(
                '%03d.%03d.%03d.%03d',
                hexdec($ip[0]),
                hexdec($ip[1]),
                hexdec($ip[2]),
                hexdec($ip[3])
            );
        }
        elseif(filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {

            return mb_strtolower($value);
        }

        return null;
    });

    return $filter;
});

$di->set('cache', function() use ($di, $config) {
    $frontcache = new \Phalcon\Cache\Frontend\Data([
        "lifetime" => (
        $config->application->production ?
            $config->application->cacheLifetime :
            10
        ),
    ]);

    switch($config->application->cacheType) {
        case 'apcu':
        case 'apc':
            $crc = substr(md5($config->system->version), 0, 4);
            $backend = new \Phalcon\Cache\Backend\Apc($frontcache, [
                'prefix' => "{$config->application->cachePrefix}_{$crc}",
            ]);

            break;

        case 'file':
        case 'files':
            $backend = new \Phalcon\Cache\Backend\File($frontcache, [
                'cacheDir' => ROOT_PATH . $config->application->cacheDirPath,
            ]);

            break;

        case 'memory':
        default:
            $backend = new \Phalcon\Cache\Backend\Memory($frontcache);
            break;
    }

    return $backend;
});

$di->set('user', function() use ($di, $config) {
    $session = $di->getShared('session');
    $cookies = $di->getShared('cookies');

    // Авторизация по session
    if($session) {
        $auth = $session->get('auth');
        if($auth && $auth['user_id']){
            return \Model\User::findFirst(array(
                "user_id = :user_id:",
                'bind' => array(
                    'user_id' => $auth['user_id'],
                )
            ));
        }
    }

    // Авторизация по member
    if($cookies) {
        try {
            $memberId = $cookies->get($config->session->member_cookie)->getValue();
        }
        catch(\Exception $e) {
            $memberId = null;
        }

        if($memberId) {
            $userMember = \Model\UserMember::findFirst(array(
                "user_member_id = :user_member_id:",
                'bind' => array(
                    'user_member_id' => $memberId,
                )
            ));

            if($userMember) {
                $user = \Model\User::findFirst(array(
                    "user_id = :user_id:",
                    'bind' => array(
                        'user_id' => $userMember->user_id,
                    )
                ));

                if($user) {
                    $transactionManager = new TransactionManager();
                    $transaction = $transactionManager->get();

                    $setCookies = function($delete = false) use ($config) {
                        setcookie(
                            $config->session->member_cookie,
                            $delete ? null : $_COOKIE[$config->session->member_cookie],
                            $delete ? -1 : time() + $config->session->member_cookie_lifetime,
                            '/',
                            $config->session->domain,
                            false,
                            true
                        );
                    };

                    try {
                        $userMember->setTransaction($transaction);
                        $userMember->assign(array(
                            'user_member_mdate' => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                        ));

                        if($userMember->save() === false) {
                            $transaction->rollback("can't save 'user_member'");
                        }

                        $userAccessLog = new \Model\UserAccessLog();
                        $userAccessLog->setTransaction($transaction);

                        $userAccessLog->assign([
                            'user_access_log_ip'    => $di->get('filter')->sanitize(IP, 'ip'),
                            'user_access_log_ua'    => $_SERVER['HTTP_USER_AGENT'],
                            'user_id'               => $user->user_id,
                        ]);

                        if($userAccessLog->save() === false) {
                            $transaction->rollback("can't save 'user_access_log'");
                        }

                        $transaction->commit();

                        $setCookies();

                        $session->set('auth', array(
                            'user_id'           => $user->user_id,
                        ));

                        return $user;
                    }
                    catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
                        $setCookies(true);
                    }
                }
            }
        }
    }

    return new \Model\User();
});

/**
 * Определение языка и домена
 */
$di->set('translate', function() use ($di, $config) {

    $user    = $di->getShared('user');
    $session = $di->getShared('session');
    $cookies = $di->getShared('cookies');

    $t = new \Gettext\Translations();
    $t->setDomain($config->application->domain);
    $lang = $config->application->default_lang;

    if ($cookies) {
        $l = $cookies->get('lang')->getValue();
        if ($l) {
            $lang = $l;
        }
    }

    if ($user && $user->user_lang) {
        $lang = $user->user_lang;
    }

    if ($session) {
        $l = $session->get('lang');
        if ($l) {
            $lang = $l;
        }
    }

    $t->setLanguage($lang);

    return $t;
});