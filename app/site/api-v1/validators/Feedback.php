<?php
namespace Site\ApiV1\Validators;

use Phalcon,
    Phalcon\Validation,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    \PhoneValidator as Phone,
    \FeedbackTypeIdValidator as FeedbackTypeId
;

class Feedback extends Validation  {
    public function initialize() {
        $this->add('feedback_user_name', new PresenceOf(array(
            'message'           => __('User name id requireble for feedback')
        )));

        $this->add('feedback_user_name', new StringLength(array(
            'max'               => 255,
            'min'               => 1,
            'messageMaximum'    => __('The name should be less than 255 symbols'),
            'messageMinimum'    => __('Name should have at list one symbol'),
        )));

        $this->add('feedback_email', new Email(array(
            'message'       => __('Email is incorrect'),
            'allowEmpty'    => true,
        )));

        $this->add('feedback_phone', new Phone(array(
            'allowEmpty'    => true,
        )));

        $this->add('feedback_description', new PresenceOf(array(
            'message'       => __('Description are required'),
        )));
    }
}