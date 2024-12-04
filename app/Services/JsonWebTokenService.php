<?php



namespace App\Services;

use App\Services\Backend\UserService;


// 在框架启动的时候读取.env文件中的KEY值，并将其赋给一个常量，然后在类中使用这个常量来初始化你的私有静态属性。

define('JWT_SECRET', env('JWT_SECRET'));
define('ACCESS_TOKEN_TIME_TO_LIVE', env('ACCESS_TOKEN_TIME_TO_LIVE'));
define('REFRESH_TOKEN_TIME_TO_LIVE', env('REFRESH_TOKEN_TIME_TO_LIVE'));
define('TEMPORARY_TOKEN_TIME_TO_LIVE', env('TEMPORARY_TOKEN_TIME_TO_LIVE'));
define('RESET_PASSWORD_TOKEN_TIME_TO_LIVE', env('RESET_PASSWORD_TOKEN_TIME_TO_LIVE'));


class JsonWebTokenService
{
    
    private static $jwt_secret = JWT_SECRET; // 加密使用的秘钥
    private static $algo = 'HS256';  // 使用的算法

    private static $access_token_time_to_live=ACCESS_TOKEN_TIME_TO_LIVE;//有效期20分钟

    private static $refresh_token_time_to_live=REFRESH_TOKEN_TIME_TO_LIVE;//有效期7天

    private static $temporary_token_time_to_live=TEMPORARY_TOKEN_TIME_TO_LIVE;//有效期5分钟

    private static $reset_password_token_time_to_live=RESET_PASSWORD_TOKEN_TIME_TO_LIVE;//有效期3小时



    

    /**
     * 生成JwtAccessToken 访问令牌
     * 
     * @param array $payload 数据负载
     * @return string
     */
    public static function generateAccessToken($payload)
    {
        // 1. 生成header
        $header = json_encode(['alg' => self::$algo, 'typ' => 'JWT']);
        $base64Header = self::base64UrlEncode($header);

        //添加访问令牌过期时间
        if(empty($payload['exp'])){
            $payload['exp']= $payload['iat']+self::$access_token_time_to_live;
        }
 
        // 2. 生成payload
        $base64Payload = self::base64UrlEncode(json_encode($payload));
 
        // 3. 生成signature
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$jwt_secret, true);
        $base64Signature = self::base64UrlEncode($signature);
 
        // 4. 组合JWT
        $jwt_access_token = "$base64Header.$base64Payload.$base64Signature";
 
        return $jwt_access_token;
    }


//     // 配置
// $jwt_secret = "YOUR_SECRET_KEY"; // 保持密钥安全
// $issuer = "YOUR_ISSUER"; // 令牌的签发者
// $audience = "YOUR_AUDIENCE"; // 令牌的接收方
// $accessTokenTTL = 3600; // 访问令牌的有效时间（秒）
// $refreshTokenTTL = 7200; // 刷新令牌的有效时间（秒）

// // 生成JWT访问令牌 
// $accessTokenPayload = [
//     'iat' => time(), // 签发时间
//     'exp' => time() + $accessTokenTTL, // 过期时间
//     'iss' => $issuer,   // 签发者
//     'aud' => $audience, // 接收者
//     'sub' => 'user_id_here', // 用户标识
//     'role' => 'user' // 用户角色
// ];
// $jwtAccessToken = JWT::encode($accessTokenPayload, $jwt_secret, 'HS256');
 
