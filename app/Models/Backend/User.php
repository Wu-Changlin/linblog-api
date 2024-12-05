<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;


class User extends BaseModel
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
              //返回 有值paginate对象有查询结果，没有值paginate对象没有有查询结果
              $get_data_by_condition = self::where($where_data)
                  ->select('user_id', 'nick_name', 'avatar', 'email', 'is_enable', 'role', 'account_status', 'login_ip')
                  ->paginate($current_page_limit, ['*'], 'page', $current_page);
          }
          // 没有查询条件
          if (empty($where_data)) {
              //返回 有值paginate对象有查询结果，没有值paginate对象没有有查询结果
              $get_data_by_condition = self::select('user_id', 'nick_name', 'avatar', 'email', 'is_enable', 'role', 'account_status', 'login_ip')
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
            $where_data[] = $nick_name_where;
        }

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data[] =$email_where;
        }


        // 当$allow_data['user_id'] 已定义，且 $allow_data['user_id']不为空时，进入 true 分支
        if (isset($allow_data['user_id']) && !empty($allow_data['user_id'])) {
            $user_id_where = ['user_id', '=',  $allow_data['user_id']];

            // 将一个数组嵌套到另一个数组
            $where_data[]= $user_id_where;
        }
         //防止空条件导致结果返回空模型对象
         if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }

        $user_res = self::where($where_data)
        ->select('user_id','nick_name','email','email_verification_code','role','account_status','login_ip','is_enable','is_logged_in','last_login_time')
        ->first();
        
        if ($user_res) {
           
            // 获取集合转数组结果 
            $results_array=$user_res->toArray();
          
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
            $where_data []= $nick_name_where;
        }
     

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $email_where;
        }

 //防止空条件导致结果返回空模型对象
 if(self::isIssetOrEmptyWhereData($where_data)){
    return false;
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
            $where_data []= $nick_name_where;
        }

        // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
        if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $email_where;
        }
 //防止空条件导致结果返回空模型对象
 if(self::isIssetOrEmptyWhereData($where_data)){
    return false;
}
        $is_logged_in_res = self::where($where_data)->select('is_logged_in')->first();
        if ($is_logged_in_res && $is_logged_in_res->is_logged_in === 1) {
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
            $where_data[] = $email_where;
        }

        // 当$allow_data['password'] 已定义，且 $allow_data['password']不为空时，进入 true 分支
        if (isset($allow_data['password']) && !empty($allow_data['password'])) {
            $password_where = ['password', '=',  $allow_data['password']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $password_where;
        }

        // 登录使用校验邮箱和密码
        if (isset($allow_data['email']) && !empty($allow_data['email'])&& isset($allow_data['password']) && !empty($allow_data['password'])) {
            $login_verify_account_where[] = ['email', '=',  $allow_data['email']];
          
            // ->first查到数据返回Eloquent 对象，查不到返回null，所以不能直接->toArray()
            $login_verify_account_res = self::where($login_verify_account_where)->select('password')->first();
            
            if(empty($login_verify_account_res)){
                return false;
            }

            if($allow_data['password'] != $login_verify_account_res->password){
                return '密码错误！';
            }

            
       
        }

        
       
        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data []= $nick_name_where;
        }


      //防止空条件导致结果返回空模型对象
      if(self::isIssetOrEmptyWhereData($where_data)){
        return false;
    }

        // 查询字段account_status,is_enable
        $res = self::where($where_data)->select('account_status','is_enable')->first();

       
        // `account_status` '账号状态 0：默认，1：正常，2：获取过多验证码锁定，3：多次输入错误密码锁定，4：销号',

        // `is_enable` 是否启用	0：默认， 1： 是 ，2：否',

        // 如果账号状态和启用状态的值都是是1，那么返true
        if ($res->account_status === 1 && $res->is_enable === 1) {
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
        


        // 当$allow_data['user_id'] 已定义，且 $allow_data['user_id']不为空时，进入 true 分支
        if (isset($allow_data['user_id']) && !empty($allow_data['user_id'])) {
            $user_id_where = ['user_id', '=',  $allow_data['user_id']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $user_id_where;
        }

         // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
         if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $email_where;
        }


        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];
            // 将一个数组嵌套到另一个数组
            $where_data[] = $nick_name_where;
        }

    
        //防止空条件导致结果返回空模型对象
        if(self::isIssetOrEmptyWhereData($where_data)){
            return false;
        }

        $is_nick_name_res = self::where($where_data)->select('user_id')->first();
  
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

        $where_data=[];

          // 当$allow_data['email'] 已定义，且 $allow_data['email']不为空时，进入 true 分支
          if (isset($allow_data['email']) && !empty($allow_data['email'])) {
            $email_where = ['email', '=',  $allow_data['email']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $email_where;
            //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
            $is_repeat_email_res = self::where($where_data)->select('user_id')->exists();

            if ($is_repeat_email_res) { //如果有数据说明email已注册
                return 'email已注册';
            }
        }

        // nick_name重复性验证

        // 当$allow_data['nick_name'] 已定义，且 $allow_data['nick_name']不为空时，进入 true 分支
        if (isset($allow_data['nick_name']) && !empty($allow_data['nick_name'])) {
            $nick_name_where = ['nick_name', '=',  $allow_data['nick_name']];

            // 将一个数组嵌套到另一个数组
            $where_data[] = $nick_name_where;
            //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
            $is_repeat_nick_name_res = self::where($where_data)->select('user_id')->exists();

            if ($is_repeat_nick_name_res) { //如果有数据说明nick_name已存在
                return 'nick_name已存在';
            }
        }


        $res = self::create($allow_data); //使用create方法新增用户
        // 添加成功
        if ($res) {
            return true;
        }

        return false;
    }


     /**
     * 编辑用户
     * @param $data 用户数据
     * @return int 0：$data为空，true：成功编辑，false.失败
     */
    public static function editUser($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;
        
        // 判断用户数据是否存在
       $is_menu_data_exist_res=self::isNickNameOrEmailUserExist(['user_id'=>$allow_data['user_id']]);
       
// 如果不存在，那么直接返回false
        if (empty($is_menu_data_exist_res)) {
            return '没有该用户数据!';
        }

        //查询该id信息
        // 获取数组的所有键
        $select_keys_array = array_keys($allow_data);
        $current_id_res = self::find($allow_data['user_id'],$select_keys_array); 
      
        $current_id_info=$current_id_res->toArray(); //集合转数组

        //判断字段是否需要修改
        $edit_info = array_diff_assoc($allow_data,$current_id_info); //1:返回[]空数组，说明2个数组相同 2:返回非空数组（数据相同字段已去除，剩下需要修改的字段数据），说明$data数据和数据库数据不一致，需要执行修改
        if (!$edit_info) {//空数组说明没有修改字段值，返回1
            return '已修改数据,请勿重复操作!';
        }


//使用update方法更新数据 ,update 方法需要一个表示应该更新的列的列和值对数组。 update 方法返回受影响的行数。
        $edit_res=self::where('user_id',$allow_data['user_id'])->update($allow_data);

        // 编辑成功
        if ($edit_res) {
            // 如果修改邮箱、昵称、密码中其一，那么退出登录、访问令牌和刷新令牌加入黑名单
            return true;
        }

        return false;
    }





}
