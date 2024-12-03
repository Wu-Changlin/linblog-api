<?php

namespace App\Http\Controllers\Api\V1\Login;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
// 验证提交参数
use  App\Http\Requests\Login\GoLoginRequest;
use  App\Http\Requests\Login\GetVerificationCodeRequest;


use App\Services\Backend\UserService;
use App\Services\VerificationCodeService;
use App\Services\JsonWebTokenService;
use App\Services\ResetPasswordEmailAndVerificationCodeForRedisService;


// use App\Redis\RedisBase;


/**
 * Class LoginController  博客后台登录和退出   保证用户信息安全用psot请求
 * @package App\Http\Controllers\Api\V1\Login\LoginController 
 */

class LoginController extends Controller
{

    /**
     *获取验证码
     * redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 
     * 每24小时只能获取21次  request_validate_code_number_list 记录  number  ip
     * 检查redis 是否有验证码；有：删除 重建；否：添加（保证只有一条关于验证码的记录）
     *  请求验证码次数ip黑名单  锁定时间
     *    request_validate_code_number_ip_black_list 记录  time  ip
     * request_validate_code_number_list
     */
    public function getVerificationCode()
    {
        // 生成类型
        $type_name = 'validate_code';
        //  访客ip
        $visitor_ip = getVisitorIP();
        // 1.检查redis该ip是否存在黑名单中

        //  结果返回 1：空记录，true：是，false：否 
        $is_ip_or_nick_name_in_black_list_exist_result = ResetPasswordEmailAndVerificationCodeForRedisService::isIpOrNickNameInBlackListExist($type_name, $visitor_ip);

        // 在黑名单中情景
        if ($is_ip_or_nick_name_in_black_list_exist_result === true) {
            // 黑名单锁定分钟
            $validate_code_limit_period = env('VALIDATE_CODE_LIMIT_PERIOD');
            $black_list_lock_minute = $validate_code_limit_period / 60;

            $error_msg = '因为调用生成信息接口频繁，所以封禁' . $black_list_lock_minute . '分钟。';
            sendErrorMSG(403, $error_msg);
        }

        // 2.检查redis该ip是否达到限制次数每24小时只能获取21次
        // IP或nick_name是否达到限制次数 返回 true：是，false：否
        $is_ip_or_nick_name_in_limit_number_achieve_result = ResetPasswordEmailAndVerificationCodeForRedisService::isIpOrNickNameInLimitNumberAchieve($type_name, $visitor_ip);

        // 达到限制次数写入黑名单
        if ($is_ip_or_nick_name_in_limit_number_achieve_result) {
            sendErrorMSG(403, '获取验证码已达到限制次数！');
        }


        // 3.生成验证码和验证图片
        $verification_code_data = VerificationCodeService::generateVerificationCode();

        // 如果没有生成验证码，那么直接返回false
        if (empty($verification_code_data)) {
            sendErrorMSG(403, '生成验证码失败！');
        }

        // 组装redis存储数据
        $verification_code_save_data = [
            "validate_code" =>  $verification_code_data['validate_code']
        ];

        // echo '验证码：';
        // var_dump($verification_code_save_data);
        // echo '<img src="'.$verification_code_save_data["validate_code_path"].'" alt="Image" />';
        // true：成功保存，false：空数据， string：错误消息。检查redis 是否有验证码；有：覆盖；否：添加  （保证只有一条关于验证码的记录）
        $save_generate_info_result = ResetPasswordEmailAndVerificationCodeForRedisService::saveGenerateInfo($type_name, '', $verification_code_save_data);

        if (empty($save_generate_info_result)) {
            sendErrorMSG(403, '获取验证码失败！');
        }

        sendMSG('200', ['validate_code_path' => $verification_code_data["validate_code_path"], 'ccc' => '中国'], '成功');
    }


    //获取页面配置（如页面标题、页面关键词、页面描述、网站log、登录验证码）
    // redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 
    // 每24小时只能获取21次  request_validate_code_number_list 记录  number  ip
    // 检查redis 是否有验证码；有：删除 重建；否：添加（保证只有一条关于验证码的记录）
    //  请求验证码次数ip黑名单  锁定时间
    //    request_validate_code_number_ip_black_list 记录  time  ip
    public function getLoginPageData()
    {
        header('HTTP/1.0 9999 Unauthorized');
        // return response('Unauthenticated.', 401);
        sendErrorMSG(403, '令牌失效');
    }

