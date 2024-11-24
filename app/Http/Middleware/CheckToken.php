<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;

/* 检查请求是否携带token和有效期 */

class CheckToken
{
    // 状态 关闭：false，开启：true
    private $status = false;

    /**
     * 处理请求
     *
     * @param \Illuminate\Http\Request; $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {

        // 开启检查黑名单IP
        if ($this->status == true) {
            // 使用Request实例的header方法获取Authorization标头
            $authorizationHeader = $request->header('Authorization');
        
            // 假设你从HTTP头部获取了Authorization头部
            // echo 'authorizationHeader:'.$authorizationHeader;
            // 解析Authorization头部，获取token
            if ($authorizationHeader) {
                // 假设token前缀是Bearer
                $token = trim(str_ireplace('Bearer ', '', $authorizationHeader));

                // 验证token
                // if (isTokenValid($token)) {
                //     // token有效，可以继续执行后续操作
                //     echo "Token is valid.";
                // } else {
                //     // token无效，返回错误
                //     header('HTTP/1.0 401 Unauthorized');
                //     echo "Token is invalid.";
                // }
            } else {
                // 没有提供Authorization头部，返回错误
                  // 没有提供Authorization头部，返回错误
            header('HTTP/1.0 401 Unauthorized');
           
            sendErrorMSG(403,$authorizationHeader);
            }
        
        }

        //非黑名单，继续
        return $next($request);
    }
}
