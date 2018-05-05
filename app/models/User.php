<?php
namespace Model;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class User extends Model {

    const LOGIN_PREFIX_FB = 'FB_';
    const LOGIN_PREFIX_VK = 'VK_';
    const LOGIN_PREFIX_OK = 'OK_';

    const ROLE_ROOT         = 'root';
    const ROLE_USER         = 'user';
    const ROLE_ADMIN        = 'admin';
    const ROLE_MODERATOR    = 'moderator';
    const ROLE_GUEST        = 'guest';

    const STATUS_INACTIVE   = 2;
    const STATUS_ACTIVE     = 1;
    const STATUS_BLOCKED    = 0;
    const STATUS_DELETED    = 3;

    static $_ = [
        'login_prefix' => [
            self::LOGIN_PREFIX_FB   => 'Facebook',
            self::LOGIN_PREFIX_VK   => 'ВКонтакте',
            self::LOGIN_PREFIX_OK   => 'Одноклассники',
        ],
        'status' => [
            self::STATUS_BLOCKED    => 'Заблокирован',
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

    static public function assignId($userId, $userName) {
        if($userName) {
            $userId = self::getActiveIdByName($userName)[0];
        }

        if($userId) {
            $user = self::find([
                "user_id = :user_id: AND user_delete = :user_delete: AND user_status = :user_status:",
                'bind' => [
                    'user_id'       => $userId,
                    'user_delete'   => self::DELETE_FALSE,
                    'user_status'   => self::STATUS_ACTIVE,
                ],
                'columns' => [
                    'user_id',
                ]
            ]);

            if($user) {
                return $userId;
            }
        }

        return null;
    }

    static public function getActiveIdByName($items) {
        if(!$items) {
            return [];
        }

        if(!is_array($items)) {
            $items = explode(';', $items);
        }

        if(!count($items)) {
            return [];
        }

        $userId = [];
        foreach($items as $item) {
            preg_match('@\[#(\d+)\]\s+@ui', trim($item), $match);
            if($match[1]) {
                $id = (int)$match[1];
                $userId[$id] = $id;
            }
        }

        if(!count($userId)) {
            return [];
        }

        list($placeholder, $value) = \Helper\DbIn::exec(array_values($userId));
        $users = self::find([
            "user_id IN ({$placeholder}) AND user_delete = :user_delete: AND user_status = :user_status:",
            'bind' => array_merge(
                $value,
                [
                    'user_delete'     => self::DELETE_FALSE,
                    'user_status'     => self::STATUS_ACTIVE,
                ]
            ),
            'columns' => [
                'user_id',
            ]
        ]);

        $return = [];
        foreach($users as $user) {
            $return[] = $user->user_id;
        }

        return $return;
    }

    /**
     * Проверка доступности user_login
     *
     * @param $userLogin
     * @param $userId
     * @return bool
     */
    static function loginValidate($userLogin, $userId = 0) {
        $userId = (int)$userId;

        $user = self::findFirst([
            'user_login = :user_login: AND user_id != :user_id:',
            'bind' => [
                'user_login'    => $userLogin,
                'user_id'       => $userId,
            ],
            'columns' => [
                'user_id',
            ],
        ]);

        if($user) {
            return false;
        }

        return true;
    }

    /**
     * Проверка вхождения роли в группу
     *
     * @param $role
     * @param $group
     * @return bool
     */
    static public function inGroup($role, $group) {
        if(!$role || !$group) {
            return false;
        }

        $groups = self::getGroup();

        if(!$groups[$group]) {
            return false;
        }

        if(!in_array($role, $groups[$group])) {
            return false;
        }

        return true;
    }

    /**
     * Получение "групп". Вхождение ролей в роль.
     * Группа - набор ролей.
     *
     * @return array
     */
    static public function getGroup() {
        $array = [];

        $array['root'] = [
            \Model\User::ROLE_ROOT,
        ];

        $array['admin'] = array_merge(
            $array['root'],
            [
                \Model\User::ROLE_ADMIN,
            ]
        );

        $array['moderator'] = array_merge(
            $array['admin'],
            [
                \Model\User::ROLE_MODERATOR,
            ]
        );

        $array['auth'] = array_merge(
            $array['moderator'],
            [
                \Model\User::ROLE_USER,
            ]
        );

        $array['public'] = array_merge(
            $array['auth'],
            [
                \Model\User::ROLE_GUEST,
            ]
        );

        return $array;
    }

    private function _getParams(&$params = null) {
        if(is_null($params)) {
            $params = $this->toArray([
                'user_id',
                'user_name',
                'user_firstname',
                'user_lastname',
                'user_picture',
            ]);
        }

        return $params;
    }

    public function getUsername($params = null) {
        $this->_getParams($params);

        return trim(
            $params['user_firstname'] .
            ($params['user_name'] ? " [{$params['user_name']}]" : "") .
            " {$params['user_lastname']}"
        );
    }

    public function getName($params = null) {
        $this->_getParams($params);

        return $params['user_name']
            ? $params['user_name']
            : trim("{$params['user_firstname']} {$params['user_lastname']}");
    }

    /**
     * Пользователь из соц. сети
     * 
     * @return bool
     */
    public function isSocial() {
        if(preg_match('@^(' . implode('|', array_keys(self::getLoginPrefixArray())) . ')\d+$@ui', $this->user_login)) {
            return true;
        }
        
        return false;
    }

    public function initialize() {
        parent::initialize();

        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'user_delete',
                'value' => self::DELETE_TRUE,
            )
        ));

        $this->setSource('user');

        $this->hasMany("user_id",  "Model\UserAccessLog",     "user_id",    array('alias' => 'user_access_log'));

        $this->hasOne("user_id",  "Model\Company",     "user_id",    array('alias' => 'company'));
    }

}