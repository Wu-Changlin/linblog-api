<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
 
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
 
    public function index(Request $request)
    {
        
        // $user = $this->userService->create($request->all());
        $user=[
            "user_id"=>1,
            "list"=>[
                ["age"=>18]
            ]
        ];

       $this->sendMSG('OK',200, $user);
        // return response()->json(['user' => $user], 201);
    }
 
    public function getAll()
    {

        echo 'getAll';
        die;
        $users = $this->userService->getAll();
        return response()->json($users, 200);
    }


     /**
     * 接口返回数据
     * @param $msg         自定义提示信息
     * @param int $code    xiang
     * @param array $data
     */
    public function sendMSG($msg, $code=0, $data = []) {
        $arr = array(
            'msg' => $msg,
            'code' => $code,
            'data' => empty($data) ? "" : $data
            // 'data' => empty($data) ? "" : enGzip($data)
        );
        exit(json_encode($arr,320));
    }

 
    // 其他路由方法
}