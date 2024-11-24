<?php
// 3个颜色数字，范围： 0-255
function rgbRandomNumbers()
{
    // 生成10个随机数的数组
    $randomNumbers = [];
    for ($i = 0; $i < 3; $i++) {
        // 使用 mt_rand() 生成一个更好的随机数
        $randomNumbers[] = mt_rand(0, 255);
    }

    // 输出结果
    return $randomNumbers;
}


// 使用explode函数来根据'/'字符分割字符串，并获取相应的部分。
// 以下是一个简单的函数，它接受一个字符串和一个位置参数，然后输出指定位置后的内容。
// 这个函数会根据提供的位置参数，返回相应的字符串部分。如果位置参数不存在，则返回null。
// 示例： 
// $str = 'api/frontend/frontend/getCurrentActivePageData';
// $position = 1; // 输出第1个'/'之后的内容，即'frontend'
// echo outputByPosition($str, $position); // 输出: frontend
function outputByPosition($str, $position) {
    $parts = explode('/', $str);
    if (isset($parts[$position])) {
        return $parts[$position];
    }
    return null;
}


function getVisitorIP()
{
    // $headers = array(
    //     'X-Forwarded-For',
    //     'X-Real-IP',
    //     'REMOTE_ADDR'
    // );

    // foreach ($headers as $header) {
    //     if (isset($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
    //         return $_SERVER[$header];
    //     }
    // }

    // return 'UNKNOWN';



    $real_Ip = '';

    if (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $real_Ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // 检查X-Forwarded-For头部可能包含的多个IP（用逗号分隔）
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $real_Ip = trim($ips[0]);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $real_Ip = $_SERVER['REMOTE_ADDR'];
    }

    // 输出真实IP地址
    return $real_Ip;
}

      // 将字符串转换为UTF-8编码
function strToUtf8Bytes($str) {
    // 将字符串转换为UTF-8编码
    $utf8Str = mb_convert_encoding($str, 'UTF-8', 'auto');
    // 初始化字节数组
    $bytes = [];
 
    // 遍历UTF-8编码的字符串，将每个字符转换为其对应的字节值
    for ($i = 0; $i < mb_strlen($utf8Str, 'UTF-8'); $i++) {
        $bytes[] = ord(mb_substr($utf8Str, $i, 1, 'UTF-8'));
    }
 
    return $bytes;
}

/**
 * 成功返回
 * 接口返回数据
 *
 * @param int $code    状态码
 * @param array $data  响应数据
 *  @param $msg        自定义提示信息
 */
function sendMSG($code = 0, $data = [],$msg)
{
    header('content-type:application/json;charset=utf8');
    // 解决Ajax跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    $arr = array(
        'msg' => $msg,
        'code' => $code,
        'data' => empty($data) ? [] : $data
        // 'data' => empty($data) ? "" : $data

        // 'data' => empty($data) ? "" : enGzip($data)
    );
    // 用 PHP 的 json_encode 来处理中文的时候，中文都会被编码，
    // 变成不可读的，类似”\u***” 的格式，如果想汉字不进行转码，可用如下方法：
// 在json_encode第二个参数添加JSON_UNESCAPED_UNICODE。
die(json_encode($arr, JSON_UNESCAPED_UNICODE));
}



// /**
//  * 成功返回不die掉
//  */
//  function nodie_success($data)
// {
//     header('content-type:application/json;charset=utf8');
//     // 解决Ajax跨域
//     header('Access-Control-Allow-Origin: *');
//     header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
//     echo json_encode([
//         'status' => true,
//         'code' => '200',
//         'data' => (object)$data,
//         'message' => '',
//     ]);

// }

/**
 * 失败返回
 */
function sendErrorMSG($code,$message = '')
{
    // 出现404错误时,把相关信息记录到日志中,以方便发现错误
    // if($code == 404){
    //     $deaslog = new BaseSeaslog();
    //     $path = $GLOBALS['OPTIONS']['seaslog']."404/";
    //     $deaslog->writeData("404: {$message}",$path,'ERROR');
    // }
    
    header('content-type:application/json;charset=utf8');
    // 解决Ajax跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    

    // 如果没有自定义message，则使用code查询语言包中对应的错误提示
    if (empty($message)) {
        $code_str='cn.'.$code;
        
         //trans( $code_str) 载入语言包
        $message =  trans( $code_str)?? $code;

        $message==$code_str?$message='未知错误':'';
    }

    // 使用preg_replace进行替换，非数字替换为空。
    $code = preg_replace('/[^\d]/', '', $code); // 输出: 403

    header('HTTP/1.0 '. $code);
        // return response('Unauthenticated.', 401);

    // 使用intval把字符串值转数字
    $code=intval($code);

    $arr = array(
        'status' => false,
        'code' => $code,
        'data' => [],
        'message' => $message,
    );
    // 用 PHP 的 json_encode 来处理中文的时候，中文都会被编码，
    // 变成不可读的，类似”\u***” 的格式，如果想汉字不进行转码，可用如下方法：
// 在json_encode第二个参数添加JSON_UNESCAPED_UNICODE。
    die(json_encode($arr, JSON_UNESCAPED_UNICODE));

}
