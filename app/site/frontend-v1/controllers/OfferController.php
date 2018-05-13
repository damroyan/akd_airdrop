<?php
namespace Site\FrontendV1\Controllers;

class OfferController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function oneAction()
    {

        $this->view->setVar('active_tab', 'index');

    }

    /**
     * @Role({"allow": ['public']})
     */
    public function pageAction()
    {
        $this->view->setVar('active_tab', 'index');

        $params = $this->dispatcher->getParams();

        $offer = \Model\Offer::findfirst([
            'conditions'    => 'offer_id = :offer_id: 
                            AND offer_status = :offer_status: 
                            AND offer_delete =:offer_delete:',
            'bind'          => [
                'offer_id' => $params['offer_id'],
                'offer_delete' => \Model\Offer::DELETE_FALSE,
                'offer_status' => \Model\Offer::STATUS_ACTIVE,
            ]
        ]);

        if(!$offer) {
            return $this->error404();
        }

        $this->view->setVar('offer', $offer);
    }

    /**
     * Главный dashboard
     *
     * @Role({"allow": ['moderator','admin']})
     */
    public function listAction() {
//        $this->view->header_tab = 'offer';

        $params = $this->request->getQuery();
        $params = $this->trimParams($params);

        $page = (int)$params['page'];
        if (!$page) { $page = 1; }

        $paramsGet  = [];
        $conditions = [];
        $bind       = [];

        if($params['query']) {
            $paramsGet['query'] = $params['query'];

            if(preg_match('@^\d+$@ui', $params['query'])) {
                $conditions[] = "o.offer_id = :offer_id:";
                $bind['offer_id'] = (int)$params['query'];
            }
            else {
                $conditions[] = "o.offer_name LIKE CONCAT('%', :offer_name:, '%')";
                $bind['offer_name'] = $params['query'];
            }
        }

        if($params['offer_delete']) {
            $paramsGet['offer_delete']   = 1;
            $params['offer_delete']      = 1;

            $conditions[] = "o.offer_delete = :offer_delete:";
            $bind['offer_delete']    = \Model\Offer::DELETE_TRUE;
        }
        else {
            $conditions[] = "o.offer_delete = :offer_delete:";
            $bind['offer_delete']    = \Model\Offer::DELETE_FALSE;
            $params['offer_delete']  = null;
        }

        if($params['offer_status'] || $params['offer_status'] === '0') {
            $paramsGet['offer_status']   = $params['offer_status'];
            $conditions[]               = "o.offer_status = :offer_status:";
            $bind['offer_status']        = (string)$params['offer_status'];
        }

        $builder = $this->modelsManager->createBuilder()
            ->addFrom('Model\Offer', 'o')
            ->andWhere(
                implode(" AND ", $conditions),
                $bind
            )
            ->orderBy('o.offer_id DESC')
        ;



        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            array(
                "builder"   => $builder,
                "limit"     => self::LIMIT_LIST,
                "page"      => $page,
            )
        );


        // Получение результатов работы ппагинатора
        $paginate = $paginator->getPaginate();
        if($paginate->current > $paginate->last && $page > 1) {
            return $this->error404();
        }

        $this->view->page = $paginate;
        $this->view->params_get = $paramsGet;

        \Phalcon\Tag::setDefaults([
            'query'         => params_has_or_null($params['query']),
            'offer_delete'  => $params['offer_delete'],
            'offer_status'  => $params['offer_status']
        ]);

    }

    /**
     * Создание/редактирование пользователя
     *
     * @Role({"allow": ['admin']})
     */
    public function editAction() {
        $offerId = $this->request->getQuery('offer_id', 'int');

        if($offerId) {
            $offer = \Model\Offer::findFirst(array(
                "offer_id = :offer_id:",
                'bind' => array(
                    'offer_id' => $offerId,
                ),
            ));

            if(!$offer) {
                return $this->error404();
            }
        }
        else {
            $offer = new \Model\Offer();

            $offer->assign([
                'user_status'   => \Model\User::STATUS_ACTIVE,
            ]);
        }

        $this->view->o = $offer;

        \Phalcon\Tag::setDefaults([
            'offer_id'              => params_has_or_null($offer->offer_id),
            'offer_name'            => params_has_or_null($offer->offer_name),
            'offer_type'            => params_has_or_null($offer->offer_type),
            'offer_description'     => params_has_or_null($offer->offer_description),
            'offer_profit'          => params_has_or_null($offer->offer_profit),
            'offer_url'             => params_has_or_null($offer->offer_url),
            'offer_site_url'        => params_has_or_null($offer->offer_site_url),
            'offer_end_date'        => params_has_or_null($offer->offer_end_date),
            'offer_rating'          => params_has_or_null($offer->offer_rating),
            'offer_status'          => params_has_or_null($offer->offer_status),
            'offer_featured'        => params_has_or_null($offer->offer_featured),
            'offer_priority'        => params_has_or_null($offer->offer_priority),
            'offer_views'           => $offer->offer_views,
            'offer_code'            => $offer->offer_code,

        ]);
    }

    /**
     * Создание/редактирование описания оффера
     *
     * @Role({"allow": ['admin']})
     */
    public function desceditAction() {
        $offerId = $this->request->getQuery('offer_id', 'int');

        if($offerId) {
            $offer = \Model\Offer::findFirst(array(
                "offer_id = :offer_id:",
                'bind' => array(
                    'offer_id' => $offerId,
                ),
            ));

            if(!$offer) {
                return $this->error404();
            }
        }

        $this->view->o = $offer;

        \Phalcon\Tag::setDefaults([
            'offer_id'              => params_has_or_null($offer->offer_id),
            'offer_name'            => params_has_or_null($offer->offer_name),
            'offer_type'            => params_has_or_null($offer->offer_type),
            'offer_description'     => params_has_or_null($offer->offer_description),
            'offer_profit'          => params_has_or_null($offer->offer_profit),
            'offer_url'             => params_has_or_null($offer->offer_url),
            'offer_site_url'        => params_has_or_null($offer->offer_site_url),
            'offer_end_date'        => params_has_or_null($offer->offer_end_date),
            'offer_rating'          => params_has_or_null($offer->offer_rating),
            'offer_status'          => params_has_or_null($offer->offer_status),
            'offer_featured'        => params_has_or_null($offer->offer_featured),
            'offer_priority'        => params_has_or_null($offer->offer_priority),
            'offer_views'           => $offer->offer_views,
        ]);
    }

}
