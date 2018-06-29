<?php
namespace Site\ApiV1\Controllers;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class OfferController extends Controller {
    use \TraitUserMember;
    use \TraitRecaptcha;
    use \TraitParamRedirect;

    public function initialize() {
        parent::initialize();
    }
    /**
     * Редактирование пользователя админом
     *
     * @Role({"allow": ['admin']})
     * @return \Phalcon\Http\Response
     */
    public function editAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\OfferEdit(),
            $params,
            function($params) {
                if( ((int)$params['offer_rating'] > 5)  OR ((int)$params['offer_rating'] < 1)) {
                    \Response::FieldsAdd('offer_rating', 'Рейтинг это число от 1 до 5');
                }

                if(!preg_match('%^[A-Za-z0-9]+$%', $params['offer_code'])) {
                    \Response::FieldsAdd('offer_code', 'Код валюты может содержать только буквы и цифры
                     от 2х до 4х символов');
                }

                if((strlen($params['offer_code']) > 4) OR (strlen($params['offer_code']) < 2)) {
                    \Response::FieldsAdd('offer_code', 'Код валюты может содержать только буквы и цифры
                     от 2х до 4х символов');
                }
            }
        );
        if(is_http_response($result)) { return $result; }


        if (!$params['offer_priority']) {
            $params['offer_priority'] = 0;
        }

        if (!$params['offer_featured']) {
            $params['offer_featured'] = 0;
        }

        if (!$params['offer_views']) {
            $params['offer_views'] = rand(100,200);
        }

        // разбираем массив с урлами
        $offer_urls = $params['offer_url'];
        $offer_urls = array_diff($offer_urls, array(''));
        $params['offer_url'] = json_encode($offer_urls);

        if($params['offer_id']) {
            $offer = \Model\Offer::findFirst([
                "offer_id = :offer_id:",
                'bind' => [
                    'offer_id' => $params['offer_id'],
                ]
            ]);

            if(!$offer) {
                return \Response::Error(400, 'offer_id_incorrect');
            }
        }
        else {
            $offer = new \Model\Offer();
        }


        $offer->assign([
            'offer_name'                => params_has_or_null($params['offer_name']),
            'offer_type'                => params_has_or_null($params['offer_type']),
            'offer_profit'              => params_has_or_null($params['offer_profit']),
            'offer_real_profit'         => params_has_or_null($params['offer_real_profit']),
            'offer_url'                 => params_has_or_null($params['offer_url']),
//            'offer_site_url'            => $params['offer_site_url'],
            'offer_end_date'            => params_has_or_null($params['offer_end_date']),
            'offer_rating'              => params_has_or_null($params['offer_rating']),
            'offer_status'              => params_has_or_null($params['offer_status']),
            'offer_views'               => params_has_or_null($params['offer_views']),
            'offer_priority'            => params_has_or_null($params['offer_priority']),
            'offer_featured'            => $params['offer_featured'],
            'offer_code'                => $params['offer_code'],
            'offer_description'         => $params['offer_description'],
            'offer_picture'             => $params['offer_picture'],
        ]);

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_insert');
        }

