<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class UserPasswordChange extends Validation  {
    public function initialize() {
        $this->add('user_password', new PresenceOf(array(
            'message'       => __('New password is a required field')
        )));

        $this->add('user_password_reply', new PresenceOf(array(
            'message'       => __('Password confirm is required field')
        )));
    }
}