<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\LoginController;


// use App\Http\Controllers\Api\v1\backend\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// 动态加载中间件组、中间件
$middleware_name = [];
/* 获取请求对象的url的prefix：前缀名动态使用中间件组 开始*/
$prefix_data_str = 'frontend,backend,login,resetPassword,token';

// 获取当前请求对象
$request = request();

// 获取当前请求对象的url非域名部分
//原: http://localhost:9090/api/frontend/frontend/getCurrentActivePageData
//截取后: api/frontend/frontend/getCurrentActivePageData
//api//{prefix}/{controller}/{action} prefix：前缀（命名空间） controller：控制名前缀 action 控制器方法
$no_domain_name_str = $request->path();

//  字符串以斜杠分割为数组 array:4 [0 => "api",1 => "frontend",2 => "frontend",3 => "getCurrentActivePageData"]
$parts_array = explode('/', $no_domain_name_str);


// 数组长度小于2
if (count($parts_array) < 2) {
    echo 111;
    abort(404); //人为触发 404 错误
}


// 过滤数组返回空值元素
$filtered_array_return_empty_elements = array_filter($parts_array, function ($value) {
    return empty($value);
});
// 实例 访问 http://localhost:9090/  打印var_dump($parts_array); 输出 array(2) { [0]=> string(0) "" [1]=> string(0) "" }
// 数组存在空值元素 
if ($filtered_array_return_empty_elements) {
    echo 222;
    abort(404); //人为触发 404 错误
}

// 获取prefix：前缀名（命名空间）
$current_prefix_name = $parts_array[1];

//匹配失败 匹配字符串以大小写字母开头，由大小写字母，数字和下划线组成。
if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $current_prefix_name)) {
    echo 333;
    abort(404); //人为触发 404 错误
}

// str_contains — 确定字符串是否包含指定子串,区分大小写;
$contains_results = str_contains($prefix_data_str, $current_prefix_name);

if (!$contains_results) { //如果没有包含,那么人为触发 404 错误
    echo 444;
    abort(404); //人为触发 404 错误
}
// 添加中间件组到middleware_name
// $middleware_name[]=$current_prefix_name;
/* 获取请求对象的url的prefix：前缀名动态使用中间件 结束*/


/* 获取请求对象的url的action：action名动态使用中间件 开始*/
// http://localhost:9090/api/frontend/frontend/getSearchKeywordMatchData
// 防止重复提交操作名称字符串
// $prevent_duplicate_submission_action_name_str='getSearchKeywordMatchData,';

// // 获取action_name：操作名称（方法名）
// $current_action_name=$parts_array[3];

// // str_contains — 确定字符串是否包含指定子串,区分大小写;
// $action_name_contains_results=str_contains($prevent_duplicate_submission_action_name_str, $current_action_name);
// if($action_name_contains_results){//如果有包含,那么人为触发 404 错误
//     $action_name_middleware='preventDuplicateSubmission';
//     // 添加中间件到middleware_name
//     $middleware_name[]=$action_name_middleware;
// }



/* 获取请求对象的url的action：action名动态使用中间件组 结束*/

// http://localhost:9090/api/backend/user/index
// http://localhost:9090/api/frontend/frontend/getCurrentActivePageData
// http://localhost:9090/api/login/login/getVerificationCode
// http://localhost:9090/api/token/token/getRefreshAccessToken
// http://localhost:9090/api/resetPassword/resetPassword/getSendResetPasswordEmailPageData

Route::middleware($current_prefix_name)->group(function () {
    Route::match(['GET', 'POST'], '/{prefix}/{controller}/{action}', function ($prefix, $controller, $action) {
        // $prefix：前缀（命名空间） $controller：控制名前缀 $action 控制器方法
        /* 
        生成一个request对象。在 Laravel 框架中，需要通过 Request 对象来获取用户请求信息，
        该对象引用的完整类名是 Illuminate\Http\Request，
        而该请求类又继承自 Symfony 的 Symfony\Component\HttpFoundation\Request
        */

        $request = app('request');
        // 拼接控制器路径
        $controller = '\App\Http\Controllers\Api\V1\\' . ucfirst($prefix) . '\\' . ucfirst($controller) . 'Controller';

        //  实例化控制器对象和请求参数
        $con     = new $controller($request);

        //使用preg_match函数来对数据进行验证，修饰符/is用于进行不区分大小写的匹配，并且让"."元字符能够匹配换行符。
        // 字字符集合中使用如下的表示方式:[a-z],[A-Z],[0-9]，分别表示小写字母，大写字母，数字。
        // 正则的意思是开头必须是英文字母，后面可以是英文字母或者数字以及下划线。
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $action)) {
            // 检查类的方法是否存在于指定的 object_or_class 中。
            // method_exists(object|string $object_or_class, string $method): bool
            // 如果 method 所指的方法在 object_or_class 所指的对象类中已定义，则返回 true，否则返回 false。
            if (method_exists($con, $action)) {
                // 调用方法
                return $con->{$action}($request);
                // return \App::call([$con, $action]);
            }
        }
        die('No route found , Please check url parameters');
    });
});




