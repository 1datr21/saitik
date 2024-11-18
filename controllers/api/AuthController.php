<?php

namespace app\controllers\api;

use app\models\Request;
use ArgumentCountError;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use app\controllers\AbstractController as AbstractController;
use Lcobucci\JWT\Signer\Rsa\Sha384;
use \Pecee\Http\Input\InputHandler as InputHandler;
use \R as R;
use app\models\Tokenizer as Tokenizer;

function generateRandomString($len_of_gen_str=15)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $var_size = strlen($chars);
    $random_str="";
   // echo "Random string ="; 
    for( $x = 0; $x < $len_of_gen_str; $x++ ) {
        $random_str= $random_str . substr($chars,random_int( 0, $var_size - 1 ),1);  
         
    }
    return $random_str;
}


class AuthController extends AbstractController
{
    public function signin()
    {
        global $auth_data;
        global $TOKEN_PARAMS;        

        $login = $this->request->login;
        $passw = $this->request->password;

        $urow = R::getRow("SELECT * FROM users WHERE login='{$login}' AND password='{$passw}'");

    //    print_r($urow);

        if($urow==null)
        {
            return $this->response->json(['signed'=>false]);
        }

        $tpair = Tokenizer::GenTokenPair($urow['id']);

        global $TOKEN_PARAMS, $Logger;
    //    $Logger->print_r($tpair);

        header("Access-Control-Allow-Origin: http://authentication-jwt/");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return $this->response->json([
            'accessToken' => $tpair['access_token'], 'signed'=>true,
            'refreshToken' => $tpair['refresh_token'], 'user'=>['login'=>$urow['login'], 'email'=>$urow['email']]
        ]);
    }

    public function refresh()
    {
        global $auth_data;
        global $TOKEN_PARAMS;        

        

        $headers = getallheaders();

        global $Logger;
       // $Logger->print_r($headers['Refresh']);

        $tpair = Tokenizer::UpdateTokenPair($headers['Refresh']);

        header("Access-Control-Allow-Origin: http://authentication-jwt/");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return $this->response->json([
            'accessToken' => $tpair['access_token'], 'signed'=>true,
            'refreshToken' => $tpair['refresh_token']
        ]); 
    }

    public function logout()
    {
        $headers = getallheaders();

        global $Logger;
        Tokenizer::logout($headers['Refresh']);
        return $this->response->json(['logged_out'=>true]);
    }
}