// // 生成JWT刷新令牌
// $refreshTokenPayload = [
//     'iat' => time(),// 签发时间
//     'exp' => time() + $refreshTokenTTL, // 过期时间
//     'iss' => $issuer,  // 签发者
//     'aud' => $audience, // 接收者
//     'jti' => bin2hex(random_bytes(10)), // 唯一令牌标识
//     'sub' => 'user_id_here', // 用户标识
//     'role' => 'user' // 用户角色
// ];

     /**
     * 生成JwtAccessToken和JwtRefreshToken 访问和刷新令牌
     * 
     * @param array $payload 数据负载
     * @return string
     */
    public static function generateJwtAccessTokenOrJwtRefreshToken($access_token_payload,$refresh_token_payload){
// 1. 生成header
$header = json_encode(['alg' => self::$algo, 'typ' => 'JWT']);
$base64Header = self::base64UrlEncode($header);

//添加访问令牌过期时间
if(empty($access_token_payload['exp'])){
    $access_token_payload['exp']= $access_token_payload['iat']+self::$access_token_time_to_live;
}

//添加刷新令牌过期时间
if(empty($refresh_token_payload['exp'])){
    $refresh_token_payload['exp']= $refresh_token_payload['iat']+self::$refresh_token_time_to_live;
}

// 2. 生成payload
$base64_access_token_payload = self::base64UrlEncode(json_encode($access_token_payload));

$base64_refresh_token_payload = self::base64UrlEncode(json_encode($refresh_token_payload));

// 3. 生成signature
$signature_access_token = hash_hmac('sha256', "$base64Header.$base64_access_token_payload", self::$jwt_secret, true);
$base64_signature_access_token = self::base64UrlEncode($signature_access_token);

$signature_refresh_token = hash_hmac('sha256', "$base64Header.$base64_refresh_token_payload", self::$jwt_secret, true);
$base64_signature_refresh_token = self::base64UrlEncode($signature_refresh_token);

// 4. 组合JWT
$jwt_access_token = "$base64Header.$base64_access_token_payload.$base64_signature_access_token";

$jwt_refresh_token = "$base64Header.$base64_refresh_token_payload.$base64_signature_refresh_token";


// 返回JwtAccessToken和JwtRefreshToken 访问和刷新令牌
return ['jwt_access_token'=>$jwt_access_token,'jwt_refresh_token'=>$jwt_refresh_token];

}



   /**
     * 生成TemporaryToken 临时访问令牌
     * 
     * @param array $payload 数据负载
     * @return string
     */
    public static function generateTemporaryToken($payload){

                // 1. 生成header
                $header = json_encode(['alg' => self::$algo, 'typ' => 'JWT']);
                $base64Header = self::base64UrlEncode($header);
         
                //添加临时令牌过期时间
    if(empty($payload['exp'])){
        $payload['exp']= $payload['iat']+self::$temporary_token_time_to_live;
    }


                // 2. 生成payload
                $base64Payload = self::base64UrlEncode(json_encode($payload));
         
                // 3. 生成signature
                $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$jwt_secret, true);
                $base64Signature = self::base64UrlEncode($signature);
         
                // 4. 组合JWT
                $temporary_token = "$base64Header.$base64Payload.$base64Signature";
         
                return $temporary_token;
    }


     /**
     * 生成ResetPasswordToken 重置密码令牌
     * 
     * @param array $payload 数据负载
     * @return string
     */
    public static function generateResetPasswordToken($payload){

        // 1. 生成header
        $header = json_encode(['alg' => self::$algo, 'typ' => 'JWT']);
        $base64Header = self::base64UrlEncode($header);
 
        //添加临时令牌过期时间
if(empty($payload['exp'])){
$payload['exp']= $payload['iat']+self::$reset_password_token_time_to_live;
}


        // 2. 生成payload
        $base64Payload = self::base64UrlEncode(json_encode($payload));
 
        // 3. 生成signature
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$jwt_secret, true);
        $base64Signature = self::base64UrlEncode($signature);
 
        // 4. 组合JWT
        $temporary_token = "$base64Header.$base64Payload.$base64Signature";
 
        return $temporary_token;
}


    /**
     * 验证JWT
     * 
     * @param string $token JWT令牌
     * @return array|bool 如果验证成功返回payload，否则返回false
     */
    public static function verifyJWT($token)
    {
        // 1. 拆分JWT
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
 
        list($base64Header, $base64Payload, $base64Signature) = $parts;
 
        // 2. 解码Header和Payload
        $header = json_decode(self::base64UrlDecode($base64Header), true);
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);
        $signature = self::base64UrlDecode($base64Signature);
 
        // 3. 验证签名
        $expectedSignature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$jwt_secret, true);
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
 
        // 4. 验证过期时间
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
  
  
        // 5.验证昵称没有定义或没有值
        if (!isset($payload['aud']) || empty($payload['aud'])) {
            return false;
        }

              // 6.验证用户账号状态、是否启用;以用户昵称为查询条件
              $verify_account_data = [
                'nick_name' => $payload['aud'],
            ];
         //   //验证账号状态： 通过 返回true，没有通过返回错误消息或false失败
            // $verify_account_result=UserService::verifyAccount($verify_account_data);
    // //验证失败
               // if ($verify_account_result!= true) {
        //    return false;
        // }
       
        // 7.验证用户是否已登录
        $is_logged_in_data = ['nick_name' => $payload['aud']];
        // is_logged_in  true 是，返回错误消息或false 失败
        $is_logged_in_result = UserService::isLogin($is_logged_in_data);
        
        // 没有登录
        if ($is_logged_in_result!=true) {
            return false;
        }
 
    //    dd($payload);
 
        return $payload;
    }
    

    /**
     * 验证JWT 令牌名称的有效期
     * 
     * @param string $token JWT令牌
     * @return array|bool 如果验证成功返回true，否则返回false
     */
    public static function verifyTokenNameTimeToLive($current_token_name,$current_time_to_live)
    {

        $token_time_to_live=[
            'access_token'=>self::$access_token_time_to_live,
            'refresh_token'=>self::$refresh_token_time_to_live,
            'temporary_token'=>self::$temporary_token_time_to_live,
            'reset_password_token'=>self::$reset_password_token_time_to_live,  
        ];
        $time_to_live=$token_time_to_live[$current_token_name];
        //如果设置令牌有效期等于地址携带令牌有效期，那么返回true
        if($time_to_live===$current_time_to_live){
            return true;
         }
        //  默认返回false
        return false;
    }


     /**
     * Base64URL编码
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
 
    /**
     * Base64URL解码
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // 其他用户相关的服务方法
}
