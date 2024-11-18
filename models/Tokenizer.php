<?php
namespace app\models;

use app\models\Request;
use ArgumentCountError;
use DateTimeImmutable;
use \Lcobucci\JWT\Configuration;
use \Lcobucci\JWT\Signer\Hmac\Sha256;
use \Lcobucci\JWT\Signer\Key\InMemory;
use app\controllers\AbstractController as AbstractController;
use Lcobucci\JWT\Signer\Rsa\Sha384;
use \Pecee\Http\Input\InputHandler as InputHandler;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\FrozenClock;
use \R as R;

use app\exceptions\NotAuthorizedHttpException as NotAuthorizedHttpException;



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

class TokenExpiredException extends NotAuthorizedHttpException
{

}

class InvalidTokenException extends NotAuthorizedHttpException
{

}


class Tokenizer
{
    static function GenTokenPair($uid)
    {
        global $TOKEN_PARAMS;  
        global $_USER;
        $USER = ['id'=>$uid];

        $access_token = self::getNewAccessToken($uid);
 
        //$refresh_token = md5("{time()}".generateRandomString(25).$TOKEN_PARAMS['RT_secret_key']);
        $refresh_token = self::getNewRefreshToken();

        
        $token_rec = R::xdispense('tokens');
        $token_rec->refresh_token = $refresh_token;
        $token_rec->access_token = $access_token;
        $token_rec->user_id = $uid;
        $token_rec->rt_expire = (new \DateTime())->modify($TOKEN_PARAMS['RT_Expires']); 
        R::store($token_rec);
        
        //R::xdispense();

        return ['access_token'=>$access_token,'refresh_token'=>$refresh_token];
    }

    static function getNewAccessToken($uid)
    {
        self::TokenDustmen();
        global $TOKEN_PARAMS;

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($TOKEN_PARAMS['AT_secret_key'])
        );
        $now = new DateTimeImmutable();

        $access_token = $config->builder()
            // Configures the issuer (iss claim)
            ->issuedBy($TOKEN_PARAMS['IssuedBy'])
            // Configures the audience (aud claim)
            ->permittedFor($TOKEN_PARAMS['PermittedFor'])
            // Configures the id (jti claim)
            ->identifiedBy($uid)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify($TOKEN_PARAMS['AT_Expires']))
            // Configures a new claim, called "uid"
            ->withClaim('uid', $uid)
            // Configures a new header, called "foo"
            ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($config->signer(), $config->signingKey());

 
        //$refresh_token = md5("{time()}".generateRandomString(25).$TOKEN_PARAMS['RT_secret_key']);
        // $refresh_token = getNewRefreshToken();

        return $access_token->toString();

    }

    static function getNewRefreshToken()
    {
        global $TOKEN_PARAMS;  
        return md5(time()."".generateRandomString(25).$TOKEN_PARAMS['RT_secret_key']);
    }

    // удаляет теги с просороченным рефреш-токеном
    static public function TokenDustmen()
    {
        R::exec("DELETE FROM tokens WHERE rt_expire < NOW()");        
    }


    static public function UpdateTokenPair($rtoken)
    {
        global $TOKEN_PARAMS;
        global $Logger;

        self::TokenDustmen();

        $sql="SELECT * FROM tokens WHERE refresh_token='$rtoken'";
        $token_row = R::getRow($sql);
        $Logger->print_r($token_row);
        $Logger->out($sql);
        if(empty($token_row)) return null;
        // отсечь перехват рефреш-токена
        $control_rows = R::getAll("SELECT * FROM tokens WHERE rtoken_old='$rtoken'");              
        
        if(!empty($control_rows))
        {
            $must_trash = R::findAll("SELECT * FROM tokens WHERE  rtoken_old='$rtoken'");
            R::trash($must_trash);
            return null;
        }
        
        
        $token_row = R::load('tokens', $token_row['id']);
        $Logger->print_r( (new \DateTime())->modify($TOKEN_PARAMS['RT_Expires']) );

        $Logger->print_r($token_row);

        $access_token = self::getNewAccessToken($token_row->user_id);
        $refresh_token = self::getNewRefreshToken();
        $token_row->rtoken_old = $token_row->refresh_token;
        $token_row->access_token = $access_token;
        $token_row->refresh_token = $refresh_token; 
        $token_row->rt_expire = (new \DateTime())->modify($TOKEN_PARAMS['RT_Expires']); 

        
        R::store($token_row);
        
        //R::xdispense();

        return ['access_token'=>$access_token,'refresh_token'=>$refresh_token];
    }

    static public function logout($rtoken)
    {
        R::exec("DELETE FROM tokens WHERE refresh_token='$rtoken'");        
    }

    static public function test_token($tokenString, $invalid_token_callback=null)
    {
      //  
        global $TOKEN_PARAMS;
        $secret  = $TOKEN_PARAMS['AT_secret_key'];
      //  $tokenString = substr($headers['Authorization'] ?? '', 7);
     //   echo ">>- $tokenString -<<";
        if($tokenString=="" || empty($tokenString)) 
        {
            //$invalid_token_callback($tokenString);
            return 'Invalid';
        }

        self::TokenDustmen();

        $token_row = R::getRow("SELECT * FROM tokens WHERE access_token='$tokenString'");
        
        if($token_row==NULL)
        {
            return 'Invalid';
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
        //  $invalid_token_callback($token);
          return 'Expired';
          //  throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
        }

        global $_USER;
        $USER = ['id'=>$token->claims()->get('uid')];
        return 'Works';
        
    }


}

?>