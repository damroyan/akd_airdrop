<?php
namespace Site\FrontendV1\Controllers;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class UserController extends Controller {

    use \TraitUserMember;
    use \TraitParamRedirect;

    public function initialize() {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function forgotAction() {
//        if ($this->session->get('forgot_count')>3) {
//            $this->view->setVar('captcha_needed',$this->session->get('forgot_count'));
//        }
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function registrationAction() {

//        if ($this->session->get('bad_logins')>3) {
//            $this->view->setVar('captcha_needed',$this->session->get('bad_logins'));
//        }
    }

    /**
     * Профиль пользователя
     *
     * @Role({"allow": ['user','admin']})
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

        $this->view->setVar('accepted_languages',
            explode(',',$this->config->application->accepted_languages)
        );


        if(!$user) {
            return $this->error404();
        }

        $this->view->u = $user;
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function authAction() {
        $params = $this->dispatcher->getParams();
        $query = $this->request->getQuery();
        $redirect = $query['redirect'] ? $query['redirect'] : null;

        unset($query['_url']);
        unset($query['redirect']);

        $for = ($params['for'] ? $params['for'] : 'frontend-v1-index-index');
        unset($params['for']);

        if(in_array($for, [
            'frontend-v1-user-logout',
            'frontend-v1-user-auth',
        ])) {
            $params = [
                'module'        => 'frontend-v1',
                'controller'    => 'index',
                'action'        => 'index',
            ];
            $for = 'frontend-v1-index-index';
        }

        if(!$redirect) {
            try {
                if (count($params)) {
                    $for_arr = array_merge([
                        $params,
                        [
                            'for' => $for,
                        ]
                    ]);
                } else {
                    $for_arr = [
                        'for' => $for,
                    ];
                }

                $redirect = $this->url->get($for_arr, $query);
            }
            catch(\Exception $e) {
            }
        }


//        if ($this->session->get('bad_logins')>3) {
//            $this->view->setVar('captcha_needed',$this->session->get('bad_logins'));
//        }
        $redirect = $this->getParamRedirect($redirect);
        \Phalcon\Tag::setDefaults(array(
            '_for'      => $for,
            '_params'   => json_encode($params),
            '_query'    => json_encode($query),
            'redirect' => $redirect,
        ));

        $this->view->redirect = $redirect;
    }

    /**
     * Выход из профиля и редирект на главную
     *
     * @Role({"allow": ['auth']})
     */
    public function logoutAction() {
        if($this->user->user_id) {
            $query = $this->db->prepare('DELETE FROM `user_member` WHERE `user_id` = :user_id;');
            $query->execute([
                'user_id' => $this->user->user_id,
            ]);
        }

        $this->session->destroy();
        $this->removeUserMemberCookie();

        return $this->response->redirect(
            $this->url->get(['for' => 'frontend-v1-index-index', ])
        );
    }
    /**
     * Получение ссылки для авторизации через FB
     *
     * @Role({"allow": ['public']})
     */
    public function authfbAction() {
        $query = $this->request->getQuery();
        $redirect = $this->getParamRedirect($query['redirect']);

        $fb = new \Facebook\Facebook([
            'app_id' => $this->config->facebook->app_id,
            'app_secret' => $this->config->facebook->app_secret,
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl(
            \Helper\String2Url::url(
                $this->url->get([
                    'for' => 'frontend-v1-user-authfbcallback'
                ])
            ),
            explode(',', $this->config->facebook->app_scope)
        );

        // Запись в сессию редиректа
        $this->session->set('auth_redirect', $redirect);

        return $this->response->redirect(
            $loginUrl,
            true
        );
    }

    /**
     * FB авторизация callback
     *
     * @Role({"allow": ['public']})
     */
    public function authfbcallbackAction() {
        $this->view->pick('user/authcallback');
        $this->view->redirect_for = 'frontend-v1-user-authfb';

        $redirect = $this->getParamRedirect(
            $this->session->get(
                'auth_redirect',
                $this->url->get([
                    'for' => 'frontend-v1-index-index'
                ]),
                true
            )
        );
        $this->view->redirect = $redirect;

        $fb = new \Facebook\Facebook([
            'app_id' => $this->config->facebook->app_id,
            'app_secret' => $this->config->facebook->app_secret,
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        }
        catch(\Facebook\Exceptions\FacebookResponseException $e) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'Graph returned an error: ' . $e->getMessage();
            return [];
        }
        catch(\Facebook\Exceptions\FacebookSDKException $e) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'Facebook SDK returned an error: ' . $e->getMessage();
            return [];
        }

        if(!isset($accessToken)) {
            if($helper->getError()) {
                $this->response->setStatusCode(401, 'Unauthorized');
                $this->view->message = "" .
                    "Error: " . $helper->getError() . "<br />" .
                    "Error Code: " . $helper->getErrorCode() . "<br />" .
                    "Error Reason: " . $helper->getErrorReason() . "<br />" .
                    "Error Description: " . $helper->getErrorDescription();
                return [];
            }
            else {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'Bad request';
                return [];
            }
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        try {
            // Validation (these will throw FacebookSDKException's when they fail)
            $tokenMetadata->validateAppId($this->config->facebook->app_id); // Replace {app-id} with your app id
        }
        catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'Error access token facebook app id: ' . $e->getMessage();
            return [];
        }

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();
        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            }
            catch (\Facebook\Exceptions\FacebookSDKException $e) {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'Error getting long-lived access token: ' . $e->getMessage();
                return [];
            }
        }

        $fbUserId = $tokenMetadata->getUserId();
        $fbAccessToken = $accessToken->getValue();
        $fbAccessTokenExpiresAt = $accessToken->getExpiresAt();

        $user = \Model\User::findFirst([
            'user_login = :user_login:',
            'bind' => [
                'user_login' => \Model\User::LOGIN_PREFIX_FB . $fbUserId,
            ]
        ]);

        if(!$user) {
            try {
                $response = $fb->get('/me/?fields=first_name,last_name,email,name,picture{url}', $accessToken);
            }
            catch(\Facebook\Exceptions\FacebookResponseException $e) {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'Graph returned an error: ' . $e->getMessage();
                return [];
            }
            catch(\Facebook\Exceptions\FacebookSDKException $e) {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'Facebook SDK returned an error: ' . $e->getMessage();
                return [];
            }

            $result = $response->getDecodedBody();

            $user = new \Model\User();
            $user->assign([
                'user_login'        => \Model\User::LOGIN_PREFIX_FB . $fbUserId,
                'user_email'        => params_email_or_null($result['email']),
                'user_name'         => params_has_or_blank($result['name']),
                'user_firstname'    => params_has_or_blank($result['first_name']),
                'user_lastname'     => params_has_or_blank($result['last_name']),
                'user_role'         => \Model\User::ROLE_USER,
                'user_cdate'        => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                'user_delete'       => \Model\User::DELETE_FALSE,
                'user_status'       => \Model\User::STATUS_ACTIVE,
                'user_picture'      => "http://graph.facebook.com/{$fbUserId}/picture",
            ]);
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            if(!$user->user_id) {
                $user->setTransaction($transaction);

                if($user->save() === false) {
                    $transaction->rollback("can't insert row in table 'user'");
                }

                // Todo email notification
            }

            $this->setUserMember($user->user_id, $transaction);
            $this->setUserAccessLog($user->user_id, $transaction);

            // userToken
            $userToken = new \Model\UserToken();
            $userToken->setTransaction($transaction);

            try {
                if(is_object($fbAccessTokenExpiresAt) && get_class($fbAccessTokenExpiresAt) == 'DateTime') {
                    $timestamp = $fbAccessTokenExpiresAt->getTimestamp();
                }
                else {
                    $timestamp = null;
                }
            }
            catch(\Exception $e) {
                $timestamp = null;
            }

            $userToken->assign([
                'user_id'           => $user->user_id,
                'user_token_type'   => \Model\UserToken::TYPE_FB,
                'user_token_value'  => $fbAccessToken,
                'user_token_params' => json_encode([
                    'fb_user_id'        => $fbUserId,
                    'fb_access_token'   => $fbAccessToken,
                ]),
                'user_token_cdate'  => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                'user_token_expired'=> \Helper\Datetime::exec($timestamp, 'sql'),
            ]);

            if($userToken->save() === false) {
                $transaction->rollback("can't insert row in table 'user_token'");
            }

            $transaction->commit();

            //Todo: Welcome Email
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }

        $this->session->set('auth', array(
            'user_id' => $user->user_id,
        ));

        return $this->response->redirect(
            $redirect
        );
    }

    /**
     * VK авторизация
     *
     * @Role({"allow": ['public']})
     */
    public function authvkAction() {
        $query = $this->request->getQuery();
        $redirect = $this->getParamRedirect($query['redirect']);

        $vk = new \Vkontakte\Vkontakte([
            'app_id' => $this->config->vkontakte->app_id,
            'app_secret' => $this->config->vkontakte->app_secret,
        ]);

        $loginUrl = $vk->getLoginUrl(
            \Helper\String2Url::url(
                $this->url->get([
                    'for' => 'frontend-v1-user-authvkcallback'
                ])
            ), [
                'email',
            ]
        );

        // Запись в сессию редиректа
        $this->session->set('auth_redirect', $redirect);

        return $this->response->redirect(
            $loginUrl,
            true
        );
    }

    /**
     * VK авторизация callback
     *
     * @Role({"allow": ['public']})
     */
    public function authvkcallbackAction() {
        $this->view->pick('user/authcallback');
        $this->view->redirect_for = 'frontend-v1-user-authvk';

        $this->view->seo = [
            'title'     => 'Ошибка авторизации',
            'noindex'   => true,
        ];

        $redirect = $this->getParamRedirect(
            $this->session->get('auth_redirect', '/', true)
        );
        $this->view->redirect = $redirect;

        $vk = new \Vkontakte\Vkontakte([
            'app_id' => $this->config->vkontakte->app_id,
            'app_secret' => $this->config->vkontakte->app_secret,
        ]);

        $vk->setRedirectUri(\Helper\String2Url::url(
            $this->url->get([
                'for' => 'frontend-v1-user-authvkcallback'
            ])
        ));

        try {
            $vkAccessToken = $vk->getAccessToken();
        }
        catch(\Vkontakte\Exceptions\VkontakteResponseException $e) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'API returned an error: ' . $e->getMessage();
            return [];
        }
        catch(\Vkontakte\Exceptions\VkontakteSDKException $e) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'Vkontakte SDK returned an error: ' . $e->getMessage();
            return [];
        }

