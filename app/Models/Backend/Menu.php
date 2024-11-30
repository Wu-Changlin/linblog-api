<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;


class Menu extends Model
{

    protected $primaryKey = 'menu_id'; //创建的表字段中主键ID的名称不为id，则需要通过 $primaryKey 来指定一下设定主键id
    protected $guarded = []; //  guarded 属性用于定义不可以批量赋值的属性（字段），也就是需要保护的属性
    //  fillable 属性用于定义可以批量赋值的属性（字段），也就是允许用户通过模型的 create 或 fill 方法来设置的属性。
    // protected $fillable = [
    //         'dict_type_id',
    //         'label',
    //         'value',
    //         'sort',
    //         'satus',
    //         'remark',
    //     ];

    // get Ip is   exist black list table  获取Ip存在黑名单表

    // get Data By Condition    按条件获取数据


    
    
    

    /**
     *  判断菜单数据是否存在
     * @param $data 查询数据  1：menu_id，2：menu_name，3：menu_title，4.menu_path
     * @return bool   true 是， false 否
     */
    public static function isMenuDataExist($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;
    
        // 初始化查询条件  多条件查询？
        $where_data = [];

         // 当$allow_data['menu_id'] 已定义，且 $allow_data['menu_id']不为空时，进入 true 分支
         if (isset($allow_data['menu_id']) && !empty($allow_data['menu_id'])) {
            $menu_id_where = ['menu_id', '=',  $allow_data['menu_id']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$menu_id_where];
        }

        

         // 当$allow_data['menu_name'] 已定义，且 $allow_data['menu_name']不为空时，进入 true 分支
         if (isset($allow_data['menu_name']) && !empty($allow_data['menu_name'])) {
            $menu_name_where = ['menu_name', '=',  $allow_data['menu_name']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$menu_name_where];
        }


        // 当$allow_data['menu_title'] 已定义，且 $allow_data['menu_title']不为空时，进入 true 分支
        if (isset($allow_data['menu_title']) && !empty($allow_data['menu_title'])) {
            $menu_title_where = ['menu_title', '=',  $allow_data['menu_title']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$menu_title_where];
        }


        // 当$allow_data['menu_path'] 已定义，且 $allow_data['menu_path']不为空时，进入 true 分支
        if (isset($allow_data['menu_path']) && !empty($allow_data['menu_path'])) {
            $menu_path_where = ['menu_path', '=',  $allow_data['menu_path']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$menu_path_where];
        }

        
        $is_menu_data_exist_res = self::where($where_data)->select('menu_id');
        if ($is_menu_data_exist_res) {
            return true;
        }

        return false;
    }




    /**
     * 新增菜单
     * @param $data 菜单数据
     * @return int 0：$data为空，1：menu_name重复，2：menu_title重复，3.menu_path重复，true：成功新增，false.失败
     */
    public static function addMenu($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $allow_data = $data;
        

         // -menu_name重复性验证
         $menu_name = $allow_data['menu_name'];
         $where = [['menu_name', '=',  $menu_name]];
 
 
         //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
         $is_repeat_menu_name_res = self::where($where)->select('menu_id')->exists();
 
         if ($is_repeat_menu_name_res) { //如果有数据说明menu_name已注册
             return 'menu_name已注册';
         }

    

        // -menu_title重复性验证
        $menu_title = $allow_data['menu_title'];
        $where = [['menu_title', '=',  $menu_title]];


        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_menu_title_res = self::where($where)->select('menu_id')->exists();

        if ($is_repeat_menu_title_res) { //如果有数据说明menu_title已注册
            return 'menu_title已注册';
        }


        // menu_path重复性验证
        $menu_path = $allow_data['menu_path'];
        $where = [['menu_path', '=',  $menu_path]];

        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_menu_path_res = self::where($where)->select('menu_id')->exists();
        if ($is_repeat_menu_path_res) { //如果有数据说明menu_path已存在
            return 'menu_path已存在';
        }

        //使用create方法新增数据
        $add_res = self::create($allow_data); 
        // 添加成功
        if ($add_res) {
            return true;
        }

        return false;
    }


     /**
     * 编辑菜单
     * @param $data 菜单数据
     * @return int 0：$data为空，true：成功编辑，false.失败
     */
    public static function editMenu($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $allow_data = $data;
        
        // 判断菜单数据是否存在
       $is_menu_data_exist_res=self::isMenuDataExist($allow_data['menu_id']);
// 如果不存在，那么直接返回false
        if (empty($is_menu_data_exist_res)) {
            return false;
        }

//使用update方法更新数据 ,update 方法需要一个表示应该更新的列的列和值对数组。 update 方法返回受影响的行数。
        $edit_res=self::where('menu_id',$allow_data['menu_id'])->update($allow_data);

        // 编辑成功
        if ($edit_res) {
            return true;
        }

        return false;
    }
}