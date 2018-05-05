<?php
namespace Model;

class UserMember extends Model {

    public function initialize() {
        parent::initialize();

        $this->setSource('user_member');

        $this->hasOne("user_id",  "Model\User",         "user_id",   array('alias' => 'user'));
    }

}