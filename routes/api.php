<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use \App\Http\Controllers\Api\UserController;

use \App\Http\Controllers\Api\V1\Backend\UserController;
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

// Route::get('/users/index', [UserController::class, 'index']);
// Route::get('/login/index', [LoginController::class, 'index']);
Route::get('/login/getVerificationCode', [LoginController::class, 'getVerificationCode']);
Route::get('/login/logIn', [LoginController::class, 'logIn']);


// http://localhost:9090/api/users

// $_mp='api';

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
// 用户登录后台
Route::prefix('backend')->middleware('checkIp')->group(function () {
    Route::match(['GET', 'POST'], '/{controller}/{action}', function($controller, $action) {

    /* 
    生成一个request对象。在 Laravel 框架中，需要通过 Request 对象来获取用户请求信息，
        该对象引用的完整类名是 Illuminate\Http\Request，
        而该请求类又继承自 Symfony 的 Symfony\Component\HttpFoundation\Request
        */
        $request = app('request');
        // 拼接控制器路径
                $controller = '\App\Http\Controllers\Api\V1\backend\\'.ucfirst($controller).'Controller';
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




// Route::match(['GET', 'POST'], '/{controller}/{action}', function($controller, $action) {
// // http://localhost:9090/api/user/index
//     $Request=new Request();
//     // 创建参数数组
//     $params = array_merge($Request->all(),[ $Request->user()]);
    


//     // 获取控制器实例
//     // $controllerInstance = app()->make('\App\Http\Controllers\Api\V1\backend\\' . ucfirst($controller) . 'Controller');



//     $request = app('request');
//         $controller = '\App\Http\Controllers\Api\V1\backend\\'.ucfirst($controller).'Controller';
//         $con     = new $controller($request);
//         if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]+/is', $action)) {
//             if (method_exists($con, $action)) {
//                 // return $con->{$action}($request);
//                return \App::call([$con, $action]);
//             }
//         }
//         die('No route found , Please check url parameters');


//     // // 使用反射获取方法
//     // $reflectionMethod = new \ReflectionMethod($controllerInstance, $action);
    
//     // // 检查方法是否可调用
//     // if ($reflectionMethod->isPublic() && !$reflectionMethod->isAbstract()) {
//     //     // 调用方法
//     //     return $reflectionMethod->invokeArgs($controllerInstance, $params);
//     // }
 
//     // // 方法不存在或不可调用
//     // return 'Method not found or not accessible.';
// });



// http://localhost:9090/api/user/index

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
