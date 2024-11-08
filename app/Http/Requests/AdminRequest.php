<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *  验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',// required 不能为空
            'name' => 'required',// required 不能为空
        ];

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'name.required'=>'昵称不能为空',
            'email.required'=>'邮箱不能为空',
            'email.email'=>'邮箱格式错误',
            'password.required'=>'密码不能为空',
        ];

    }
}
