<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack. 
     * 应用程序的全局HTTP中间件堆栈。
     *
     * These middleware are run during every request to your application.
     *  这些中间件在每次向应用程序发出请求时都会运行。
     * 
     * 每次HTTP请求时都被执行
     * 
     * 作用：
     * 全局中间件，要对所有的请求要做一些处理的时候，就适合定义在该属性内。（比如统计请求次数这些）
     *
     * @var array<int, class-string|string> 
     * @var数组<int，类字符串|string>
     */


    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        //自定义中间件
        //  根据路由前缀动态应用中间件
        // 'checkToken' => \App\Http\Middleware\CheckToken::class,  //检测Token

    ];

    /**
     * The application's route middleware groups.
     * 应用程序的路由中间件组。
     * 
     * 作用：
     * 中间件组，比如我们项目有 api 请求和 web 的请求的时候，
     * 就要把两种类型的请求中间件分离开来，这时候就需要我们中间件组啦。
     * 
     * 可以通过一个键名将相关中间件分配给同一个路由。例如，用户模块的各个页面都需要验证用户的登陆状态，
     * 如果对每个路由都添加中间件组会很繁琐，通过中间件组可以将中间件一次分配给多个路由。
     *
     * @var array<string, array<int, class-string|string>>
     * @var数组<string，数组<int，类字符串|string>>
     */

    protected $middlewareGroups = [
        // 对web.php文件生效

        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        // 对api.php文件生效
        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // 自定义  写在api.php文件的路由都生效（公共中间件？）
           'checkIp' => \App\Http\Middleware\CheckIp::class,  //检测黑名单IP 
           'preventDuplicateSubmission' => \App\Http\Middleware\PreventDuplicateSubmission::class,  //检测Token
        ],
        //自定义中间件组

        //前端路由中间组
        'frontend' => [
            'checkToken' => \App\Http\Middleware\CheckToken::class,  //检测Token
        ],

        //后台路由中间组
        'backend' => [
            'checkToken' => \App\Http\Middleware\CheckToken::class,  //检测Token
        ],
        
        //登录路由中间组
        'login' => [
            'checkToken' => \App\Http\Middleware\CheckToken::class,  //检测Token
        ],

        //重置密码中间组
        'reset_password' => [
            'checkToken' => \App\Http\Middleware\CheckToken::class,  //检测Token 
        ],
        


    ];

    /**
     * The application's route middleware.
     * 应用程序的路由中间件。
     * 
     * 作用：
     * 这些中间件仅在定义的路由中使用。如果你尝试在路由文件以外使用这些中间件，
     * 它们将不会有任何效果，因为这些中间件仅在路由过程中被调用。
     * 
     * 路由中间件，有些个别的请求，我们需要执行特别的中间件时，就适合定义在这属性里面。
     * 
     * 指定路由中间件
     *
     * These middleware may be assigned to groups or used individually.
     * 这些中间件可以分配给组或单独使用。
     *
     * @var array<string, class-string|string>
     * @var数组<string，类字符串|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        // 自定义
        'preventDuplicateSubmission' => \App\Http\Middleware\PreventDuplicateSubmission::class,

        

    ];
}
