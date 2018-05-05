<?php
namespace Model;

class Message extends Model {

    /**
     * ID сообщения
     *
     * @var int
     */
    public $message_id;

    /**
     * User Role пользователей, которым это сообщение может быть показано
     *
     * @var string
     */
    public $user_role;

    /**
     * Текст сообщения. Может быть с HTML
     *
     * @var text
     */
    public $message_text;

    /**
     * Тип сообщения. Влияет на цвет отображения.
     * Допустимы следуюшие значения:
     *
     * default - серый
     * primary - синий
     * success - зеленый
     * info - голубой
     * warning - оранжевый
     * danger - красный
     *
     * @var string
     */
    public $message_type;

    /**
     * Дата создания
     *
     * @var timestamp
     */
    public $message_date_create;

    /**
     * Частота показа сообщения. Глобальное/персональное.
     * Константы FREQUENCY_*
     *
     * @var string
     */
    public $message_frequency;

    /**
     * Текущий статус из констант STATUS_*
     *
     * @var string
     */
    public $message_status;

    /**
     * Используется для глоабльных сообщений показывающихся широкому кругу пользователей
     * в зависимости от выбранной роли
     */
    const FREQUENCY_GLOBAL  = 'global';
    /**
     * Используется для сообщений из кода при возникновении условий
     * Сделано чтобы трекать, что сообщение было закрыто.
     */
    const FREQUENCY_USER    = 'user';

    /**
     * Проект активен, можно отправлять на анализ
     */
    const STATUS_ACTIVE     = 'active';
    /**
     * Возникает при удалении проекта
     */
    const STATUS_ARCHIVED   = 'archived';

    public function initialize() {
        parent::initialize();

        $this->setSource('message');
    }

    /**
     * Подготавливает сообщение для отправки на фронт.
     * ГЛАВНОЕ: оборачивает текст сообщения в геттекст и накладывает переданные в values параметры
     *
     * @param $values
     * @return array
     */
    public function prepare($values) {
        if ($this->message_id) {
            return [
                'message_id'    => $this->message_id,
                'message_text'  => __($this->message_text,$values),
                'message_type'  => $this->message_type,
            ];
        }

        return [];
    }

    /**
     * Получает объект крайнего показа сообщения
     *
     * @param $user_id
     * @param $message_id
     * @return static
     */
    static function wasShown($user_id,$message_id) {

        return \Model\UserMessage::findFirst([
            'conditions' => 'user_id = :user_id: AND message_id = :message_id:',
            'bind'      => [
                'user_id'   => $user_id,
                'message_id'=> $message_id
            ],
        ]);
    }

}