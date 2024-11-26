<?php

namespace App\Http\Controllers\Api\V1\Login;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
// 验证提交参数
use  App\Http\Requests\Login\GoLoginRequest;
use  App\Http\Requests\Login\GetVerificationCodeRequest;


use App\Services\UserService;
use App\Services\VerificationCodeService;
use App\Services\JsonWebTokenService;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Class LoginController  博客后台登录和退出   保证用户信息安全用psot请求
 * @package App\Http\Controllers\Api\V1\Login\LoginController 
 */

class LoginController extends Controller
{

    /**
     *获取验证码
     * redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 每24小时只能获取21次（保证只有一条关于验证码的记录）
     * 检查redis 是否有验证码；有：删除 重建；否：添加
     * 请求验证码次数IP黑名单  锁定时间
     *  request_validate_code_number_ip_black_list 记录  time  IP
     */
    public function getVerificationCode()
    {

        // $verification_code_service_data= [ 
        //     ["validate_code_path"]=> "data:image/png;base64,iVBORw0KGgoAAAA...",
        //     ["validate_code"]=>  "zKBgMj"
        // ]

        // 检查redis 是否有验证码；有：删除 重建；否：添加  （保证只有一条关于验证码的记录） 

        // 生成验证码和验证图片
        $verification_code_service_data = VerificationCodeService::index();
        // echo '验证码：';
        // var_dump($verification_code_service_data);
        // echo '<img src="'.$verification_code_service_data["validate_code_path"].'" alt="Image" />';
        sendMSG('200', ['validate_code_path' => $verification_code_service_data["validate_code_path"], 'ccc' => '中国'], '成功');
    }


    //获取页面配置（如页面标题、页面关键词、页面描述、网站log、登录验证码）
    // redis 存储 validate_code_IP:{'validate_code':validate_code}  计数 每24小时只能获取21次（保证只有一条关于验证码的记录）
    // 检查redis 是否有验证码；有：删除 重建；否：添加
    //  请求验证码次数IP黑名单  锁定时间
    //    request_validate_code_number_ip_black_list 记录  time  IP
    public function getLoginPageData()
    {
        header('HTTP/1.0 9999 Unauthorized');
        // return response('Unauthenticated.', 401);
        sendErrorMSG(403, '令牌失效');
    }

    //去验证登录账号 redis 存储 email_validate_code_nick_name:{'temporary_token':temporary_token},临时令牌 temporary_token（含有用户信息）为值
    //   返回临时令牌 temporary_token 和发送邮箱验证码  设置有效期5分钟
    // 每24小时内仅可获取3次邮件验证码   request_email_validate_code_number_list 记录  time  nick_name
    // 检查redis 是否有验证码；有：删除 重建；否：添加 （保证只有一条关于验证码的记录） 
    //  请求邮箱验证码次数昵称黑名单  锁定时间
    //    request_email_validate_code_number_nick_name_black_list 记录  time  nick_name
    //  post  email 用户邮箱  password 用户密码  validate_code 登录验证码 动态生成
    public function goVerifyLoginAccount(GetVerificationCodeRequest $request)
    {

        // 1.验证用户信息;邮箱是否存在,密码是否正确,验证码是否存在
        //    组装temporary_token_payload
        $temporary_token_payload = [
            'iat' => time(), // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => 'nick_name', // 接收者
            'sub' => 'nick_name', // 用户标识
            'role' => 'user', // 用户角色
            'jti' => 'temporary_token'.bin2hex(random_bytes(10)) // 唯一令牌标识
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
        // 获取请求参数的签名
        $request_params_email_verification_code = $request->input('email_verification_code');
        if (empty($request_params_email_verification_code)) {
            sendErrorMSG(403, '空邮箱验证码');
        }

        // 获取请求参数的临时令牌 
        $request_temporary_token= $request->input('temporary_token'); 

        // 校验临时令牌  有效期5分钟。如果验证成功返回payload，否则返回false
        $temporary_token_payload=JsonWebTokenService::verifyJWT($request_temporary_token);

        if(empty($temporary_token_payload)){
            sendErrorMSG(403, '令牌失效');
        }

        $nick_name=$temporary_token_payload['aud'];

        // 根据昵称在数据库查找用户 

        $is_nick_name_user_exist_result=UserService::isNickNameUserExist(['nick_name'=> $nick_name]);
        
        if(empty($is_nick_name_user_exist_result)){
            sendErrorMSG(403, '用户数据错误！');
        }

        //    组装access_token_payload
        $access_token_payload = [
            'iat' => time(), // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => $nick_name, // 接收者
            'sub' => 'nick_name', // 用户标识
            'role' => 'user', // 用户角色
            'jti' => 'access_token'.bin2hex(random_bytes(10)) // 唯一令牌标识
        ];

        //    组装refresh_token_payload
        $refresh_token_payload = [
            'iat' => time(), // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => $nick_name, // 接收者
            'sub' => 'nick_name', // 用户标识
            'role' => 'user', // 用户角色
            'jti' => 'refresh_token'.bin2hex(random_bytes(10)) // 唯一令牌标识

        ];
        // 生成JwtAccessToken和JwtRefreshToken 访问和刷新令牌 ，返回数组。
        $token_array= JsonWebTokenService::generateJwtAccessTokenOrJwtRefreshToken($access_token_payload,$refresh_token_payload);

        $token_array['nick_name']=$nick_name;
    
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
