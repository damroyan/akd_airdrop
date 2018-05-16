<?php
namespace Site\FrontendV1\Controllers;

/**
 * Class Controller
 *
 * @property $dispatcher \Phalcon\Dispatcher
 * @package Site\FrontendV1\Controllers
 */
class Controller extends \Controller {

    const LIMIT_LIST = 20;

    public function initialize() {

        $this->view->setVar('language',$this->translate->getLanguage());

        /*
 * CORE get GLOBAL messages -- START
 */
        $system_messages = \Model\Message::find([
            'conditions'    => 'user_role = :user_role: AND message_status=:message_status: AND message_frequency = :message_frequency:',
            'bind'          => [
                'user_role'      => $this->user->user_role,
                'message_status' => \Model\Message::STATUS_ACTIVE,
                'message_frequency' => \Model\Message::FREQUENCY_GLOBAL,
            ]
        ]);

        $all_message_arr = $system_messages->toArray();
        $message_arr = [];
        for($i=0;$i<count($all_message_arr);$i++) {
            $m = \Model\UserMessage::findFirst([
                'conditions'    => 'user_id = :user_id: AND message_id = :message_id:',
                'bind'          => [
                    'user_id'       => $this->user->user_id,
                    'message_id'    => $all_message_arr[$i]['message_id'],
                ]
            ]);
            if (!$m) {
                $message_arr[] = $all_message_arr[$i];
            }
        }
        $this->view->setVar('system_messages',$message_arr);
        /*
         * CORE get GLOBAL messages -- END
         */


        /**
         * Global featured offers
         */
        $featured_offers = \Model\Offer::find([
            'conditions'    => 'offer_featured = :offer_featured: AND offer_status=:offer_status: AND offer_delete = :offer_delete:',
            'bind'          => [
                'offer_featured'    => \Model\Offer::STATUS_ACTIVE,
                'offer_status'      => \Model\Offer::STATUS_ACTIVE,
                'offer_delete'      => \Model\Offer::DELETE_FALSE,
            ],
            'order'         => 'offer_id DESC'
        ]);

        $featured_offers = $featured_offers->toArray();

        $i = 0;
        while ($i < count($featured_offers)) {
            $featured_offers[$i]['offer_days_left'] = round((strtotime($featured_offers[$i]['offer_end_date']) - time())/(60*60*24));
            $i++;
        }

        $this->view->setVar('featured_offers',$featured_offers);


    }
    final protected function paginateSeoUrl($route, $currentPage = null, $lastPage = null, $title = null) {
        $return = [];
        $return['og_url'] = $this->url->get(
            $route, [
            'page' => $currentPage > 1 ? $currentPage : null,
        ]);

        if($currentPage) {
            if($lastPage && $lastPage > $currentPage) {
                $return['og_url_next'] = $this->url->get(
                    $route, [
                    'page' => ($currentPage + 1),
                ]);
            }

            if($currentPage > 1) {
                $return['og_url_prev'] = $this->url->get(
                    $route, [
                    'page' => ($currentPage - 1) > 1 ? ($currentPage - 1) : null,
                ]);
            }
        }

        if($title) {
            $return['og_title'] = $title;
            $return['title'] = $title . (
                $currentPage > 1 ? " (Страница: {$currentPage})" : ''
            );
        }

        return $return;
    }

    final protected function error401() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\FrontendV1\Controllers',
            'controller'    => 'error',
            'action'        => 'auth',
        ]);
    }

    final protected function error403() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\FrontendV1\Controllers',
            'controller'    => 'error',
            'action'        => 'forbidden',
        ]);
    }
    
    final protected function error404() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\FrontendV1\Controllers',
            'controller'    => 'error',
            'action'        => 'notfound',
        ]);
    }

    final protected function error550() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\FrontendV1\Controllers',
            'controller'    => 'error',
            'action'        => 'permission',
        ]);
    }

    final protected function error500() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\FrontendV1\Controllers',
            'controller'    => 'error',
            'action'        => 'internal',
        ]);
    }
}