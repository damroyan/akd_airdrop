<?php
namespace Site\ApiV1\Plugins;

use Phalcon\Acl;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

class SecurityPlugin extends \SecurityPlugin {
    public function beforeDispatch(Event $event, Dispatcher $dispatcher) {
        $role = $this->getUserRole();

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // Namespace support
        $namespace  = $dispatcher->getNamespaceName();
        $namespace = mb_strtolower(explode("Controllers\\", $namespace, 2)[1]);
        $controller = ($namespace ? "{$namespace}\\" : "") . $controller;

        // User blocked
        if(
            $this->user->user_id &&
            (
                $this->user->user_status == \Model\User::STATUS_BLOCKED ||
                $this->user->user_status == \Model\User::STATUS_INACTIVE ||
                $this->user->user_delete == \Model\User::DELETE_TRUE
            )
        ) {
            if(
                ($controller == 'error' && $action == 'userblocked') ||
                ($controller == 'error' && $action == 'notfound') ||
                ($controller == 'index' && $action == 'index') ||
                ($controller == 'user'  && $action == 'logout')
            ) {
            }
            else {
                return $this->dispatcher->forward(array(
                    'namespace'     => 'Site\ApiV1\Controllers',
                    'controller'    => 'error',
                    'action'        => 'userblocked',
                ));
            }
        }

        $acl = $this->getAcl(
            $this->annotationRole($dispatcher, $controller, $action)
        );

//        if (!$acl->isRole($role)) {
//            $role = \Model\User::ROLE_GUEST;
//        }

        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {
            if($role != \Model\User::ROLE_GUEST) {
                return $this->dispatcher->forward(array(
                    'namespace'     => 'Site\ApiV1\Controllers',
                    'controller'    => 'error',
                    'action'        => 'permission',
                ));
            }
            else {
                return $this->dispatcher->forward(array(
                    'namespace'     => 'Site\ApiV1\Controllers',
                    'controller'    => 'error',
                    'action'        => 'auth',
                ));
            }
        }
    }

    /**
     * Returns an existing or new access control list
     *
     * @returns \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl($additionRole = array()) {
        $roles = array(
            'root' => array(
                'allow' => array(
                    '*' => array('*', ),
                ),
            ),
        );

        $roles = $this->mergeRole(
            $roles,
            $additionRole
        );

        return $this->getAclList($roles, Acl::DENY, 'api_v1');
    }
}