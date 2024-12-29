<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

use Illuminate\Support\Facades\Schema; //‌Schema facade‌是Laravel框架中用于创建和操作数据库结构的一个功能强大的工具


class Article extends BaseModel
{

    protected $primaryKey = 'article_id'; //创建的表字段中主键ID的名称不为id，则需要通过 $primaryKey 来指定一下设定主键id
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

    // 获取表中的所有字段名

    public static function  getTableAllFieldNames()
    {
        // 假设你已经有了一个模型实例，比如 $model 
        $model = self::first(); // 替换 YourModel 为你的实际模型名

        // 获取表名
        $table_name = $model->getTable();

        // 获取所有字段名，示例： $get_table_all_field_names_res= [  0 => "Article",1 => "menu_name]
        $get_table_all_field_names_res = Schema::getColumnListing($table_name);



        // 是数组、存在且有值
        if (is_array($get_table_all_field_names_res) &&  isset($get_table_all_field_names_res) && !empty($get_table_all_field_names_res)) {
            /* 
                使用array_flip()函数将值转换为键,示例： 
                原 $get_table_all_field_names_res= [  0 => "Article",1 => "menu_name]
                转换为 $results_array= [   "Article"=>0, "menu_name"=>1]
                */
            $results_array = array_flip($get_table_all_field_names_res);
            return $results_array;
        }

        return false;

        // 使用array_flip()函数将值转换为键
        // $v=array_flip($get_table_all_field_names_res);

        // 打印所有字段名
        // foreach ($get_table_all_field_names_res as $column) {
        //     echo $column . PHP_EOL;
        // }

    }


    // 按条件获取数据 返回 条件为空返回0 有数据返回查询结果  空数据，返回false
    public static function  getDataByCondition($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;

        // 初始化查询条件  多条件查询？
        $where_data = [];

        // 示例：[["id",'=' ,1],['name",'=','menu']]
        $where_data = $allow_data;

         //防止空条件导致结果返回空模型对象
         if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }

        $get_data_by_condition = self::where($where_data)
            ->select('Article', 'business_level', 'icon', 'is_pull', 'menu_description', 'menu_keywords', 'menu_name', 'menu_path', 'menu_title')
            ->get();


        // `isEmpty()` 方法判断空值返回 `true`，否则返回 `false`。
        //有查询结果
        if (!$get_data_by_condition->isEmpty()) {

            // 获取集合转多维数组结果 
            $multidimensional_array = $get_data_by_condition->toArray();


            $results_array = $multidimensional_array;

            return $results_array;
        }

