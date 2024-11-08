<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class loginRequest extends FormRequest
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
            'email' => 'required|email', // required 不能为空，  email 邮箱格式 
//            'email' => 'required|email|unique:admins,email', // required 不能为空，  email 邮箱格式 ， unique:admins,email  数据唯一验证  admins表名email字段名

            'password' => 'required',// required 不能为空
        ];

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'email.required'=>'邮箱不能为空',
//            'email.unique'=>'邮箱已注册',
            'email.email'=>'邮箱格式错误',
            'password.required'=>'密码不能为空',
        ];

    }
}
