<?php
namespace Site\BackendV1\Controllers;

class ErrorController extends Controller {
    use \TraitErrorlog;

    public function initialize() {
        parent::initialize();
        // SEO
        $this->view->seo = [];
    }

    /**
     * Пользователь заблокирован
     *
     * @Role({"allow": ['public']})
     */
    public function userblockedAction() {
        $this->response->setStatusCode(550, 'Permission Denied');

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => 'Пользователь заблокирован',
                'description'   => 'К сожаления ваш пользователь заблокирован :-(',
            )
        );

        $this->_errorlog(550);
    }

    /**
     * Страница не найдена
     *
     * @Role({"allow": ['public']})
     */
    public function forbiddenAction() {
        $this->response->setStatusCode(403, 'Forbidden');

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => 'Доступ ограничен',
                'description'   => 'К сожалению доступ к данной странице ограничен',
            )
        );

        $this->_errorlog(404);
    }

    /**
     * Страница не найдена
     *
     * @Role({"allow": ['public']})
     */
    public function notfoundAction() {
        $this->response->setStatusCode(404, 'Not Found');

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => 'Страница не найдена',
                'description'   => 'К сожалению данная страница не найдена :-(',
            )
        );

        $this->_errorlog(404);
    }

    /**
     * Нет прав доступа
     *
     * @Role({"allow": ['public']})
     */
    public function permissionAction() {
        $this->response->setStatusCode(550, 'Permission Denied');

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => 'Недостаточно прав',
                'description'   => 'К сожалению у вас нет прав доступа на просмотр этого раздела сайта :-(',
            )
        );

        $this->_errorlog(550);
    }
} 