        //空数据，返回false
        return false;
    }


    // get Ip is   exist black list table  获取Ip存在黑名单表

    // 按条件获取页码数据 返回 条件为空返回0； 有数据返回查询结果 ； 空数据，返回[]。
    public static function  getPageDataByCondition($data, $current_page, $current_page_limit)
    {

        if (empty($current_page) || empty($current_page_limit)) { //如果$current_page 或$current_page_limit为空直接返回0
            return 0;
        }
        $allow_data = $data;

        // 初始化查询条件  多条件查询？
        $where_data = [];

        // 条件数组 示例：[["id",'=' ,1],['name",'=','menu']]
        $where_data = $allow_data;


        // paginate() 分页响应通常包含data（数据）和meta（元数据）键，其中meta键包含current_page、last_page、per_page、total等信息，
        // 用于表示当前页、最后一页、每页数量和总数据量。此外，links键包含分页导航链接，如first、prev、next、last。
        // 自定义分页数量为$current_page_limit，当前页为$current_page，查询字段为xxx，查询条件为$where_data
        /*
        paginate()参数有四个,第一个是limit 每页的数据条数,第二个是可以不用去操作直接写：['*'],
        第三个是页面的名称一般都是：‘page’,第四个是当前页：$cur_page。
        */


        // 有查询条件
        if (isset($where_data) && !empty($where_data)) {
             //防止空条件导致结果返回空模型对象
        if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }
            //返回 有值paginate对象有查询结果，没有值paginate对象没有有查询结果
            $get_data_by_condition = self::where($where_data)
                ->select('Article', 'business_level', 'icon', 'is_pull', 'menu_description', 'menu_keywords', 'menu_name', 'menu_path', 'menu_title')
                ->paginate($current_page_limit, ['*'], 'page', $current_page);
        }
        // 没有查询条件
        if (empty($where_data)) {
             //防止空条件导致结果返回空模型对象
        if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }
            //返回 有值paginate对象有查询结果，没有值paginate对象没有有查询结果
            $get_data_by_condition = self::select('Article', 'business_level', 'icon', 'is_pull', 'menu_description', 'menu_keywords', 'menu_name', 'menu_path', 'menu_title')
                ->paginate($current_page_limit, ['*'], 'page', $current_page);
        }


        // dd($get_data_by_condition);

        //     // 获取总页数
        // $totalPages = $get_data_by_condition->lastPage();

        // // 获取总条目数
        // $totalItems = $get_data_by_condition->total();

        // 获取集合转数组结果 
        $results_array = $get_data_by_condition->toArray();

        // echo "</br>";
        // echo '值：'. !$get_data_by_condition->isEmpty();
        // echo "</br>";

        // dd($results_array);
        // get_data_by_condition有值，data键存在且有值
        if ($results_array && isset($results_array['data']) && !empty($results_array['data'])) {

            return $results_array;
        }

        //空数据，返回false
        return false;
    }



    // 获取当前菜单信息  返回  true 菜单信息   ， false 失败
    public static function getCurrentMenuInfo($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;

        // 初始化查询条件  多条件查询？
        $where_data = [];

        // 当$allow_data['Article'] 已定义，且 $allow_data['Article']不为空时，进入 true 分支
        if (isset($allow_data['Article']) && !empty($allow_data['Article'])) {
            $menu_id_where = ['Article', '=',  $allow_data['Article']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_id_where;
        }


         //防止空条件导致结果返回空模型对象
         if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }

        // ->get查到数据返回Eloquent 集合，查不到返回Eloquent 空集合
        $get_current_edit_menu_info_condition = self::where($where_data)->select('Article', 'business_level', 'icon', 'is_pull', 'menu_description', 'menu_keywords', 'menu_name', 'menu_path', 'menu_title')->first();


        // 有值继续执行
        if ($get_current_edit_menu_info_condition) {


            // 获取集合转数组结果 

            $results_array = $get_current_edit_menu_info_condition->toArray();
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

        // 当$allow_data['Article'] 已定义，且 $allow_data['Article']不为空时，进入 true 分支
        if (isset($allow_data['Article']) && !empty($allow_data['Article'])) {
            $menu_id_where = ['Article', '=',  $allow_data['Article']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_id_where;
        }



        // 当$allow_data['menu_name'] 已定义，且 $allow_data['menu_name']不为空时，进入 true 分支
        if (isset($allow_data['menu_name']) && !empty($allow_data['menu_name'])) {
            $menu_name_where = ['menu_name', '=',  $allow_data['menu_name']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_name_where;
        }


        // 当$allow_data['menu_title'] 已定义，且 $allow_data['menu_title']不为空时，进入 true 分支
        if (isset($allow_data['menu_title']) && !empty($allow_data['menu_title'])) {
            $menu_title_where = ['menu_title', '=',  $allow_data['menu_title']];
            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_title_where;
        }


        // 当$allow_data['menu_path'] 已定义，且 $allow_data['menu_path']不为空时，进入 true 分支
        if (isset($allow_data['menu_path']) && !empty($allow_data['menu_path'])) {
            $menu_path_where = ['menu_path', '=',  $allow_data['menu_path']];
            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_path_where;
        }

         //防止空条件导致结果返回空模型对象
         if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }
        // ->first查到数据返回Eloquent 对象，查不到返回null
        $is_menu_data_exist_res = self::where($where_data)->select('Article')->first();
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
            return 0;
        }
        $allow_data = $data;

        // -menu_name重复性验证
        // 当$allow_data['menu_name'] 已定义，且 $allow_data['menu_name']不为空时，进入 true 分支
        if (isset($allow_data['menu_name']) && !empty($allow_data['menu_name'])) {
            $menu_name_where = ['menu_name', '=',  $allow_data['menu_name']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $menu_name_where;
            //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
            $is_repeat_menu_name_res = self::where($where_data)->select('user_id')->exists();

            if ($is_repeat_menu_name_res) { //如果有数据说明menu_name已存在
                return 'menu_name已存在';
            }
        }




        // -menu_title重复性验证

     // 当$allow_data['menu_title'] 已定义，且 $allow_data['menu_title']不为空时，进入 true 分支
     if (isset($allow_data['menu_title']) && !empty($allow_data['menu_title'])) {
        $menu_title_where = ['menu_title', '=',  $allow_data['menu_title']];

        // 将一个数组嵌套到另一个数组
        $where_data[] = $menu_title_where;
        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_menu_title_res = self::where($where_data)->select('user_id')->exists();

        if ($is_repeat_menu_title_res) { //如果有数据说明menu_title已存在
            return 'menu_title已存在';
        }
    }
        // menu_path重复性验证

             // 当$allow_data['menu_path'] 已定义，且 $allow_data['menu_path']不为空时，进入 true 分支
     if (isset($allow_data['menu_path']) && !empty($allow_data['menu_path'])) {
        $menu_path_where = ['menu_path', '=',  $allow_data['menu_path']];

        // 将一个数组嵌套到另一个数组
        $where_data[] = $menu_path_where;
        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_menu_path_res = self::where($where_data)->select('user_id')->exists();

        if ($is_repeat_menu_path_res) { //如果有数据说明menu_path已存在
            return 'menu_path已存在';
        }
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
            return 0;
        }
        $allow_data = $data;

        // 判断菜单数据是否存在
        $is_menu_data_exist_res = self::isMenuDataExist(['Article' => $allow_data['Article']]);
        // 如果不存在，那么直接返回false
        if (empty($is_menu_data_exist_res)) {
            return '没有该菜单数据!';
        }

        //查询该id信息
        // 获取数组的所有键
        $select_keys_array = array_keys($allow_data);
        $current_id_res = self::find($allow_data['Article'], $select_keys_array);
        $current_id_info = $current_id_res->toArray(); //集合转数组

        //判断字段是否需要修改
        $edit_info = array_diff_assoc($allow_data, $current_id_info); //1:返回[]空数组，说明2个数组相同 2:返回非空数组（数据相同字段已去除，剩下需要修改的字段数据），说明$data数据和数据库数据不一致，需要执行修改
        if (!$edit_info) { //空数组说明没有修改字段值，返回1
            return '已修改数据,请勿重复操作!';
        }


        //使用update方法更新数据 ,update 方法需要一个表示应该更新的列的列和值对数组。 update 方法返回受影响的行数。
        $edit_res = self::where('Article', $allow_data['Article'])->update($allow_data);

        // 编辑成功
        if ($edit_res) {
            // 返回最新数据
            $get_current_edit_menu_info_result = self::getCurrentMenuInfo($data);
            // 成功情景
            if ($get_current_edit_menu_info_result) {
                return $get_current_edit_menu_info_result;
            }
            return '没有返回最新数据！' ;
        }

        return false;
    }
}
