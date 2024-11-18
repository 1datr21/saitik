<?php

namespace app\controllers\frontend;

use app\controllers\AbstractController as AbstractController;


class UserController extends AbstractController
{
    public function index():string
    {
       
        return $this->renderTemplate('./templates/frontend/invbook.php');
    }
    
    public function login(): string
    {
        return $this->renderTemplate('./templates/front/index.php');
        // return $this->renderTemplate('./templates/frontend/login.php');
    }
    
}