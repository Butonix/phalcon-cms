<?php

error_reporting(E_ALL);
require __DIR__.'/../apps/libraries/simple_html_dom.php';
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;

define('APP_PATH', realpath('..') . '/');
define('BACKEND_CONFIG', APP_PATH . "apps/backend/config/config.ini");
define('FRONTEND_CONFIG', APP_PATH . "apps/frontend/config/config.ini");

class Application extends BaseApplication
{

    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    protected function registerServices()
    {

        $di = new FactoryDefault();

        $loader = new Loader();

        /**
         * We're a registering a set of directories taken from the configuration file
         */
        $loader
            ->registerDirs([__DIR__ . '/../apps/library/'])
            ->register();

        // Registering a router
        $di->set('router', function () {

            $router = new Router();

            $router->setDefaultModule("frontend");
//==================== FRONT ROUTES START =======================================

            require __DIR__.'/../apps/frontend/config/routes.php';

//==================== FRONT ROUTES END =======================================


//==================== BACKEND ROUTES START =======================================

            require __DIR__.'/../apps/backend/config/routes.php';

//==================== BACKEND ROUTES END =======================================

            return $router;
        });

        $this->setDI($di);
    }

    public function main()
    {

        $this->registerServices();

        // Register the installed modules
        $this->registerModules([
            'frontend' => [
                'className' => 'Multiple\Frontend\Module',
                'path'      => '../apps/frontend/Module.php'
            ],
            'backend'  => [
                'className' => 'Multiple\Backend\Module',
                'path'      => '../apps/backend/Module.php'
            ]
        ]);

        echo $this->handle()->getContent();
    }
}

$debug = new \Phalcon\Debug();
$debug->listen();

$application = new Application();
$application->main();
