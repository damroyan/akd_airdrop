<?php
namespace Model;

class EmailBlacklist extends Model {
    const STATUS_ACTIVE     = 1;
    const STATUS_INACTIVE   = 0;

    static private $_status = array(
        self::STATUS_INACTIVE   => 'Не активный',
        self::STATUS_ACTIVE     => 'Активный',
    );

    static function getStatusArray() {
        return self::$_status;
    }

    static function getStatusStr($statusId) {
        return isset(self::$_status[$statusId]) ? self::$_status[$statusId] : $statusId;
    }

    static function checkStatus($statusId) {
        return isset(self::$_status[$statusId]) ? true : false;
    }

    /**
     * Однородный вид email
     *
     * @param $email
     * @return string
     */
    static public function prepareEmail($email) {
        return trim(mb_strtolower($email));
    }

    public function initialize() {
        parent::initialize();

        $this->setSource("email_blacklist");
    }
}