<?php

// app/Services/UserService.php

namespace App\Services;
use App\Models\IpBlackList as IpBlackListModel;


class IpBlackListService
{

    //检查是否禁止IP
 public function isBanned($data)
{
    if(empty($data)){ //如果$data为空直接返回
        return 0;
    }
    
    $res=IpBlackListModel::getIpExistStatusByCondition($data); //执行查询;按条件获取ip存在状态；返回bool值

    return $res;


}

    //添加黑名单IP
    public function addBlackIp($data)
    {
        if(empty($data)){ //如果$data为空直接返回
            return 0;
        }
        $res=IpBlackListModel::addIpBlackList($data); //执行新增
    
        switch ($res) { //判断新增返回值
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