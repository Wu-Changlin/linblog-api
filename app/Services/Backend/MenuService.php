<?php

namespace App\Services\Backend;

use App\Models\Backend\Menu as  MenuModels;

class MenuService
{


    // 获取查询输入数据
    public static function    getQueryInputData($data,$current_page,$current_page_limit){
        if (empty($data) || empty($current_page) || empty($current_page_limit) ) { //如果$data或$current_page 或$current_page_limit为空直接返回0
            return 0;
        }
        
        $get_query_input_data_res=MenuModels::getPageDataByCondition($data,$current_page,$current_page_limit);

        // 获取成功 返回菜单信息 
        if ($get_query_input_data_res) {

            return $get_query_input_data_res;
        }

        //返回错误消息或false 失败
        $error_msg = $get_query_input_data_res;

        return  $error_msg;
    }


        // 获取表中的所有字段名

    public static function  getTableAllFieldNames()
    {
        $get_table_all_field_names_res=MenuModels::getTableAllFieldNames();

        // 获取成功 返回菜单信息 
        if ($get_table_all_field_names_res) {

            return $get_table_all_field_names_res;
        }

        //返回错误消息或false 失败
        $error_msg = $get_table_all_field_names_res;

        return  $error_msg;

    }

    //获取list页面数据，表格数据、页数相关数据
    public static function  getMenuListPageData($data,$current_page,$current_page_limit)
    {
        if (empty($current_page) || empty($current_page_limit) ) { //如果$current_page 或$current_page_limit为空直接返回0
            return 0;
        }
        
        $get_menu_list_page_data_res=MenuModels::getPageDataByCondition($data,$current_page,$current_page_limit);

        // 获取成功 返回菜单信息 
        if ($get_menu_list_page_data_res) {

            return $get_menu_list_page_data_res;
        }

        //返回错误消息或false 失败
        $error_msg = $get_menu_list_page_data_res;

        return  $error_msg;
    }




    // 获取当前编辑菜单信息  返回  true 菜单信息   ， false 失败
    public static function getCurrentMenuInfo($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }

        $get_current_edit_menu_info_res = MenuModels::getCurrentMenuInfo($data); //执行新增

        // 获取成功 返回菜单信息 
        if ($get_current_edit_menu_info_res) {

            return $get_current_edit_menu_info_res;
        }

        //返回错误消息或false 失败
        $error_msg = $get_current_edit_menu_info_res;

        return  $error_msg;
       
    }

    // 获取没有下架的数据
    public static function getIsNoPulledData($data)
    {

        if (empty($data)) { //如果$data为空直接返回 0
            return 0;
        }
        // 返回 条件为空返回0； 有数据返回查询结果 ； 空数据，返回[]。
        $get_is_no_pulled_data_res = MenuModels::getDataByCondition($data); //执行新增

        // 获取成功 返回菜单信息 
        if ($get_is_no_pulled_data_res) {

            return $get_is_no_pulled_data_res;
        }

         //返回错误消息或false 失败
         $error_msg = $get_is_no_pulled_data_res;

         return  $error_msg;

    }



    // 添加菜单  返回  true 成功  ， 错误消息或false 失败
    public static function addMenu($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }

        $add_menu_res = MenuModels::addMenu($data);


        // 添加成功 返回true
        if ($add_menu_res === true) {
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
            return 0;
        }

        $edit_menu_res = MenuModels::editMenu($data);


        // 编辑成功，返回编辑成功后的最新数据
        if (is_array($edit_menu_res) && !empty($edit_menu_res) ) {
            return $edit_menu_res;
        }

        //返回错误消息或false 失败
        $error_msg = $edit_menu_res;

        return  $error_msg;
    }




      // 校验菜单信息(是否下架)  返回  true 通过   ， false 失败
      public static function validationMenuInfo($data){
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }

        $get_current_menu_info_res = MenuModels::getCurrentMenuInfo($data); //执行新增

        // 有菜单信息 
        if ($get_current_menu_info_res) {
            //   `is_pull`  '0' COMMENT '是否下架	0：默认， 1： 是 	 ，2：否',
            if($get_current_menu_info_res['is_pull']===2){
                return true;
            }

            
        }

        //返回false 失败
      
        return  false;


    }

     // 获取当前菜单id信息  返回  true 菜单信息   ， false 失败
     public static function getCurrentMenuIdInfo($data)
     {
 
         if (empty($data)) { //如果$data为空直接返回
             return 0;
         }
 
         $get_current_edit_menu_info_res = MenuModels::getCurrentMenuInfo($data); //执行新增
 
         // 获取成功 返回菜单信息 
         if ($get_current_edit_menu_info_res) {
 
             return $get_current_edit_menu_info_res;
         }
 
         //返回错误消息或false 失败
         $error_msg = $get_current_edit_menu_info_res;
 
         return  $error_msg;
        
     }

}
