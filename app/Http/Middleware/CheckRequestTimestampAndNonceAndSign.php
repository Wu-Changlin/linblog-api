<?php
// app/Http/Middleware/PreventDuplicateSubmission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Redis;//加载Redis扩展类
use App\Services\SignService;


/* 实现基于timestamp和nonce实现防重放 http防重放功能 */

/*        
基于nonce + timestamp 的方案
nonce的意思是仅一次有效的随机字符串，要求每次请求时该参数要保证不同。实际使用用户信息+时间戳+随机数等信息做个哈希之后，作为nonce参数。

此时服务端的处理流程如下：

1.提交参数的时间戳与服务器当前时间戳是否超过60秒（过期时间根据业务情况设置）。
如果超过了提示签名过期。


2.请求重复
去 redis 中查找是否有 key 为 nonce:{nonce}的 string。

如果没有，则创建这个 key，把这个 key 失效的时间和验证 timestamp 失效的时间一致，比如是 60s。

如果有，说明这个 key 在 60s 内已经被使用了，那么这个请求就可以判断为重放请求。
*/

class CheckRequestTimestampAndNonceAndSign
{

    //限制时间
    private static $limit_time = 60;

    public function handle(Request $request, Closure $next)
    {

        $data = $request->all();

        if(empty($data)){
            sendErrorMSG(403,'空提交');
        }

        // 获取请求参数的签名
        $request_params_sign = $request->input('sign');
            if(empty($request_params_sign)){
                sendErrorMSG(403,'空签名');
            }

        //  访客ip
        $visitor_ip = getVisitorIP();

// 服务器当前时间戳
        $server_current_timestamp = time();

        // 获取请求参数的timestamp
        $request_params_timestamp = $request->input('timestamp');

        if(empty($request_params_timestamp)){
            sendErrorMSG(403,'时间戳为空');
        }
        
        /*1.重放验证
        提交参数的时间戳与服务器当前时间戳是否超过60秒（过期时间根据业务情况设置）
        如果超过了提示签名过期
        */

    //   if(intval($server_current_timestamp) - intval($request_params_timestamp)  > self::$limit_time){
    //     sendMSG(403, [], '签名过期!');
    //   }
        
    //2.请求重复
        // 获取请求参数的nonce
        $request_params_nonce = $request->input('nonce');

        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $redis_host_value = env('REDIS_HOST');
        $redis_password_value = env('REDIS_PASSWORD');
        $redis_port_value = env('REDIS_PORT');
        $repository_serial_number_value = env('REDIS_PREVENT_DUPLICATE_SUBMISSION_REPOSITORY_SERIAL_NUMBER');


        #一分钟接口调用只能10次
        $redis = new Redis();
        $redis->open($redis_host_value, $redis_port_value); //服务器连接的Ip与端口号
        $redis->auth($redis_password_value); //redis服务的密码
        $redis->select($repository_serial_number_value); //选择连接的redis，默认redis的库有16个
        
        #记录nonce
        $nonce_value = $redis->get($request_params_nonce); //get命令用于获取指定的keyz值，如果key值不存在返回null
        // 不存在
        if (!$nonce_value) {
            #设置过期时间为60秒
            $redis->setex($request_params_nonce,self::$limit_time,$request_params_nonce);
            // $redis->expire($visitor_ip, ); //给key值设置生存时间
        } 
        //存在
        // if($nonce_value){
        //     sendMSG(403, [], '重复请求!');
        // }
        
       
        $server_current_sign=SignService::getSign($data);

        //    使用empty()函数来检查变量是否为空值，这个函数会认为空字符串、0、"0"、null、false、undefined、空数组都是空的。
   if(empty($server_current_sign)){
        sendMSG(403, [], '签名失效!');

}

   

   echo $request_params_sign;
   echo "</br>";
   echo $server_current_sign;
   if($server_current_sign!=$request_params_sign){
    sendMSG(403, [], '无效签名!');
   }


        return $next($request);
    }
}
