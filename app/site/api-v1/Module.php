<?php
namespace Site\ApiV1;

class Module {
	public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = null) {
        $loader = new \Phalcon\Loader();

        $loader->registerDirs(array(
            __DIR__ . '/controllers/',
            __DIR__ . '/plugins/',
            __DIR__ . '/validators/',
        ));

        $loader->registerNamespaces(
            array(
                'Site\ApiV1\Controllers' => __DIR__ . '/controllers/',
                'Site\ApiV1\Validators'  => __DIR__ . '/validators/',
                'Site\ApiV1\Plugins'     => __DIR__ . '/plugins/',
                'Site\ApiV1'             => __DIR__,
            )
        );

        $loader->register();
	}

	public function registerServices(\Phalcon\DiInterface $di) {
        $di->set('dispatcher', function() {
            $eventsManager = new \Phalcon\Events\Manager();

            /**
             * Handle exceptions and not-found exceptions using NotFoundPlugin
             */
            //$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

            /**
             * Check if the user is allowed to access certain action using the SecurityPlugin
             */
            $eventsManager->attach('dispatch:beforeDispatch', new Plugins\SecurityPlugin());

            $dispatcher = new \Phalcon\Mvc\Dispatcher;
            $dispatcher->setDefaultNamespace('Api\V1\Controllers');
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });

        $view = $di->get('view');
        $config = $di->get('config');
        $translate = $di->get('translate');

        // Регистрация компонента представлений
        $di->set(
            'view',
            function() use ($view, $config, $translate) {
                $view->setViewsDir(__DIR__ . '/views/');
                $t = new \Gettext\Translator();

                if ($translate) {
                    $lang = $translate->getLanguage();


                    $translate_file = __DIR__.'/views/languages/'.$lang.'/'.$lang.'.po';
                    if (file_exists($translate_file)) {
                        \Gettext\Extractors\Po::fromFile($translate_file,$translate);
                        $t->loadTranslations($translate);
                    }
                }

                $view->setVar('t',$t);
                $view->setVar('lang',$lang);

                $t->register();

                return $view;
            }
        );
	}
}