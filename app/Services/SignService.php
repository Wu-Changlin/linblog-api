<?php

// app/Services/UserService.php

namespace App\Services;

use App\Services\LunarService;

//获取实现签名后数据，返回参数对象。
/*
顺序：

    1.参数名转大写字符，排序并转换为url查询字符串；（这里需要注意的是所有。对所有待签名参数按照字段名的ASCII 码从小到大排序后）
    2.data字符串转bs64； 
    3.base64转64卦；
    4.64卦转二进制；
    5.64卦二进制位反 1变0，0变1；
    6.用key=secret加密二进制位反数据生成sign（十进制数字数组）；
        循环十进制数字数组转换为十六进制字符串；
        十六进制字符串生成散列值。
*/

class SignService
{
    #六十四卦集合
    private static  $trigrams_collection = ['0' => '小过', '1' => '渐', '2' => '归妹', '3' => '丰', '4' => '旅', '5' => '巽', '6' => '兑', '7' => '涣', '8' => '节', '9' => '中孚', 'A' => '乾', 'B' => '坤', 'C' => '屯', 'D' => '蒙', 'E' => '需', 'F' => '讼', 'G' => '师', 'H' => '比', 'I' => '小畜', 'J' => '履', 'K' => '泰', 'L' => '否', 'M' => '同人', 'N' => '大有', 'O' => '谦', 'P' => '豫', 'Q' => '随', 'R' => '蛊', 'S' => '临', 'T' => '观', 'U' => '噬嗑', 'V' => '贲', 'W' => '剥', 'X' => '复', 'Y' => '无妄', 'Z' => '大畜', 'a' => '颐', 'b' => '大过', 'c' => '坎', 'd' => '离', 'e' => '咸', 'f' => '恒', 'g' => '遁', 'h' => '大壮', 'i' => '晋', 'j' => '明夷', 'k' => '家人', 'l' => '睽', 'm' => '蹇', 'n' => '解', 'o' => '损', 'p' => '益', 'q' => '夬', 'r' => '姤', 's' => '萃', 't' => '升', 'u' => '困', 'v' => '井', 'w' => '革', 'x' => '鼎', 'y' => '震', 'z' => '艮', '/' => '未济', '+' => '既济', '=' => '乾'];

    # 六十四卦二进制集合
    private static $trigrams_binary_collection = ['坤' => '000000', '剥' => '000001', '比' => '000010', '观' => '000011', '豫' => '000100', '晋' => '000101', '萃' => '000110', '否' => '000111', '谦' => '001000', '艮' => '001001', '蹇' => '001010', '渐' => '001011', '小过' => '001100', '旅' => '001101', '咸' => '001110', '遁' => '001111', '师' => '010000', '蒙' => '010001', '坎' => '010010', '涣' => '010011', '解' => '010100', '未济' => '010101', '困' => '010110', '讼' => '010111', '升' => '011000', '蛊' => '011001', '井' => '011010', '巽' => '011011', '恒' => '011100', '鼎' => '011101', '大过' => '011110', '姤' => '011111', '复' => '100000', '颐' => '100001', '屯' => '100010', '益' => '100011', '震' => '100100', '噬嗑' => '100101', '随' => '100110', '无妄' => '100111', '明夷' => '101000', '贲' => '101001', '既济' => '101010', '家人' => '101011', '丰' => '101100', '离' => '101101', '革' => '101110', '同人' => '101111', '临' => '110000', '损' => '110001', '节' => '110010', '中孚' => '110011', '归妹' => '110100', '睽' => '110101', '兑' => '110110', '履' => '110111', '泰' => '111000', '大畜' => '111001', '需' => '111010', '小畜' => '111011', '大壮' => '111100', '大有' => '111101', '夬' => '111110', '乾' => '111111'];


    /**
     * 获取签名
     */
    public static function getSign($param)
    {


        // 删除sign字段
        unset($param['sign']);
        // // role字段不参与签名
        // unset($param['role']);

        $timestamp = $param['timestamp'];

        /* 1.参数名转大写字符，排序并转换为url查询字符串 */
        $tmp = [];
        foreach ($param as $k => $v) {
            $tmp[$k] = $v;
        }
        // 使用array_change_key_case函数将数组的键转换为大写
        $tmp = array_change_key_case($tmp, CASE_UPPER);
        ksort($tmp);
        $key_tmp = [];
        foreach ($tmp as $k => $v) {
            $key_tmp[] = $k . '=' . $v;
        }

     
        $data_str = implode('&', $key_tmp);
        // echo  '换为大写-data_str:'.$data_str;

        /* 2.data字符串转bs64 */
        // 使用 mb_convert_encoding 函数转换为 UTF-8
        $data_str_to_utf8 = mb_convert_encoding($data_str, 'UTF-8');
        // 转base64
        $data_base64_str = base64_encode($data_str_to_utf8);
        // echo "</br>";
        // echo  'data字符串转bs64-data_base64_str:'.$data_base64_str;

        /* 3.base64转64卦 */
        $data_base64_to_trigrams = self::replaceMultiple($data_base64_str, self::$trigrams_collection);
        // echo "</br>";
        // echo 'base64转64卦-data_base64_to_trigrams:'.$data_base64_to_trigrams;
   
        /* 4.64卦转二进制  */
        $data_to_trigrams_binary = self::replaceMultiple($data_base64_to_trigrams, self::$trigrams_binary_collection);
        // echo "</br>";
        // echo '64卦转二进制-data_to_trigrams_binary:'.$data_to_trigrams_binary;

        /* 5.64卦二进制位反 1变0，0变1 */

        $replacement = array('1' => '0', '0' => '1');

        $data_trigrams_binary_to_bitwiseNot = strtr($data_to_trigrams_binary, $replacement);
        // echo "</br>";
        // echo '二进制位反-data_trigrams_binary_to_bitwiseNot:'.$data_trigrams_binary_to_bitwiseNot;

        $secret_key = self::createSecret($timestamp);
        //    使用empty()函数来检查变量是否为空值，这个函数会认为空字符串、0、"0"、null、false、undefined、空数组都是空的。
        if (empty($secret_key)) {
            return 0;
        }

        /* 6. 生成签名 使用HMAC生成SHA-256哈希值的函数 */
        $sign_str = hash('sha256', hash_hmac('sha256', $data_trigrams_binary_to_bitwiseNot, $secret_key));

        // 返回签名字符串
        return  $sign_str;
    }

