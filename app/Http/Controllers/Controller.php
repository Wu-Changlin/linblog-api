<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    /**
     * 接口返回数据
     * @param $msg         自定义提示信息
     * @param int $code    xiang
     * @param array $data
     */
    public function sendMSG($msg, $code=0, $data = []) {
        header('content-type:application/json;charset=utf8');
        // 解决Ajax跨域
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        $arr = array(
            'msg' => $msg,
            'code' => $code,
            'data' => empty($data) ? "" : $data
            // 'data' => empty($data) ? "" : enGzip($data)
        );
        die(json_encode($arr));
    }




    //   /**
    //  * 成功返回
    //  */
    // public static function success($data)
    // {
    //     header('content-type:application/json;charset=utf8');
    //     // 解决Ajax跨域
    //     header('Access-Control-Allow-Origin: *');
    //     header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    //     echo json_encode([
    //         'status' => true,
    //         'code' => '200',
    //         'data' => (object)$data,
    //         'message' => '',
    //     ]);

    //     die;
    // }

    // /**
    //  * 成功返回不die掉
    //  */
    // public static function nodie_success($data)
    // {
    //     header('content-type:application/json;charset=utf8');
    //     // 解决Ajax跨域
    //     header('Access-Control-Allow-Origin: *');
    //     header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    //     echo json_encode([
    //         'status' => true,
    //         'code' => '200',
    //         'data' => (object)$data,
    //         'message' => '',
    //     ]);

    // }

    // /**
    //  * 失败返回
    //  */
    // public static function error($code, $message = '')
    // {
    //     // 出现404错误时,把相关信息记录到日志中,以方便发现错误
    //     // if($code == 404){
    //     //     $deaslog = new BaseSeaslog();
    //     //     $path = $GLOBALS['OPTIONS']['seaslog']."404/";
    //     //     $deaslog->writeData("404: {$message}",$path,'ERROR');
    //     // }

    //     header('content-type:application/json;charset=utf8');
    //     // 解决Ajax跨域
    //     header('Access-Control-Allow-Origin: *');
    //     header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    //     // 载入语言包
    //     // self::langPackageLoad();

    //     // 如果没有自定义message，则使用code查询语言包中对应的错误提示
    //     if(empty($message)){
    //         $message = self::$_lang[$code] ?? $code;
    //     }

    //     echo json_encode([
    //         'status' => false,
    //         'code' => $code,
    //         'data' => new \stdClass(),
    //         'message' => $message,
    //     ]);

    //     die;
    // }




    
}
