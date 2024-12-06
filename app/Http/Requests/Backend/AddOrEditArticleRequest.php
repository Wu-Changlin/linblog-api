<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

// 博文模块
class AddOrEditArticleRequest extends FormRequest
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

      /*  这个正则表达式的含义如下：

^([A-Za-z0-9+/]{4})* 表示字符串以0或多个base64编码组开头。
([A-Za-z0-9+/]{4}|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==){1} 表示字符串以三种形式之一结束：
[A-Za-z0-9+/]{4}：完整的base64编码组。
[A-Za-z0-9+/]{3}=：缺少一个字符的base64编码组，末尾有一个等号。
[A-Za-z0-9+/]{2}==：缺少两个字符的base64编码组，末尾有两个等号。
‌使用这个正则表达式可以有效地检查字符串是否为base64编码。‌如果字符串符合上述模式，则说明它是有效的base64编码；如果不符合，则不是有效的base64编码。
*/
        // 'cover' =>['required','regex: /^data:image\/(?:png|jpeg|webp|gif|svg|x-icon);base64,(([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==))$/'],
        // 'cover' =>['required','regex: /^data:image\/(?:png|jpeg|webp|gif|svg|x-icon);base64,(([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)){1}$/'],
        

        $rules =  [
            'article_id' => 'required|regex:/^[0-9]+$/',
            
            'title' => [
                'required',
                'max:100',
                'regex:/^[\p{L}\p{Han}\p{P}\p{S}]+$/u'
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
            'is_pulled' => 'required|regex:/^[0-9]+$/',
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

            'is_pulled.required'=>'下架不能为空',
            'is_pulled.required'=>'下架格式错误',


            'action.required'=>'操作代码不能为空',
            'action.regex'=>'操作代码格式错误',

        ];

    }
}
