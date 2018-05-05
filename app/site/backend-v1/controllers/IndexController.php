<?php
namespace Site\BackendV1\Controllers;

class IndexController extends Controller {
    public function initialize() {
        parent::initialize();
    }

    /**
     * Главный dashboard
     *
     * @Role({"allow": ['moderator','admin']})
     */
    public function indexAction() {
        $this->view->setVar('user_messages',[[
            'message_type'=>'danger',
            'message_text'=>'Hello USer! here is notification system from the box',
        ],]);
    }

    /**
     * Тестирование дизайна мейла, который будет отправляться пользователям
     *
     * @Role({"allow": ['moderator','admin']})
     */
    public function emailAction() {

        $template = $this->request->getQuery('template', 'string');
        if (!$template) $template = 'test_email';

        $params = $this->request->get();

        $view = $this->view;

        $view->setViewsDir(APPLICATION_PATH . "/site/frontend-v1/views/");
        $view->start();
        $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $view->setVars($params, true);
        $view->render('email', $template);
        $view->finish();

        echo $view->getContent();

        exit;
    }

}