        if(!$vkAccessToken) {
            $this->response->setStatusCode(400, 'Bad Request');
            $this->view->message = 'Bad request';
            return [];
        }

        $vkUserId = $vk->getUserId();
        $vkAccessTokenExpiresAt = $vk->getUserAccessTokenExpires();

        $user = \Model\User::findFirst([
            'user_login = :user_login:',
            'bind' => [
                'user_login' => \Model\User::LOGIN_PREFIX_VK . $vkUserId,
            ]
        ]);

        if(!$user) {
            try {
                $response = $vk->get('users.get', [
                    'fields' => join(',', [
                        'photo_100',
                        'photo_200',
                        'bdate',
                        'nickname',
                        'first_name',
                        'last_name',
                    ])
                ]);
            }
            catch(\Vkontakte\Exceptions\VkontakteResponseException $e) {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'API returned an error: ' . $e->getMessage();
                return [];
            }
            catch(\Vkontakte\Exceptions\VkontakteSDKException $e) {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'Vkontakte SDK returned an error: ' . $e->getMessage();
                return [];
            }

            if($response['response'][0]) {
                $result = $response['response'][0];

                $user = new \Model\User();
                $user->assign([
                    'user_login'        => \Model\User::LOGIN_PREFIX_VK . $vkUserId,
                    'user_email'        => params_email_or_null($vk->getEmail()),
                    'user_name'         => params_has_or_blank($result['nickname']),
                    'user_firstname'    => params_has_or_blank($result['first_name']),
                    'user_lastname'     => params_has_or_blank($result['last_name']),
                    'user_role'         => \Model\User::ROLE_USER,
                    'user_cdate'        => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                    'user_delete'       => \Model\User::DELETE_FALSE,
                    'user_status'       => \Model\User::STATUS_ACTIVE,
                    'user_picture'      => params_has_or_blank($result['photo_200'] ? $result['photo_200'] : $result['photo_100']),
                ]);
            }
            else {
                $this->response->setStatusCode(400, 'Bad Request');
                $this->view->message = 'API returned an error: ' . json_encode($response);
                return [];
            }
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            if(!$user->user_id) {
                $user->setTransaction($transaction);

                if($user->save() === false) {
                    $transaction->rollback("can't insert row in table 'user'");
                }

                // Todo email notification
            }

            $this->setUserMember($user->user_id, $transaction);
            $this->setUserAccessLog($user->user_id, $transaction);

            // userToken
            $userToken = new \Model\UserToken();
            $userToken->setTransaction($transaction);

            $userToken->assign([
                'user_id'           => $user->user_id,
                'user_token_type'   => \Model\UserToken::TYPE_VK,
                'user_token_value'  => $vkAccessToken,
                'user_token_params' => json_encode([
                    'vk_user_id'        => $vkUserId,
                    'vk_access_token'   => $vkAccessToken,
                ]),
                'user_token_cdate'  => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                'user_token_expired'=> \Helper\Datetime::exec($vkAccessTokenExpiresAt, 'sql'),
            ]);

            if($userToken->save() === false) {
                $transaction->rollback("can't insert row in table 'user_token'");
            }

            $transaction->commit();

            //Todo: Welcome Email
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }

        $this->session->set('auth', array(
            'user_id' => $user->user_id,
        ));

        return $this->response->redirect(
            $redirect
        );
    }
}