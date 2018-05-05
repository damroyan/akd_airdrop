<?php
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Mvc\Dispatcher;

class SecurityPlugin extends Plugin {
    /**
     * Склеивание ролей
     *
     * @param array $roles
     * @param array $additionRole
     * @return array
     */
    protected function mergeRole($roles = array(), $additionRole = array()) {
        $groups = \Model\User::getGroup();

        foreach($additionRole as $u => $v1) {
            if(isset($groups[$u])) {
                $users = $groups[$u];
            }
            else {
                $users = array($u, );
            }

            foreach($users as $user) {
                foreach($v1 as $permission => $v2) {
                    foreach($v2 as $action => $method) {
                        if(!isset($roles[$user])) {
                            $roles[$user] = array();
                        }

                        if(!isset($roles[$user][$permission])) {
                            $roles[$user][$permission] = array();
                        }

                        if(!isset($roles[$user][$permission][$action])) {
                            $roles[$user][$permission][$action] = array();
                        }

//                        $roles[$user][$permission][$action] = array_merge($roles[$user][$permission][$action], $method);
                        if(is_array($method)) {
                            foreach($method as $v) {
                                if(!in_array($v, $roles[$user][$permission][$action])) {
                                    $roles[$user][$permission][$action][] = $v;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $roles;
    }

    protected function annotationRole(Dispatcher $dispatcher, $controller, $action) {
        $classController = implode('', array_map(function($value) {
            return ucfirst($value);
        }, explode('-', $dispatcher->getControllerName())));

        $class = $dispatcher->getNamespaceName() . "\\{$classController}Controller";

        try {
            /**
             * @var $annotations \Phalcon\Annotations\Collection
             */
            $annotations = $this->annotations->getMethod(
                $class,
                "{$action}Action"
            );
        }
        catch(\Exception $e) {
            throw $e;
        }

        $roles = array();
        if ($annotations->has('Role')) {
            $annotationRole = $annotations->get('Role');
            $argument = $annotationRole->getArguments()[0];

            foreach(array('allow', 'deny') as $permission) {
                if(isset($argument[$permission])) {
                    foreach($argument[$permission] as $role) {
                        if(!isset($roles[$role])) {
                            $roles[$role] = array();
                        }

                        if(!isset($roles[$role][$permission])) {
                            $roles[$role][$permission] = array();
                        }

                        $roles[$role][$permission][$controller] = array($action);
                    }
                }
            }
        }

        return $roles;
    }

    protected function getUserRole() {
        if($this->user->user_id) {
            return $this->user->user_role ? $this->user->user_role : \Model\User::ROLE_GUEST;
        }

        return \Model\User::ROLE_GUEST;
    }

    protected function getAclList($roles, $rule = Acl::DENY, $container = 'global') {
        $container = "acl_{$container}";

        $acl = new AclList();
        $acl->setDefaultAction($rule);

        foreach ($roles as $role => $value) {
            $acl->addRole(new Role($role));
        }

        $arrayResource = array();
        foreach($roles as $accesses) {
            foreach($accesses as $access => $resources) {
                if(!in_array($access, array('allow', 'deny', ))) {
                    break;
                }

                foreach ($resources as $resource => $actions) {
                    if ($resource == '*') {
                        continue;
                    }

                    if (!$arrayResource[$resource]) {
                        $arrayResource[$resource] = array();
                    }

                    foreach ($actions as $action) {
                        if ($action == '*') {
                            continue;
                        }

                        if (!in_array($action, $arrayResource[$resource])) {
                            array_push($arrayResource[$resource], $action);
                        }
                    }
                }
            }
        }

        foreach($arrayResource as $resource => $actions) {
            if(!count($actions)) {
                $actions = array('*', );
            }

            $acl->addResource(new Resource($resource), $actions);
        }

        foreach ($roles as $role => $accesses) {
            foreach($accesses as $access => $resources) {
                if(!in_array($access, array('allow', 'deny', ))) {
                    break;
                }

                foreach ($resources as $resource => $actions) {
                    if($access == 'allow') {
                        $acl->allow($role, $resource, $actions);
                    }
                    else {
                        $acl->deny($role, $resource, $actions);
                    }
                }
            }
        }

        //The acl is stored in session, APC would be useful here too
//        $this->persistent->{$container} = $acl;

        return $acl;
    }
}