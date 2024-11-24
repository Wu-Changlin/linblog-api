<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use App\Services\UserService;
use Illuminate\Http\Request;

// 用户模块
class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
    
        $this->userService = new UserService(); 
        
    }


    public function index(Request $request)
    {
        
        // $user = $this->userService->create($request->all());
        $user=[
            "user_id"=>1,
            "list"=>[
                ["age"=>19]
            ]
        ];

    sendMSG(200, $user,'OK');
        // return response()->json(['user' => $user], 201);
    }
 
    public function getAll()
    {

        echo 'getAll';
        die;
        $users = UserService::getAll();
        return response()->json($users, 200);
    }


    public function  getRefreshAccessToken(){
        $user=[
            "jwt_access_token"=>"php_new_jwt_refresh_token",
        
        ];
        sendMSG(200, $user,'OK');
    }


    // 其他路由方法
}