    //去验证登录账号 redis 存储 email_validate_code_nick_name:{'temporary_token':temporary_token},临时令牌 temporary_token（含有用户信息）为值
    //   返回临时令牌 temporary_token 和发送邮箱验证码  设置有效期5分钟
    // 每24小时内仅可获取3次邮件验证码   request_email_validate_code_number_list 记录  number  nick_name
    // 检查redis 是否有验证码；有：删除 重建；否：添加 （保证只有一条关于验证码的记录） 
    //  请求邮箱验证码次数昵称黑名单  锁定时间
    //    request_email_validate_code_number_nick_name_black_list 记录  time  nick_name
    //  post  email 用户邮箱  password 用户密码  validate_code 登录验证码 动态生成
    public function goVerifyLoginAccount(GetVerificationCodeRequest $request)
    {

        // $temporary_token_payload = [
        //     'iat' => time(), // 签发时间
        //     'iss' => 'linBlog',  // 签发者
        //     'aud' => 'nick_name', // 接收者
        //     'sub' => 'nick_name', // 用户标识
        //     'role' => 'user', // 用户角色
        //     'jti' => 'temporary_token' . bin2hex(random_bytes(10)) // 唯一令牌标识
        // ];


        // $temporary_token =  JsonWebTokenService::generateTemporaryToken($temporary_token_payload);

        // sendMSG('200', ['temporary_token' => $temporary_token], '成功');

        $request_params_all_data = $request->all();
        // 1.验证用户账号状态、是否启用;以邮箱和密码为查询条件
        $verify_account_data = [
            'email' => $request_params_all_data['email'],
            'password' => $request_params_all_data['password']
        ];

      //验证账号状态： 通过 返回true，没有通过返回错误消息或false失败
   
        $verify_account_result=UserService::verifyAccount($verify_account_data);

         // 失败情景
         if($verify_account_result===false){
            sendMSG(200, [], '验证账号失败！');
        }

        // 空数据情景
        if ($verify_account_result===0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($verify_account_result) && $verify_account_result) {
            sendErrorMSG(403, $verify_account_result);
        }


        // 2.验证用户是否登录
        $is_logged_in_data = ['email' => $request_params_all_data['email']];
        // is_logged_in  true 是， false 否
        $is_logged_in_result = UserService::isLogin($is_logged_in_data);
        if ($is_logged_in_result) {
            sendErrorMSG(403, '请勿重复登录！');
        }

        // 获取用户信息

        $get_current_user_info_result = UserService::getCurrentUserInfo(['email' => $request_params_all_data['email']]);
        if (empty($get_current_user_info_result)) {
            sendErrorMSG(403, '用户信息错误！');
        }


        // 获取用户昵称
        $nick_name = $get_current_user_info_result['nick_name'];

        //  访客ip
        $visitor_ip = getVisitorIP();

        // 生成类型
        $type_name = 'email_validate_code';
        $validate_code_type_name = 'validate_code';
        // 3.检查redis该ip或昵称是否存在黑名单中 request_validate_code_number_ip_black_list

        //  结果返回 1：空记录，true：是，false：否 
        $is_ip_or_nick_name_in_black_list_exist_result = ResetPasswordEmailAndVerificationCodeForRedisService::isIpOrNickNameInBlackListExist($validate_code_type_name, $visitor_ip);

        // 在黑名单中情景
        if ($is_ip_or_nick_name_in_black_list_exist_result === true) {
            // 黑名单锁定分钟
            $email_validate_code_limit_period = env('EMAIL_VALIDATE_CODE_LIMIT_PERIOD');
            $black_list_lock_minute = $email_validate_code_limit_period / 60;

            $error_msg = '因为调用生成信息接口频繁，所以封禁' . $black_list_lock_minute . '分钟。';
            sendErrorMSG(403, $error_msg);
        }

        // 4.检查redis该ip或昵称是否达到限制次数每24小时只能获取21次  request_validate_code_number_list_127.0.0.1
        // IP或nick_name是否达到限制次数 返回 true：是，false：否

        $is_ip_or_nick_name_in_limit_number_achieve_result = ResetPasswordEmailAndVerificationCodeForRedisService::isIpOrNickNameInLimitNumberAchieve($validate_code_type_name, $visitor_ip);

        // 达到限制次数写入黑名单
        if ($is_ip_or_nick_name_in_limit_number_achieve_result) {
            sendErrorMSG(403, '获取邮箱验证码已达到限制次数！');
        }

        // 5.检查提交参数的验证码是否存在redis中   validate_code_127.0.0.1

        $get_validate_code_or_email_validate_code_or_reset_password_email_result = ResetPasswordEmailAndVerificationCodeForRedisService::getValidateCodeOrEmailValidateCodeOrResetPasswordEmail($validate_code_type_name, $visitor_ip);

        // 空值
        if (empty($get_validate_code_or_email_validate_code_or_reset_password_email_result)) {
            sendErrorMSG(403, '非法验证码！');
        }



        //    json_decode会返回对象，要返回关联数组，需要提供第二个参数true 将JSON字符串转换为数组 $array = json_decode($jsonString, true);

        $get_validate_code_or_email_validate_code_or_reset_password_email_result_to_array = json_decode($get_validate_code_or_email_validate_code_or_reset_password_email_result, true);

        // 不等于
        if ($request_params_all_data['validate_code'] != $get_validate_code_or_email_validate_code_or_reset_password_email_result_to_array['validate_code']) {
            sendErrorMSG(403, '校验验证码失败！');
        }

        // 6.检查redis 是否有邮箱验证码；有：覆盖；否：添加  （保证只有一条关于验证码的记录）
        // 生成一个包含大小写字母和数字的任意位随机数，默认6位
        $email_validate_code_data = generateRandomNumber(6);

        // 组装redis存储数据
        $email_validate_code_save_data = [
            "email_validate_code" =>  $email_validate_code_data
        ];

        $save_generate_info_result = ResetPasswordEmailAndVerificationCodeForRedisService::saveGenerateInfo($type_name, $nick_name, $email_validate_code_save_data);

        if (empty($save_generate_info_result)) {
            sendErrorMSG(403, '获取邮箱验证码失败！');
        }



        // 7.生成并输出temporary_token

        //    组装temporary_token_payload
        $temporary_token_payload = [
            'iat' => time(), // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' =>  $nick_name, // 接收者
            'sub' =>  $nick_name, // 用户标识
            'role' => 'user', // 用户角色
            'jti' => 'temporary_token' . bin2hex(random_bytes(10)) // 唯一令牌标识
        ];


        $temporary_token =  JsonWebTokenService::generateTemporaryToken($temporary_token_payload);

        sendMSG('200', ['temporary_token' => $temporary_token], '成功');
    }


