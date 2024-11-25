<?php
namespace App\Http\Controllers\Api\V1\Token;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\JsonWebTokenService;
/**
 * Class TokenController   token相关
 * @package App\Http\Controllers\Api\V1\Token\TokenController 
 */

class TokenController extends Controller
{


    //使用刷新令牌获取新访问令牌 有效期20分钟
    public function getRefreshAccessToken(Request $request){

        // 获取请求参数的jwt_refresh_token
        $request_params_jwt_refresh_token = $request->input('jwt_refresh_token');
        if(empty($request_params_jwt_refresh_token)){
            sendErrorMSG(403,'空令牌');
        }

        
        $refresh_access_token_payload = [
            'iat' => time(),// 签发时间
                'iss' => 'linBlog',  // 签发者
                'aud' => 'nick_name', // 接收者
                'sub' => 'nick_name', // 用户标识
                'role' => 'user' // 用户角色
];



        $refresh_access_token=  JsonWebTokenService::generateTemporaryToken($refresh_access_token_payload);

        sendMSG('200',['jwt_access_token'=>$refresh_access_token],'成功');
    }






}
