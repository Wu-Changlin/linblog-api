<?php

namespace App\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class GetVerificationCodeRequest extends FormRequest
{

    /**
     * 表示验证器是否应在第一个规则失败时停止。
     *
     * @var bool
     */

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    // 当验证失败时自定义 JSON 响应可以通过重写 FormRequest 类中的 failedValidation 方法来实现。
    protected function failedValidation(Validator $validator)
    {
        //通过 Validator 实例调用 errors 方法，它会返回一个 Illuminate\Support\MessageBag 实例，
        // 该实例包含了各种可以很方便地处理错误信息的方法。
        // 并自动给所有视图提供 $errors 变量，也是 MessageBag 类的一个实例。

        // $errors = $validator->errors();
    
        // 使用 $validator->errors()->first() 来获取验证失败后的第一个错误信息。
        // 这个方法会返回错误信息集合中的第一条错误信息
        $first_error_msg=$validator->errors()->first();
        // 错误响应
        sendErrorMSG(422,  $first_error_msg);
    }


    /**
     * Get the validation rules that apply to the request.
     *  验证规则
     * @return array
     * 添加自定义规则
     */
    public function rules()
    {

    //required 不能为空 | 匹配字符串是否包含字母、数字
    // 正则表达式用于验证SHA-256输出格式的表达式为：^[0-9a-fA-F]{64}$‌。
    // 这个表达式确保字符串由64个十六进制字符组成，每个字符可以是0到9或a到f的大写或小写形式‌
        $rules =  [
            'email' => 'required|regex:/^\s*\w+(?:\.{0,1}[\w-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+\s*$/',
            'password' => 'required|regex:/^[0-9a-fA-F]{64}$/',
            'validate_code' => 'required|regex:/^[A-Za-z0-9]+$/',

        ];
        return $rules;

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'email.required'=>'邮箱不能为空',
            'email.regex'=>'邮箱格式错误',
            'password.required'=>'密码不能为空',
            'password.regex'=>'密码格式错误',
            'validate_code.required'=>'验证码不能为空',
            'validate_code.regex'=>'验证码格式错误',
        ];

    }
}
