<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

class UserAuth extends Validation  {
    public function initialize() {
        $this->add('user_login', new PresenceOf(array(
            'message'       => __('User login is required')
        )));

        $this->add('user_login', new Email(array(
            'message'       => __('Email you provide are incorrect'),
            'allowEmpty'    => true,
        )));

        $this->add('user_password', new PresenceOf(array(
            'message'       => __('Password are required')
        )));
    }
}