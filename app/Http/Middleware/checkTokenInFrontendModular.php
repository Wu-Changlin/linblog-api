<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


/* 检查请求是否携带token (因为前端使用签名代替token,所以没有有效期 ) */

class checkTokenInFrontendModular
{
    // 状态 关闭：false，开启：true
    private static $status = false;

    /**
     * 处理请求
     *
     * @param \Illuminate\Http\Request; $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {

        // 开启token
        if (self::$status == true) {
            // 使用Request实例的header方法获取Authorization标头
            $authorizationHeader = $request->header('Authorization');
        
            // 假设你从HTTP头部获取了Authorization头部
            // echo 'authorizationHeader:'.$authorizationHeader;
            // 解析Authorization头部，获取token
            if ($authorizationHeader) {
                // 假设token前缀是Bearer
                $token = trim(str_ireplace('Bearer ', '', $authorizationHeader));

                // 获取提交参数的签名
                $request_params_sign= $request->input('sign');

                // 如果没有token，那么响应错误
                if (empty($token)) {
                    // token有效，可以继续执行后续操作
                    sendErrorMSG(403,'数据缺失！');
                } 

                // 检验是否一致性
                if($token != $request_params_sign){
                    sendErrorMSG(403,'令牌无效');
                }

            } else {
                // 没有提供Authorization 头部，返回错误

                    sendErrorMSG(403,'数据缺失！');
            }
        
        }

        //非黑名单，继续
        return $next($request);
    }
}
