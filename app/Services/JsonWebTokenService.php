<?php

// app/Services/UserService.php

namespace App\Services;
use Firebase\JWT\JWT;//加载jwt插件库

class JsonWebTokenService
{



    private static $secretKey = 'your_secret_key'; // 加密使用的秘钥
    private static $algo = 'HS256';  // 使用的算法

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
 
        // 2. 生成payload
        $base64Payload = self::base64UrlEncode(json_encode($payload));
 
        // 3. 生成signature
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$secretKey, true);
        $base64Signature = self::base64UrlEncode($signature);
 
        // 4. 组合JWT
        $jwt_access_token = "$base64Header.$base64Payload.$base64Signature";
 
        return $jwt_access_token;
    }


//     // 配置
// $secretKey = "YOUR_SECRET_KEY"; // 保持密钥安全
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
// $jwtAccessToken = JWT::encode($accessTokenPayload, $secretKey, 'HS256');
 
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

// 2. 生成payload
$base64_access_token_payload = self::base64UrlEncode(json_encode($access_token_payload));

$base64_refresh_token_payload = self::base64UrlEncode(json_encode($refresh_token_payload));

// 3. 生成signature
$signature_access_token = hash_hmac('sha256', "$base64Header.$base64_access_token_payload", self::$secretKey, true);
$base64_signature_access_token = self::base64UrlEncode($signature_access_token);

$signature_refresh_token = hash_hmac('sha256', "$base64Header.$base64_refresh_token_payload", self::$secretKey, true);
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
         
                // 2. 生成payload
                $base64Payload = self::base64UrlEncode(json_encode($payload));
         
                // 3. 生成signature
                $signature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$secretKey, true);
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
        $expectedSignature = hash_hmac('sha256', "$base64Header.$base64Payload", self::$secretKey, true);
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
 
        // 4. 验证过期时间
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
 
        return $payload;
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
