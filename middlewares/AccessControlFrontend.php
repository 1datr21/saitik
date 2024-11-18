<?php
namespace app\middlewares;

use app\exceptions\NotAuthorizedHttpException;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use app\middlewares\AccessControl as AccessControl;
use Pecee\SimpleRouter\Route\ILoadableRoute;
use app\models\Tokenizer as Tokenizer;

class AccessControlFrontend extends AccessControl
{
    public function handle(Request $request): void
    {
        global $Logger;
        $tokenString = $_COOKIE['access_token'] ;
      //  $Logger->out(">>$tokenString<<");
        
        $url_to_login = '/login';
        $testres = Tokenizer::test_token($tokenString);//, function($token) use($request, $url_to_login)
        //echo $testres;
        switch($testres)
        {//  
            case 'Invalid':
              {
               // echo "IDI NA HYu";
                  setcookie("URL",$request->getUrl());
                  $request->setRewriteUrl($url_to_login);
              }
        }
      //);
/*
        $this->test_token( $tokenString, function($token) use($request, $url_to_login)
          {//  echo "IDI NA HYu";
            setcookie("URL",$request->getUrl());
            $request->setRewriteUrl($url_to_login);
            //throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
          }
        );*/
/*      $userId = $token->claims()->get('uid');
        $this->request['uid'] = $userId;   */
    }
}