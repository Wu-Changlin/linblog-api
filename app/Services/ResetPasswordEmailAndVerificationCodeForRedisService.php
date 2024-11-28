<?php

// app/Services/UserService.php

namespace App\Services;
use App\Redis\RedisBase;

// 重置密码电子邮件和验证码的Redis服务
class ResetPasswordEmailAndVerificationCodeForRedisService
{


    /* 验证码（登录页）、 邮箱验证码、 重置密码链接邮件*/



    // 存生成信息的key_name
    private static $save_data_key_name = [
        'validate_code' => 'validate_code_',
        'email_validate_code' => 'email_validate_code_',
        'reset_password_email' => 'reset_password_email_'
    ];
    // 存生成信息的请求次数表
    private static $request_generate_info_number_list_name = [
        'validate_code' => 'request_validate_code_number_list_',
        'email_validate_code' => 'request_email_validate_code_number_list_',
        'reset_password_email' => 'request_reset_password_email_number_list_'
    ];
    // 存生成信息的请求次数黑名单表
    private static $request_generate_info_number_black_list = [
        'validate_code' => 'request_validate_code_number_ip_black_list',
        'email_validate_code' => 'request_email_validate_code_number_nick_name_black_list',
        'reset_password_email' => 'request_reset_password_email_number_nick_name_black_list'
    ];

    // 生成信息的限制次数
    private static $request_generate_info_limit_number = [
        'validate_code' => 21,
        'email_validate_code' => 3,
        'reset_password_email' => 3
    ];


     // 获取生成信息的存活时间
    public static function getTimeToLive($type_name){
        $time_to_live = [
                'validate_code' => env('VALIDATE_CODE_TIME_TO_LIVE'),
                'email_validate_code' => env('TEMPORARY_TOKEN_TIME_TO_LIVE'),
                'reset_password_email' => env('RESET_PASSWORD_TOKEN_TIME_TO_LIVE')
            ];

            return   $time_to_live[$type_name];
    }


     // 获取生成信息的限制时期  （单位：秒）24小时= 86400秒
    public static function getRequestGenerateInfoLimitPeriod($type_name){
        $request_generate_info_limit_period = [
                'validate_code' => env('VALIDATE_CODE_LIMIT_PERIOD'),
                'email_validate_code' => env('EMAIL_VALIDATE_CODE_LIMIT_PERIOD'),
                'reset_password_email' => env('RESET_PASSWORD_EMAIL_LIMIT_PERIOD')
            ];

            return   $request_generate_info_limit_period[$type_name];
    }

