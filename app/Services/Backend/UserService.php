<?php

namespace App\Services\Backend;

use App\Models\Backend\User as  UserModels;

class UserService
{

    // 获取当前用户信息  返回  true 用户信息  ， false 失败
    public static function getCurrentUserInfo($data){

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $get_current_user_info_res = UserModels::getCurrentUserInfo($data); //执行新增
       
        // 添加成功 返回true
        if($get_current_user_info_res){
           
         return $get_current_user_info_res;
        }

        //返回false 失败
        return  false;
    }


    // 添加用户  返回  true 成功  ， 错误消息或false 失败
    public static function addUser($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $add_user_res = UserModels::addUser($data); //执行新增

        // 添加成功 返回true
        if($add_user_res===true){
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $add_user_res;

        return  $error_msg;

    }


// 用户登录
    public static function userLogin($data)
    {
        $user_login_res = UserModels::userLogin($data);
        return $user_login_res;

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
    public static function isNickNameOrEmailUserExist($data) {

        $res = UserModels::isNickNameOrEmailUserExist($data); //是否存在
        
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
