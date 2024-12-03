<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;

use App\Services\Backend\MenuService;
use App\Services\JsonWebTokenService;

use Illuminate\Http\Request;
use App\Http\Requests\Backend\AddOrEditMenuRequest;
use App\Http\Requests\Backend\PaginationRequest;

// 菜单模块
class MenuController extends Controller
{


    // 获取页面框架数据，表格头、下拉菜单、单选框、复选框等数据
    public function getPageLayoutData(Request $request)
    {
        $get_page_layout_data_result = [];
        $table_header = [
            ["prop" => "menu_id", "label" => "menu_id", "key" => "menu_id"],
            ["prop" => "icon", "label" => "图标", "key" => "icon", "scopedSlot" => "icon"],
            ["prop" => "menu_title", "label" => "展示名称", "key" => "menu_title"],
            ["prop" => "menu_name", "label" => "路由Name", "key" => "menu_name"],
            ["prop" => "menu_path", "label" => "路由Path", "key" => "menu_path"],
            ["prop" => "business_level", "label" => "业务层面", "key" => "business_level"],
            ["prop" => "parent_id", "label" => "父节点", "key" => "parent_id"],
            ["prop" => "is_pulled", "label" => "下架", "key" => "is_pulled"],
            ["prop" => "created_time", "label" => "创建时间", "key" => "created_time"],
            ["prop" => "update_time", "label" => "更新时间", "key" => "update_time"],
            ["prop" => "delete_time", "label" => "删除时间", "key" => "delete_time"]
        ];
        $is_pulled_data = [
            [
                "is_pulled" => 0,
                "label" => "否"
            ],
            [
                "is_pulled" => 1,
                "label" => "是"
            ]
        ];
        $options_business_level_data = [
            [
                "business_level" => 1,
                "label" => "前端"
            ],
            [
                "business_level" => 2,
                "label" => "后台"
            ]
        ];
        //is_pulled  '是否下架	0：默认， 1： 是 	 ，2：否',

        // 返回 条件为空返回0； 有数据返回查询结果 ； 空数据，返回[]。用于编辑和添加时选项值
        //    $where_data= toConditionsArray(["is_pulled"=>2]);
        $options_parent_id_data_result = MenuService::getIsNoPulledData([["is_pulled", '=', 2]]);

        // 如果查询条件为空直接返回 0
        if ($options_parent_id_data_result === 0) {
            sendErrorMSG(403, '数据异常！');
        }

        // 组转数据
        $get_page_layout_data_result['table_header'] = $table_header;
        $get_page_layout_data_result['is_pulled_data'] = $is_pulled_data;
        $get_page_layout_data_result['options_business_level_data'] = $options_business_level_data;
        $get_page_layout_data_result['options_parent_id_data'] = $options_parent_id_data_result;

        sendMSG(200, $get_page_layout_data_result, '成功！');
    }


    //获取list页面数据，表格数据、页数相关数据
    public function  getMenuListPageData(PaginationRequest $request)
    {
        // "total_pages": 2,  总页数
        // "total_count": 10,       总个数
        // "current_page": 1,       当前页
        // "current_page_limit": 10,   当前分页数量
        // $request_params_all_data = $request->all();

        // 当前页
        $current_page = $request->input('current_page');
        // 当前分页数量
        $current_page_limit = $request->input('current_page_limit');

        $authorization_header = $request->header('Authorization');

        // 假设你从HTTP头部获取了Authorization头部
        // echo 'authorizationHeader:'.$authorizationHeader;
        // 解析Authorization头部，获取token

        // 假设token前缀是Bearer
        $token = trim(str_ireplace('Bearer ', '', $authorization_header));
        // 校验令牌如果验证成功返回payload，否则返回false  
        $payload = JsonWebTokenService::verifyJWT($token);

        if (empty($payload)) {
            sendErrorMSG(403, '访问令牌数据异常！');
        }

        // 从token 中取 role的值   `role` '角色；0：默认，1：普通用户，2：管理员',
        $role_name = self::mate_role_number_to_name($payload['role']);

        //普通用户 只能查询 deleted_at 为空数据
        if ($role_name === 'user') {
            // 查询条件
            $where_data[] = ['deleted_at', '=', ''];
            $get_menu_list_page_data_result = MenuService::getMenuListPageData($where_data, $current_page, $current_page_limit);
        }

        //管理员   无限制deleted_at 
        if ($role_name === 'admin') {

            $get_menu_list_page_data_result = MenuService::getMenuListPageData([], $current_page, $current_page_limit);
        }

        // 成功情景
        if (is_array($get_menu_list_page_data_result)) {
            sendMSG(200, $get_menu_list_page_data_result,  '成功！');
        }

        // 失败情景
        if ($get_menu_list_page_data_result === false) {
            sendMSG(200, [], '失败！');
        }

        // 空数据情景
        if ($get_menu_list_page_data_result === 0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($get_menu_list_page_data_result) && $get_menu_list_page_data_result) {
            sendErrorMSG(403, $get_menu_list_page_data_result);
        }


        // paginate() 分页响应通常包含data（数据）和meta（元数据）键，其中meta键包含current_page、last_page、per_page、total等信息，
        // 用于表示当前页、最后一页、每页数量和总数据量。此外，links键包含分页导航链接，如first、prev、next、last。
    }


