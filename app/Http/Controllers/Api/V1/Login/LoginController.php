<?php
namespace App\Http\Controllers\Api\V1\Login;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;

use App\Services\UserService;
use App\Services\VerificationCodeService;


use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
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
    // 使用HMAC生成SHA-256哈希值的函数


// 示例使用
$data = "1111000101000011001101110110011100110110100011000000100010110010000010110111101011110010100111001000011010110101100010010100001101011011011101010101100001010010100000101001100101100000100110100110010110101101000110011110111111000001010100110100001101100001010110110100001111001011010100110100001101100001010";
$secretKey = "7e0fa9e711273fab363634f348d936ba2e32a3148dd98567d52fe2a4c3cc008c";
$hash = hash('sha256', (hash_hmac('sha256', $data, $secretKey))) ;
echo $hash; // 输出哈希值

    }


    


    /**
     *获取验证码
     */
    public function getVerificationCode(){
        // $this->senderr('你好',200);


    sendErrorMSG('403Sign','');
    die;

    $verification_code_service_data= $this->verificationCodeService->index();
    echo '验证码：'.$verification_code_service_data['validate_code'];
    echo '<img src="'.$verification_code_service_data["validate_code_path"].'" alt="Image" />';

    }



    /**
     *博客后台登录操作   (用户或管理员登录)
     * post  $user_name 用户名称(用户邮箱)  $password 用户密码  $verification _code 登录验证码 动态生成
     */
    public function logIn(Request $request)
    {

        $key = '344'; //key，唯一标识
        $time = time(); //当前时间
        $token = [
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
                'device_id' => 'asdfghj',
            ]
        ];
        $token = JWT::encode($token, $key,'HS256'); //签发token
        $data = ['error'=>0,'mgs'=>'success','data'=>['token'=>$token]];
        return json_encode($data);
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
