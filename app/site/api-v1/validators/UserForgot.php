<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

class UserForgot extends Validation  {
    public function initialize() {
        $this->add('user_login', new PresenceOf(array(
            'message'       => 'User login/email is a required field'
        )));

        $this->add('user_login', new Email(array(
            'message'       => 'User login is a required field',
            'allowEmpty'    => false,
        )));
    }
}