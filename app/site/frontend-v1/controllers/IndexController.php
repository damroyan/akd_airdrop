<?php
namespace Site\FrontendV1\Controllers;

class IndexController extends Controller {
    public function initialize() {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function indexAction() {

        $this->view->setVar('active_tab','index');

        /*$this->view->setVar('user_messages',[
            [
            'message_type'=>'warning',
            'message_text'=>'Hello USer! here is notification system from the box',
        ],
        ]);*/
    }

    /**
     * Выставляет нужную локаль для сессии и кук
     *
     * @Role({"allow": ['public']})
     */
    public function setLanguageAction() {

        $lang = $this->request->getQuery('lang');

        if (!in_array($lang,explode(',',$this->config->application->accepted_languages))) {
            return $this->error404();
        }

        $this->session->set('lang',$lang);

        $this->cookies->set(
            'lang',
            $lang,
            time() + $this->config->session->member_cookie_lifetime,
            '/',
            null,
            $this->config->session->domain,
            true
        );
        $this->response->sendCookies();

        return $this->response->redirect($this->url->get(array('for'=>'frontend-v1-index-index')));

    }

    /**
     * Результаты поиска. Все что хотим найти фигарим сюда
     *
     * @Role({"allow": ['public']})
     *
     */
    public function searchAction() {
        $q = $this->request->getQuery('q');

        $results    = [];
        if ($q) {
            //  все что мы должны делать с запросом, делаем здесь
        }

        $this->view->setVar('q',        $q);
        $this->view->setVar('results',  $results);
    }
}