    /**
     * 检查保存生成信息
     * @description: 
     * @param {*} $type_name  生成信息类型名
     * @param {*} $data       生成信息
     * @return {*} true：成功保存，false：空数据， string：错误消息，
     */
    public static function saveGenerateInfo($type_name, $nick_name, $data)
    {

      
        //如果是空数据，那么直接返回
        if (empty($data)) {
            return false;
        }
        // 要存储的数据
        $redis_save_data = $data;

        // 将数组转换为JSON字符串
        $json_string = json_encode($redis_save_data);

        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
       
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        

        // 连接redis
        RedisBase::_initialize(['db'=>$repository_serial_number_value]);

        /* 添加生成信息记录  开始*/

         // 如果nick_name存在使用nick_name，返回值是IP
        $query_key = $nick_name?$nick_name:getVisitorIP();

        $add_generate_info_result=self::addGenerateInfo($type_name,$query_key,$json_string);
        if(empty($add_generate_info_result)){
            $error_msg = '写入生成信息数据错误！';
            return $error_msg;
        }

        /* 添加生成信息记录 结束*/

        //当前时间
        $now_time = time();
        

        /* 检查是否存在黑名单 开始*/
       

        //  结果返回 1：空记录，true：是，false：否 
        $is_ip_or_nick_name_in_black_list_exist_result = self::isIpOrNickNameInBlackListExist($type_name,$query_key);
        //黑名单名称
        $black_list_name = self::$request_generate_info_number_black_list[$type_name];

        // 在黑名单中情景
        if ($is_ip_or_nick_name_in_black_list_exist_result === true) {
            // 黑名单锁定分钟
            $black_list_lock_minute = (self::getRequestGenerateInfoLimitPeriod($type_name)) / 60;
            $error_msg = '因为调用生成信息接口频繁，所以封禁' . $black_list_lock_minute . '分钟。';
            return $error_msg;
        }

        // 不在黑名单中情景
        if ($is_ip_or_nick_name_in_black_list_exist_result === false) {
            /*
            Redis的ZREM命令用于删除有序集合中的一个或多个成员，当删除最后一个元素时，
            有序集合仍然存在，除非该有序集合中没有其他元素，此时整个有序集合会被删除‌。
            */
             RedisBase::zRem($black_list_name,  $query_key); //redis中zRem命令用于移除有序集合中的一个或者是多个成员，不存在的成员将被忽略，当key存在但是不是有序集合类型是，返回一个错误

        }

        /* 检查是否存在黑名单 结束*/

        /* 操作生成信息的请求次数 开始*/
        // 记录生成次数，key name为相应生成信息的类型对应字符串+$query_key（ip或nick_name）
        $add_or_edit_generate_info_number_in_number_list_result=self::addOrEditGenerateInfoNumberInNumberList($type_name,$query_key);
        
        if(empty($add_or_edit_generate_info_number_in_number_list_result)){
            $error_msg = '写入次数数据错误！';
            return $error_msg;
        }
        /* 操作生成信息的请求次数 结束*/

        /* 操作黑名单 开始*/

        #集合里边的元素不会重复 字符串
        #把ip当做key 存入redis 请求次数用户黑名单锁定时间 目前设置相应生成信息的类型限制时期
        // IP或nick_name是否达到限制次数 返回 true：是，false：否
        $is_ip_or_nick_name_in_limit_number_achieve_result=self::isIpOrNickNameInLimitNumberAchieve($type_name,$query_key);
        // 达到限制次数写入黑名单 
        if ($is_ip_or_nick_name_in_limit_number_achieve_result) {
            #使用有序集合  $query_key（ip或nick_name）
             RedisBase::zAdd($black_list_name, $now_time,  $query_key); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
            // return 2; //调用接口频繁
            $error_msg = '调用生成信息接口频繁！';
            return $error_msg;
        }
        /* 操作黑名单 结束*/
        // 手动关闭Redis连接
         // 关闭连接
        RedisBase::close();

        return true;
    }


    //获取验证码（登录页）、 邮箱验证码、 重置密码链接邮件其一  返回 成功：查询值  失败 ：false
    public static function getValidateCodeOrEmailValidateCodeOrResetPasswordEmail($type_name,$query_key){
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        
        // 连接redis
        RedisBase::_initialize(['db' => $repository_serial_number_value]);

        /* 查询生成信息记录  开始*/
        $redis_query_key_name = self::$save_data_key_name[$type_name] . $query_key;

        // 获取查询值
        $query_key_name_result= RedisBase::get($redis_query_key_name); //给key值设置生存时间

        // 手动关闭Redis连接
        // 关闭连接
        RedisBase::close();
        // 如果查询值存在，那么返回查询值
        if ($query_key_name_result) {
            return $query_key_name_result;
        }
        return false;

    }


    // 添加生成信息  query_key：用户昵称或IP
    public static function addGenerateInfo($type_name,$query_key,$json_string) {
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');


        // 连接redis
        RedisBase::_initialize(['db' => $repository_serial_number_value]);

        /* 添加生成信息记录  开始*/
        $redis_save_data_key_name = self::$save_data_key_name[$type_name] . $query_key;

        // 获取相应生成信息的类型生存时间
        $generate_info_time_to_live = self::getTimeToLive($type_name);
        #设置过期时间为相应生成信息的类型生存时间
        //  RedisBase::setex($key_name, $seconds, $value); $seconds=单位：秒
        //  setex 是『SET if Not eXists』(如果不存在，则 SET)的简写
        // 如果 key 已经存在， SETEX 命令将覆写旧值。
        // 返回值 设置成功时返回 OK ；当 seconds 参数不合法时，返回一个错误。 
        RedisBase::setex($redis_save_data_key_name, $generate_info_time_to_live, $json_string); //给key值设置生存时间

        //  查询添加结果
        $add_generate_info_result =  RedisBase::get($redis_save_data_key_name);

        // 手动关闭Redis连接
        // 关闭连接
        RedisBase::close();
        // 如果添加成功，那么返回true
        if ($add_generate_info_result) {
            return true;
        }
        return false;

    }


