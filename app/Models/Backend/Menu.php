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



        // 获取当前编辑菜单信息  返回  true 菜单信息   ， false 失败
        public static function getCurrentEditMenuInfo($data){

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
    
            
            $get_current_edit_menu_info_res = self::where($where_data)->select('menu_id','business_level', 'icon', 'is_pulled', 'menu_description', 'menu_keywords','menu_name','menu_path', 'menu_title');
           
        
          
        
            if ($get_current_edit_menu_info_res) {
                // 使用 get 方法来获取结果
                $results = $get_current_edit_menu_info_res->get();
               
                // 获取多维数组结果 
                $multidimensional_array=$results->toArray();
               
                // 多维数组扁平为一维
                $results_array=flattenArray($multidimensional_array);
              
          

                return $results_array;
            }
    
    
            return false;
        }
    

    
    

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
 
         if ($is_repeat_menu_name_res) { //如果有数据说明menu_name已存在
             return 'menu_name已存在';
         }

    

        // -menu_title重复性验证
        $menu_title = $allow_data['menu_title'];
        $where = [['menu_title', '=',  $menu_title]];


        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_menu_title_res = self::where($where)->select('menu_id')->exists();

        if ($is_repeat_menu_title_res) { //如果有数据说明menu_title已存在
            return 'menu_title已存在';
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

        //查询该id信息
        // 获取数组的所有键
        $select_keys_array = array_keys($allow_data);
        $current_id_res = self::find($allow_data['menu_id'],$select_keys_array); 
        $current_id_info=$current_id_res->toArray(); //集合转数组

        //判断字段是否需要修改
        $edit_info = array_diff_assoc($allow_data,$current_id_info); //1:返回[]空数组，说明2个数组相同 2:返回非空数组（数据相同字段已去除，剩下需要修改的字段数据），说明$data数据和数据库数据不一致，需要执行修改
        if (!$edit_info) {//空数组说明没有修改字段值，返回1
            return '已修改数据,请勿重复操作!';
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
