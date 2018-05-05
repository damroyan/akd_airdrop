<?php
namespace Model;

class Email extends Model {
    const STATUS_BLACKLIST = 3;
    const STATUS_ERROR  = 2;
    const STATUS_SEND   = 1;
    const STATUS_WAIT   = 0;
    
    static $_ = [
        'status' => [
            self::STATUS_BLACKLIST  => 'Черный список',
            self::STATUS_ERROR      => 'Ошибка',
            self::STATUS_SEND       => 'Отправлено',
            self::STATUS_WAIT       => 'Ожидание',
        ]
    ];

    public function initialize() {
        parent::initialize();

        $this->hasOne("user_id",    "Model\User",     "user_id",    array('alias' => 'user'));
    }
}