    // IP或nick_name是否存在黑名单中  返回 1：空记录，true：是，false：否 
    public static function isIpOrNickNameInBlackListExist($type_name,$query_key)
    {
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
       
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        // 连接redis
       
        RedisBase::_initialize(['db'=>$repository_serial_number_value]);
        

        //当前时间
        $now_time = time();
        //黑名单名称
        $black_list_name = self::$request_generate_info_number_black_list[$type_name];

        //         RedisBase::flushAll();exit;//清空redis的所有库
        $lock_time =  RedisBase::zScore($black_list_name, $query_key); //返回有序集中key中成员member的score

        // 手动关闭Redis连接
         // 关闭连接
        RedisBase::close();

        // 空记录 返回1
        // if (empty($lock_time)) {
        //     return 1;
        // }

        // 黑名单锁定时间 根据生成信息类型设置
        if ($now_time - $lock_time < self::getRequestGenerateInfoLimitPeriod($type_name)) {
            // return 1; //在黑名单中
            // 黑名单锁定分钟
            return true;
        }

        return false;
    }

    // IP或nick_name是否达到限制次数 返回 true：是，false：否
    public static function isIpOrNickNameInLimitNumberAchieve($type_name,$query_key)
    {
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        // 连接redis
        RedisBase::_initialize(['db'=>$repository_serial_number_value]);
        

        // 拼接key_name
        $generate_info_number_key_name = self::$request_generate_info_number_list_name[$type_name].$query_key;
        // 获取生成信息次数  使用GET命令获取的值总是作为字符串返回
        $total_generate_info_number_string_value =  RedisBase::get($generate_info_number_key_name);
        // 字符串转换为整数
        $total_generate_info_number_value=intval($total_generate_info_number_string_value);
        // 最大请求生成信息次数
        $max_request_generate_info_number = self::$request_generate_info_limit_number[$type_name];
    
        // 手动关闭Redis连接
         // 关闭连接
        RedisBase::close();
        // 如果存在记录且生成信息次数=最大请求生成信息次数，那么返回true
        if ($total_generate_info_number_value && $total_generate_info_number_value === $max_request_generate_info_number) {
       
            // 达到限制次数写入黑名单  #使用有序集合  $query_key（ip或nick_name）
            #使用有序集合  $query_key（ip或nick_name）
             self::addIpOrNickNameBlackList($type_name,$query_key); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
            
            return true;
        }
        return false;
    }


    // 在请求生成信息次数名单添加或修改生成信息次数 返回  true：操作成功，false：操作失败
    public static function addOrEditGenerateInfoNumberInNumberList($type_name,$query_key)
    {
        // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        // 连接redis
        RedisBase::_initialize(['db'=>$repository_serial_number_value]);
    
        // 拼接key_name
        $generate_info_number_key_name = self::$request_generate_info_number_list_name[$type_name].$query_key;

        // 获取生成信息次数  使用GET命令获取的值总是作为字符串返回
        $total_generate_info_number_string_value =  RedisBase::get($generate_info_number_key_name); //get命令用于获取指定的keyz值，如果key值不存在返回null
        // 字符串转换为整数
        $total_generate_info_number_value=intval($total_generate_info_number_string_value);

        // 不存在  添加记录
        if (!$total_generate_info_number_value) {
            // 获取相应生成信息的类型限制时期
            $generate_info_limit_period = self::getRequestGenerateInfoLimitPeriod($type_name);
            #设置过期时间为相应生成信息的类型限制时期  
            // 验证码（登录页）、 邮箱验证码、 重置密码链接邮件都是24小时= 86400秒
             RedisBase::setex($generate_info_number_key_name, $generate_info_limit_period, 1); //给key值设置生存时间

            return true;
        }

        // 最大请求生成信息次数
        $max_request_generate_info_number = self::$request_generate_info_limit_number[$type_name];
        // 存在  修改生成信息次数
        if ($total_generate_info_number_value) {
            // 当前生成信息次数小于时间内最大生成信息次数
            if ($total_generate_info_number_value < $max_request_generate_info_number) {
                #设置key自增
                 RedisBase::incr($generate_info_number_key_name);
                return true;
            }
        }

        return false;
    }


