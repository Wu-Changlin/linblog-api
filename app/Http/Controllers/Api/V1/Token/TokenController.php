<?php
namespace App\Http\Controllers\Api\V1\Token;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Services\Backend\UserService;
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




        // 校验刷新令牌是存在黑名单中

        $verify_access_token_or_refresh_token_is_on_black_list_result=JsonWebTokenService::verifyAccessTokenOrRefreshTokenIsOnBlackList($request_params_jwt_refresh_token);

        if($verify_access_token_or_refresh_token_is_on_black_list_result===true){
            sendErrorMSG(403,'刷新令牌已禁止！');
        }

        /* 
            校验用户是否已登录，
            虽然没有登录，但是刷新令牌在有效期
            （如果邮箱、昵称、密码、确认密码中其一，那么退出登录、访问令牌和刷新令牌加入黑名单）;
            添加到黑名单。
        */

         // 判断用户已登录  is_logged_in  true 是， false 否
         // 2.验证用户是否登录

         //   解码JWT
        $payload=JsonWebTokenService::decodeJWT($request_params_jwt_refresh_token);
        
        if(empty($payload)){
            sendErrorMSG(403, '解码JWT错误！');
        }

         $is_logged_in_data_where = ['email' => $payload['aud']];
         // is_logged_in  true 是， false 否
         $is_logged_in_result = UserService::isLogin($is_logged_in_data_where);
        //  如果没有登录，那么添加刷新令牌到黑名单
         if (empty($is_logged_in_result)) {
            $add_access_token_or_refresh_token_is_on_black_list_result=JsonWebTokenService::addAccessTokenOrRefreshTokenIsOnBlackList($request_params_jwt_refresh_token);

            if(empty($add_access_token_or_refresh_token_is_on_black_list_result)){
                sendErrorMSG(403,'禁止刷新令牌失败！');
            }
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
