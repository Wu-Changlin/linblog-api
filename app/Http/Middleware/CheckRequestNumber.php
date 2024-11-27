<?php
// app/Http/Middleware/PreventDuplicateSubmission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Redis;//加载Redis扩展类

use App\Redis\RedisBase;

/* 实现检测请求次数的功能 */

class CheckRequestNumber
{

     // 状态 关闭：false，开启：true
     private static $status = true;
    //时间内访问的总次数

    private static $total = 0;
    //时间内最大访问次数
    private static $max_frequency = 10;
    //限制时间
    private static $limit_time = 60;
    //黑名单锁定时间
    private static $black_list_lock_time=180;

    //当前时间
    private $now_time;

    public function __construct() {
        $this->now_time = time();
    }

    public function handle(Request $request, Closure $next)
    {
        $visitor_ip = getVisitorIP();
        // 初始化类
        RedisBase::_initialize($config=['db'=>1]);

    
        $lock_time =RedisBase::zScore('request_number_user_black_list', $visitor_ip); 

        RedisBase::close();
        echo   $lock_time;

        $lock_time =RedisBase::zScore('request_number_user_black_list', $visitor_ip); 

        echo 'close后再查：'.$lock_time;
   

        dd($lock_time);

        // 开启检测请求次数
        if(self::$status == true){

        //  访客ip
        $visitor_ip = getVisitorIP();
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $redis_host_value = env('REDIS_HOST');
        $redis_password_value = env('REDIS_PASSWORD');
        $redis_port_value = env('REDIS_PORT');
        $repository_serial_number_value = env('REDIS_REQUEST_NUMBER_SERIAL_NUMBER');

        #一分钟接口调用只能10次
        $redis = new Redis();
        $redis->open($redis_host_value, $redis_port_value); //服务器连接的Ip与端口号
        $redis->auth($redis_password_value); //redis服务的密码
        $redis->select($repository_serial_number_value); //选择连接的redis，默认redis的库有16个


        //        $redis->flushAll();exit;//清空redis的所有库
        $lock_time = $redis->zScore('request_number_user_black_list', $visitor_ip); //返回有序集中key中成员member的score
        // 黑名单锁定时间 目前设置3分钟
        if (($this->now_time - $lock_time) < self::$black_list_lock_time) {
            // return 1; //在黑名单中
            // 黑名单锁定分钟
            $black_list_lock_minute=(self::$black_list_lock_time)/60;
            sendErrorMSG(403,  '因为调用接口频繁，所以封禁'.$black_list_lock_minute.'分钟。');

        } else {
            /*
            Redis的ZREM命令用于删除有序集合中的一个或多个成员，当删除最后一个元素时，
            有序集合仍然存在，除非该有序集合中没有其他元素，此时整个有序集合会被删除‌。
            */
            $redis->zRem('request_number_user_black_list', $visitor_ip); //redis中zRem命令用于移除有序集合中的一个或者是多个成员，不存在的成员将被忽略，当key存在但是不是有序集合类型是，返回一个错误
        }
        
        #记录访问次数
        $ip_value = $redis->get($visitor_ip); //get命令用于获取指定的keyz值，如果key值不存在返回null
        // 不存在
        if (!$ip_value) {
            #设置key自增
            $redis->incr($visitor_ip); //将key中存储的数字值增1
            #设置过期时间为60秒
            $redis->expire($visitor_ip, self::$limit_time); //给key值设置生存时间
        } 
        //存在
        if($ip_value){
            // 当前访问次数小于时间内最大访问次数
            if($ip_value< self::$max_frequency) {
                $redis->incr($visitor_ip);
            }
        }
        
        #集合里边的元素不会重复 字符串
        #把ip当做key 存入redis 请求次数用户黑名单锁定时间 目前设置3分钟（180）
        if ($ip_value >= self::$max_frequency) {
            #使用有序集合
            $redis->zAdd('request_number_user_black_list',$this->now_time, $visitor_ip); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
            // return 2; //调用接口频繁
            sendErrorMSG(403,'调用接口频繁');
        }
    }
        return $next($request);
    }
}