        return \Response::Ok([
            'message'       => __('Offer created.'),
            'redirect'      => $this->url->get([
                'for' => 'frontend-v1-offer-list'
            ])
        ]);
    }

    /**
     * Активация / деактивация оффера
     *
     * @Role({"allow": ['admin']})
     * @return \Phalcon\Http\Response
     */
    public function togglebanAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $user = null;
        if($params['offer_id']) {
            $offer = \Model\Offer::findFirst([
                "offer_id = :offer_id:",
                'bind' => [
                    'offer_id'       => $params['offer_id'],
                ]
            ]);
        }

        if(!$offer) {
            return \Response::Error(400, 'offer_id_incorrect');
        }

        switch((int)$offer->offer_status) {
            case \Model\Offer::STATUS_ACTIVE:
                $offer->assign([
                    'offer_status' => \Model\Offer::STATUS_INACTIVE,
                ]);
                break;

            case \Model\Offer::STATUS_INACTIVE:
                $offer->assign([
                    'offer_status' => \Model\Offer::STATUS_ACTIVE,
                ]);
                break;

            default:
                return \Response::Error(400, 'api_method_bad_request');
                break;
        }

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_update');
        }

        return \Response::Ok([
            'offer_id'       => $offer->offer_id,
        ]);
    }


    /**
     * Удаление / восстановление оффера
     *
     * @Role({"allow": ['admin']})
     * @return \Phalcon\Http\Response
     */
    public function toggledeleteAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $user = null;
        if($params['offer_id']) {
            $offer = \Model\Offer::findFirst([
                "offer_id = :offer_id:",
                'bind' => [
                    'offer_id'       => $params['offer_id'],
                ]
            ]);
        }

        if(!$offer) {
            return \Response::Error(400, 'offer_id_incorrect');
        }

        switch((int)$offer->offer_delete) {
            case \Model\Offer::DELETE_FALSE:
                $offer->assign([
                    'offer_delete' => \Model\Offer::DELETE_TRUE,
                ]);

                break;

            case \Model\Offer::DELETE_TRUE:
                $offer->assign([
                    'offer_delete' => \Model\Offer::DELETE_FALSE,
                ]);

                break;

            default:
                break;
        }

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_update');
        }

        return \Response::Ok([
            'offer_id'       => $offer->offer_id,
        ]);
    }


    /**
     * Возвращает список офферов
     *
     * на вход limit, offset
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function listAction() {
//        $params = $this->getParams();
        $params = $this->request->getQuery();
        if(is_http_response($params)) { return $params; }

        if (!$params['hash'] or $params['hash'] != 'fjhdTe7dnvWckid_fjhsyY98124') {
            return \Response::Error(403, 'hash_error');
        }

        if (!$params['limit']) {
            $limit = 50;
        } else {
            $limit = $params['limit'];
        }

        if (!$params['offset']) {
            $offset = 0;
        } else {
            $offset = $params['offset'];
        }

        $offers = \Model\Offer::find([
            "offer_status = :offer_status: AND offer_delete = :offer_delete:",
            'bind' => [
                'offer_status'  => \Model\Offer::STATUS_ACTIVE,
                'offer_delete'  => \Model\Offer::DELETE_FALSE,
            ],
            'columns' => 'offer_id,offer_name,offer_picture,offer_type,offer_description,offer_url,offer_profit,offer_real_profit,offer_end_date,offer_rating,offer_priority,offer_status,offer_code,offer_limit',
            'limit' => $limit,
            'offset' => $offset,
            'order'     => 'offer_id DESC'
        ]);

        $offers = $offers->toarray();

        $count = 0;
        while ($count < count($offers)) {
            if (!empty($offers[$count]['offer_picture'])) {
                $offers[$count]['offer_picture'] = $this->config->application->protocol.$this->config->application->domain.$offers[$count]['offer_picture'];
            }
            $count++;
        }

        ob_start("ob_gzhandler");

        return \Response::Ok($offers);
    }

    /**
     * Возвращает данные по офферу
     *
     * на вход offer_id
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function detailsAction() {
//        $params = $this->getParams();
        $params = $this->request->getQuery();

        if(is_http_response($params)) { return $params; }

        if (!$params['hash'] or $params['hash'] != 'fjhdTe7dnvWckid_fjhsyY98124') {
            return \Response::Error(403, 'hash_error');
        }

        if (!$params['offer_id']) {
            return \Response::Error(400, 'offer_id_empty');
        }

        $offer = \Model\Offer::findfirst([
            'columns' => 'offer_id,offer_name,offer_picture,offer_description,offer_type,offer_profit,offer_real_profit,offer_url,offer_end_date,offer_rating,offer_code,offer_status',
            "offer_id = :offer_id:",
            'bind' => [
                'offer_id' => $params['offer_id'],
            ]
        ]);

        if (!empty($offer->offer_picture)) {
            $offer->offer_picture = $this->config->application->protocol.$this->config->application->domain.$offer->offer_picture;
        }
        return \Response::Ok($offer);
    }

}
