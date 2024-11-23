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