    // 获取查询输入数据
    public function  queryInputData(PaginationRequest $request)
    {

        $request_params_all_data = $request->all();

        // 获取表中的所有字段名 (定义允许的字段)
$allowed_field_names_array_res=  MenuService::GetTableAllFieldNames();

if(!is_array($allowed_field_names_array_res)){
    sendErrorMSG(403, '字段数据异常！');
}


// 过滤并验证数据
$filtered_data = array_intersect_key($request_params_all_data,$allowed_field_names_array_res);

// 查询输入数据
$where_data =[];

// 查询条件数组
$where_data=toConditionsArray($filtered_data);


        // "total_pages": 2,  总页数
        // "total_count": 10,       总个数
        // "current_page": 1,       当前页
        // "current_page_limit": 10,   当前分页数量
        // $request_params_all_data = $request->all();

        // 当前页
        $current_page = $request->input('current_page');
        // 当前分页数量
        $current_page_limit = $request->input('current_page_limit');

        $authorization_header = $request->header('Authorization');

        // 假设你从HTTP头部获取了Authorization头部
        // echo 'authorizationHeader:'.$authorizationHeader;
        // 解析Authorization头部，获取token

        // 假设token前缀是Bearer
        $token = trim(str_ireplace('Bearer ', '', $authorization_header));
        // 校验令牌如果验证成功返回payload，否则返回false  
        $payload = JsonWebTokenService::verifyJWT($token);

        if (empty($payload)) {
            sendErrorMSG(403, '访问令牌数据异常！');
        }

        // 从token 中取 role的值   `role` '角色；0：默认，1：普通用户，2：管理员',
        $role_name = self::mate_role_number_to_name($payload['role']);

        //普通用户 只能查询 deleted_at 为空数据
        if ($role_name === 'user') {
            // 查询条件
            $where_data[] = ['deleted_at', '=', ''];
            $query_input_data_result = MenuService::getQueryInputData($where_data, $current_page, $current_page_limit);
        }

        //管理员   无限制deleted_at 
        if ($role_name === 'admin') {

            $query_input_data_result = MenuService::getQueryInputData([], $current_page, $current_page_limit);
        }

        // 成功情景
        if (is_array($query_input_data_result)) {
            sendMSG(200, $query_input_data_result,  '成功！');
        }

        // 失败情景
        if ($query_input_data_result === false) {
            sendMSG(200, [], '失败！');
        }

        // 空数据情景
        if ($query_input_data_result === 0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($query_input_data_result) && $query_input_data_result) {
            sendErrorMSG(403, $query_input_data_result);
        }
    }

    // 分页数据
    public function  getChildPaginationChangeData(PaginationRequest $request) {}


    // 获取当前编辑菜单信息  返回  true 菜单信息   ， false 失败
    public function  getCurrentEditMenuInfo(Request $request)
    {

        // 获取请求参数的id
        $request_params_data_id = $request->input('id');

        if (isset($request_params_data_id) && !empty($request_params_data_id)) {
            $get_current_edit_menu_info_result = MenuService::getCurrentMenuInfo(['menu_id' => $request_params_data_id]);

            // 成功情景
            if ($get_current_edit_menu_info_result) {
                sendMSG(200, $get_current_edit_menu_info_result, '成功！');
            }

            // 失败情景
            if ($get_current_edit_menu_info_result === false) {
                sendMSG(200, [], '失败！');
            }


            // 空数据情景
            if (empty($get_current_edit_menu_info_result)) {
                sendErrorMSG(403, '数据异常！');
            }
            // 数据没有通过校验情景
            if (is_string($get_current_edit_menu_info_result) && $get_current_edit_menu_info_result) {
                sendErrorMSG(403, $get_current_edit_menu_info_result);
            }
        }

        sendErrorMSG(403, '提交数据错误！');
    }

