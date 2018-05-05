<?php
namespace Model;

class Company extends Model {

    public $company_id;
    public $user_id;
    public $company_name;
    public $company_logo;
    public $company_description;
    /**
     * Дата создания
     *
     * @var date
     */
    public $company_cdate;
    /**
     * Дата крайнего изменения
     *
     * @var date
     */
    public $company_mdate;
    /**
     * по умолчанию всегда active.
     * Внести изменения в описание если в кастомном
     * проекте что-то поменяется
     *
     * @var string
     */
    public $company_status;

    const STATUS_ACTIVE = 'active';

    public function initialize() {
        parent::initialize();

        $this->setSource('company');

        $this->hasOne("user_id",  "Model\User",     "user_id",    array('alias' => 'user'));
    }
}