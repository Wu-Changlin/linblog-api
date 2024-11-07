<?php

// app/Services/UserService.php

namespace App\Services;

use App\Models\User;

class UserService
{
public function create($data)
{
return User::create($data);
}

public function getAll()
{
return User::all();
}

// 其他用户相关的服务方法
}