<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

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
                "age"=>18 
            ]
        ];
        return response()->json(['user' => $user], 201);
    }

    public function getAll()
    {
        echo 'getAll';
        die;
        return response()->json(['users' => $users], 200);
    }

    // 其他路由方法
}