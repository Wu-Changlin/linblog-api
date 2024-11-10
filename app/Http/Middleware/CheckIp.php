<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use DB;


use App\Services\IpBlackListService;

class CheckIp
{
    // 状态 关闭：false，开启：true
    private $status = true;
    // ip库
    private $StoreIp = ['127.0.0.2', '127.0.0.1'];


    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 开启检查黑名单IP
        if ($this->status == true) {
            $ipBlackListService = new IpBlackListService();
            //  访客ip
            $visitor_ip = getVisitorIP();
            $data = [
                'ip_address' => $visitor_ip
            ];
            // 调用ip服务检查IP是否存在黑名单IP表
            $check_res = $ipBlackListService->checkIpInBlackListTable($data);

            // 存在禁止访问，停止运行代码
            if ($check_res) {
                sendMSG('禁止访问', 403, []);
                die;
            }
        }
        //非黑名单，继续
        return $next($request);
    }
}
