<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use App\Services\Backend\UserService;
use Illuminate\Http\Request;

// 后台首页模块
class HomeController extends Controller
{
    protected $userService;

    public function __construct()
    {
    
        $this->userService = new UserService(); 
        
    }


   
    // 其他路由方法
}