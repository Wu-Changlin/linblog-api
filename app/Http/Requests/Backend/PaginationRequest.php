<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/* 公共校验分页提交参数 */
class PaginationRequest extends FormRequest
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
    // /^[1-9][0-9]*$/：表示至少有一个1-9的数字，后面可以跟任意个0-9的数字。
        $rules =  [
            'current_page' => 'required|regex:/^[1-9][0-9]*$/',
            'current_page_limit' => 'required|regex:/^[1-9][0-9]*$/',
        ];
        return $rules;
        // current_page: 1, //当前页数
        // current_page_limit: 10, // 每页显示个数选择器的选项设置

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'current_page.required'=>'页数不能为空',
            'current_page.regex'=>'页数格式错误',
            'current_page_limit.required'=>'每页显示个数不能为空',
            'current_page_limit.regex'=>'每页显示个数格式错误',
        ];

    }
}
