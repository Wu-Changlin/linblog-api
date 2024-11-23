<?php

// app/Services/UserService.php

namespace App\Services;

use App\Models\User as  UserModels;

class UserService
{

    // 添加管理员
    public function addUser()
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $add_user_res = UserModels::addUser($data); //执行新增

        switch ($add_user_res) { //判断新增返回值
            case 0:
                return  '数据为空';
                break;
            case 1:
                return  '邮箱已注册';
                break;
            case 2:
                return  "昵称v已存在";
                break;
            case 3:
                return  "新增管理员成功";
                break;
            default:
                return  '数据写入失败,新增管理员失败';
        }
    }

    // 判断用户登录 
    public function isLogin($data)
    {

        $is_login_res = UserModels::getUserLoginStatus($data);
        return $is_login_res;
    }


    /**
     * 管理员退出
     * @return 返回登录页
     */
    public function userLogout()
    {
        $logout_res = UserModels::userLogout(); //执行退出
        if ($logout_res) {
            return '成功退出登录';
        }

        return '退出登录失败';
    }

    // 其他用户相关的服务方法
}
