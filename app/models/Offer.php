<?php
namespace Model;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Offer extends Model {

    const TYPE_AIRDROP = 1;
    const TYPE_BOUNTY = 2;
    const TYPE_ICO = 3;

    const ROLE_ROOT         = 'root';
    const ROLE_USER         = 'user';
    const ROLE_ADMIN        = 'admin';
    const ROLE_MODERATOR    = 'moderator';
    const ROLE_GUEST        = 'guest';

    const STATUS_INACTIVE   = 2;
    const STATUS_ACTIVE     = 1;

    const DELETE_TRUE = 1;
    const DELETE_FALSE = 0;

    static $_ = [
        'type' => [
            self::TYPE_AIRDROP   => 'AirDrop',
            self::TYPE_BOUNTY   => 'Bounty',
            self::TYPE_ICO   => 'ICO',
        ],
        'status' => [
            self::STATUS_ACTIVE     => 'Активный',
            self::STATUS_INACTIVE   => 'Не активен',
        ],
        'role' => [
            self::ROLE_ROOT         => 'Главный администратор',
            self::ROLE_ADMIN        => 'Администратор',
            self::ROLE_MODERATOR    => 'Модератор',
            self::ROLE_USER         => 'Пользователь',
        ]
    ];

    public function initialize() {
        parent::initialize();

        $this->setSource('offer');

    }

}