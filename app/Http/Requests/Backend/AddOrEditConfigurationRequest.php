<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

// 配置模块
class AddOrEditConfigurationRequest extends FormRequest
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


        // "configuration_id": 0,
        // "cn_name": "中文配置",
        // "en_name": "name",
        // "form_tag_type": 2,
        // "form_tag_name": "textarea",
        // "form_tag_values": "",
        // "form_tag_active_value": "",
        // "action": "",
        // "form_tag_value‌s": "是，否",
        // "form_tag_active_value‌": "是"

        $rules =  [
            'id' => 'required|regex:/^[0-9]+$/',
            
            'form_tag_values' => [
                'required',
                'regex:/^([\x{4e00}-\x{9fa5},]*)+(,|[\sa-zA-Z],)*(,|[\d]+)*$/u'
            ],
            
            'article_abstract' => [
                'required',
                'max:100',
                'regex:/^[\p{L}\p{Han}\p{P}\p{S}]+$/u'
            ],

            'cover' =>['required','regex: /^data:image\/(?:jpeg|webp|gif|svg|x-icon|png);base64,(([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==))$/'],
            'tag_ids' => 'required|regex:/^\d+(?:,\d+)*$/',
            'tag_ids_names' =>  'required|regex:/^[A-Za-z0-9,]*$/',
            'author_name' => 'required|regex:/^[0-9]+$/',

            'menu_id' => 'required|regex:/^[0-9]+$/',
             'article_content' =>[
                'required',
                'regex:/^[\p{L}\p{Han}\p{P}\p{S}]+$/u'
            ],
            'is_pull' => 'required|regex:/^[0-9]+$/',
            'action' => ['required','regex:/^(add|edit)$/'],
        ];
        return $rules;




    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'article_id.required'=>'博文id不能为空',
            'article_id.regex'=>'博文id格式错误',

            'title.required'=>'标题不能为空',
            'title.max'=>'标题过长',
            'title.regex'=>'标题格式错误',

            'article_abstract.required'=>'摘要名称不能为空',
            'article_abstract.max'=>'摘要过长',
            'article_abstract.regex'=>'摘要名称格式错误',

            'cover.required'=>'封面不能为空',
            'cover.regex'=>'封面码格式错误',

            'tag_ids.required'=>'标签不能为空',
            'tag_ids.regex'=>'标签格式错误9',

            'tag_ids_names.required'=>'标签名称不能为空',
            'tag_ids_names.regex'=>'标签名称格式错误',

            'author_name.required'=>'作者不能为空',
            'author_name.regex'=>'作者格式错误',

            'is_pull.required'=>'下架不能为空',
            'is_pull.required'=>'下架格式错误',


            'action.required'=>'操作代码不能为空',
            'action.regex'=>'操作代码格式错误',

        ];

    }
}
