<?php

namespace Multiple\Backend;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Security;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Multiple\Backend\Plugins\SecurityPlugin;
use Multiple\Backend\Plugins\NotFoundPlugin;


class Module implements ModuleDefinitionInterface
{
    var $configBackend ;

    /**
     * Module constructor.
     */
    public function __construct()
    {
        // Read the configuration
        $this->configBackend = new ConfigIni(BACKEND_CONFIG);
    }

    /**
     * Registers the module auto-loader
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {

        $loader = new Loader();

        $loader->registerNamespaces(
            [
                'Multiple\Backend\Controllers' => $this->configBackend->backend->controllersDir,
                'Multiple\Backend\Models'      => $this->configBackend->backend->modelsDir,
                'Multiple\Backend\Models\Behaviors' => $this->configBackend->backend->behaviorsDir,
                'Multiple\Backend\Plugins'     => $this->configBackend->backend->pluginsDir,
                'Multiple\Backend\Validations'  => $this->configBackend->backend->validations
            ]
        );

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {

        // Registering a dispatcher
        $di->set('dispatcher', function () {
            // Create an events manager
            $eventsManager = new EventsManager();

            // Listen for events produced in the dispatcher using the Security plugin
            $eventsManager->attach("dispatch:beforeExecuteRoute", new SecurityPlugin());

            // Handle exceptions and not-found exceptions using NotFoundPlugin
//            $eventsManager->attach("dispatch:beforeException", new NotFoundPlugin());
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Multiple\Backend\Controllers\\');

            // Assign the events manager to the dispatcher
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });

        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/backend/views/');
            return $view;
        });

        // session adapter
        $di->setShared(
            "session",
            function () {
                $session = new \Phalcon\Session\Adapter\Files();

                $session->start();

                return $session;
            }
        );

        // security component
        $di->set(
            "security",
            function () {
                $security = new Security();

                // Set the password hashing factor to 12 rounds
                $security->setWorkFactor(12);

                return $security;
            },
            true
        );

        // Set up the flash service
        $di->set(
            "flash",
            function () {
                return new FlashDirect( [
                    "error"   => "alert alert-danger",
                    "success" => "alert alert-success alert-dismissable",
                    "notice"  => "alert alert-info",
                    "warning" => "alert alert-warning",
                ]);
            }
        );

        // Set up the flash session service
        $di->set(
            "flashSession",
            function () {
                return new FlashSession( [
                    "error"   => "alert alert-danger",
                    "success" => "alert alert-success alert-dismissable",
                    "notice"  => "alert alert-info",
                    "warning" => "alert alert-warning",
                ]);
            }
        );

        // Set a different connection in each module
        $db_credentials = [
            "host" => $this->configBackend->database->host,
            "username" => $this->configBackend->database->username,
            "password" => $this->configBackend->database->password,
            "dbname" => $this->configBackend->database->name,
        ];
        $di->set('db', function () use ($db_credentials) {
            return new Database($db_credentials);
        });
    }
}