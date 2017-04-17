<?php

    $router->add(
        "/:controller/:action",
        [
            'module'     => 'frontend',
            "controller" => 1,
            "action"     => 2,
        ]
    );

    ## Home page route ##
    $router->add("/", [
        'module'     => 'frontend',
        'controller' => 'index',
        'action'     => 'index',
    ])->setName('frontend-home');