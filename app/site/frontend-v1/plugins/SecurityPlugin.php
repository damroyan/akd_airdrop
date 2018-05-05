<?php
namespace Site\FrontendV1\Plugins;

use Phalcon\Acl;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

class SecurityPlugin extends \SecurityPlugin {
    public function beforeDispatch(Event $event, Dispatcher $dispatcher) {
        $role = $this->getUserRole();

        $params = $dispatcher->getParams();
        $params = array_merge(
            array(
//                'namespace'     => $dispatcher->getNamespaceName(),
                'controller'    => $dispatcher->getControllerName(),
                'action'        => $dispatcher->getActionName(),
                'module'        => $dispatcher->getModuleName(),
                'for'           => ($dispatcher->getDI()->getShared('router')->getMatchedRoute()
                                        ? $dispatcher->getDI()->getShared('router')->getMatchedRoute()->getName()
                                        : ''),
            ),
            $params
        );

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // Namespace support
        $namespace  = $dispatcher->getNamespaceName();
        $namespace = mb_strtolower(explode("Controllers\\", $namespace, 2)[1]);
        $controller = ($namespace ? "{$namespace}\\" : "") . $controller;

        $acl = $this->getAcl(
            $this->annotationRole($dispatcher, $controller, $action)
        );



        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {
            return $dispatcher->forward(array(
                'namespace'  => 'Site\FrontendV1\Controllers',
                'controller' => 'error',
                'action'     => 'permission',
            ));

        }

    }

    /**
     * Returns an existing or new access control list
     *
     * @returns \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl($additionRole = []) {
        $roles = [
            'root' => [
                'allow' => [
                    '*' => ['*', ],
                ],
            ],
        ];

        $roles = $this->mergeRole(
            $roles,
            $additionRole
        );

        return $this->getAclList($roles, Acl::DENY, 'backend_v1');
    }
} 