    /**
     * 生成本地密钥
     * 它接受一个字符串作为参数，然后将其分割成单个字符，为每个字符获取ASCII值，
     * 将ASCII值转换为二进制字符串，并用空格连接起来返回。
     * @return {字符串}
     */
    public static function createSecret($timestamp)
    {

        $lunar_year_month_day_hour_number = LunarService::convertTimestampToLunar($timestamp);
        // $lunar_year_month_day_hour_Number:4 [ 
        //     "lunar_year" => 8
        //     "lunar_month" => 10
        //     "lunar_day" => 22.0
        //     "lunar_hour" => 12
        //   ]

        // dd($lunar_year_month_day_hour_number);

        //    使用empty()函数来检查变量是否为空值，这个函数会认为空字符串、0、"0"、null、false、undefined、空数组都是空的。

        if (empty($lunar_year_month_day_hour_number)) {
            return 0;
        }


        /* 
     lunarYear: ly,lunarMonth: lm,lunarDay: ld,lunarHours: lh
     以年月日数之和除以8，余数为上卦；
     以年月日时数之和除以8，余数为下卦；
     以年月日时数之和除以6，余数取动爻。
     */
        $up_number_trigram = ($lunar_year_month_day_hour_number['lunar_year'] + $lunar_year_month_day_hour_number['lunar_month'] + $lunar_year_month_day_hour_number['lunar_day']) % 8;
        $down_number_trigram = ($lunar_year_month_day_hour_number['lunar_year'] + $lunar_year_month_day_hour_number['lunar_month'] + $lunar_year_month_day_hour_number['lunar_day'] + $lunar_year_month_day_hour_number['lunar_hour']) % 8;
        $update_number = ($lunar_year_month_day_hour_number['lunar_year'] + $lunar_year_month_day_hour_number['lunar_month'] + $lunar_year_month_day_hour_number['lunar_day'] + $lunar_year_month_day_hour_number['lunar_hour']) % 6;

        // 加密，拼接时间戳、上卦、下卦、动爻字符串值。
        $lunar_number_or_timestamp_to_string = $timestamp . $up_number_trigram . $down_number_trigram . $update_number;

        //每个字符获取ASCII值并获取二进制值
        $lunar_number_or_timestamp_string_to_bit = '';
        for ($i = 0; $i < strlen($lunar_number_or_timestamp_to_string); $i++) {
            $ascii = ord($lunar_number_or_timestamp_to_string[$i]);
            $lunar_number_or_timestamp_string_to_bit .= decbin($ascii);
        }

        //获取二进制值按位反 1变0，0变1

        $replacement = array('1' => '0', '0' => '1');

        $lunar_number_or_timestamp_string_bit_to_bitwiseNot = strtr($lunar_number_or_timestamp_string_to_bit, $replacement);

        // $lunar_number_or_timestamp_string_bit_to_bitwiseNot = self::replaceMultiple($lunar_number_or_timestamp_string_to_bit,self::$bitwise_not);

        // 哈希  256
        $secret_str = hash('sha256', $lunar_number_or_timestamp_string_bit_to_bitwiseNot);

        return $secret_str;
    }

    // replace替换文本中的多个字符，批量正则替换字符为对象属性
    public static  function replaceMultiple($str, $replacements)
    {

        // str_replace() 函数以其他字符替换字符串中的一些字符（区分大小写）。
        // str_replace(find,replace,string,count)
        // 返回带有替换值的字符串或数组。

        // // 该函数区分大小写。请使用 str_ireplace() 函数执行不区分大小写的搜索。

        // // 获取所有键
        // $keys = array_keys($replacements);

        // // 将键作为值创建新数组
        // $find_data_array = array_values($keys);
        // // 将值作为值创建新数组
        // $replace_data_array=array_values($replacements);

        // // var_dump('find_data_array',$find_data_array);
        // // var_dump('replace_data_array',$replace_data_array);

        // $result = str_replace($find_data_array, $replace_data_array,$str);
// strtr(string, from, to)  或  strtr(string, array)

        $result =  strtr($str, $replacements);

        return $result;
    }


    // 其他用户相关的服务方法
}
