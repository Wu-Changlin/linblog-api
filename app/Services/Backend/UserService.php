<?php

namespace App\Services\Backend;

use App\Models\Backend\User as  UserModels;

class UserService
{

    // 获取当前用户信息  返回  true 用户信息  ， false 失败
    public static function getCurrentUserInfo($data){

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
        $get_current_user_info_res = UserModels::getCurrentUserInfo($data); //执行新增
       
        // 添加成功 返回true
        if($get_current_user_info_res){
        
            return $get_current_user_info_res;
        }


        //返回错误消息或false 失败
        $error_msg = $get_current_user_info_res;

        return  $error_msg;
    }


    // 添加用户  返回  true 成功  ， 错误消息或false 失败
    public static function addUser($data)
    {

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
//   `account_status`账号状态 0：默认，1：正常，2：获取过多验证码锁定，3：多次输入错误密码锁定，4：销号',

        $data['account_status']=1;
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
        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }

        $user_login_res = UserModels::userLogin($data);
  // 添加成功 返回true
  if ($user_login_res === true) {
    return true;
}

//返回错误消息或false 失败
$error_msg = $user_login_res;

return  $error_msg;


    }


    // 判断用户已登录  is_logged_in  true 是， false 否
    public static function isLogin($data)
    {
        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
        $is_login_res = UserModels::getUserLoginStatus($data);
             // 添加成功 返回true
             if ($is_login_res === true) {
                return true;
            }
    
            //返回错误消息或false 失败
            $error_msg = $is_login_res;
    
            return  $error_msg;
    }


    /**
     * 管理员退出
     * @return 返回登录页
     */
    public static function userLogout($data)
    {
        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }

        $user_logout_res = UserModels::userLogout(); //执行退出
         // 添加成功 返回true
         if ($user_logout_res === true) {
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $user_logout_res;

        return  $error_msg;
    }


    public static function getAll(){
        
    }


        /**
     *  判断该昵称用户是否存在
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function isNickNameOrEmailUserExist($data) 
    {

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
        $is_nick_name_or_email_user_exist_res = UserModels::isNickNameOrEmailUserExist($data); //是否存在

        // 添加成功 返回true
        if ($is_nick_name_or_email_user_exist_res === true) {
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $is_nick_name_or_email_user_exist_res;

        return  $error_msg;
        
    }


       /**
     *  验证账号状态
     * @param $data 查询数据
     * @return bool  通过 返回true，没有通过返回错误消息或false失败
     */
    public static function verifyAccount($data) {

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }

        $verify_account_res = UserModels::verifyAccount($data); //是否正常
        

        
        // 添加成功 返回true
        if ($verify_account_res === true) {
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $verify_account_res;

        return  $error_msg;


        return $res;
        
    }


    

    // 其他用户相关的服务方法
}
