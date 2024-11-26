<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{

    protected $primaryKey = 'ip_black_list_id'; //创建的表字段中主键ID的名称不为id，则需要通过 $primaryKey 来指定一下设定主键id
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
        // -nick_name
        $nick_name = $allow_data['nick_name'];
        $where = [['nick_name', '=',  $nick_name]];
    

        $is_logged_in_res = self::where($where)->select('is_logged_in');
        if($is_logged_in_res && $is_logged_in_res===1) {
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
    public static function verifyAccount($data){

        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        
        $allow_data = $data;
    
        // 多条件查询
        $where = [
            ['email', '=',  $allow_data['email']],
            ['password', '=',  $allow_data['password']],
        ];
    

        // 查询字段account_status,is_enable
        $res = self::where($where)->select('account_status,is_enable');
        // 如果账号状态和启用状态的值都是是1，那么返true
        if($res['account_status']===1 && $res['is_enable']===1){
            return true;
        }

        return false;
    }
    

     /**
     *  判断该昵称用户是否存在
     * @param $data 查询数据
     * @return bool   true 是， false 否
     */
    public static function isNickNameUserExist($data)
    {
        if (empty($data)) { //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;
        // -nick_name
        $nick_name = $allow_data['nick_name'];
        $where = [['nick_name', '=',  $nick_name]];
    
        $is_nick_name_res = self::where($where)->select('is_logged_in');
        if($is_nick_name_res && $is_nick_name_res===1) {
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
            return 0;
        }
        $allow_data = $data;

        // -email重复性验证
        $email = $allow_data['email'];
        $where = ['email', '=',  $email];


        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_email_res = self::where($where)->select('ip_black_list_id')->exists();

        if ($is_repeat_email_res) { //如果有数据说明email已注册
            return 1;
        }

        // nick_name重复性验证
        $nick_name = $allow_data['nick_name'];
        $where = ['nick_name', '=',  $nick_name];
        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $is_repeat_nick_name_res = self::where($where)->select('ip_black_list_id')->exists();
        if ($is_repeat_nick_name_res) { //如果有数据说明nick_name已注册
            return 2;
        }

        $res = self::create($allow_data); //使用create方法新增用户
        // 添加成功
        if ($res) {
            return 3;
        }

        return 444;
    }
}