    // 添加黑名单
    public static function addIpOrNickNameBlackList($type_name,$query_key) {
        $is_ip_or_nick_name_in_black_list_exist_result = ResetPasswordEmailAndVerificationCodeForRedisService::isIpOrNickNameInBlackListExist($type_name,$query_key);
        

        // 在黑名单中情景
        if ($is_ip_or_nick_name_in_black_list_exist_result === false) {
            // 使用 Laravel 提供的 env() 函数来获取.env文件环境变量
        
        $repository_serial_number_value = env('RESET_PASSWORD_EMAIL_AND_VERIFICATION_CODE_FOR_REDIS_SERVICE_SERIAL_NUMBER');
        // 连接redis
        RedisBase::_initialize(['db'=>$repository_serial_number_value]);
            //黑名单名称
            $black_list_name = self::$request_generate_info_number_black_list[$type_name];
            $now_time=time();
        
            RedisBase::zAdd($black_list_name, $now_time, $query_key); //命令用于将一个或者是多个于是怒以及分数值加入到有序集合中
         
         // 手动关闭Redis连接
         // 关闭连接
         RedisBase::close();
        }


    }


    //请求生成信息次数名单
    public static function requestGenerateInfoNumberList($data) {}

    //请求生成信息次数黑名单
    public static function requestGenerateInfoNumberBlackList($data) {}


    // 其他用户相关的服务方法
}


    //获取页面配置（如页面标题、页面关键词、页面描述、网站log、登录验证码）
    // redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 
    // 每24小时只能获取21次  request_validate_code_number_list + ip记录  number  ip
    // 检查redis 是否有验证码；有：删除 重建；否：添加（保证只有一条关于验证码的记录）
    //  请求验证码次数ip黑名单  锁定时间
    //    request_validate_code_number_ip_black_list 记录  time  ip


    //去验证登录账号 redis 存储 email_validate_code_nick_name:{'temporary_token':temporary_token},临时令牌 temporary_token（含有用户信息）为值
    //   返回临时令牌 temporary_token 和发送邮箱验证码  设置有效期5分钟
    // 每24小时内仅可获取3次邮件验证码   request_email_validate_code_number_list +nick_name  记录  number  nick_name
    // 检查redis 是否有验证码；有：删除 重建；否：添加 （保证只有一条关于验证码的记录） 
    //  请求邮箱验证码次数昵称黑名单  锁定时间
    //    request_email_validate_code_number_nick_name_black_list 记录  time  nick_name
    //  post  email 用户邮箱  password 用户密码  validate_code 登录验证码 动态生成




    //发送重置密码链接邮件   返回send_reset_password_email_result  成功true 失败 false
    //redis 存储  reset_password_email_nick_name:{'temporary_token':temporary_token} 生成的临时令牌 temporary_token  设置有效期3小时
    // 每24小时内仅可发送3次重置密码邮件 request_reset_password_email_number_list + nick_name 记录  number  nick_name
    // 检查redis 是否有重置密码链接；有：删除 重建；否：添加 （保证只有一条关于重置密码链接的记录） 
    //  请求重置密码链接次数昵称黑名单  锁定时间
    //     request_reset_password_email_number_nick_name_black_list 记录  time  nick_name