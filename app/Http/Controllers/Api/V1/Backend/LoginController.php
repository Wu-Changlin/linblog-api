<?php
namespace App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;

use App\Services\UserService;
use App\Services\VerificationCodeService;


/**
 * Class LoginController  博客后台登录和退出   保证用户信息安全用psot请求
 * @package App\Http\Controllers\Api\V1\Backend\UserController 
 */

class LoginController extends Controller
{

    protected $userService; protected $verificationCodeService;

    public function __construct()
    {

        $this->userService = new UserService(); 
        $this->verificationCodeService = new VerificationCodeService(); 
    }

    /**
     *显示博客后台登录页面
     */
    public function index(){
       // $this->senderr('你好',200);
    echo 111;


    $verification_code_service_data= $this->verificationCodeService->index();

    echo '验证码：'.$verification_code_service_data['validate_code'];
    echo '<img src="'.$verification_code_service_data["validate_code_path"].'" alt="Image" />';

    }


    /**
     *获取验证码
     */
    public function getVerificationCode(){
        // $this->senderr('你好',200);
    echo "getVerificationCode:";
    $verification_code_service_data= $this->verificationCodeService->index();
    echo '验证码：'.$verification_code_service_data['validate_code'];
    echo '<img src="'.$verification_code_service_data["validate_code_path"].'" alt="Image" />';

    }



    /**
     *博客后台登录操作   (用户或管理员登录)
     * post  $user_name 用户名称(用户邮箱)  $password 用户密码  $verification _code 登录验证码 动态生成
     */
    public function logIn(loginRequest $request)
    {

        echo 111;


        // if ($request->isMethod('post')) {//判断请求方式是post
        //     $input = $request->except('s','_token'); //去除 s：路由地址 ，_token： 表单中包含一个隐藏的 CSRF 令牌字段
        //     $login['email'] = isset($input['email']) ? $input['email'] : "";
        //     $login['password'] = isset($input['password']) ? $input['password'] : "";

        // }else{
        //     return redirect()->back()->withInput()->with('err', '非法请求');
        // };

        // $res=AdminModels::adminLogin($login); //执行登录
         
     
      
        // switch ($res) { //判断登录返回值
        //     case 0:
        //         return redirect()->back()->withInput()->with('err', '用户不存在');
        //         break;
        //     case 1:
        //         return redirect()->back()->withInput()->with('err', '密码错误');
        //         break;
        //     case 2:
        //         return redirect()->route('admin.index')->with('msg', '登录成功');
        //         break;
        //     default:
        //         return redirect()->route('admin.index')->with('err', '网络错误');
        // }

    }




         // 创建允许的数据
        //  $allow_data = $this->create($data);

        //  // 常规验证
        //  $this->check($allow_data);
 
        //  // ---其它验证--------------
        //  // -Email重复性验证
        //  $email = $allow_data['email'];
        //  $where['email'] = ['eq', $email];
        //  if(!empty($data['id'])){
        //      $where['id'] = ['neq', $data['id']];
        //  }
 
        //  //优化mysql查询,如果只是判断数据是否存在,用getField查询并只返回id是最快的
        //  $administrator_Email = M('administrator')->where($where)->getField('id');
 
        //  if(!empty($administrator_Email)){
        //      JsonReturn::error('-4017');
        //  }
 
        //  $allow_data['status'] = (int)$allow_data['status'];
 
        //  //统一密码到Password表
        //  if(empty(PasswordService::getPasswordByEmail($email))){
        //      // 当还没有这个Email的统一密码时，创建一个
        //      $passwordService = new PasswordService();
        //      $generateService = new GenerateService();


}
