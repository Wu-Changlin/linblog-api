<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use App\Services\Backend\UserService;
use Illuminate\Http\Request;

// 用户模块
class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
    
        $this->userService = new UserService(); 
        
    }


    // `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用	0：默认， 1： 是 	 ，2：否',


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


    // 添加用户
    public function addUser(Request $request){
        // 获取全部提交数据
        $request_params_all_data = $request->all();

        // 拼接添加用户数据
        $add_user_data['nick_name']=$request_params_all_data['nick_name'];
        $add_user_data['email']=$request_params_all_data['email'];
        $add_user_data['avatar']=$request_params_all_data['avatar'];
        $add_user_data['password']=$request_params_all_data['password'];
        $add_user_data['confirm_password']=$request_params_all_data['confirm_password'];
        $add_user_data['role']=$request_params_all_data['role'];
        $add_user_data['is_enable']=$request_params_all_data['is_enable'];

        // 添加用户  返回 0 空数据  true 成功  ， 错误消息或false 失败
        $add_user_result= UserService::addUser($add_user_data);

        // 成功情景
        if($add_user_result===true){
            sendMSG(200, $add_user_result,'添加成功！');
        }

         // 失败情景
         if($add_user_result===false){
            sendMSG(200, [],'添加失败！');
        }

        // 提交空用户数据情景
        if($add_user_result===0){
            sendErrorMSG(403,'提交空数据！');
        }
        // 用户数据没有通过校验情景
        if(is_string($add_user_result) && $add_user_result){
            sendErrorMSG(403,$add_user_result);
        }

        

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