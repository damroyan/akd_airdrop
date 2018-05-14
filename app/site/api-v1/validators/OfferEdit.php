<?php
namespace Site\ApiV1\Validators;

use Phalcon;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use \UserIdValidator as UserId;
use Phalcon\Validation\Validator\InclusionIn;

class OfferEdit extends Validation  {
    public function initialize() {
        $this->add('offer_name', new PresenceOf(array(
            'message'       => __('Name is a required field')
        )));

        $this->add('offer_type', new InclusionIn(array(
            'allowEmpty'    => false,
            'domain'        => array_keys(\Model\Offer::getTypeArray()),
            'message'       => __('Incorrect type'),
        )));

        $this->add('offer_url', new PresenceOf(array(
            'message'       => __('Offer URL is required')
        )));


        $this->add('offer_site_url', new PresenceOf(array(
            'message'       => __('Offer website URL is required')
        )));


        $this->add('offer_end_date', new PresenceOf(array(
            'message'       => __('End date is required field')
        )));


        $this->add('offer_rating', new PresenceOf(array(
            'message'       => __('Rating is a required field')
        )));

        $this->add('offer_status', new PresenceOf(array(
            'message'       => __('Status is a required field')
        )));

    }
}