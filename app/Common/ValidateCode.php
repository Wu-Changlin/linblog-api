<?php

namespace App\Common;

class ValidateCode
{
  //图片对象、宽度、高度、验证码长度
  private $im;
  private $im_width;
  private $im_height;
  private $len;
  //随机字符串、y轴坐标值、随机颜色
  private $rand_num;
  private $y;
  private $rand_color;
  //背景色的红绿蓝，默认是浅灰色
  public $red = 238;
  public $green = 238;
  public $blue = 238;
  /**

   * 可选设置：验证码类型、干扰点、干扰线、Y轴随机
   * 设为 false 表示不启用
   **/
  //默认是大小写数字混合型，1 2 3 分别表示 小写、大写、数字型
  public $ext_num_type = '';
  public $ext_pixel = false; //干扰点
  public $ext_line = false; //干扰线
  public $ext_rand_y = true; //Y轴随机
  function __construct($len = 4, $im_width = 80, $im_height = 25)
  {
    // 验证码长度、图片宽度、高度是实例化类时必需的数据

    $this->len = $len;
    $im_width = $len * 15;
    $this->im_width = $im_width;
    $this->im_height = $im_height;
    $this->im = imagecreate($im_width, $im_height);
  }
  // 设置图片背景颜色，默认是浅灰色背景
  function setBgColor()
  {
    imagecolorallocate($this->im, $this->red, $this->green, $this->blue);
  }
  // 获得任意位数的随机码
  function getRandNum()
  {
    $an1 = 'abcdefghijklmnopqrstuvwxyz';
    $an2 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $an3 = '0123456789';
    if ($this->ext_num_type == '') $str = $an1 . $an2 . $an3;
    if ($this->ext_num_type == 1) $str = $an1;
    if ($this->ext_num_type == 2) $str = $an2;
    if ($this->ext_num_type == 3) $str = $an3;
    $rand_num = '';
    for ($i = 0; $i < $this->len; $i++) {
      // $rand_num .= substr($str,(rand()%(strlen($str))),1);
      $start = rand(1, strlen($str) - 1);
      $rand_num .= substr($str, $start, 1);
    }
    $this->rand_num = $rand_num;
    // $_SESSION['an'] = $this->rand_num;
  }
  // 获得验证码图片Y轴
  function get_y()
  {
    if ($this->ext_rand_y) $this->y = rand(5, $this->im_height / 5);
    else $this->y = $this->im_height / 4;
  }
  // 获得随机色
  function getRandColor()
  {
    $this->rand_color = imagecolorallocate($this->im, rand(0, 100), rand(0, 150), rand(0, 200));
  }
  // 添加干扰点
  function setExtPixel()
  {
    if ($this->ext_pixel) {
      for ($i = 0; $i < 100; $i++) {
        $this->getRandColor();
        imagesetpixel($this->im, rand() % 100, rand() % 100, $this->rand_color);
      }
    }
  }
  // 添加干扰线
  function setExtLine()
  {
    if ($this->ext_line) {
      for ($j = 0; $j < 2; $j++) {
        $rand_x = rand(2, $this->im_width);
        $rand_y = rand(2, $this->im_height);
        $rand_x2 = rand(2, $this->im_width);
        $rand_y2 = rand(2, $this->im_height);
        $this->getRandColor();
        imageline($this->im, $rand_x, $rand_y, $rand_x2, $rand_y2, $this->rand_color);
      }
    }
  }
  /**创建验证码图像：

   * 建立画布（__construct函数）
   * 设置画布背景（$this->setBgColor();）
   * 获取随机字符串（$this->getRandNum();）
   * 文字写到图片上（imagestring函数）
   * 添加干扰点/线（$this->setExtLine(); $this->setExtPixel();）
   * 输出图片
   **/
  function create()
  {
    $this->setBgColor();
    $this->getRandNum();
    for ($i = 0; $i < $this->len; $i++) {
      $font = rand(4, 6);
      $x = $i / $this->len * $this->im_width + rand(1, $this->len);
      $this->get_y();
      $this->getRandColor();
      imagestring($this->im, $font, $x, $this->y, substr($this->rand_num, $i, 1), $this->rand_color);
    }
    $this->setExtLine();
    $this->setExtPixel();
    //  UTF-8 编码
    header('content-type:application/json;charset=utf8');
    header("content-type:image/png");
    // 将生成的图片转换为Base64
ob_start();
    imagepng($this->im);
    


$image_data = ob_get_clean();
$base64_image = base64_encode($image_data);
imagedestroy($this->im); //释放图像资源
// 输出验证码图片Base64编码的字符串和验证码
$data=[
  "validate_code_path"=>'data:image/png;base64,'.$base64_image,
  "validate_code"=>$this->rand_num

];
return  $data;

    // return $this->rand_num;
  }/*  */
}


