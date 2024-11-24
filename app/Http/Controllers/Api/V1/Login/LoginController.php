<?php
namespace App\Http\Controllers\Api\V1\Login;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;

use App\Services\UserService;
use App\Services\VerificationCodeService;
use App\Services\JsonWebTokenService;


use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
/**
 * Class LoginController  博客后台登录和退出   保证用户信息安全用psot请求
 * @package App\Http\Controllers\Api\V1\Login\LoginController 
 */

class LoginController extends Controller
{

        /**
     *获取验证码
     */
    public function getVerificationCode(){
        // $this->senderr('你好',200);




    $verification_code_service_data= VerificationCodeService::index();
    echo '验证码：';
    var_dump($verification_code_service_data);
    echo '<img src="'.$verification_code_service_data["validate_code_path"].'" alt="Image" />';

    }


	//获取页面配置（如页面标题、页面关键词、页面描述、、网站log）

    public function getLoginPageData(){
        header('HTTP/1.0 9999 Unauthorized');
        // return response('Unauthenticated.', 401);
        sendErrorMSG(403,'令牌失效');
    }
 
    //去验证登录账号 redis 存储 邮箱验证码为键key,临时令牌 temporary_token（含有用户信息）为值
    //   返回临时令牌 temporary_token 和发送邮箱验证码  设置有效期5分钟
    public function goVerifyLoginAccount(){

        $temporary_token_payload = [
            'iat' => time(),// 签发时间
                'exp' => time() + 300, // 过期时间
                'iss' => 'linBlog',  // 签发者
                'aud' => 'nick_name', // 接收者
                'sub' => 'nick_name', // 用户标识
                'role' => 'user' // 用户角色
];



        $temporary_token=  JsonWebTokenService::generateTemporaryToken($temporary_token_payload);

        sendMSG('200',['temporary_token'=>$temporary_token],'成功');
    }

    


    /**
     *博客后台登录操作   (用户或管理员登录)
     * email_verification_code
     * post  $user_name 用户名称(用户邮箱)  $password 用户密码  $verification _code 登录验证码 动态生成
     */
    public function goLogin(Request $request)
    {

           // 获取请求参数的签名
           $request_params_email_verification_code = $request->input('email_verification_code');
           if(empty($request_params_email_verification_code)){
               sendErrorMSG(403,'空邮箱验证码');
           }

        $key = '344'; //key，唯一标识
        $time = time(); //当前时间
        $token = [
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
                'nick_name' => 'lin',
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
