<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

// 标签模块
class AddOrEditTagRequest extends FormRequest
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

    /* /^[\p{L}\p{Han}\p{P}\p{S}]+$/u 正则表达式规则
        数字: \d
        中文: \p{Han}
        字母: \p{L}
        半角全角符号: 这个比较复杂，可以参考正则表达式中的 \p{P} 来匹配任何标点符号，但这包括了下划线等常见符号
        特殊符号: \p{S}
        
        示例规则：至少1个中文或字母，可以包含数字、半角全角符号和特殊符号
    */    
    
    /*
只包含中文
                /^[\x{4e00}-\x{9fa5}]+$/u
    
    */
        $rules =  [
            'tag_id' => 'required|regex:/^[0-9]+$/',
            'menu_id' => 'required|regex:/^[0-9]+$/',

            'menu_title' => [
                'required',
                'max:100',
                'regex:/^[\x{4e00}-\x{9fa5}]+$/u'
            ],
            
            'tag_name' => [
                'required',
                'max:100',
                'regex:/^[\p{L}\p{Han}\p{P}\p{S}]+$/u'
            ],


            'tag_keywords' =>[
                'required',
                'max:100'
        ],

        'tag_description' =>[
                'required',
                'max:100'
            
        ],
            'is_pull' => 'required|regex:/^[0-9]+$/',
            'action' => ['required','regex:/^(add|edit)$/']
        ];
        return $rules;

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'tag_id.required'=>'标签id不能为空',
            'tag_id.regex'=>'标签id格式错误',

            'menu_id.required'=>'菜单id不能为空',
            'menu_id.regex'=>'菜单id格式错误',

            'menu_title.required'=>'菜单名称不能为空',
            'menu_title.max'=>'菜单名称过长',
            'menu_title.regex'=>'菜单名称格式错误',

            'tag_name.required'=>'标签名称不能为空',
            'tag_name.max'=>'标签过长',
            'tag_name.regex'=>'标签名称格式错误',

            'tag_keywords.required'=>'标签关键词不能为空',
            'tag_keywords.max'=>'标签关键词过长',

            'tag_description.required'=>'标签描述不能为空',
            'tag_description.max'=>'标签描述过长',

            'is_pull.required'=>'下架不能为空',
            'is_pull.required'=>'下架格式错误',

            'action.required'=>'操作代码不能为空',
            'action.regex'=>'操作代码格式错误',

        ];

    }
}
