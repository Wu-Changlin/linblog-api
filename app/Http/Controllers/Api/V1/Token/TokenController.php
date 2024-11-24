<?php
namespace App\Http\Controllers\Api\V1\Token;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
/**
 * Class TokenController   token相关
 * @package App\Http\Controllers\Api\V1\Token\TokenController 
 */

class TokenController extends Controller
{


    //使用刷新令牌获取新访问令牌
    public function getRefreshAccessToken(Request $request){

        // 获取请求参数的jwt_refresh_token
        $request_params_jwt_refresh_token = $request->input('jwt_refresh_token');
        if(empty($request_params_jwt_refresh_token)){
            sendErrorMSG(403,'空令牌');
        }
    }






}
