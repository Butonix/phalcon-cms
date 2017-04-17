<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 2/13/17
 * Time: 10:31 PM
 */

namespace Multiple\Backend\Controllers;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Multiple\Backend\Models\Users;
use Phalcon\Http\Response;
use Phalcon\Http\Request;


class SessionController extends Controller
{

    public function indexAction(){
        if(!$this->session->get('Auth.User')){
            $this->response->redirect(array('for' => "backend-login"));
        }
        $this->view->setRenderLevel( View::LEVEL_ACTION_VIEW );
        $this->view->pick("index");

    }

    /**
     * Ajax login. Receive data via json
     *
     */
    public function loginAction()
    {
        if ($this->request->isAjax()) {
            $this->view->setRenderLevel(View::LEVEL_NO_RENDER);

            $post_data = $this->request->getJsonRawBody();

                $username = $post_data->username;
                $password = $post_data->password;
                $token = $post_data->token;

//            if ($this->security->checkToken()) {
                // The token is OK
                $user = Users::findFirstByUsername($username);
                if ($user) {
                    if ($this->security->checkHash($password, $user->password)) {
                        $this->session->set("Auth.User", $user);
                        return json_encode(['error' => false, 'message' => 'You successfully logged in']);
                    } else {
                        return json_encode(['error' => true, 'message' => 'Password not valid. Try again.']);
                    }
                } else {
                    $this->security->hash(rand());
                    return json_encode(['error' => true, 'message' => 'User not found']);
                }
//            } else {
//                return json_encode(['error' => true, 'message' => 'Invalid token!']);
//            }
        } else {
            $this->view->setRenderLevel( View::LEVEL_ACTION_VIEW );
        }
    }

    /**
     * Logout. Destroy only backend user session and redirect to login form.
     *
     */
    public function logoutAction(){

        $this->session->remove("Auth.User");
        $this->response->redirect(array('for' => "backend-login"));
    }
}