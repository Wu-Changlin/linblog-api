<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
 
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // 访问在 Laravel 中为不存在的控制器返回自定义的 404 JSON 响应
    // 这段代码会检测是否抛出了 NotFoundHttpException 异常，
    // 如果是，则返回一个包含错误消息的 JSON 响应。其他未处理的异常则会按默认方式处理。
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            // return response()->json(['message' => '无效访问'], 404);
            sendErrorMSG(404,'');
            
        }
    
        return parent::render($request, $exception);
    }
}
