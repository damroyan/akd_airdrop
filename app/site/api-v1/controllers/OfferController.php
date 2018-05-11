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
            'offer_description'         => params_has_or_null($params['offer_description']),
            'offer_profit'              => params_has_or_null($params['offer_profit']),
            'offer_url'                 => params_has_or_null($params['offer_url']),
            'offer_site_url'            => $params['offer_site_url'],
            'offer_end_date'            => params_has_or_null($params['offer_end_date']),
            'offer_rating'              => params_has_or_null($params['offer_rating']),
            'offer_status'              => params_has_or_null($params['offer_status']),
            'offer_views'               => params_has_or_null($params['offer_views']),
            'offer_priority'            => params_has_or_null($params['offer_priority']),
            'offer_featured'            => $params['offer_featured'],
        ]);

        if(!$offer->save()) {
            return \Response::Error(500, 'db_error_insert');
        }

        return \Response::Ok([
            'offer_id'   => $offer->user_id,
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

                if(!$offer->save()) {
                    return \Response::Error(500, 'db_error_update');
                }

                break;

            default:
                break;
        }

        return \Response::Ok([
            'offer_id'       => $offer->offer_id,
        ]);
    }

}
