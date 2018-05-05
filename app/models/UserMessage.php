<?php
namespace Model;

class UserMessage extends Model {

    /**
     * ID записи
     *
     * @var int
     */
    public $user_message_id;
    /**
     * ID сообщения
     *
     * @var int
     */
    public $message_id;

    /**
     * ID пользователя закрывшего сообщение
     *
     * @var int
     */
    public $user_id;

    /**
     * Точная дата закрытия
     *
     * @var timestamp
     */
    public $user_message_date;

    public function initialize() {
        parent::initialize();

        $this->setSource('user_message');
    }

}