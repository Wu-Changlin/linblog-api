<?php



namespace App\Services;
use App\Models\IpBlackList as IpBlackListModel;


class IpBlackListService
{

    //检查是否禁止IP
 public static function isBanned($data)
{
    if(empty($data)){ //如果$data为空直接返回
        return 0;
    }
    
    $is_banned_res=IpBlackListModel::getIpExistStatusByCondition($data); //执行查询;按条件获取ip存在状态；返回bool值

    return $is_banned_res;


}

    //添加黑名单IP
    public static function addBlackIp($data)
    {
        if(empty($data)){ //如果$data为空直接返回
            return 0;
        }
        $add_black_ip_res=IpBlackListModel::addIpBlackList($data); //执行新增
    
        switch ($add_black_ip_res) { //判断新增返回值
            case 0:
                return  '数据为空';
                break;
            case 1:
                return  '邮箱已注册';
                break;
            case 2:
                return  "新增管理员成功";
                break;
            default:
                return  '数据写入失败,新增管理员失败';
        }
    
    
    }



// 其他用户相关的服务方法
}