<?php

// app/Services/UserService.php

namespace App\Services;

use App\Models\User as  UserModels;

class UserService
{

    // 添加管理员
    public static function addUser()
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

    // 判断用户已登录  is_logged_in  true 是， false 否
    public static function isLogin($data)
    {

        $is_login_res = UserModels::getUserLoginStatus($data);
        return $is_login_res;
    }


    /**
     * 管理员退出
     * @return 返回登录页
     */
    public static function userLogout()
    {
        $logout_res = UserModels::userLogout(); //执行退出
        if ($logout_res) {
            return '成功退出登录';
        }

        return '退出登录失败';
    }


    public static function getAll(){
        
    }


        /**
     *  判断该昵称用户是否存在
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function isNickNameUserExist($data) {

        $res = UserModels::isNickNameUserExist($data); //是否存在
        
        return $res;
        
    }


       /**
     *  验证账号状态
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function verifyAccount($data) {

        $res = UserModels::verifyAccount($data); //是否正常
        
        return $res;
        
    }


    

    // 其他用户相关的服务方法
}
