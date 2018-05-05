<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email;

class UserRegistration extends Validation  {
    public function initialize() {
        $this->add('user_login', new PresenceOf(array(
            'message'       => __('User login is required field')
        )));

        $this->add('user_login', new Email(array(
            'message'       => __('Email that you provide are incorrect'),
            'allowEmpty'    => false,
        )));
        
        $this->add('user_password', new PresenceOf(array(
            'message'       => __('User password is a required field')
        )));

        $this->add('user_password_reply', new PresenceOf(array(
            'message'       => __('Password confirm field is required')
        )));

        $this->add('user_firstname', new PresenceOf(array(
            'message'       => __('User Firstname is a required field')
        )));

        $this->add('user_firstname', new StringLength(array(
            'max' => 50,
            'messageMaximum' => __('First name should be less than 50 symbols'),
        )));

        $this->add('user_lastname', new PresenceOf(array(
            'message'       => __('User lastn ame is a required field')
        )));

        $this->add('user_lastname', new StringLength(array(
            'max' => 50,
            'messageMaximum' => __('Last Name should be less than 50 symbols'),
        )));

        $this->add('user_name', new StringLength(array(
            'max' => 32,
            'messageMaximum' => __('Username should be less than 32 symbols'),
        )));
    }
}