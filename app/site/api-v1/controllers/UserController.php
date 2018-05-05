<?php
namespace Site\ApiV1\Controllers;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class UserController extends Controller {
    use \TraitUserMember;
    use \TraitRecaptcha;
    use \TraitParamRedirect;

    public function initialize() {
        parent::initialize();
    }

    /**
     * Удаление пользователя самим пользователем
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function deleteAction(){

        $this->user->assign([
            'user_status'   => \Model\User::STATUS_DELETED,
            'user_mdate'    => date('Y-m-d H:i:s'),
        ]);
        $this->user->save();

        return \Response::Ok([
            'message'       => __('User was delete successfully. Hope you will come to us in the future.'),
            'redirect'      => $this->url->get(['for'=>'frontend-v1-user-logout']),
        ]);
    }

    /**
     * Редактирование пользователя самим пользователем
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function profileEditAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $params['user_id']      = $this->user->user_id;
        $params['user_login']   = $this->user->user_login;
        $params['user_email']   = $this->user->user_email;

        $result = $this->validation(
            new \Site\ApiV1\Validators\UserEdit(),
            $params
        );
        if(is_http_response($result)) { return $result; }

        if (!in_array($params['user_lang'],explode(',',$this->config->application->accepted_languages))){
            $params['user_lang'] = $this->config->application->default_lang;
        }

        $this->user->assign([
            'user_firstname'    => $params['user_firstname'],
            'user_lastname'     => $params['user_lastname'],
            'user_name'         => $params['user_firstname'].' '.$params['user_lastname'],
            'user_lang'         => $params['user_lang'],
            'user_mdate'        => date('Y-m-d H:i:s'),
        ]);
        $this->user->save();

        return \Response::Ok([
            'message'   => __('Your profile data was saved successfully'),
            'redirect'  => '',
        ]);
    }

    /**
     * Смена пароля пользователем
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function passwordAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        if (!$this->security->checkHash($params['user_password_old'], $this->user->user_password)) {
            return \Response::FieldsAddAndStop('user_password_old', __('Old password are incorrect'));
        }

        $result = $this->validation(
            new \Site\ApiV1\Validators\UserPasswordChange(),
            $params,
            function($params) {
                if($params['user_password'] != $params['user_password_reply']) {
                    \Response::FieldsAdd('user_password', 'password_not_match');
                    \Response::FieldsAdd('user_password_reply', 'password_not_match');
                }
            }
        );
        if(is_http_response($result)) { return $result; }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            $this->user->setTransaction($transaction);
            $this->user->assign([
                'user_status'   => \Model\User::STATUS_ACTIVE,
                'user_password' => $this->security->hash($params['user_password']),
                'user_mdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            ]);
            if($this->user->save() === false) {
                $transaction->rollback("can't save 'user'");
            }

            $transaction->commit();

            $redirect = $this->getParamRedirect(
                $params['redirect'],
                function() use ($params) {
                    $redirect = null;
                    if($params['_for']) {
                        $redirectParams = [];
                        try {
                            $redirectParams = json_decode($params['_params'], true);
                        }
                        catch(\Exception $e) {}
                        $redirectParams['for'] = $params['_for'];

                        $redirectQuery = array();
                        try {
                            $redirectQuery = json_decode($params['_query'], true);
                        }
                        catch(\Exception $e) {}

                        try {
                            $redirect = $this->url->get(
                                $redirectParams,
                                $redirectQuery
                            );
                        }
                        catch(\Exception $e) {
                            $redirect = null;
                        }
                    }

                    return $redirect;
                }
            );

            return \Response::Ok([
                'message'   => __('Your password was changed successfully!'),
                'redirect'  => $redirect,
            ]);

        } catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }

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
            new \Site\ApiV1\Validators\UserEdit(),
            $params,
            function($params) {
                if(!$params['user_id'] && !$params['user_password']) {
                    \Response::FieldsAdd('user_password', 'string_required');
                }

                if(!\Model\User::loginValidate($params['user_login'], $params['user_id'])) {
                    \Response::FieldsAdd('user_login', 'user_login_used');
                }
            }
        );
        if(is_http_response($result)) { return $result; }

        if($params['user_id']) {
            $user = \Model\User::findFirst([
                "user_id = :user_id:",
                'bind' => [
                    'user_id' => $params['user_id'],
                ]
            ]);

            if(!$user) {
                return \Response::Error(400, 'user_id_incorrect');
            }

            if($params['user_password'] && $params['password']) {
                $user->assign([
                    'user_password' => $this->security->hash($params['user_password']),
                ]);
            }

            $user->assign([
                'user_mdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            ]);
        }
        else {
            $user = new \Model\User();

            $user->assign([
                'user_cdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                'user_login'    => params_has_or_null($params['user_login']),
                'user_delete'   => \Model\User::DELETE_FALSE,
                'user_password' => $this->security->hash($params['user_password']),
            ]);
        }

        $user->assign([
            'user_email'        => params_email_or_null($params['user_email']),
            'user_name'         => params_has_or_null($params['user_name']),
            'user_firstname'    => params_has_or_null($params['user_firstname']),
            'user_lastname'     => params_has_or_null($params['user_lastname']),
            'user_role'         => params_has_or_null($params['user_role']),
            'user_status'       => $params['user_status'],
        ]);

        if(!$user->save()) {
            return \Response::Error(500, 'db_error_insert');
        }

        return \Response::Ok([
            'user_id'   => $user->user_id,
        ]);
    }
    
    /**
     * Удаление / восстановление пользователя
     *
     * @Role({"allow": ['root']})
     * @return \Phalcon\Http\Response
     */
    public function toggledeleteAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $user = null;
        if($params['user_id']) {
            $user = \Model\User::findFirst([
                "user_id = :user_id:",
                'bind' => [
                    'user_id'       => $params['user_id'],
                ]
            ]);
        }

