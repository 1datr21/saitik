<?php

namespace app\controllers\frontend;

use app\controllers\AbstractController as AbstractController;

class InvbookController extends AbstractController
{
    public function index():string
    {
       // print_r($_COOKIE);
       return $this->renderTemplate('./templates/front/index.php');
       // return $this->renderTemplate('./templates/frontend/invbook.php');
    }
    
    /*
    public function create(): string
    {
        return $this->response->json([
            [
                'response' => 'OK',
                'request' => $this->request->project,
            ]
        ]);
    }
    
    public function update(int $id): string
    {
        return $this->response->json([
            [
                'response' => 'OK',
                'request' => $this->request->project,
                'id' => $id
            ]
        ]);
    }
    */
}