    // 添加或编辑菜单
    public function addOrEditMenu(AddOrEditMenuRequest $request)
    {

        $modular_name = '菜单';
        // 获取全部提交数据
        //     "menu_id": 0,
        // "menu_name": "menu",
        // "menu_title": "菜单管理",
        // "menu_path": "/menu",
        // "icon": "menus",
        // "business_level": 2,
        // "parent_id": 0,
        // "is_pulled": 0,
        // "action": "add",
        $request_params_all_data = $request->all();

        // 拼接添加菜单数据
        $add_or_edit_menu_data['menu_name'] = $request_params_all_data['menu_name'];
        $add_or_edit_menu_data['menu_title'] = $request_params_all_data['menu_title'];
        $add_or_edit_menu_data['menu_path'] = $request_params_all_data['menu_path'];
        $add_or_edit_menu_data['icon'] = $request_params_all_data['icon'];
        $add_or_edit_menu_data['business_level'] = $request_params_all_data['business_level'];
        $add_or_edit_menu_data['parent_id'] = $request_params_all_data['parent_id'];
        $add_or_edit_menu_data['is_pulled'] = $request_params_all_data['is_pulled'];

        // 当$add_or_edit_menu_data['menu_keywords'] 已定义，且 $add_or_edit_menu_data['menu_keywords']不为空时，进入 true 分支
        if (isset($add_or_edit_menu_data['menu_keywords']) && !empty($add_or_edit_menu_data['menu_keywords'])) {
            $add_or_edit_menu_data['menu_keywords'] = $request_params_all_data['menu_keywords'];
        }

        // 当$add_or_edit_menu_data['menu_description'] 已定义，且 $add_or_edit_menu_data['menu_description']不为空时，进入 true 分支
        if (isset($add_or_edit_menu_data['menu_description']) && !empty($add_or_edit_menu_data['menu_description'])) {
            $add_or_edit_menu_data['menu_description'] = $request_params_all_data['menu_description'];
        }

        /* 根目录路径  删除前缀斜杠 开始*/
        //    使用substr_count()函数来计算字符串中特定子字符串出现的次数。对于寻找斜杠/
        $count = substr_count($add_or_edit_menu_data['menu_path'], "/");

        if ($count === 1) {
            $add_or_edit_menu_data['menu_path'] = substr($add_or_edit_menu_data['menu_path'], 1); // 从第二个字符开始切割到末尾
        }
        /* 根目录路径  删除前缀斜杠 结束*/

        //执行添加
        if ($request_params_all_data['action'] === 'add') {
        // 添加  返回  true 成功  ， 错误消息或false 失败
            $add_or_edit_menu_result = MenuService::addMenu($add_or_edit_menu_data);
        }

        //执行编辑
        if ($request_params_all_data['action'] === 'edit') {
            $add_or_edit_menu_data['menu_id'] = $request_params_all_data['menu_id'];
        // 添加  返回  true 成功  ， 错误消息或false 失败
            $add_or_edit_menu_result = MenuService::editMenu($add_or_edit_menu_data);
        }

        // 成功情景
        if (is_array($add_or_edit_menu_result)) {
            sendMSG(200, $add_or_edit_menu_result, $request_params_all_data['action'] . $modular_name . '成功！');
        }

        // 失败情景
        if ($add_or_edit_menu_result === false) {
            sendMSG(200, [], $request_params_all_data['action'] . $modular_name . '失败！');
        }

        // 空数据情景
        if ($add_or_edit_menu_result === 0) {
            sendErrorMSG(403, $request_params_all_data['action'] . $modular_name . '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($add_or_edit_menu_result) && $add_or_edit_menu_result) {
            sendErrorMSG(403, $add_or_edit_menu_result);
        }
    }



    // 其他路由方法
}
