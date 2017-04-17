<?php

    $router->add("/admin/:controller/:action/:params",
        [
            'module'     => 'backend',
            "controller" => 1,
            "action"     => 2,
            "params"     => 3,
        ]
    );
    //TODO privremena ruta dok se crawler ne namesti dinamicki
    $router->add("/interno-cms/crawler", [
        'module'     => 'backend',
        'controller' => 'crawler',
        'action'     => 'data',
    ])->setName('backend-crawler');

    ## route for login page ##
    $router->add("/interno-cms/login", [
        'module'     => 'backend',
        'controller' => 'session',
        'action'     => 'login',
    ])->setName('backend-login');

    ## route for logout ##
    $router->add("/interno-cms/logout", [
        'module'     => 'backend',
        'controller' => 'session',
        'action'     => 'logout',
    ])->setName('backend-logout');

    ## route for cms index ##
    $router->add("/interno-cms/index", [
        'module'     => 'backend',
        'controller' => 'session',
        'action'     => 'index',
    ])->setName('backend-index');
