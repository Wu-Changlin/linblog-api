<?php
namespace App\Http\Controllers\Api\V1\ResetPassword;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;

use App\Services\JsonWebTokenService;


use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
/**
 * Class ResetPasswordController 重置密码相关 
 * @package App\Http\Controllers\Api\V1\ResetPassword\ResetPasswordController 
 */

class ResetPasswordController extends Controller
{


    //获取发送重置密码邮件页面配置（如页面标题、页面关键词、页面描述、、网站log）

    public function getSendResetPasswordEmailPageData(){
        echo 111;
    }
    
    //获取重置密码页面配置（如页面标题、页面关键词、页面描述、、网站log）

    public function getResetPasswordPageData(){

    }

    //发送重置密码链接邮件   返回send_reset_password_email_result  成功true 失败 false
    //redis 存储  reset_password_email_nick_name:{'temporary_token':temporary_token} 生成的临时令牌 temporary_token  设置有效期3小时
    // 每24小时内仅可发送3次重置密码邮件 request_reset_password_email_number_list 记录  number  nick_name
    // 检查redis 是否有重置密码链接；有：删除 重建；否：添加 （保证只有一条关于重置密码链接的记录） 
     //  请求重置密码链接次数昵称黑名单  锁定时间
    //     request_reset_password_email_number_nick_name_black_list 记录  time  nick_name
    public function sendResetPasswordEmail(Request $request){

          //    组装temporary_token_payload
          $reset_password_token_payload = [
            'iat' => time(), // 签发时间
            'iss' => 'linBlog',  // 签发者
            'aud' => 'nick_name', // 接收者
            'sub' => 'nick_name', // 用户标识
            'role' => 'user', // 用户角色
            'jti' => 'reset_password_token'.bin2hex(random_bytes(10)) // 唯一令牌标识
];


        $temporary_token= JsonWebTokenService::generateResetPasswordToken($reset_password_token_payload);

        // 拼接重置密码链接
        $reset_password_url="http".$temporary_token;
        // 把重置密码链接添加到邮件正文

        // 发送邮件
        // sendEmail()
        // 判断邮件是否发送成功返回响应数据
        sendMSG('200',['send_reset_password_email'=>true],'成功');

    }

     //去重置密码  校验临时令牌 temporary_token 有效期3小时
    public function goResetPassword(Request $request){
        $data = $request->all();

        if(empty($data)){
            sendErrorMSG(403,'空提交');
        }

        if(empty($data)){
            sendErrorMSG(403,'空提交');
        }

        // temporary_token: '',
        // password: '',
        // confirm_password:'',

        $temporary_token= JsonWebTokenService::verifyJWT();
        sendMSG(200,[],'成功重置密码！');

    }





}
