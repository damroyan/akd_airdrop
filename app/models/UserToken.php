<?php
namespace Model;

class UserToken extends Model {
    const TYPE_FB = 'facebook';
    const TYPE_VK = 'vkontakte';
    const TYPE_OK = 'odnoklassniki';
    
    public function initialize() {
        parent::initialize();

        $this->setSource('user_token');

        $this->hasOne("user_id",  "Model\User",         "user_id",   array('alias' => 'user'));
    }
}