<?php

// app/Services/UserService.php

namespace App\Services;

use App\Models\IpBlackList as IpBlackListModel;
use Redis; //加载Redis扩展类

// 在框架启动的时候读取.env文件中的KEY值，并将其赋给一个常量，然后在类中使用这个常量来初始化你的私有静态属性。

define('VALIDATE_CODE_TIME_TO_LIVE', env('VALIDATE_CODE_TIME_TO_LIVE'));
define('TEMPORARY_TOKEN_TIME_TO_LIVE', env('TEMPORARY_TOKEN_TIME_TO_LIVE'));
define('RESET_PASSWORD_TOKEN_TIME_TO_LIVE', env('RESET_PASSWORD_TOKEN_TIME_TO_LIVE'));


// 重置密码电子邮件和验证码的Redis服务
class ResetPasswordEmailAndVerificationCodeForRedisService
{

  /* 有效期 开始*/

    //因为系统设置validate_code有效期5分钟 ，所以redis设置过期时间应保持一致
    private static $validate_code_time_to_live=VALIDATE_CODE_TIME_TO_LIVE;//有效期7天

    //因为登录使用temporary_token有效期5分钟 ，所以redis设置过期时间应保持一致
    private static $temporary_token_time_to_live=TEMPORARY_TOKEN_TIME_TO_LIVE;//有效期5分钟

    //因为重置密码使用reset_password_token 有效期3小时 ，所以redis设置过期时间应保持一致
    private static $reset_password_token_time_to_live=RESET_PASSWORD_TOKEN_TIME_TO_LIVE;//有效期3小时

 /* 有效期 结束*/

    /* 验证码（登录页）、 邮箱验证码、 重置密码链接邮件*/
    // 存生成信息的key_name
    private static $save_data_key_name = [
        'validate_code' => 'validate_code_',
        'email_validate_code' => 'email_validate_code_',
        'reset_password_email' => 'reset_password_email_'
    ];
    // 存生成信息的请求次数表
    private static $request_generate_info_number_list_name = [
        'validate_code' => 'request_validate_code_number_list',
        'email_validate_code' => 'request_email_validate_code_number_list',
        'reset_password_email' => 'request_reset_password_email_number_list'
    ];
    // 存生成信息的请求次数黑名单表
    private static $request_generate_info_number_black_list = [
        'validate_code' => 'request_validate_code_number_ip_black_list',
        'email_validate_code' => 'request_email_validate_code_number_nick_name_black_list',
        'reset_password_email' => 'request_reset_password_email_number_nick_name_black_list'
    ];


    

    //获取页面配置（如页面标题、页面关键词、页面描述、网站log、登录验证码）
    // redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 
    // 每24小时只能获取21次  request_validate_code_number_list 记录  name  ip
    // 检查redis 是否有验证码；有：删除 重建；否：添加（保证只有一条关于验证码的记录）
    //  请求验证码次数ip黑名单  锁定时间
    //    request_validate_code_number_ip_black_list 记录  time  ip


    //去验证登录账号 redis 存储 email_validate_code_nick_name:{'temporary_token':temporary_token},临时令牌 temporary_token（含有用户信息）为值
    //   返回临时令牌 temporary_token 和发送邮箱验证码  设置有效期5分钟
    // 每24小时内仅可获取3次邮件验证码   request_email_validate_code_number_list 记录  name  nick_name
    // 检查redis 是否有验证码；有：删除 重建；否：添加 （保证只有一条关于验证码的记录） 
    //  请求邮箱验证码次数昵称黑名单  锁定时间
    //    request_email_validate_code_number_nick_name_black_list 记录  time  nick_name
    //  post  email 用户邮箱  password 用户密码  validate_code 登录验证码 动态生成




    //发送重置密码链接邮件   返回send_reset_password_email_result  成功true 失败 false
    //redis 存储  reset_password_email_nick_name:{'temporary_token':temporary_token} 生成的临时令牌 temporary_token  设置有效期3小时
    // 每24小时内仅可发送3次重置密码邮件 request_reset_password_email_number_list 记录  number  nick_name
    // 检查redis 是否有重置密码链接；有：删除 重建；否：添加 （保证只有一条关于重置密码链接的记录） 
    //  请求重置密码链接次数昵称黑名单  锁定时间
    //     request_reset_password_email_number_nick_name_black_list 记录  time  nick_name


