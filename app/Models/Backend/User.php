<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{

    protected $primaryKey = 'user_id'; //创建的表字段中主键ID的名称不为id，则需要通过 $primaryKey 来指定一下设定主键id
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
     * 获取当前用户信息
     * @param $data 查询条件  
     * @return array 用户信息
     */
    public static function getCurrentUserInfo($data){
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;

        // 初始化查询条件
        $where_data = [];

        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$nick_name_where];
        }

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }


        // 当$allow_data['user_id'] 已定义，且 $allow_data['user_id']不为空时，进入 true 分支
        if (isset($allow_data['user_id']) && !empty($allow_data['user_id'])) {
            $email_where = ['user_id', '=',  $allow_data['user_id']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }
       
        $user_res = self::where($where_data)->select('user_id','nick_name','email','email_verification_code','role','account_status','login_ip','is_enable','is_logged_in','last_login_time');
        
      
        if ($user_res) {
            // 使用 get 方法来获取结果
            $results = $user_res->get();
           
            // 获取多维数组结果 
            $multidimensional_array=$results->toArray();
           
            // 多维数组扁平为一维
            $results_array=flattenArray($multidimensional_array);
          
            return $results_array;
        }

        return false;

    }




    /**
     * 用户登录
     * @param $data 登录数据  
     * @return bool is_logged_in  true 是， false 否
     */
    public static function userLogin($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $allow_data = $data;
        // 初始化查询条件
        $where_data = [];

        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$nick_name_where];
        }
     

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }


        // first()方法用于获取满足条件的第一条记录。如果查询结果为空，first()方法会返回null而不是一个空的实例对象。
        // 这意味着如果数据库中没有找到匹配的记录，$res变量将不会被赋予任何值，而是保持为null。
        // 有值返回实例对象‌Eloquent模型实例‌：如果查询成功找到匹配的记录，
        // first()方法会返回一个Eloquent模型实例。例如，如果查询的是用户表，返回的将是一个User模型的实例。
        $user_eloquent = self::where($where_data)->first(); //根据用户输入邮箱查询数据库管理员信息
        
        if (empty($user_eloquent)) {
            return false;
        }

       
        //更新 最近登录时间、登录IP、是否登录标志
        $user_eloquent->last_login_time = $allow_data['last_login_time'];
        $user_eloquent->login_ip = $allow_data['login_ip'];
        $user_eloquent->is_logged_in = 1;
        $user_eloquent->save();
        //本次登录信息写入log
        // self::addAadminLog(6,4,$admin_users->login_number,date('Y-m-d H:i:s', time()));
        //登录成功状态
        return true;
    }


    /**
     * 获取用户登录状态 
     * @param $data 查询数据
     * @return bool is_logged_in  true 是， false 否
     */
    public static function getUserLoginStatus($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;


        // 初始化查询条件
        $where_data = [];

        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$nick_name_where];
        }

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }


        $is_logged_in_res = self::where($where_data)->select('is_logged_in');
        if ($is_logged_in_res && $is_logged_in_res === 1) {
            return true;
        }

        return false;
    }

    /**
     *  验证账号
     * email
     * password
     * account_status 账号状态 0：默认，1：正常，2：获取过多验证码锁定，3：多次输入错误密码锁定，4：销号',
     * is_enable  '是否启用	0：默认， 1： 是 	 ，2：否',
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function verifyAccount($data)
    {

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }

        $allow_data = $data;

        // 初始化查询条件  多条件查询？
        $where_data = [];

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }

        // 当$allow_data['password'] 已定义，且 $allow_data['password']不为空时，进入 true 分支
        if (isset($allow_data['password']) && !empty($allow_data['password'])) {
            $email_where = ['password', '=',  $allow_data['password']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }

        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$nick_name_where];
        }




        // 查询字段account_status,is_enable
        $res = self::where($where_data)->select('account_status,is_enable');
        // 如果账号状态和启用状态的值都是是1，那么返true
        if ($res['account_status'] === 1 && $res['is_enable'] === 1) {
            return true;
        }

        return false;
    }


    /**
     *  判断该昵称用户或邮箱用户是否存在
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function isNickNameOrEmailUserExist($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;
    
        // 初始化查询条件  多条件查询？
        $where_data = [];
        

         // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
         if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data = [$email_where];
        }


        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data = [$nick_name_where];
        }


        $is_nick_name_res = self::where($where_data)->select('user_id');
        if ($is_nick_name_res) {
            return true;
        }

        return false;
    }




    /**
     * 新增用户
     * @param $data 用户数据
     * @return int 0：$data为空，1：email重复，2.nick_name重复，3：成功新增，4.失败
     */
    public static function addUser($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return false;
        }
        $allow_data = $data;
        
        // 判断该昵称用户或邮箱用户是否存在   true 是， false 否
        $is_nick_name_user_exist_result = self::isNickNameOrEmailUserExist($allow_data['email']);
        
        if($is_nick_name_user_exist_result) {
        
            return '请勿重复添加！';
    
        }


        // -email重复性验证
        $email = $allow_data['email'];
        $where = [['email', '=',  $email]];


        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_email_res = self::where($where)->select('user_id')->exists();

        if ($is_repeat_email_res) { //如果有数据说明email已注册
            return 'email已注册';
        }


        // nick_name重复性验证
        $nick_name = $allow_data['nick_name'];
        $where = [['nick_name', '=',  $nick_name]];

        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_nick_name_res = self::where($where)->select('user_id')->exists();
        if ($is_repeat_nick_name_res) { //如果有数据说明nick_name已存在
            return 'nick_name已存在';
        }

        $res = self::create($allow_data); //使用create方法新增用户
        // 添加成功
        if ($res) {
            return true;
        }

        return false;
    }
}
