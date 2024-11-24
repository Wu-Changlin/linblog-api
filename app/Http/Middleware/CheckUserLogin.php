<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\IpBlackListService;

/* 检查用户是否登录 */
class CheckUserLogin
{
    // 状态 关闭：false，开启：true
    private $status = true;

    /**
     * 处理请求
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        // 开启检查黑名单IP
        if ($this->status == true) {
            //  访客ip
            $visitor_ip = getVisitorIP();
            $data = [
                'ip_address' => $visitor_ip
            ];
            // 调用ip服务检查是否禁止IP
            $check_res = IpBlackListService::isBanned($data);

            // 存在禁止访问，停止运行代码
            if ($check_res) {
                sendMSG(403, [],'禁止访问');
                die;
            }
        }
        //非黑名单，继续
        return $next($request);
    }
}
