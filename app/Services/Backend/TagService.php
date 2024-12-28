<?php

namespace App\Services\Backend;

use App\Models\Backend\Tag as  TagModels;

use Illuminate\Support\Facades\Schema; //‌Schema facade‌是Laravel框架中用于创建和操作数据库结构的一个功能强大的工具


class TagService
{

 // 获取表中的所有字段名

 public static function  getTableAllFieldNames()
 {
     $get_table_all_field_names_res=TagModels::getTableAllFieldNames();

     // 获取成功 返回菜单信息 
     if ($get_table_all_field_names_res) {

         return $get_table_all_field_names_res;
     }

     

     //返回错误消息或false 失败
     $error_msg = $get_table_all_field_names_res;

     return  $error_msg;

 }

  // 获取查询输入数据
  public static function    getQueryInputData($data,$current_page,$current_page_limit){
    if (empty($data) || empty($current_page) || empty($current_page_limit) ) { //如果$data或$current_page 或$current_page_limit为空直接返回0
        return 0;
    }
    
    $get_query_input_data_res=TagModels::getPageDataByCondition($data,$current_page,$current_page_limit);

    // 获取成功 返回菜单信息 
    if ($get_query_input_data_res) {

        return $get_query_input_data_res;
    }

    //返回错误消息或false 失败
    $error_msg = $get_query_input_data_res;

    return  $error_msg;
}


    //获取list页面数据，表格数据、页数相关数据
    public static function  getTagListPageData($data,$current_page,$current_page_limit)
    {
        if (empty($current_page) || empty($current_page_limit) ) { //如果$current_page 或$current_page_limit为空直接返回0
            return 0;
        }
        
        $get_menu_list_page_data_res=TagModels::getPageDataByCondition($data,$current_page,$current_page_limit);

        // 获取成功 返回菜单信息 
        if ($get_menu_list_page_data_res) {

            return $get_menu_list_page_data_res;
        }

        //返回错误消息或false 失败
        $error_msg = $get_menu_list_page_data_res;

        return  $error_msg;
    }




    // 获取当前用户信息  返回  true 用户信息  ， false 失败
    public static function getCurrentTagInfo($data){

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
        $get_current_user_info_res = TagModels::getCurrentTagInfo($data); //执行查询
       
        // 添加成功 返回true
        if($get_current_user_info_res){
        
            return $get_current_user_info_res;
        }


        //返回错误消息或false 失败
        $error_msg = $get_current_user_info_res;

        return  $error_msg;
    }


    // 添加用户  返回  true 成功  ， 错误消息或false 失败
    public static function addTag($data)
    {

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
//   `account_status`账号状态 0：默认，1：正常，2：获取过多验证码锁定，3：多次输入错误密码锁定，4：销号',

        $data['account_status']=1;
        $add_user_res = TagModels::addTag($data); //执行新增

        // 添加成功 返回true
        if($add_user_res===true){
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $add_user_res;

        return  $error_msg;

    }

        // 编辑用户  返回  true 成功  ， 错误消息或false 失败
        // 如果修改邮箱、昵称、密码中其一，那么退出登录、访问令牌和刷新令牌加入黑名单
        public static function editTag($data,$access_token)
        {
    
            if (empty($data)) { //如果$data为空直接返回0
                return 0;
            }
    //   `account_status`账号状态 0：默认，1：正常，2：获取过多验证码锁定，3：多次输入错误密码锁定，4：销号',
    

            $edit_user_res = TagModels::editTag($data,$access_token); //执行新增
            

            // 添加成功 返回true
            if($edit_user_res===true){
                return true;
            }
    
            //返回错误消息或false 失败
            $error_msg = $edit_user_res;
    
            return  $error_msg;
    
        }



// 用户登录
    public static function userLogin($data)
    {
        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }

        $user_login_res = TagModels::userLogin($data);
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
        $is_login_res = TagModels::getTagLoginStatus($data);
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

        $user_logout_res = TagModels::userLogout(); //执行退出
         // 添加成功 返回true
         if ($user_logout_res === true) {
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $user_logout_res;

        return  $error_msg;
    }



        /**
     *  判断该昵称用户是否存在
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function isNickNameOrEmailTagExist($data) 
    {

        if (empty($data)) { //如果$data为空直接返回0
            return 0;
        }
        $is_nick_name_or_email_user_exist_res = TagModels::isNickNameOrEmailTagExist($data); //是否存在

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

        $verify_account_res = TagModels::verifyAccount($data); //是否正常
        

        
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
