<?php
namespace Site\ApiV1\Controllers;

class ErrorController extends Controller {
    use \TraitErrorlog;

    public function initialize() {
        parent::initialize();
    }

    /**
     * Ошибка "страница не найдена"
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function forbiddenAction() {
        $this->_errorlog(403);

        return \Response::Error(403, 'api_method_forbidden');
    }

    /**
     * Ошибка "страница не найдена"
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function notfoundAction() {
        $this->_errorlog(404);

        return \Response::Error(404, 'api_method_not_found');
    }

    /**
     * Ошибка "пользователь заблокирован"
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function userblockedAction() {
        $this->_errorlog(550);

        return \Response::Error(550, 'user_blocked');
    }

    /**
     * Ошибка "доступ ограничен"
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function permissionAction() {
        $this->_errorlog(550);

        return \Response::Error(550, 'user_permission_denied');
    }

    /**
     * Ошибка "надо авторизоваться"
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function authAction() {
        $this->_errorlog(401);

        return \Response::Error(401, 'user_auth_required');
    }
}
