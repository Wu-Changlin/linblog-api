<?php
namespace App\Http\Controllers\Api\V1\ResetPassword;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;

use App\Services\UserService;
use App\Services\VerificationCodeService;


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

    //发送重置密码邮件   返回send_reset_password_email_result  成功true 失败 false
    //redis 存储  生成的临时令牌 temporary_token  设置有效期3小时
    public function sendResetPasswordEmail(){

    }

     //去重置密码  校验临时令牌 temporary_token 有效期3小时
    public function goResetPassword(Request $request){
        $data = $request->all();

        if(empty($data)){
            sendErrorMSG(403,'空提交');
        }
        sendMSG(200,[],'成功重置密码！');

    }





}
