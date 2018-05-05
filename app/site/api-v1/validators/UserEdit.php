<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use \UserIdValidator as UserId;
use Phalcon\Validation\Validator\InclusionIn;

class UserEdit extends Validation  {
    public function initialize() {
        $this->add('user_id', new UserId(array(
            'allowEmpty'    => true,
        )));

        $this->add('user_login', new PresenceOf(array(
            'message'       => __('User login/email is a required field')
        )));

        $this->add('user_email', new Email(array(
            'message'       => __('User email are incorrect'),
            'allowEmpty'    => true,
        )));

        $this->add('user_firstname', new PresenceOf(array(
            'message'       => __('User First Name are required')
        )));

        $this->add('user_firstname', new StringLength(array(
            'max' => 50,
            'messageMaximum' => __('The length of user first name should be less than 32 symbols'),
        )));

        $this->add('user_lastname', new PresenceOf(array(
            'message'       => __('Last Name is a required field')
        )));

        $this->add('user_lastname', new StringLength(array(
            'max' => 50,
            'messageMaximum' => __('Length of Last Name should be less than 50 symbols'),
        )));

        $this->add('user_name', new StringLength(array(
            'max' => 32,
            'messageMaximum' => __('User Name should be less than 32 symbols'),
        )));

        $this->add('user_role', new InclusionIn(array(
            'allowEmpty'    => false,
            'domain'        => array_keys(\Model\User::getRoleArray()),
            'message'       => __('User role are incorrect'),
        )));
    }
}