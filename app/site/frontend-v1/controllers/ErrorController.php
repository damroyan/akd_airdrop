<?php
namespace Site\FrontendV1\Controllers;

class ErrorController extends Controller {
    use \TraitErrorlog;

    public function initialize() {
        parent::initialize();
        // SEO
        $this->view->seo = [];
    }

    /**
     * Служебная пустая страница
     *
     * @Role({"allow": ['public']})
     */
    public function blankAction() {
        $code = (string)$this->request->getQuery('code');

        switch($code) {
            case '400':
                $text = __('Bad Request');
                break;

            case '401':
                $text = __('Unauthorized');
                break;

            case '403':
                $text = __('Forbidden');
                break;

            case '404':
                $text = __('Not Found');
                break;

            case '500':
                $text = __('Internal Server Error');
                break;

            case '503':
                $text = __('Service Unavailable');
                break;

            default:
                $code = '404';
                $text = __('Not Found');
                break;
        }

        $this->response->setStatusCode($code, $text);

        $this->view->setVars(array(
            'code' => $code,
            'text' => $text,
        ));
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
                'title'         => __('User blocked! Please contact support for more info.'),
            )
        );

        $this->_errorlog(550);
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function authAction() {
        $this->response->setStatusCode(401, __('Unauthorized'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('You need login in to use this app.'),
            )
        );

        $this->_errorlog(401);
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function forbiddenAction() {
        $this->response->setStatusCode(403, __('Permission Denied'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('Permission Denied'),
            )
        );

        $this->_errorlog(403);
        $this->view->pick('error/permission');
    }

    /**
     * Страница не найдена
     *
     * @Role({"allow": ['public']})
     */
    public function notfoundAction() {
        $this->response->setStatusCode(404, __('Page was not found'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('Page was not found'),
            )
        );

        $this->_errorlog(404);
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function internalAction() {
        $this->response->setStatusCode(500, __('Internal Server Error'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('Internal Server Error'),
            )
        );

        $this->_errorlog(500);
    }

    /**
     * Технические работы
     *
     * @Role({"allow": ['public']})
     */
    public function techAction() {
        $this->response->setStatusCode(503, __('Service Unavailable'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('Service Unavailable'),
            )
        );

        $this->_errorlog(503);
    }

    /**
     * Нет прав доступа
     *
     * @Role({"allow": ['public']})
     */
    public function permissionAction() {
        $this->response->setStatusCode(550, __('Permission Denied'));

        $this->view->seo = array_merge(
            $this->view->seo,
            array(
                'title'         => __('Permission Denied'),
            )
        );

        $this->_errorlog(550);
    }
} 