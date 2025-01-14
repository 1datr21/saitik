<?php

namespace app\controllers;

class ProjectController extends AbstractController
{
    public function index():string
    {
        return $this->response->json(\R::getAll('SELECT * FROM invbook'));
    }
    /**
     * post /api/v1/project/create
     * body:
       {
            "project": {
                "prop": "value"
             }
        }
     */
    public function create(): string
    {
        return $this->response->json([
            [
                'response' => 'OK',
                'request' => $this->request->project,
            ]
        ]);
    }
    /**
     * post /api/v1/project/update/3
     * body:
        {
            "project": {
                "prop": "value"
            }
        }
     */
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
}