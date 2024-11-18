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
use app\models\Tokenizer as Tokenizer;
use app\models\{InvalidTokenException, TokenExpiredException};

class AccessControl implements IMiddleware
{

    /**
     * @inheritDoc
     * 
     */
    public function test_token($tokenString, $invalid_token_callback)
    {
      //  
        global $TOKEN_PARAMS;
        $secret  = $TOKEN_PARAMS['AT_secret_key'];
      //  $tokenString = substr($headers['Authorization'] ?? '', 7);
     //   echo ">>".empty($tokenString)."<<";
      
        if($tokenString=="" || empty($tokenString)) 
        {
            $invalid_token_callback($tokenString);
            return;
        }

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($TOKEN_PARAMS['AT_secret_key'])
        );

        $token = $config->parser()->parse($tokenString);

        if (
            !$config->validator()->validate(
                $token,
                new SignedWith(
                    new Sha256(),
                    InMemory::plainText($secret)
                ),
                new ValidAt(new FrozenClock(new DateTimeImmutable()))
            )
        ) {
        //    echo "IDI NA HYu";
          $invalid_token_callback($token);
          //  throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
        }

        $userId = $token->claims()->get('uid');
        $this->request['uid'] = $userId;
    }

    public function handle(Request $request): void
    {
        $headers = getallheaders();
        $tokenString = substr($headers['Authorization'] ?? '', 7);
        
      //  echo ">> $tokenString <<";
        /*
        $this->test_token($tokenString, function($token)
          {//  echo "IDI NA HYu";
            throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
          }
        );
*/
        $tstate = Tokenizer::test_token($tokenString);
        switch($tstate)
        {
            case 'Expired':     
                //$this->request['tsatus']='expired';         
                throw new TokenExpiredException('Токен доступа просрочен');
              break;
            case 'Invalid':
                //$this->request['tsatus']='invalid'; 
                throw new InvalidTokenException('Токен доступа не валиден'); 
              break;
        }
        
        /*, function($token) use($request)        
          {//  echo "IDI NA HYu";
            throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
          }
        );*/
/*      $userId = $token->claims()->get('uid');
        $this->request['uid'] = $userId;   */
    }
}