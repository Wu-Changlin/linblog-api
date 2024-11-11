<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class RouteMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */


     
    // public function boot()
    // {
    //     $routePrefix = $this->app['request']->route()->getPrefix();
    //     // 或者
    //     $route = Route::getCurrentRoute();
    //     if ($route) {
    //         $routePrefix = $route->getPrefix();
    //     }
 
    //     // 使用$routePrefix进行进一步操作
    // }
    public function boot(Request $request)
    {
        

         // 获取当前请求的完整 URL
        //  return Request::url();
        // echo $request->url();

        $str = 'api/backend/user/index';
$parts = explode('/', $str);
if (count($parts) >= 3) {
    $backend = $parts[2];
    echo $backend; // 输出 backend
} else {
    echo '字符串格式不正确';
}
var_dump( $parts);
echo 'parts[1]'. $parts[1];

$middleware = $this->getMiddlewareByPrefixName($parts[1]);

var_dump('middleware:',$middleware);



$this->app->make('router')->middleware('checkToken');
var_dump('$this->app->make(router)->middleware(checkToken):',$this->app->make('router')->middleware('checkToken'));

        // var_dump( $request->path());
        
        // echo $request->route();

        
        // 根据路由前缀动态添加中间件
        // if ($prefix === 'admin') {
        //     $this->app['router']->pushMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        // } elseif ($prefix === 'member') {
        //     $this->app['router']->pushMiddleware('member', \App\Http\Middleware\MemberMiddleware::class);
        // }


          // 获取当前路由前缀名称

        // // 获取所有路由
        // $routes = Route::getRoutes();

        // // 遍历路由，查找具有特定前缀的路由
        // foreach ($routes as $route) {
        //     // 假设我们要找的前缀是'admin'
        //     if (str_starts_with($route->getName(), 'admin.')) {
        //         // 添加中间件到这个路由或者路由组
        //         $route->middleware('checkToken');
        //     }
        // }



        // Route::matched(function ($route) {
        
        //     $path =$request->path(); // 获取当前接口的非域名部分
            
        //     $prefixName = $parts[1]; // 获取路由前缀名称
        //     $middleware = $this->getMiddlewareByPrefixName($prefixName);

        //     if ($middleware) {
        //         $route->middleware($middleware); // 应用对应的中间件
        //     }
        // });
    }


    protected function getMiddlewareByPrefixName($prefixName)
    {
        // 路由前缀数组
        $middlewares = [
            //  'admin' => 'admin.auth', // 假设路由前缀为"admin"时使用"admin.auth"中间件
            'frontend' => ['checkToken' ,\App\Http\Middleware\CheckToken::class]  //检测Token
            , // 这里'checkToken'是假设的中间件别名
            'backend' => ['checkToken' ,\App\Http\Middleware\CheckToken::class]  //检测Token
            , // 这里'checkToken'是假设的中间件别名
            // 其他前缀对应的中间件...
        ];

        return $middlewares[$prefixName] ?? null; // 返回对应的中间件，如果没有找到则返回null
    }


    // public function boot()
    // {
    //     // 假设你有一个路由前缀数组
    //     $prefixes = [
    //         'frontend' => ['checkToken'], // 这里'checkToken'是假设的中间件别名
    //         'backend' => ['checkToken'], // 这里'checkToken'是假设的中间件别名
    //         // 'user' => ['user.middleware'],
    //         // 可以添加更多前缀和中间件
    //     ];
 
    //     foreach ($prefixes as $prefix => $middleware) {
    //         // var_dump('middleware:',$middleware);
    //         echo 'prefix:'. $prefix;
    //         die;
    //         Route::prefix($prefix)->middleware($middleware)->group(function () {
    //             // 在这里定义与前缀相关的路由
    //         });
    //     }
    // }
}