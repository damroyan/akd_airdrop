<?php
namespace Site\FrontendV1;

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
                'Site\FrontendV1\Controllers' => __DIR__ . '/controllers/',
                'Site\FrontendV1\Validators'  => __DIR__ . '/validators/',
                'Site\FrontendV1\Plugins'     => __DIR__ . '/plugins/',
                'Site\FrontendV1\Widgets'     => __DIR__ . '/widgets/',
                'Site\FrontendV1'             => __DIR__,
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
            $dispatcher->setDefaultNamespace('Site\FrontendV1\Controllers');
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });

        // Регистрация компонента представлений
        $view = $di->get('view');
        $config = $di->get('config');
        $translate = $di->get('translate');

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
                $view->setVar('language',$lang);

                $t->register();

                return $view;
            }
        );
/*
        $di->set(
            'widgetView',
            function() use ($di) {
                $view = clone $di->get('view');

                $view->setPartialsDir('../views/partials/');
                $view->setViewsDir(__DIR__ . '/widgets/');

                return $view;
            }
        );*/

	}
}