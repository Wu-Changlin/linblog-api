<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;

use App\Services\Backend\UserService;
use App\Services\JsonWebTokenService;

use Illuminate\Http\Request;
use App\Http\Requests\Backend\AddOrEditArticleRequest;
use App\Http\Requests\Backend\PaginationRequest;

// 图片模块
class ImageController extends Controller
{

    // 获取页面框架数据，表格头、下拉用户、单选框、复选框等数据
    public function getPageLayoutData(Request $request)
    {
        $get_page_layout_data_result = [];
        $table_header = [
            ["prop" => "user_id", "label" => "user_id", "key" => "user_id"],
            ["prop" => "nick_name", "label" => "昵称", "key" => "nick_name"],
            ["prop" => "avatar", "label" => "头像", "key" => "avatar", "scopedSlot" => "avatar"],
            ["prop" => "email", "label" => "邮箱", "key" => "email"],
            ["prop" => "is_enable", "label" => "启用", "key" => "is_enable"],
            ["prop" => "role", "label" => "角色", "key" => "role"],
            ["prop" => "account_status", "label" => "账号状态", "key" => "account_status"],
            ["prop" => "ip", "label" => "登陆ip", "key" => "ip"],

            ["prop" => "created_time", "label" => "创建时间", "key" => "created_time"],
            ["prop" => "update_time", "label" => "更新时间", "key" => "update_time"],
            ["prop" => "delete_time", "label" => "删除时间", "key" => "delete_time"]
        ];
        $options_role_data = [
            [
                "is_pull" => 1,
                "label" => "普通用户"
            ],
            [
                "is_pull" => 2,
                "label" => "管理员"
            ]
        ];
        $is_enable_data = [
            [
                "is_enable" => 0,
                "label" => "否"
            ],
            [
                "is_enable" => 2,
                "label" => "是"
            ]
        ];


        // 组转数据
        $get_page_layout_data_result['table_header'] = $table_header;
        $get_page_layout_data_result['options_role_data'] = $options_role_data;
        $get_page_layout_data_result['is_enable_data'] = $is_enable_data;

        sendMSG(200, $get_page_layout_data_result, '成功！');
    }


    //获取list页面数据，表格数据、页数相关数据
    public function  getUserListPageData(PaginationRequest $request)
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

        //普通用户 只能查询当前登录用户数据
        if ($role_name === 'user') {
            // 查询条件
            $where_data[] = ['nick_name', '=',$payload['aud']];
            $get_user_list_page_data_result = UserService::getUserListPageData($where_data, $current_page, $current_page_limit);
        }

        //管理员   无限制deleted_at 
        if ($role_name === 'admin') {

            $get_user_list_page_data_result = UserService::getUserListPageData([], $current_page, $current_page_limit);
        }

        // 成功情景
        if (is_array($get_user_list_page_data_result)) {
            // 组装响应数组

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            //空查询结果， last_page(最后一页数字)值是1；通过判断查询结果总数是等于0.重新设置last_page值为0，否则保持last_page值。
            $total_pages = empty($get_user_list_page_data_result['total']) ? $get_user_list_page_data_result['last_page'] = 0 : $get_user_list_page_data_result['last_page'];
            $response_data = [
                "total_pages" => $total_pages,
                "total_count" => $get_user_list_page_data_result['total'],
                "current_page" => $get_user_list_page_data_result['current_page'],
                "current_page_limit" => $get_user_list_page_data_result['per_page'],
                "user_list_data" => $get_user_list_page_data_result['data']

            ];

            sendMSG(200, $response_data,  '成功！');
        }

        // 失败情景
        if ($get_user_list_page_data_result === false) {
            sendMSG(200, [], '失败，没有结果！');
        }

