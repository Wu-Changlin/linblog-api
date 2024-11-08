<?php

// 3个颜色数字，范围： 0-255
function rgbRandomNumbers () {
    // 生成10个随机数的数组
$randomNumbers = [];
for ($i = 0; $i < 3; $i++) {
    // 使用 mt_rand() 生成一个更好的随机数
    $randomNumbers[] = mt_rand(0, 255);
}
 
// 输出结果
return $randomNumbers;
    
}

?>