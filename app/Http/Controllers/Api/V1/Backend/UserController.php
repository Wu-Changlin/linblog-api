<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
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
                ["age"=>19]
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


 
    // 其他路由方法
}