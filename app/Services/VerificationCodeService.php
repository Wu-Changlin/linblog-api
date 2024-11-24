<?php

// app/Services/UserService.php

namespace App\Services;
use \App\Common\ValidateCode;


class VerificationCodeService
{

    public static function index()
{

    /**使用验证码类的方法：
 * $an = new ValidateCode(验证码长度,图片宽度,图片高度);
 * 实例化时不带参数则默认是四位的60*25尺寸的常规验证码图片
 * 表单页面检测验证码的方法，对比 $_SESSION[an] 是否等于 $_POST[验证码文本框ID]
 * 可选配置：
 * 1.验证码类型：$an->ext_num_type=1; 值为1是小写类型，2是大写类型，3是数字类型
 * 2.干扰点：$an->ext_pixel = false; 值为false表示不添加干扰点
 * 3.干扰线：$an->ext_line = false; 值为false表示不添加干扰线
 * 4.Y轴随机：$an->ext_rand_y = false; 值为false表示不支持图片Y轴随机
 * 5.图片背景：改变 $red $green $blue 三个成员变量的值即可
 **/
$an=new ValidateCode (6,200,35);
// $an->ext_num_type='';
$an->ext_pixel = true; //干扰点
$an->ext_line = false; //干扰线
$an->ext_rand_y= true; //Y轴随机
$random_number_array =rgbRandomNumbers();
$an->red = $random_number_array[0];
$an->green = $random_number_array[1];
$an->blue = $random_number_array[2];



$verification_code_data=$an->create();
return $verification_code_data;




}

public static function create($data)
{

}



// 其他用户相关的服务方法
}