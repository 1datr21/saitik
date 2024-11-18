<?php

use app\exceptions\{
    NotAuthorizedHttpException
};
use app\middlewares\{
    AccessControl, //Authenticate,
    AccessControlFrontend,
    ProccessRawBody
};
use app\models\{InvalidTokenException, TokenExpiredException};
use Pecee\{
    Http\Request,
    SimpleRouter\SimpleRouter as Router
};

const PROD = false;

Router::setDefaultNamespace('app\controllers');


// фронтендовский роут, только для авторизованных




//Router::get('/login', 'frontend\UserController@login');

Router::group([
    'prefix' => 'api/v1',
    'middleware' => [
        ProccessRawBody::class
    ]
], function () {
    Router::post('/auth/sign_in', 'api\AuthController@signin');

    Router::get('/auth/refresh', 'api\AuthController@refresh');

    // апишные роуты, доступные только авторизованным юзерам
    Router::group([
        'middleware' => [
            AccessControl::class
        ]
    ], function () {
        // authenticated routes
        Router::get('/logout', 'api\AuthController@logout');

        Router::get('/invbook', 'api\InvbookController@index');
        Router::post('/invbook/save', 'api\InvbookController@save');
        Router::get('/acts', 'api\InvbookController@acts');

       // Router::post('/project/create', 'ProjectController@create');
        //Router::post('/project/update/{id}', 'ProjectController@update')            ->where(['id' => '[\d]+']);
    });
});
//Router::get('/', 'frontend\InvbookController@index',['middleware'=>AccessControlFrontend::class])->setMatch('/\/([\w]+)/');
//Router::get('/', 'frontend\InvbookController@index');
Router::get('/', 'frontend\InvbookController@index')->setMatch('/\/([\w]*)/');

//Router::get('/invbook', 'VueController@invbook');//->setMatch('/\/([\w]+)/');
//Router::get('/invbook', 'frontend\InvbookController@index', ['middleware' => [    ProccessRawBody::class  ]]);

Router::get('/controller', 'VueController@run')->setMatch('/\/([\w]+)/');



Router::error(function(Request $request, Exception $exception) {
  
    $response = Router::response();
    
    switch (get_class($exception)) {
    /*    case NotAuthorizedHttpException::class: {
            $response->httpCode(401);
            break;
        }*/
        case InvalidTokenException::class: {
            $response->json(['tstate'=>'invalid']);
            break;
        }
        case TokenExpiredException::class: {
            $response->json(['tstate'=>'expired']);
            break;
        }
        case Exception::class: {
            $response->httpCode(500);
            break;
        }
    }
    if (PROD) {
        return $response->json([
            'status' => 'error',
            'message' => $exception->getMessage()
        ]);
    } else {
        /*
        return $response->json([
            'status' => 'error',
            'message' => $exception->getMessage()
        ]);*/
    }
});

