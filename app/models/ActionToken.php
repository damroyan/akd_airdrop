<?php
namespace Model;

class ActionToken extends Model {

    const ACTION_PASSWORD_CHANGE    = 'password_change';
    const ACTION_USER_REGISTRATION  = 'user_registration_confirm';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    static public function generationToken() {
        $tokenId = '';

        $tokenId .= md5(time() . microtime(true) . rand(1000, 9999));
        $tokenId .= md5(time() . microtime(true) . rand(1000, 9999));

        return $tokenId;
    }

    static public function setToken($action, $params = [], $userId = null, $exp = '+1 month', $transaction = null) {
        $tokenId = self::generationToken();

        $actionToken = new self();
        if($transaction) {
            $actionToken->setTransaction($transaction);
        }

        $actionToken->assign([
            'action_token_id'       => params_has_or_null($tokenId),
            'user_id'               => params_has_or_null($userId),
            'action_token_action'   => params_has_or_null($action),
            'action_token_params'   => (is_array($params) ? json_encode($params) : null),
            'action_token_cdate'    => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            'action_token_status'   => self::STATUS_ACTIVE,
            'action_token_edate'    => ($exp ? \Helper\Datetime::exec($exp, 'sql') : null),
        ]);

        if(!$actionToken->save()) {
            return null;
        }

        return $tokenId;
    }

    public function initialize() {
        parent::initialize();

        $this->setSource('action_token');
 
        $this->hasOne("user_id",    "Model\User",     "user_id",    array('alias' => 'user'));
    }

}