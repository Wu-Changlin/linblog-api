<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use App\Services\Backend\MenuService;
use Illuminate\Http\Request;

use App\Http\Requests\Backend\AddOrEditMenuRequest;


// 菜单模块
class MenuController extends Controller
{
    // protected $userService;

    // public function __construct()
    // {
    
    //     $this->userService = new UserService(); 
        
    // }


       //获取log和菜单导航栏   // 获取网站配置（如网站标题、网站关键词、网站描述、网站log）
       public function getAdminAndMenuListData(Request $request){
        // $request_params_all_data = $request->all();
        sendMSG('200', [], 'getAdminAndMenuListData');

        // "log_data":
        // "menu_data"
    }


    // 添加或编辑菜单
    public function addOrEditMenu(AddOrEditMenuRequest $request){

        $modular_name='菜单';
           // 获取全部提交数据
        $request_params_all_data = $request->all();

    //     "menu_id": 0,
    // "menu_name": "menu",
    // "menu_title": "菜单管理",
    // "menu_path": "/menu",
    // "icon": "menus",
    // "business_level": 2,
    // "parent_id": 0,
    // "is_pulled": 0,
    // "action": "add",


        // 拼接添加菜单数据
       
       $add_or_edit_menu_data['menu_name']=$request_params_all_data['menu_name'];
       $add_or_edit_menu_data['menu_title']=$request_params_all_data['menu_title'];
       $add_or_edit_menu_data['menu_path']=$request_params_all_data['menu_path'];
       $add_or_edit_menu_data['icon']=$request_params_all_data['icon'];
       $add_or_edit_menu_data['business_level']=$request_params_all_data['business_level'];
       $add_or_edit_menu_data['parent_id']=$request_params_all_data['parent_id'];
       $add_or_edit_menu_data['is_pulled']=$request_params_all_data['is_pulled'];

        // 当$add_or_edit_menu_data['menu_keywords'] 已定义，且 $add_or_edit_menu_data['menu_keywords']不为空时，进入 true 分支
        if (isset($add_or_edit_menu_data['menu_keywords']) && !empty($add_or_edit_menu_data['menu_keywords'])) {
            $add_or_edit_menu_data['menu_keywords']=$request_params_all_data['menu_keywords'];
        }

         // 当$add_or_edit_menu_data['menu_description'] 已定义，且 $add_or_edit_menu_data['menu_description']不为空时，进入 true 分支
         if (isset($add_or_edit_menu_data['menu_description']) && !empty($add_or_edit_menu_data['menu_description'])) {
       $add_or_edit_menu_data['menu_description']=$request_params_all_data['menu_description'];
           
        }
       


    /* 根目录路径  删除前缀斜杠 开始*/
    //    使用substr_count()函数来计算字符串中特定子字符串出现的次数。对于寻找斜杠/
    $count = substr_count($add_or_edit_menu_data['menu_path'], "/");

    if($count===1){
        $add_or_edit_menu_data['menu_path']= substr($add_or_edit_menu_data['menu_path'], 1); // 从第二个字符开始切割到末尾
    }
    /* 根目录路径  删除前缀斜杠 结束*/

        // 添加  返回  true 成功  ， 错误消息或false 失败

         //执行添加
         if($request_params_all_data['action']==='add'){
            $add_or_edit_menu_result = MenuService::addMenu($add_or_edit_menu_data); 

        }

        //执行编辑
        if($request_params_all_data['action']==='edit'){
            $add_or_edit_menu_data['menu_id']=$request_params_all_data['menu_id'];
            $add_or_edit_menu_result = MenuService::editMenu($add_or_edit_menu_data); 
        }

        


        // 成功情景
        if($add_or_edit_menu_result===true){
            sendMSG(200, $add_or_edit_menu_result,$request_params_all_data['action'].$modular_name.'成功！');
        }

        // 空数据情景
        if(empty($add_or_edit_menu_result)){
            sendErrorMSG(403,$request_params_all_data['action'].$modular_name.'数据异常！');
        }
        // 数据没有通过校验情景
        if(is_string($add_or_edit_menu_result) && $add_or_edit_menu_result){
            sendErrorMSG(403,$add_or_edit_menu_result);
        }

    }


   
    // 其他路由方法
}