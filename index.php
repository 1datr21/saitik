<?php

use Pecee\SimpleRouter\SimpleRouter as Router;

class EasyLogger {
    var $log_file;

    function __construct($fname){
        $this->log_file = $fname;
    }

    function out($txt)
    {
        $date = new \DateTime();
        file_put_contents($this->log_file, date_format($date, 'Y-m-d H:i:s')." : ".$txt.PHP_EOL , FILE_APPEND | LOCK_EX);
    }

    function print_r($obj)
    {
        ob_start();
        print_r($obj);
        $str = ob_get_clean();
        ob_end_clean();
        
        $this->out($str);
    }
}
//error_reporting(E_ERROR | E_PARSE);
$Logger = new EasyLogger("log.txt");
//Router::startDebug();

require_once __DIR__ . '/vendor/autoload.php';
require_once (__DIR__ . '/config/routes.php'); // таблица маршрутизации
class_alias('\RedBeanPHP\R', '\R');
R::ext('xdispense', function( $type ){
    return R::getRedBean()->dispense( $type );
});
require_once './dbcfg.php';
require_once './authcfg.php';


try {
    Router::start();
} catch (Throwable $e) {
    echo json_encode(['Message'=>$e->getMessage(), 'ErrCode'=>$e->getCode(),
        'file'=>$e->getFile(),'line'=>$e->getLine()]);    
}
catch (\Exception $e) {
    echo json_encode(['Message'=>$e->getMessage(), 'ErrCode'=>$e->getCode(),
        'file'=>$e->getFile(),'line'=>$e->getLine()]);    
}

