<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


   //  匹配角色数字转名称 
   //   `role` t'角色；0：默认，1：普通用户，2：管理员',
    public static function mate_role_number_to_name ($num){
      $is_verify=[0=>'default', 1=>'user',2=>'admin',];
      return $is_verify[$num];

  }


 public static function echoTest(){
    dd('BaseController->echoTest()');
 }

}
