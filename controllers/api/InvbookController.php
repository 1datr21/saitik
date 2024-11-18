<?php

namespace app\controllers\api;

use app\controllers\AbstractController as AbstractController;
use \Pecee\Http\Input\InputHandler as InputHandler;
use \R as R;

class InvbookController extends AbstractController
{
    public function index()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        return $this->response->json(R::getAll('SELECT * FROM invbook'));
    }

    public function acts()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        return $this->response->json(R::getAll('SELECT * FROM acts'));
    }

    private function saveActValidate($act)
    {
        if(empty($act['Delegate'])) return ['mess'=>'Введите представителя'];
        if(empty($act['Former'])) return ['mess'=>'Введите составителя'];
        $empty_count = 0;
        foreach($act['items'] as $inv)
        {
            if(empty($inv['name']) && empty($inv['charact']))
            {
                $empty_count++;
            }
        }
        if($empty_count==count($act['items']))
        {
            return ['mess'=>'Введите хотя бы один элемент инвентаря'];
        }

        return null;
    }

    public function save()
    {        
        //$values = input();
        $act = $this->request->act;
        R::begin();
        try{
            $validate_result = $this->saveActValidate($act);
            if($validate_result!=NULL)
            {
                return $this->response->json([
                    'response' => 'error',
                    'message'=>$validate_result['mess']
                ]);
            }
            $act_rec = R::xdispense('acts');
            $act_rec->create_dt = date('Y-m-d');
            $act_rec->delegate = $act['Delegate'];
            $act_rec->former = $act['Former'];
            R::store($act_rec);
            // add all inventars
            foreach($act['items'] as $inv)
            {
                
                $inv_rec = R::xdispense('invbook');
                $inv_rec->act_id = $act_rec->id;
                $inv_rec->name = $inv['name'];
                $inv_rec->charact = $inv['charact'];

                R::store($inv_rec);
            }
            R::commit();
        }
        catch(\Exception $exc)
        {
            R::rollback();

            $this->OutException($exc);
        }
            
        //echo "ERRRORRRR";
        return $this->response->json([
            [
                'response' => 'OK',
                'request' => $act,
                'act_rec'=>$act_rec
            ]
        ]);
    }

    public function create(): string
    {
        return $this->response->json([
            [
                'response' => 'OK',
                'request' => "{$this->request->firstName} {$this->request->lastName}",
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