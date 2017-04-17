<?php

namespace Multiple\Backend\Controllers;

use Phalcon\Mvc\Controller;

class UsersController extends Controller
{

    public function indexAction(){

    }

    /**
     * Only generate password for backend user
     */
    public function registerAction($password)
    {
        // Store the password hashed
        $password = $this->security->hash($password);
//        var_dump($password); die;

    }
}
