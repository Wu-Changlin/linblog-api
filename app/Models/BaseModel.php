<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;



class BaseModel extends Model
{

    /**
     * 禁止被批量赋值的字段
     * @var array
     */
    protected $guarded = [];



    // /**
    //  * 记录操作
    //  * @param $exec_object 执行操作对象 0:默认 1：分类， 2：标签 ，3：文章，4：评论，5：网站配置 ， 6：管理员， 7：资源库，8：友链，9：权限，10：角色
    //  *@param $exec_type    执行操作类型1：删除， 2：添加， 3：修改， 4：登录， 5：退出，6：前台添加',  如果 $exec_type=4 或 $exec_type=5 那么执行操作对象id exec_object_id=登录次数
    //  * @param $exec_object_id   执行操作对象id    如果登录或退出 $exec_object_id=登录次数
    //  * @param $created_at       创建记录时间
    //  */
    // public static function addLog($exec_object,$exec_type,$exec_object_id,$created_at){
    //     $admin_user=session('admin_user');
    //     $admin_log['last_login_ip']=$admin_user['last_login_ip'];    //管理员IP
    //     $admin_log['admin_id']=$admin_user['admin_id'];  //管理员id
    //     $admin_log['exec_object']=$exec_object;
    //     $admin_log['exec_type']=$exec_type;
    //     $admin_log['exec_object_id']=$exec_object_id;
    //     $admin_log['created_at']=$created_at;//执行操作创建时间
    //     DB::table('admins_logs')->insert($admin_log);
    // }

    // /**
    //  * 删除图片
    //  * @param $path  图片地址
    //  * @param $num   图片地址数据类型  1： 字符串，2：数组
    //  */
    // public static  function  deletedCover($path,$num)
    // {
    //     if(!empty($path)){//判断是否有图片路径
    //         if($num==1){//图片路径是字符串
    //             if(file_exists(public_path().$path)){//如果存在图片路径
    //                 //echo '1'.public_path().$path."</br>";
    //                 unlink(public_path().$path); //删除资源分类图片
    //             }
    //         }elseif($num==2){//图片路径是数组
    //             $path=array_filter($path);//去除数组中空值的方法
    //             if(!empty($path)){//数组有值
    //                 foreach ($path as $k=>$v){
    //                     if(file_exists(public_path().$v)){
    //                         unlink(public_path().$v);
    //                         //echo '2'.public_path().$v."</br>";
    //                     }
    //                 }
    //             }else{
    //                 //echo '图片路径数组空';
    //             }
    //         }
    //     }else{
    //         // echo '图片路径不存在';
    //     }
    // }

    // public static function mate_is_verify ($num){
    //     $is_verify=[0=>'默认', 1=>'未通过',2=>'已通过',];
    //     return $is_verify[$num];

    // }

    // public static function mate_is_pull ($num){
    //     $is_pull=[0=>'默认', 1=>'是',2=>'否',3=>'已失效'];
    //     return $is_pull[$num];
    // }
}