        // 空数据情景
        if ($get_user_list_page_data_result === 0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($get_user_list_page_data_result) && $get_user_list_page_data_result) {
            sendErrorMSG(403, $get_user_list_page_data_result);
        }
    }


    // 获取查询输入数据
    public function  queryInputData(PaginationRequest $request)
    {

        $request_params_all_data = $request->all();

        // 获取表中的所有字段名 (定义允许的字段)
        $allowed_field_names_array_res =  UserService::GetTableAllFieldNames();

        if (!is_array($allowed_field_names_array_res)) {
            sendErrorMSG(403, '字段数据异常！');
        }


        // 过滤并验证数据
        $filtered_data = array_intersect_key($request_params_all_data, $allowed_field_names_array_res);

        // 查询输入数据
        $where_data = [];



        // 查询条件数组
        $where_data = toConditionsArray($filtered_data);

        // 如果查询条件数组为空
        if (empty($where_data)) {
            sendErrorMSG(403, '请输入查询内容！');
        }


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

        //普通用户 只能查询 deleted_at 为空数据,deleted_at=''添加到查询条件
        if ($role_name === 'user') {
            // 查询条件
            $where_data[] = ['deleted_at', '=', ''];
        }

        $query_input_data_result = UserService::getQueryInputData($where_data, $current_page, $current_page_limit);




        // 成功情景
        if (is_array($query_input_data_result)) {

            // 组装响应数组

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            //空查询结果， last_page(最后一页数字)值是1；通过判断查询结果总数是等于0.重新设置last_page值为0，否则保持last_page值。
            $total_pages = empty($query_input_data_result['total']) ? $query_input_data_result['last_page'] = 0 : $query_input_data_result['last_page'];
            $response_data = [
                "total_pages" => $total_pages,
                "total_count" => $query_input_data_result['total'],
                "current_page" => $query_input_data_result['current_page'],
                "current_page_limit" => $query_input_data_result['per_page'],
                "user_list_data" => $query_input_data_result['data']

            ];

            sendMSG(200, $response_data,  '成功！');
        }

        // 失败情景（没有数据）
        if ($query_input_data_result === false) {
            sendMSG(200, [], '失败，没有结果！');
        }

        // 提交空数据情景
        if ($query_input_data_result === 0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($query_input_data_result) && $query_input_data_result) {
            sendErrorMSG(403, $query_input_data_result);
        }
    }

    // 分页数据
    public function  getChildPaginationChangeData(PaginationRequest $request)
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
            $get_child_pagination_change_data_result = UserService::getUserListPageData($where_data, $current_page, $current_page_limit);
        }

        //管理员   无限制deleted_at 
        if ($role_name === 'admin') {

            $get_child_pagination_change_data_result = UserService::getUserListPageData([], $current_page, $current_page_limit);
        }

        // 成功情景
        if (is_array($get_child_pagination_change_data_result)) {
            // 组装响应数组

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            // 前端接收格式 "data":{
            //     "total_pages": 2,
            //     "total_count": 10,
            //     "current_page": 1,
            //     "current_page_limit": 10,
            //     "模块名_list_data": [{},{}]
            // }

            //空查询结果， last_page(最后一页数字)值是1；通过判断查询结果总数是等于0.重新设置last_page值为0，否则保持last_page值。
            $total_pages = empty($get_child_pagination_change_data_result['total']) ? $get_child_pagination_change_data_result['last_page'] = 0 : $get_child_pagination_change_data_result['last_page'];
            $response_data = [
                "total_pages" => $total_pages,
                "total_count" => $get_child_pagination_change_data_result['total'],
                "current_page" => $get_child_pagination_change_data_result['current_page'],
                "current_page_limit" => $get_child_pagination_change_data_result['per_page'],
                "user_list_data" => $get_child_pagination_change_data_result['data']

            ];

            sendMSG(200, $response_data,  '成功！');
        }

        // 失败情景
        if ($get_child_pagination_change_data_result === false) {
            sendMSG(200, [], '失败，没有结果！');
        }

        // 空数据情景
        if ($get_child_pagination_change_data_result === 0) {
            sendErrorMSG(403,  '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($get_child_pagination_change_data_result) && $get_child_pagination_change_data_result) {
            sendErrorMSG(403, $get_child_pagination_change_data_result);
        }
    }


    // 获取当前编辑用户信息  返回  true 用户信息   ， false 失败
    public function  getCurrentEditUserInfo(Request $request)
    {

        // 获取请求参数的id
        $request_params_data_id = $request->input('id');

        if (isset($request_params_data_id) && !empty($request_params_data_id)) {
            $get_current_edit_user_info_result = UserService::getCurrentUserInfo(['user_id' => $request_params_data_id]);

            // 成功情景
            if ($get_current_edit_user_info_result) {
                sendMSG(200, $get_current_edit_user_info_result, '成功！');
            }

            // 失败情景
            if ($get_current_edit_user_info_result === false) {
                sendMSG(200, [], '失败，没有结果！');
            }

            // 空数据情景
            if (empty($get_current_edit_user_info_result)) {
                sendErrorMSG(403, '数据异常！');
            }
            // 数据没有通过校验情景
            if (is_string($get_current_edit_user_info_result) && $get_current_edit_user_info_result) {
                sendErrorMSG(403, $get_current_edit_user_info_result);
            }
        }

        sendErrorMSG(403, '提交数据错误！');
    }


    // 添加用户
    // public function addUser(Request $request)
    // {
    //     // 获取全部提交数据
    //     $request_params_all_data = $request->all();

    //     // 拼接添加用户数据
    //     $add_user_data['nick_name'] = $request_params_all_data['nick_name'];
    //     $add_user_data['email'] = $request_params_all_data['email'];
    //     $add_user_data['avatar'] = $request_params_all_data['avatar'];
    //     $add_user_data['password'] = $request_params_all_data['password'];
    //     $add_user_data['confirm_password'] = $request_params_all_data['confirm_password'];
    //     $add_user_data['role'] = $request_params_all_data['role'];
    //     $add_user_data['is_enable'] = $request_params_all_data['is_enable'];

    //     // 添加用户  返回 0 空数据  true 成功  ， 错误消息或false 失败
    //     $add_user_result = UserService::addUser($add_user_data);

    //     // 成功情景
    //     if ($add_user_result === true) {
    //         sendMSG(200, $add_user_result, '添加成功！');
    //     }

    //     // 失败情景
    //     if ($add_user_result === false) {
    //         sendMSG(200, [], '失败，没有结果！');
    //     }

    //     // 提交空用户数据情景
    //     if ($add_user_result === 0) {
    //         sendErrorMSG(403, '提交空数据！');
    //     }
    //     // 用户数据没有通过校验情景
    //     if (is_string($add_user_result) && $add_user_result) {
    //         sendErrorMSG(403, $add_user_result);
    //     }
    // }




    // 添加或编辑用户  如果修改邮箱、昵称、密码中其一，那么退出登录、访问令牌和刷新令牌加入黑名单
    public function addOrEditArticle(AddOrEditArticleRequest $request)
    {

        $modular_name = '博文';
        // 获取全部提交数据
//         "article_id": 0,
//   "title": "205ba86950f4577eae53fcd4d872fda942e056607f01362f38847281f1ab6ea9205ba86950f4577eae53fcd4d872fda942e056607f01362f38847281f1ab6ea9",
//   "article_abstract": "摘要",
//   "cover": "",
//   "tag_ids": "20,25",
//   "tag_ids_names": "html,ts",
//   "author_name": "作者",
//   "menu_id": 2,
//   "article_content": "文章内容",
//   "is_pull": 0,
//   "action": "add",
        $request_params_all_data = $request->all();

        // 拼接添加用户数据

        $add_or_edit_article_data['article_id'] = $request_params_all_data['article_id'];
        $add_or_edit_article_data['title'] = $request_params_all_data['title'];
        $add_or_edit_article_data['article_abstract'] = $request_params_all_data['article_abstract'];
        $add_or_edit_article_data['cover'] = $request_params_all_data['cover'];
        $add_or_edit_article_data['tag_ids'] = $request_params_all_data['tag_ids'];
        $add_or_edit_article_data['author_name'] = $request_params_all_data['author_name'];
        $add_or_edit_article_data['menu_id'] = $request_params_all_data['menu_id'];
        $add_or_edit_article_data['article_content'] = $request_params_all_data['article_content'];

        $add_or_edit_article_data['is_pull'] = $request_params_all_data['is_pull'];

        //执行添加
        if ($request_params_all_data['action'] === 'add') {
            // 添加  返回  true 成功  ， 错误消息或false 失败
            $add_or_edit_user_result = UserService::addUser($add_or_edit_article_data);
        }

        //执行编辑
        if ($request_params_all_data['action'] === 'edit') {
            
                 // 使用Request实例的header方法获取Authorization标头
                 $authorizationHeader = $request->header('Authorization');

                 // $authorizationHeader =$request->input('temporary_token');
     
                 // 假设你从HTTP头部获取了Authorization头部
                 // echo 'authorizationHeader:'.$authorizationHeader;
                 // 解析Authorization头部，获取token
                 if ($authorizationHeader) {
                     // 假设token前缀是Bearer
                     $token_data = trim(str_ireplace('Bearer ', '', $authorizationHeader));
                     $add_or_edit_article_data['user_id'] = $request_params_all_data['user_id'];
                     // 添加  返回  true 成功  ， 错误消息或false 失败
                     $add_or_edit_user_result = UserService::editUser($add_or_edit_article_data,$token_data);
                    }
                    sendErrorMSG(403, '没有访问令牌！');

           
        }

        // 成功情景
        if (is_array($add_or_edit_user_result)  || $add_or_edit_user_result===true ) {
            sendMSG(200, $add_or_edit_user_result, $request_params_all_data['action'] . $modular_name . '成功！');
        }

        // 失败情景
        if ($add_or_edit_user_result === false) {
            sendMSG(200, [], $request_params_all_data['action'] . $modular_name . '失败，没有结果！');
        }

        // 空数据情景
        if ($add_or_edit_user_result === 0) {
            sendErrorMSG(403, $request_params_all_data['action'] . $modular_name . '数据异常！');
        }
        // 数据没有通过校验情景
        if (is_string($add_or_edit_user_result) && $add_or_edit_user_result) {
            sendErrorMSG(403, $add_or_edit_user_result);
        }
    }



    // 其他路由方法
}