        if(!$user) {
            return \Response::Error(400, 'user_id_incorrect');
        }

        switch((int)$user->user_delete) {
            case \Model\User::DELETE_FALSE:
                if(!$user->delete()) {
                    return \Response::Error(500, 'db_error_delete');
                }

                break;

            case \Model\User::DELETE_TRUE:
                $user->assign([
                    'user_delete' => \Model\User::DELETE_FALSE,
                ]);

                if(!$user->save()) {
                    return \Response::Error(500, 'db_error_update');
                }

                break;

            default:
                break;
        }

        return \Response::Ok([
            'user_id'       => $user->user_id,
        ]);
    }
    
    /**
     * Бан / разбан пользователя
     *
     * @Role({"allow": ['admin']})
     * @return \Phalcon\Http\Response
     */
    public function togglebanAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $user = null;
        if($params['user_id']) {
            $user = \Model\User::findFirst([
                "user_id = :user_id:",
                'bind' => [
                    'user_id'       => $params['user_id'],
                ]
            ]);
        }

        if(!$user) {
            return \Response::Error(400, 'user_id_incorrect');
        }
        
        switch((int)$user->user_status) {
            case \Model\User::STATUS_BLOCKED:
                $user->assign([
                    'user_status' => \Model\User::STATUS_ACTIVE,
                ]);
                break;
            
            case \Model\User::STATUS_ACTIVE:
                $user->assign([
                    'user_status' => \Model\User::STATUS_BLOCKED,
                ]);
                break;
            
            default:
                return \Response::Error(400, 'api_method_bad_request');
                break;
        }

        if(!$user->save()) {
            return \Response::Error(500, 'db_error_update');
        }

        return \Response::Ok([
            'user_id'       => $user->user_id,
        ]);        
    }

    /**
     * Регистрация пользователя
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function registrationAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\UserRegistration(),
            $params,
            function($params) {

                if(!\Model\User::loginValidate($params['user_login'])) {
                    \Response::FieldsAdd('user_login', __('User with such login are already exists'));
                }

                if($params['user_password'] != $params['user_password_reply']) {
                    \Response::FieldsAdd('user_password', __('Password and password confirm are not matched'));
                    \Response::FieldsAdd('user_password_reply', 'password_not_match');
                }

            }
        );

        if ($this->config->recaptcha->enabled) {
            $result = $this->recaptchaVerification($params['g-recaptcha-response']);
            if($result['response'] == \Response::RESPONSE_ERROR) {
                return \Response::Error(
                    $result['error_code'],
                    $result['error_msg']
                );
            }
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            $user = new \Model\User();
            $user->setTransaction($transaction);

            $user->assign([
                'user_login'    => params_email_or_null($params['user_login']),
                'user_email'    => params_email_or_null($params['user_login']),
                'user_password' => $this->security->hash($params['user_password']),
                'user_firstname'=> params_has_or_blank($params['user_firstname']),
                'user_lastname' => params_has_or_blank($params['user_lastname']),
                'user_name'     => params_has_or_blank(
                    $params['user_name'] ?
                        $params['user_name'] :
                        "{$params['user_firstname']} {$params['user_lastname']}"
                ),

                'user_role'     => \Model\User::ROLE_USER,
                'user_cdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                'user_delete'   => \Model\User::DELETE_FALSE,
                'user_newsletter_subscription_date' => ( isset($params['user_newsletter_subscription_date']) ? date('Y-m-d H:i:s'):'no'),
                'user_status'   => \Model\User::STATUS_INACTIVE,
            ]);

            if($user->save() === false) {
                $transaction->rollback("can't save 'user'");
            }

            $tokenId = \Model\ActionToken::setToken(
                \Model\ActionToken::ACTION_USER_REGISTRATION,
                [
                    'user_id'   => $user->user_id,
                ],
                ($this->user->user_id ? $this->user->user_id : null),
                '+1 week',
                $transaction
            );
            if(!$tokenId) {
                $transaction->rollback("can't create 'action_token'");
            }

            $tokenUrl = \Helper\String2Url::url($this->url->get([
                'for' => 'frontend-v1-token-index'
            ], [
                'action_token_id' => $tokenId
            ]));

            $config = $this->di->get('config');
            \Helper\Email::send(
                clone $this->view, //объект view фалькона
                $user->user_id, // текущий ID пользователя или 0
                array($user->user_email, ), //массив адресов куда слать
                'registration', //название шаблона + строки перевода для subject письма
                array(
                    'tokenUrl'  => $tokenUrl,
                    'userName'  => $user->getUsername(),
                    'unsubscribe'   => false// массив параметров для шаблона
                ),
                array(
                    'email' => $config->contacts->noreply_email,
                    'name'  => $config->contacts->noreply_name,
                ), // email который будет указан как мейл отправителя и имя пользователя отправителя
                $priority = 100 //приоритет отправки, используется в случае большого количества мейлом, чтобы, например, сначала отправлялись мейлы с активацией аккаунта, потом новостные. Чем больше приоритет, тем быстрее отправка.
            );

            // Создаем новую компанию для пользователя.
            // Если проект некорпоративный, то данный блок можно закомментировать
            $company = new \Model\Company();
            $company->setTransaction($transaction);
            $company->assign([
                'user_id'       => $user->user_id,
                'company_name'  => $user->user_name.' Company',
            ]);
            $company->save();


            $transaction->commit();

            return \Response::Ok([
                'status'        => true,
                'message'       => __('User registered sucessfully! Please check your email for the activation link'),
                //'message'       => "Откройте ссылку: {$tokenUrl}",
                //'redirect'      => $this->url->get(['for' => 'frontend-v1-index-index',]),
                'user_id'       => $user->user_id,
            ]);
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, __('Data base error. Please try again later or contact with support now.'), $e);
        }
    }

    /**
     * Забыли пароль
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
	public function forgotAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\UserForgot(),
            $params
        );
        if(is_http_response($result)) { return $result; }

        if ($this->config->recaptcha->enabled) {
            $result = $this->recaptchaVerification($params['g-recaptcha-response']);
            if ($result['response'] == \Response::RESPONSE_ERROR) {
                return \Response::Error(
                    $result['error_code'],
                    $result['error_msg']
                );
            }
        }

        $user = \Model\User::findFirst(array(
            "user_login = :user_login:",
            "bind" => array(
                'user_login' => $params['user_login'],
            ),
        ));

        if(!$user) {
            return \Response::Error(400, __('User email was not found!'));
        }

        switch((int)$user->user_delete) {
            case \Model\User::DELETE_TRUE:
                return \Response::Error(550, __('User was deleted by you or moderators. Please contact to support if you want to re-open this account.'));
                break;

            default:
                break;
        }

        switch((int)$user->user_status) {
            case \Model\User::STATUS_INACTIVE:
            case \Model\User::STATUS_ACTIVE:
                break;

            default:
            case \Model\User::STATUS_BLOCKED:
                return \Response::Error(550, __('User was blocked by administrations. Please contact to support for more information.'));
                break;
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            $tokenId = \Model\ActionToken::setToken(
                \Model\ActionToken::ACTION_PASSWORD_CHANGE,
                [
                    'user_id'   => $user->user_id,
                ],
                ($this->user->user_id ? $this->user->user_id : null),
                '+1 week',
                $transaction
            );
            if(!$tokenId) {
                $transaction->rollback("can't create 'action_token'");
            }

            $tokenUrl = \Helper\String2Url::url($this->url->get([
                'for' => 'frontend-v1-token-index'
            ], [
                'action_token_id' => $tokenId
            ]));

            $config = $this->di->get('config');

            \Helper\Email::send(
                clone $this->view, //объект view фалькона
                $user->user_id, // текущий ID пользователя или 0
                array($user->user_email, ), //массив адресов куда слать
                'forgotpass', //название шаблона + строки перевода для subject письма
                array(
                    'tokenUrl'  => $tokenUrl,
                    'userName'  => $user->getUsername(),
                    'unsubscribe'   => false// массив параметров для шаблона
                ),
                array(
                    'email' => $config->contacts->noreply_email,
                    'name'  => $config->contacts->noreply_name,
                ), // email который будет указан как мейл отправителя и имя пользователя отправителя
                $priority = 100 //приоритет отправки, используется в случае большого количества мейлом, чтобы, например, сначала отправлялись мейлы с активацией аккаунта, потом новостные. Чем больше приоритет, тем быстрее отправка.
            );

            $transaction->commit();

            return \Response::Ok([
                'status'        => true,
                'message'       => 'change_password_confirm_send',
 //               'message'       => "Откройте ссылку: {$tokenUrl}",
                'redirect'      => $this->url->get([
                    'for' => 'frontend-v1-user-auth',
                ]),
                'user_id'       => $user->user_id,
            ]);
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, __('Data base transaction error'), $e);
        }
    }

    /**
     * Авторизация по логину и паролю
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function authAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\UserAuth(),
            $params
        );
        if(is_http_response($result)) { return $result; }

        if(\Response::FieldsError()) {
            return \Response::Error(400);
        }

        if ($this->config->recaptcha->enabled) {
            // Validate recaptcha
            $result = $this->recaptchaVerification($params['g-recaptcha-response']);
            if($result['response'] == \Response::RESPONSE_ERROR) {
                return \Response::Error(
                    $result['error_code'],
                    $result['error_msg']
                );
            }
        }

        $user = \Model\User::findFirst(array(
            "user_login = :user_login:",
            "bind" => array(
                'user_login' => $params['user_login'],
            ),
        ));

        if(!$user) {
            return \Response::Error(400, __('Incorrect pair email/password'));
        }

        if (!$this->security->checkHash($params['user_password'], $user->user_password)) {
            return \Response::FieldsAddAndStop(400, __('Incorrect pair email/password'));
        }

      /*  switch((int)$user->user_delete) {
            case \Model\User::DELETE_TRUE:
                return \Response::Error(550, __('User was deleted by you or moderators. Please contact to support if you want to re-open this account.'));
                break;

            default:
                break;
        }*/

        switch((int)$user->user_status) {
            case \Model\User::STATUS_ACTIVE:
                break;

            case \Model\User::STATUS_DELETED:
                return \Response::Error(550,__('This account was deleted. If you did not do this or you want to reestablish an account please contact with support.'));
                break;

            case \Model\User::STATUS_INACTIVE:
                return \Response::Error(550, __('User was not activated via link from email. Please activate user or contact with support.'));
                break;

            default:
            case \Model\User::STATUS_BLOCKED:
                return \Response::Error(550, __('User was blocked by administrations. Please contact to support for more information.'));
                break;
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            if($params['member']) {
                $this->setUserMember($user->user_id, $transaction);
            }
            $this->setUserAccessLog($user->user_id, $transaction);

            $transaction->commit();
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }

        $this->session->set('auth', array(
            'user_id' => $user->user_id,
        ));

        $user->save();

        $redirect = $this->getParamRedirect(
            $params['redirect'],
            function() use ($params) {
                $redirect = null;
                if($params['_for']) {
                    $redirectParams = [];
                    try {
                        $redirectParams = json_decode($params['_params'], true);
                    }
                    catch(\Exception $e) {}
                    $redirectParams['for'] = $params['_for'];

                    $redirectQuery = array();
                    try {
                        $redirectQuery = json_decode($params['_query'], true);
                    }
                    catch(\Exception $e) {}

                    try {
                        $redirect = $this->url->get(
                            $redirectParams,
                            $redirectQuery
                        );
                    }
                    catch(\Exception $e) {
                        $redirect = null;
                    }
                }

                return $redirect;
            }
        );

        return \Response::Ok([
           // 'user_id'   => $user->user_id,
           // 'message'   => 'user_auth_success',
            'redirect'  => $redirect,
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


        $this->user->assign([
            'user_picture'  => $params['user_picture'],
        ]);
        $this->user->save();

        $image =  \Helper\Image::getInstance();

        return \Response::Ok([
            'user_picture'  => $image->image($this->user->user_picture,50, 50, 'mwh', 'png', true)->__toString(),
        ]);

    }
}
