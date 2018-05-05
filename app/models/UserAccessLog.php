<?php
namespace Model;

class UserAccessLog extends Model {
    public function initialize() {
        parent::initialize();

        $this->setSource('user_access_log');

        $this->hasOne("user_id",  "Model\User",         "user_id",   array('alias' => 'user'));
    }
}