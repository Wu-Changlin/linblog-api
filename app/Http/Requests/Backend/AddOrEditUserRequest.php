<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

// 用户模块
class AddOrEditMenuRequest extends FormRequest
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
    // 'regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/' // 正则表达式匹配汉字、字母、数字和下划线
        $rules =  [
            'user_id' => 'required|regex:/^[0-9]+$/',
            'nick_name' => 'required|max:10|regex:/^[a-z]+([_][a-z]+)?$/',
            'email' => 'required|regex:/^\s*\w+(?:\.{0,1}[\w-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+\s*$/',
            'password' => 'required|regex:/^[0-9a-fA-F]{64}$/',
            'confirm_password' =>  'required|regex:/^[0-9a-fA-F]{64}$/',
            'role' => 'required|regex:/^[0-9]+$/',
            'parent_id' => 'required|regex:/^[0-9]+$/',
            'is_enable' => 'required|regex:/^[0-9]+$/',
            'action' => 'required|regex:/^[a-z]+$/',
        ];
        return $rules;

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'user_id.required'=>'用户id不能为空',
            'user_id.regex'=>'用户id格式错误',

            'nick_name.required'=>'昵称不能为空',
            'nick_name.max'=>'昵称过长',
            'nick_name.regex'=>'昵称格式错误，示例：汉字、字母、数字和下划线',

            'email.required'=>'展示名称不能为空',
            'email.regex'=>'展示名称格式错误，请输入中文字符',

            'password.required'=>'密码不能为空',
            'password.regex'=>'密码格式错误,示例：64位包含a-F、0-9',

            'confirm_password.required'=>'确认密码不能为空',
            'confirm_password.regex'=>'确认密码格式错误',

            'role.required'=>'角色不能为空',
            'role.regex'=>'角色格式错误',

            'parent_id.required'=>'父节点不能为空',
            'parent_id.regex'=>'父节点码格式错误',

            'is_enable.required'=>'启用不能为空',

            'action.required'=>'操作代码不能为空',
            'action.regex'=>'操作代码格式错误',

        ];

    }
}
