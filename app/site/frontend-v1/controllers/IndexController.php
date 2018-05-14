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
        $params = $this->dispatcher->getParams();
        $this->view->setVar('active_tab','index');

        $offer_status = \Model\Offer::STATUS_ACTIVE;
        if ($params[0] == 'closed') {
            $this->view->setVar('active_tab','closed_offers');
            $offer_status = \Model\Offer::STATUS_INACTIVE;
        } elseif ($params[0] == 'active') {
            $this->view->setVar('active_tab','offers');
        }

        $offers = \Model\Offer::find([
            'conditions'    => 'offer_status = :offer_status: AND offer_delete=:offer_delete:',
            'bind'          => [
                'offer_delete' => \Model\Offer::DELETE_FALSE,
                'offer_status' => $offer_status,
            ],
            'order'         => 'offer_priority DESC, offer_id DESC'
        ]);

        $fresh_offers = \Model\Offer::find([
            'conditions'    => 'offer_status = :offer_status: AND offer_delete=:offer_delete:',
            'bind'          => [
                'offer_delete' => \Model\Offer::DELETE_FALSE,
                'offer_status' => \Model\Offer::STATUS_ACTIVE,
            ],
            'order'         => 'offer_id DESC',
            'limit'         => 7
        ]);

        $fresh_offers_array = [];
        foreach ($fresh_offers as $fresh_offer) {
            $fresh_offers_array[] = $fresh_offer->offer_id;
        }

        $hot_offers = \Model\Offer::find([
            'conditions'    => 'offer_status = :offer_status: AND offer_delete=:offer_delete: AND offer_priority=:offer_priority:',
            'bind'          => [
                'offer_delete' => \Model\Offer::DELETE_FALSE,
                'offer_status' => \Model\Offer::STATUS_ACTIVE,
                'offer_priority' => \Model\Offer::STATUS_ACTIVE,
            ],
        ]);

        $hot_offers_array = [];
        foreach ($hot_offers as $hot_offer) {
            $hot_offers_array[] = $hot_offer->offer_id;
        }

        $this->view->setVar('offers', $offers);
        $this->view->setVar('fresh_offers', $fresh_offers_array);
        $this->view->setVar('hot_offers', $hot_offers_array);



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

    /**
     * @Role({"allow": ['public']})
     */
    public function faqAction() {

        $this->view->setVar('sidebar_active','faq');
    }
}