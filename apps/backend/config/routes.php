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

//    ## route for crawler sites ##
//    $router->add("/interno-cms/sites", [
//        'module'     => 'backend',
//        'controller' => 'crawler',
//        'action'     => 'allWebsite',
//    ])->setName('backend-crawler');
//
//    $router->add("/interno-cms/crawler/add-site", [
//        'module'     => 'backend',
//        'controller' => 'crawler',
//        'action'     => 'createWebsite',
//    ])->setName('backend-create-website');
//
//    $router->add("/interno-cms/update-site", [
//    'module'     => 'backend',
//    'controller' => 'crawler',
//    'action'     => 'updateWebsite',
//])->setName('backend-update-website');
//
;
//    #Categories
//    $router->add("/interno-cms/categories", [
//        'module'     => 'backend',
//        'controller' => 'categories',
//        'action'     => 'allCategories',
//    ])->setName('backend-categories');
//
//    $router->add("/interno-cms/categories/add", [
//        'module'     => 'backend',
//        'controller' => 'categories',
//        'action'     => 'add',
//    ])->setName('backend-categories-add');
//
//
//
