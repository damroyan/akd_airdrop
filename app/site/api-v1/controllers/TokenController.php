<?php
namespace Site\ApiV1\Controllers;

use \Response;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class TokenController extends Controller {

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function cancelAction() {
        $params = $this->getParams();
        if (is_http_response($params)) {
            return $params;
        }

        $actionToken = null;
        if ($params['action_token_id']) {
            $actionToken = \Model\ActionToken::findFirst([
                'action_token_id = :action_token_id:',
                'bind' => [
                    'action_token_id' => $params['action_token_id'],
                ]
            ]);
        }

        if (!$actionToken) {
            return \Response::Error(404, 'action_token_not_found');
        }

        if ($actionToken->action_token_status == \Model\ActionToken::STATUS_INACTIVE) {
            return \Response::Error(404, 'action_token_used');
        }

        if (\Helper\Datetime::exec($actionToken->action_token_edate, 'timestamp') < time()) {
            return \Response::Error(404, 'action_token_expired');
        }

        $actionToken->assign([
            'action_token_status' => \Model\ActionToken::STATUS_INACTIVE,
            'action_token_ip' => $this->filter->sanitize(IP, 'ip'),
        ]);

        if (!$actionToken->save()) {
            return \Response::Error(500, 'db_error_update');
        }

        return \Response::Ok([
            'redirect' => $this->url->get([
                'for' => 'frontend-v1-index-index'
            ])
        ]);
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function indexAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $actionToken = null;
        if($params['action_token_id']) {
            $actionToken = \Model\ActionToken::findFirst([
                'action_token_id = :action_token_id:',
                'bind' => [
                    'action_token_id' => $params['action_token_id'],
                ]
            ]);
        }

        if(!$actionToken) {
            return \Response::Error(404, 'action_token_not_found');
        }

        if($actionToken->action_token_status == \Model\ActionToken::STATUS_INACTIVE) {
            return \Response::Error(404, 'action_token_used');
        }

        if(\Helper\Datetime::exec($actionToken->action_token_edate, 'timestamp') < time()) {
            return \Response::Error(404, 'action_token_expired');
        }

        switch($actionToken->action_token_action) {
            case \Model\ActionToken::ACTION_USER_REGISTRATION:
                return $this->_userRegistration($actionToken, $params);
                break;

            case \Model\ActionToken::ACTION_PASSWORD_CHANGE:
                return $this->_passwordChange($actionToken, $params);
                break;

            default:
                return \Response::Error(404, 'action_token_not_found');
                break;
        }
    }
    
    /**
     * @param |Phalcon\Mvc\Model $actionToken
     * @param $actionTokenParams
     * @return \Phalcon\Http\Response
     */
    private function _userRegistration($actionToken, $params = []) {
        $actionTokenParams = json_decode($actionToken->action_token_params, true);

        $user = \Model\User::findFirst([
            'user_id = :user_id:',
            'bind' => [
                'user_id' => $actionTokenParams['user_id'],
            ],
        ]);

        if(!$user) {
            return \Response::Error(500, 'action_token_error');
        }
        
        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            $user->setTransaction($transaction);
            $user->assign([
                'user_status'   => \Model\User::STATUS_ACTIVE,
                'user_mdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            ]);
            if($user->save() === false) {
                $transaction->rollback("can't save 'user'");
            }

            $actionToken->setTransaction($transaction);
            $actionToken->assign([
                'action_token_status'   => \Model\ActionToken::STATUS_INACTIVE,
                'action_token_ip'       => $this->filter->sanitize(IP, 'ip')
            ]);
            if($actionToken->save() === false) {
                $transaction->rollback("can't save 'action_token'");
            }

            // Todo email welcome

            $transaction->commit();

            $this->session->set('auth', array(
                'user_id' => $user->user_id,
            ));

            return \Response::Ok([
                'redirect'  => $this->url->get([
                    'for'       => 'frontend-v1-index-index',
                ]),
                'message'   => __('Registration was successfully completed. Your account was activated.'),
            ]);
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }        
    }

    /**
     * Смена пароля
     * 
     * @param |Phalcon\Mvc\Model $actionToken
     * @param $actionTokenParams
     * @return \Phalcon\Http\Response
     */
    private function _passwordChange($actionToken, $params = []) {
        $actionTokenParams = json_decode($actionToken->action_token_params, true);

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

        $user = \Model\User::findFirst([
            'user_id = :user_id:',
            'bind' => [
                'user_id' => $actionTokenParams['user_id'],
            ],
        ]);

        if(!$user) {
            return \Response::Error(500, 'action_token_error');
        }

        switch((int)$user->user_delete) {
            case \Model\User::DELETE_TRUE:
                return \Response::Error(550, 'user_delete');
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
                return \Response::Error(550, 'user_blocked');
                break;
        }

        $transactionManager = new TransactionManager();
        $transaction = $transactionManager->get();
        try {
            $user->setTransaction($transaction);
            $user->assign([
                'user_status'   => \Model\User::STATUS_ACTIVE,
                'user_password' => $this->security->hash($params['user_password']),
                'user_mdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            ]);
            if($user->save() === false) {
                $transaction->rollback("can't save 'user'");
            }

            $actionToken->setTransaction($transaction);
            $actionToken->assign([
                'action_token_status'   => \Model\ActionToken::STATUS_INACTIVE,
                'action_token_ip'       => $this->filter->sanitize(IP, 'ip')
            ]);
            if($actionToken->save() === false) {
                $transaction->rollback("can't save 'action_token'");
            }

            // Todo email alert

            $transaction->commit();

            $this->session->set('auth', array(
                'user_id' => $user->user_id,
            ));

            return \Response::Ok([
                'redirect'  => $this->url->get([
                    'for'       => 'frontend-v1-index-index',
                ]),
                'message'   => 'action_token_complete_password_change',
            ]);
        }
        catch(\Phalcon\Mvc\Model\Transaction\Failed $e) {
            return \Response::Error(500, 'transaction_error', $e);
        }
    }

}