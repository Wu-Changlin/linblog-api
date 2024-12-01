<?php

namespace App\Services\Backend;

use App\Models\Backend\Menu as  MenuModels;

class MenuService
{

    // 获取当前编辑菜单信息  返回  true 菜单信息   ， false 失败
    public static function getCurrentEditMenuInfo($data){

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $get_current_edit_menu_info_res = MenuModels::getCurrentEditMenuInfo($data); //执行新增
       
        // 获取成功 返回菜单信息 
        if($get_current_edit_menu_info_res){
           
         return $get_current_edit_menu_info_res;
        }

        //获取失败，返回false 失败
        return  false;
    }


    // 添加菜单  返回  true 成功  ， 错误消息或false 失败
    public static function addMenu($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
 
            $add_menu_res = MenuModels::addMenu($data); 


        // 添加成功 返回true
        if($add_menu_res===true){
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $add_menu_res;

        return  $error_msg;

    }


    // 编辑菜单 返回  0：$data为空，true：成功编辑，false.失败

    public static function editMenu($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }

        $edit_menu_res = MenuModels::editMenu($data); 


         // 添加成功 返回true
         if($edit_menu_res===true){
            return true;
        }

        //返回错误消息或false 失败
        $error_msg = $edit_menu_res;

        return  $error_msg;

    }


    

    // 其他用户相关的服务方法
}
