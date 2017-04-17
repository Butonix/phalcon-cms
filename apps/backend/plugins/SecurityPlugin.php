<?php

namespace Multiple\Backend\Plugins;


use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;

class SecurityPlugin extends Plugin
{

    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    public function getAcl()
    {
        //unseting data from memory so action can cache again
        unset($this->persistent->acl);
        if (!isset($this->persistent->acl)) {
            $acl = new AclList();
            $acl->setDefaultAction(Acl::DENY);

            // Register roles
            $roles = [
                "users"  => new Role("Users"),
                "guests" => new Role("Guests"),
            ];

            foreach ($roles as $role) {
                $acl->addRole($role);
            }
            //Private area resources. Add to array functions that you want to access public
            $privateResources = [
                'crawler'    => ['createWebsite', 'updateWebsite', 'allWebsite', 'editWebsite', 'deleteWebsite', 'mission'],
                'session'    => ['logout'],
                'categories' => ['allCategories','delete','editCategory','updateCategory','createCategory']
            ];
            foreach ($privateResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
            }

            //Public area resources
            $publicResources = [
                'session'      => ['login'],
            ];
            foreach ($publicResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
            }

            // Grant access to public areas to both users and guests
            foreach ($roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    $acl->allow($role->getName(), $resource, "*");
                }
            }
            //Grant access to private area to role Users
            foreach ($privateResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Users', $resource, $action);
                }
            }
            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
        }
        return $this->persistent->acl;
    }

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        // Check whether the "auth" variable exists in session to define the active role
        $auth = $this->session->get("Auth.User");

        if (!$auth) {
            $role = "Guests";
        } else {
            $role = "Users";
        }

        // Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action     = $dispatcher->getActionName();

        // Obtain the ACL list
        $acl = $this->getAcl();

        // Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed($role, $controller, $action);


        if (!$allowed) {
            // If he doesn't have access forward him to the index controller
            $this->flashSession->error(
                "You don't have access to this module"
            );

            $dispatcher->forward(
                [
                    'module'     => 'backend',
                    'controller' => 'session',
                    'action'     => 'login',
                ]
            );

            // Returning "false" we tell to the dispatcher to stop the current operation
            return false;
        }

    }
}