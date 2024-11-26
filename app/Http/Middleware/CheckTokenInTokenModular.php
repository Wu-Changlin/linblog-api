<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\JsonWebTokenService;
use DB;

/* 检查请求是否携带refresh_token和有效期 */

class CheckTokenInTokenModular
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

          // 开启检查token
          if (self::$status == true) {
            // 使用Request实例的header方法获取Authorization标头
            $authorizationHeader = $request->header('Authorization');

            // $authorizationHeader =$request->input('temporary_token');

            // 假设你从HTTP头部获取了Authorization头部
            // echo 'authorizationHeader:'.$authorizationHeader;
            // 解析Authorization头部，获取token
            if ($authorizationHeader) {
                // 假设token前缀是Bearer
                $token = trim(str_ireplace('Bearer ', '', $authorizationHeader));

                // 校验令牌如果验证成功返回payload，否则返回false
                $payload = JsonWebTokenService::verifyJWT($token);

                if (empty($payload)) {
                    sendErrorMSG(403, '令牌失效');
                }

                // 使用strpos查找字母n首次出现的位置
                $pos = strpos($payload['jti'], 'n');
                //$pos + 1 截取首次出现字母'n'之前的字符（包括'n'）
                $token_name = substr($payload['jti'], 0, $pos + 1);

                // 如果不是临时令牌，那么响应错误
                if ($token_name != 'refresh_token') {
                    sendErrorMSG(403, '令牌格式错误！');
                }

                $time_to_live = $payload['exp'] - $payload['iat'];

                // 验证令牌名称的有效时间
                $verify_token_name_time_to_live_result = JsonWebTokenService::verifyTokenNameTimeToLive('refresh_token', $time_to_live);

                if (empty($verify_token_name_time_to_live_result)) {
                    sendErrorMSG(403, '令牌有效时间错误！');
                }
            } else {
                // 没有提供Authorization头部，返回错误

                sendErrorMSG(403, '数据缺失！');
            }
        }

        //非黑名单，继续
        return $next($request);
    }
}