// // $_mp='api';

// Route::group(['prefix' => $_mp], function () use ($router) {
// echo 111;

//     Route::match(['GET', 'POST'], '/', function ($controller, $action) use (Router $router) {
//         echo 222;
//         $request = app('request');
//         $controller = 'App\Http\Controllers\Api\V1\\'.ucfirst($controller).'Controller';
//         $con     = new $controller($request);
//         if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $action)) {
//             if (method_exists($con, $action)) {
//                 return $con->{$action}($request);
// //                return \App::call([$con, $action]);
//             }
//         }
//         die('No route found , Please check url parameters');
//     });
// });

// ->middleware('admin.login','checkrbac')


// 前端接口
// Route::prefix('frontend')->middleware('checkIp')->group(function () {
//     Route::match(['GET', 'POST'], '/{controller}/{action}', function($controller, $action) {
//         // http://localhost:9090/api/frontend/frontend/getCurrentActivePageData
//     /* 
//     生成一个request对象。在 Laravel 框架中，需要通过 Request 对象来获取用户请求信息，
//         该对象引用的完整类名是 Illuminate\Http\Request，
//         而该请求类又继承自 Symfony 的 Symfony\Component\HttpFoundation\Request
//         */
//         $request = app('request');
//         // 拼接控制器路径
//         $controller = '\App\Http\Controllers\Api\V1\frontend\\'.ucfirst($controller).'Controller';
//     //  实例化控制器对象和请求参数
//                 $con     = new $controller($request);
//                 //使用preg_match函数来对数据进行验证，修饰符/is用于进行不区分大小写的匹配，并且让"."元字符能够匹配换行符。
//                 // 字字符集合中使用如下的表示方式:[a-z],[A-Z],[0-9]，分别表示小写字母，大写字母，数字。
//                 // 正则的意思是开头必须是英文字母，后面可以是英文字母或者数字以及下划线。
//                 if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $action)) {
//                     // 检查类的方法是否存在于指定的 object_or_class 中。
//                     // method_exists(object|string $object_or_class, string $method): bool
//                     // 如果 method 所指的方法在 object_or_class 所指的对象类中已定义，则返回 true，否则返回 false。
//                     if (method_exists($con, $action)) {
//                          // 调用方法
//                         return $con->{$action}($request);
//                     // return \App::call([$con, $action]);
//                     }
//                 }
//                 die('No route found , Please check url parameters');
        
//         });
        



// });

// 后台接口
// Route::prefix('backend')->middleware('checkIp')->group(function () {
//     Route::match(['GET', 'POST'], '/{prefix}/{controller}/{action}', function($prefix,$controller, $action) {
//         // http://localhost:9090/api/backend/user/index
//     /* 
//     生成一个request对象。在 Laravel 框架中，需要通过 Request 对象来获取用户请求信息，
//         该对象引用的完整类名是 Illuminate\Http\Request，
//         而该请求类又继承自 Symfony 的 Symfony\Component\HttpFoundation\Request
//         */
//         $request = app('request');
//         // 拼接控制器路径
//                 $controller = '\App\Http\Controllers\Api\V1\backend\\'.ucfirst($controller).'Controller';
//     //  实例化控制器对象和请求参数
//                 $con     = new $controller($request);
//                 //使用preg_match函数来对数据进行验证，修饰符/is用于进行不区分大小写的匹配，并且让"."元字符能够匹配换行符。
//                 // 字字符集合中使用如下的表示方式:[a-z],[A-Z],[0-9]，分别表示小写字母，大写字母，数字。
//                 // 正则的意思是开头必须是英文字母，后面可以是英文字母或者数字以及下划线。
//                 if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $action)) {
//                     // 检查类的方法是否存在于指定的 object_or_class 中。
//                     // method_exists(object|string $object_or_class, string $method): bool
//                     // 如果 method 所指的方法在 object_or_class 所指的对象类中已定义，则返回 true，否则返回 false。
//                     if (method_exists($con, $action)) {
//                          // 调用方法
//                         return $con->{$action}($request);
//                     // return \App::call([$con, $action]);
//                     }
//                 }
//                 die('No route found , Please check url parameters');
        
//         });
        



// });

// Route::match('POST',  function() {Route::middleware('checkToken');}





// http://localhost:9090/api/user/index

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