    /**
     *博客后台登录操作   (用户或管理员登录)
     * email_verification_code
     * post    email_verification_code 邮箱验证码 
     *  redis 存储 email_validate_code_nick_name:email_verification_code,
     */
    public function goLogin(GoLoginRequest $request)
    {

        $request_params_all_data = $request->all();
        // 获取请求参数的签名
        $request_params_email_verification_code = $request_params_all_data['email_verification_code'];
        if (empty($request_params_email_verification_code)) {
            sendErrorMSG(403, '空邮箱验证码');
        }

        
        //   1.检查临时令牌

        // 获取请求参数的临时令牌 
        $request_temporary_token = $request_params_all_data['temporary_token'];

        // 校验临时令牌  有效期5分钟。如果验证成功返回payload，否则返回false
        $temporary_token_payload = JsonWebTokenService::verifyJWT($request_temporary_token);

        // if (empty($temporary_token_payload)) {
        //     sendErrorMSG(403, '令牌失效');
        // }

        $nick_name = $temporary_token_payload['aud'];

        // 2.检查提交参数的邮箱验证码是否存在redis中
        $type_name = 'email_validate_code';

        $get_validate_code_or_email_validate_code_or_reset_password_email_result = ResetPasswordEmailAndVerificationCodeForRedisService::getValidateCodeOrEmailValidateCodeOrResetPasswordEmail($type_name, $nick_name);

        // 空值
        if (empty($get_validate_code_or_email_validate_code_or_reset_password_email_result)) {
            sendErrorMSG(403, '非法邮箱验证码！');
        }

        //    json_decode会返回对象，要返回关联数组，需要提供第二个参数true 将JSON字符串转换为数组 $array = json_decode($jsonString, true);
        $get_validate_code_or_email_validate_code_or_reset_password_email_result_to_array = json_decode($get_validate_code_or_email_validate_code_or_reset_password_email_result, true);
        
        // 不等于
        if ($request_params_email_verification_code != $get_validate_code_or_email_validate_code_or_reset_password_email_result_to_array['email_validate_code']) {
            sendErrorMSG(403, '校验邮箱验证码失败！');
        }

        // 3.验证用户账号状态、是否启用;以用户昵称为查询条件
        $verify_account_data = [
            'nick_name' => $nick_name,
        ];
        //验证账号状态： 通过 返回true，没有通过返回错误消息或false失败
        // $verify_account_result=UserService::verifyAccount($verify_account_data);

        // if ($verify_account_result!= true) {
        //     sendErrorMSG(403, '验证账号失败！');
        // }


        // 4.验证用户是否已登录
        $is_logged_in_data = ['nick_name' => $nick_name];
        // is_logged_in  true 是， false 否
        $is_logged_in_result = UserService::isLogin($is_logged_in_data);
        if ($is_logged_in_result) {
            sendErrorMSG(403, '请勿重复登录！');
        }


        //当前时间
        $now_time = time();

        // 5.执行登录
        // 拼接登录数据
        //  访客ip
        $login_ip = getVisitorIP();

        $login_data = [
            'nick_name' => $nick_name,
            'last_login_time' => $now_time,
            'login_ip' => $login_ip,
            'is_logged_in' => 1
        ];

        $user_login_result = UserService::userLogin($login_data);

        if (empty($user_login_result)) {
            sendErrorMSG(403, '登录失败！');
        }

        // login_ip
        // is_logged_in 1： 是，2：否',

        // 6.生成并返回token
        //    组装access_token_payload
        $access_token_payload = [
            'iat' =>  $now_time, // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => $nick_name, // 接收者
            'sub' =>  $nick_name, // 用户标识
            'role' => 2, // 用户角色
            'jti' => 'access_token' . bin2hex(random_bytes(10)) // 唯一令牌标识
        ];

        // `role` '角色；0：默认，1：普通用户，2：管理员',


        //    组装refresh_token_payload
        $refresh_token_payload = [
            'iat' =>  $now_time, // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => $nick_name, // 接收者
            'sub' =>  $nick_name, // 用户标识
            'role' => 2, // 用户角色
            'jti' => 'refresh_token' . bin2hex(random_bytes(10)) // 唯一令牌标识

        ];
        // 生成JwtAccessToken和JwtRefreshToken 访问和刷新令牌 ，返回数组。
        $token_array = JsonWebTokenService::generateJwtAccessTokenOrJwtRefreshToken($access_token_payload, $refresh_token_payload);

        $token_array['nick_name'] = $nick_name;



        sendMSG('200', $token_array, '成功');
    }




    // 创建允许的数据
    //  $allow_data = $this->create($data);

    //  // 常规验证
    //  $this->check($allow_data);

    //  // ---其它验证--------------
    //  // -Email重复性验证
    //  $email = $allow_data['email'];
    //  $where['email'] = ['eq', $email];
    //  if(!empty($data['id'])){
    //      $where['id'] = ['neq', $data['id']];
    //  }

    //  //优化mysql查询,如果只是判断数据是否存在,用getField查询并只返回id是最快的
    //  $administrator_Email = M('administrator')->where($where)->getField('id');

    //  if(!empty($administrator_Email)){
    //      JsonReturn::error('-4017');
    //  }

    //  $allow_data['status'] = (int)$allow_data['status'];

    //  //统一密码到Password表
    //  if(empty(PasswordService::getPasswordByEmail($email))){
    //      // 当还没有这个Email的统一密码时，创建一个
    //      $passwordService = new PasswordService();
    //      $generateService = new GenerateService();


}
