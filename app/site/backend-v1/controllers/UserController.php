<?php
namespace Site\BackendV1\Controllers;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class UserController extends Controller {
    use \TraitUserMember;
    use \TraitParamRedirect;

    public function initialize() {
        parent::initialize();

        $this->view->header_tab = 'user_user';
    }

    /**
     * Создание/редактирование пользователя
     *
     * @Role({"allow": ['admin']})
     */
    public function editAction() {
        $userId = $this->request->getQuery('user_id', 'int');

        if($userId) {
            $user = \Model\User::findFirst(array(
                "user_id = :user_id:",
                'bind' => array(
                    'user_id' => $userId,
                ),
            ));

            if(!$user) {
                return $this->error404();
            }
        }
        else {
            $user = new \Model\User();

            $user->assign([
                'user_status'   => \Model\User::STATUS_ACTIVE,
                'user_role'     => \Model\User::ROLE_USER,
            ]);
        }

        $this->view->u = $user;

        \Phalcon\Tag::setDefaults([
            'user_id'           => params_has_or_null($user->user_id),
            'user_login'        => params_email_or_null($user->user_login),
            'user_email'        => params_email_or_null($user->user_email),
            'user_name'         => params_has_or_null($user->user_name),
            'user_firstname'    => params_has_or_null($user->user_firstname),
            'user_lastname'     => params_has_or_null($user->user_lastname),
            'user_picture'      => params_has_or_null($user->user_picture),
            'user_role'         => params_has_or_null($user->user_role),
            'user_cdate'        => params_datetime_or_null($user->user_cdate),
            'user_status'       => $user->user_status,
        ]);
    }

    /**
     * Профиль пользователя
     *
     * @Role({"allow": ['moderator']})
     */
    public function profileAction() {
        $userId = $this->request->getQuery('user_id', 'int');

        $user = null;
        if(!$userId) {
            $user = $this->user;
        }
        else {
            $user = \Model\User::findFirst(array(
                "user_id = :user_id:",
                'bind' => array(
                    'user_id' => $userId,
                ),
            ));
        }

        if(!$user) {
            return $this->error404();
        }

        $this->view->u = $user;
    }

    /**
     * Список авторизаций
     *
     * @Role({"allow": ['admin']})
     */
    public function accesslogAction() {
        $this->view->header_tab = 'user_accesslog';

        $params = $this->request->getQuery();
        $params = $this->trimParams($params);

        $page = (int)$params['page'];
        if (!$page) { $page = 1; }

        $paramsGet  = [];
        $conditions = [];
        $bind       = [];

        $builder = $this->modelsManager->createBuilder()
            ->addFrom('Model\UserAccessLog', 'ual');

        if($params['user_access_log_ip']) {
            $paramsGet['user_access_log_ip']    = $params['user_access_log_ip'];
            $conditions[]                       = "ual.user_access_log_ip = :user_access_log_ip:";
            $bind['user_access_log_ip']         = (string)$params['user_access_log_ip'];
        }

        if($params['user_id']) {
            $paramsGet['user_id']   = $params['user_id'];
            $conditions[]           = "ual.user_id = :user_id:";
            $bind['user_id']        = (string)$params['user_id'];
        }

        $builder
            ->andWhere(
                implode(" AND ", $conditions),
                $bind
            )
            ->orderBy('ual.user_access_log_id DESC')
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
    }

    /**
     * Список пользователей
     *
     * @Role({"allow": ['admin']})
     */
    public function indexAction() {
        $params = $this->request->getQuery();
        $params = $this->trimParams($params);

        $page = (int)$params['page'];
        if (!$page) { $page = 1; }

        $paramsGet  = [];
        $conditions = [];
        $bind       = [];

        if($params['user_delete']) {
            $paramsGet['user_delete']   = 1;
            $params['user_delete']      = 1;

            $conditions[] = "u.user_delete = :user_delete:";
            $bind['user_delete']    = \Model\User::DELETE_TRUE;
        }
        else {
            $conditions[] = "u.user_delete = :user_delete:";
            $bind['user_delete']    = \Model\User::DELETE_FALSE;
            $params['user_delete']  = null;
        }

        if($params['query']) {
            $paramsGet['query'] = $params['query'];

            if(preg_match('@^\d+$@ui', $params['query'])) {
                $conditions[] = "u.user_id = :user_id:";
                $bind['user_id'] = (int)$params['query'];
            }
            elseif(params_email_or_null($params['query'])) {
                $conditions[] = "u.user_email = :user_email:";
                $bind['user_email'] = params_email_or_null($params['query']);
            }
            else {
                $conditions[] = "u.user_name LIKE CONCAT('%', :user_name:, '%')";
                $bind['user_name'] = $params['query'];
            }
        }

        if($params['user_role']) {
            $paramsGet['user_role']  = $params['user_role'];
            $conditions[]            = "u.user_role = :user_role:";
            $bind['user_role']       = (string)$params['user_role'];
        }

        if($params['user_status'] || $params['user_status'] === '0') {
            $paramsGet['user_status']   = $params['user_status'];
            $conditions[]               = "u.user_status = :user_status:";
            $bind['user_status']        = (string)$params['user_status'];
        }

        $builder = $this->modelsManager->createBuilder()
            ->addFrom('Model\User', 'u')
            ->andWhere(
                implode(" AND ", $conditions),
                $bind
            )
            ->orderBy('u.user_id DESC')
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
            'user_role'     => params_has_or_null($params['user_role']),
            'user_status'   => $params['user_status'],
            'user_delete'   => params_bool($params['user_delete']),
        ]);
    }    
}