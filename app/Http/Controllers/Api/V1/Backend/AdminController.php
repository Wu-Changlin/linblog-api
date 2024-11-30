<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use App\Services\Backend\UserService;
use Illuminate\Http\Request;

// 公共模块
class AdminController extends Controller
{
    protected $userService;

    public function __construct()
    {
    
        $this->userService = new UserService(); 
        
    }



    //获取log和菜单导航栏   // 获取网站配置（如网站标题、网站关键词、网站描述、网站log）
    public function getAdminAndMenuListData(Request $request){
        // $request_params_all_data = $request->all();
        sendMSG('200', [], 'getAdminAndMenuListData');

        // "log_data":
        // "menu_data"
    }


   
    // 其他路由方法
}