<?php
namespace Model;

class Feedback extends Model {

    /**
     * ID feedback
     *
     * @var int
     */
    public $feedback_id;

    /**
     * Тип фидбека. Константы TYPE_*
     *
     * @var string(30)
     */
    public $feedback_type;

    /**
     * Самооставленное пользователем имя
     *
     * @var string(256)
     */
    public $feedback_user_name;

    /**
     * Текст фидбека
     *
     * @var text
     */
    public $feedback_description;

    /**
     * Самооставленный мейл пользователя
     *
     * @var string
     */
    public $feedback_email;

    /**
     * Самооставленный телефон пользователя
     *
     * @var string
     */
    public $feedback_phone;

    /**
     * Если был авторизоан то сохраняем ID
     *
     * @var int
     */
    public $user_id;

    /**
     * ID модератора обработавшего feedback
     *
     * @var int
     */
    public $feedback_moderator_user_id;

    /**
     * Время обработки feedback
     *
     * @var timestamp
     */
    public $feedback_moderator_mdate;

    /**
     * Время создания фидбека
     *
     * @var timestamp
     */
    public $feedback_cdate;

    /**
     * Текущее состояние фидбека. Константы STATUS_*
     *
     * @var string
     */
    public $feedback_status;


    //новая
    const STATUS_NEW = 'new';
    //архивирована
    const STATUS_ARCHIVED = 'archived';
    //заявка обработана
    const STATUS_PROCEEDED = 'proceeded';


    //базовые типы фидбека
    const TYPE_FEEDBACK         = 'feedback';
    const TYPE_THANKS           = 'thanks';
    const TYPE_TECHNICAL        = 'tech';
    const TYPE_FINANCIAL        = 'fin';
    const TYPE_IDEA             = 'idea';
    const TYPE_OTHER            = 'other';

    // далее добавляются новые типы, если нужны


    public function initialize() {
        parent::initialize();

        $this->hasOne('user_id','Model\User','user_id',array('alias'=>'user'));
        $this->hasOne('moderator_user_id', 'Model\User','user_id',array('alias'=>'moderator'));
    }

    /**
     * Проверяет допустимый ли тип фидбека пришел
     *
     * @param $type
     * @return bool
     */
    static function isTypeCorrect($type) {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();

        foreach($constants as $k => $v) {
            $s = explode('_',$k);
            if ($s[0] == 'TYPE' && $v==$type)
                return true;
        }

        return false;
    }
}