    /**
     * 检查保存生成信息
     * @description: 
     * @param {*} $type_name  生成信息类型名
     * @param {*} $data       生成信息
     * @return {*}
     */
    public static function saveGenerateInfo($type_name, $data)
    {
        if (empty($data)) {
            return false;
        }
        // 要存储的数据
        $redis_save_data = $data;

        // 将数组转换为JSON字符串
        $json_string = json_encode($redis_save_data);

        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $redis_host_value = env('REDIS_HOST');
        $redis_password_value = env('REDIS_PASSWORD');
        $redis_port_value = env('REDIS_PORT');
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        // 连接redis
        $redis = new Redis();
        $redis->open($redis_host_value, $redis_port_value); //服务器连接的Ip与端口号
        $redis->auth($redis_password_value); //redis服务的密码
        $redis->select($repository_serial_number_value); //选择连接的redis，默认redis的库有16个

        // 存储JSON字符串到Redis，key为生成信息的key_name
        $redis->set(self::$save_data_key_name[$type_name], $json_string);

        //  访客ip
        $visitor_ip = getVisitorIP();
        
        //        $redis->flushAll();exit;//清空redis的所有库
        $lock_time = $redis->zScore('request_number_user_black_list', $visitor_ip); //返回有序集中key中成员member的score
        // 黑名单锁定时间 目前设置3分钟
        if ($this->now_time - $lock_time < $this->black_list_lock_time) {
            // return 1; //在黑名单中
            // 黑名单锁定分钟
            $black_list_lock_minute = ($this->black_list_lock_time) / 60;
            sendMSG(403, [], '因为调用接口频繁，所以封禁' . $black_list_lock_minute . '分钟。');
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
            $redis->expire($visitor_ip, $this->limit_time); //给key值设置生存时间
        }
        //存在
        if ($ip_value) {
            // 当前访问次数小于时间内最大访问次数
            if ($ip_value < $this->max_frequency) {
                $redis->incr($visitor_ip);
            }
        }

        #集合里边的元素不会重复 字符串
        #把ip当做key 存入redis 请求次数用户黑名单锁定时间 目前设置3分钟（180）
        if ($ip_value >= $this->max_frequency) {
            #使用有序集合
            $redis->zAdd('request_number_user_black_list', $this->now_time, $visitor_ip); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
            // return 2; //调用接口频繁
            sendMSG(403, [], '调用接口频繁');
        }
    }



    //请求生成信息次数名单
    public static function requestGenerateInfoNumberList($data)
    {

        //  访客ip
        $visitor_ip = getVisitorIP();
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $redis_host_value = env('REDIS_HOST');
        $redis_password_value = env('REDIS_PASSWORD');
        $redis_port_value = env('REDIS_PORT');
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');

        #一分钟接口调用只能10次
        $redis = new Redis();
        $redis->open($redis_host_value, $redis_port_value); //服务器连接的Ip与端口号
        $redis->auth($redis_password_value); //redis服务的密码
        $redis->select($repository_serial_number_value); //选择连接的redis，默认redis的库有16个


        //        $redis->flushAll();exit;//清空redis的所有库
        $lock_time = $redis->zScore('request_number_user_black_list', $visitor_ip); //返回有序集中key中成员member的score
        // 黑名单锁定时间 目前设置3分钟
        if ($this->now_time - $lock_time < $this->black_list_lock_time) {
            // return 1; //在黑名单中
            // 黑名单锁定分钟
            $black_list_lock_minute = ($this->black_list_lock_time) / 60;
            sendMSG(403, [], '因为调用接口频繁，所以封禁' . $black_list_lock_minute . '分钟。');
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
            $redis->expire($visitor_ip, $this->limit_time); //给key值设置生存时间
        }
        //存在
        if ($ip_value) {
            // 当前访问次数小于时间内最大访问次数
            if ($ip_value < $this->max_frequency) {
                $redis->incr($visitor_ip);
            }
        }

        #集合里边的元素不会重复 字符串
        #把ip当做key 存入redis 请求次数用户黑名单锁定时间 目前设置3分钟（180）
        if ($ip_value >= $this->max_frequency) {
            #使用有序集合
            $redis->zAdd('request_number_user_black_list', $this->now_time, $visitor_ip); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
            // return 2; //调用接口频繁
            sendMSG(403, [], '调用接口频繁');
        }
    }

    //请求生成信息次数黑名单
    public static function requestGenerateInfoNumberBlackList($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $add_black_ip_res = IpBlackListModel::addIpBlackList($data); //执行新增

        switch ($add_black_ip_res) { //判断新增返回值
            case 0:
                return  '数据为空';
                break;
            case 1:
                return  '邮箱已注册';
                break;
            case 2:
                return  "新增管理员成功";
                break;
            default:
                return  '数据写入失败,新增管理员失败';
        }
    }



    // 其他用户相关的服务方法
}
