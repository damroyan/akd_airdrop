<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use \UserIdValidator as UserId;
use Phalcon\Validation\Validator\InclusionIn;

class CompanyEdit extends Validation  {
    public function initialize()
    {
        $this->add('company_id', new UserId(array(
            'allowEmpty' => true,
        )));

        $this->add('company_name', new PresenceOf(array(
            'message' => __('Company name is Required!')
        )));

        $this->add('company_description', new StringLength(array(
            'max' => 1000,
            'messageMaximum' => __('Length of Description should be less than 1000 symbols'),
        )));
    }

}