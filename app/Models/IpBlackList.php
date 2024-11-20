<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class IpBlackList extends Model
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
     * 按条件获取ip存在状态 
     * @param $data IP数据
     * @return bool 存在  true 是， false 否
     */
    public static function getIpExistStatusByCondition($data){
        if(empty($data)){ //如果$data为空直接返回
            return 0;
        }
        $allow_data= $data;
        // -Email重复性验证
        $ip = $allow_data['ip_address'];
        $where= [['ip_address','=',  $ip]];
        // if (!empty($data['id'])) {
        //     $where['id'] = ['<>', $data['id']];
        // }

    
        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的
        $check_ip_res = self::where($where)->select('ip_black_list_id')->exists();
        if($check_ip_res){
            return true;
        }
        
        return false;
    }


    
    /**
     * 新增黑名单IP
     * @param $data IP数据
     * @return int 0：$data为空，1：黑名单IP已存在，2：成功新增黑名单IP
     */
    public static function addIpBlackList($data) {
        if(empty($data)){ //如果$data为空直接返回
            return 0;
        }
        $allow_data = $data;
    
        // -Email重复性验证
        $ip = $allow_data['ip_address'];
        $where= ['ip_address','=',  $ip];
        // if (!empty($data['id'])) {
        //     $where['id'] = ['<>', $data['id']];
        // }
    
        //优化mysql查询,如果只是判断数据是否存在,用exists查询并只返回id是最快的？
        $select_ip_res = self::where($where)->select('ip_black_list_id')->exists();
        // $ip_count = self::where('ip_address',$data['ip'])->count(); //根据$data查询数据库黑名单表的ip_address
        if($select_ip_res){//如果有数据说明邮箱已注册
            return 1;
        }



        $res=self::create($allow_data);//使用create方法新增黑名单IP
    
        //本次新增黑名单IP信息写入log
        // self::addAadminLog(6,2,$res->admin_id,$res->created_at);
        return 2;
    }

}
