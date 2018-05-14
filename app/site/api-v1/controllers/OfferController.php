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
            'offer_url'                 => params_has_or_null($params['offer_url']),
            'offer_site_url'            => $params['offer_site_url'],
            'offer_end_date'            => params_has_or_null($params['offer_end_date']),
            'offer_rating'              => params_has_or_null($params['offer_rating']),
            'offer_status'              => params_has_or_null($params['offer_status']),
            'offer_views'               => params_has_or_null($params['offer_views']),
            'offer_priority'            => params_has_or_null($params['offer_priority']),
            'offer_featured'            => $params['offer_featured'],
            'offer_code'                => $params['offer_code'],
        ]);

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_insert');
        }

        return \Response::Ok([
            'message'       => __('Оффер создан. Осталось добавить картинку и описание'),
            'redirect'      => $this->url->get([
                'for' => 'frontend-v1-offer-descedit'
            ], [
                'offer_id' => $offer->offer_id
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
     * Редактирование пользователя админом
     *
     * @Role({"allow": ['admin']})
     * @return \Phalcon\Http\Response
     */
    public function descUpdateAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

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
        } else {
            return \Response::Error(400, 'offer_id_empty');
        }


        $offer->assign([
            'offer_description'         => params_has_or_null($params['offer_description']),
        ]);

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_insert');
        }

        return \Response::Ok([
            'message'       => __('Описание обновлено.'),
            'redirect'      => $this->url->get(['for'=>'frontend-v1-offer-list']),
        ]);
    }

    /**
     * Обновление картинки пользователя
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function pictureAction() {

        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        if(!isset($params['user_picture'])) {
            return \Response::Error(
                400,
                __('New User Photo was not saved because of technical reasons (we not receive image). Please contact support.'));
        }

        // проверка, что файл локальный
        // сделать епта :)

        $uploadSingle = $this->uploadSingleImage(
            $this->user,
            $params,
            'user_picture',
            $this->config->image->directory_upload
        );

        $offer = \Model\Offer::findFirst([
            'conditions'    => 'offer_id = :offer_id:',
            'bind'          => [
                'offer_id'       => $params['offer_id'],
            ]
        ]);
        if (!$offer) {
            return \Response::Error(404,
                __('Offer was not found. Please contact support to fix this problem.'));
        }

        $offer->assign([
            'offer_picture'  => $params['user_picture'],
        ]);
        $offer->save();

        $image =  \Helper\Image::getInstance();

        return \Response::Ok([
            'user_picture'  => $image->image($this->user->user_picture,50, 50, 'mwh', 'png', true)->__toString(),
        ]);

    }

}
