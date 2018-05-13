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
            'message'       => __('Имя это обязательное поле')
        )));

        $this->add('offer_type', new InclusionIn(array(
            'allowEmpty'    => false,
            'domain'        => array_keys(\Model\Offer::getTypeArray()),
            'message'       => __('Тип выбран некорректно'),
        )));

        $this->add('offer_url', new PresenceOf(array(
            'message'       => __('Ссылка на оффер это обязательное поле')
        )));


        $this->add('offer_site_url', new PresenceOf(array(
            'message'       => __('Ссылка на сайт оффера это обязательное поле')
        )));


        $this->add('offer_end_date', new PresenceOf(array(
            'message'       => __('Дата окончания оффера это обязательное поле')
        )));


        $this->add('offer_rating', new PresenceOf(array(
            'message'       => __('Рейтинг это обязательное поле')
        )));

        $this->add('offer_status', new PresenceOf(array(
            'message'       => __('Статус это обязательное поле')
        )));

    }
}