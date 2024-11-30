<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

// 菜单模块
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
    //required 不能为空 | 匹配字符串是否包含字母、数字
    // 匹配非负整数（包括零和正整数）：/^[0-9]+$/
    // 字符串以小写字母开头和结尾，中间是下划线/^[a-z][_][a-z]$/
    /*/^[a-z]+([_][a-z]+)?$/ /‌^[a-z]+‌：这部分表示匹配字符串的开头必须是1个或多个小写字母。^表示字符串的开头，[a-z]表示匹配任意一个小写字母，+表示匹配前面的字符一次或多次。

‌([_][a-z]+)?‌：这部分是一个可选的分组，表示匹配下划线后跟一个或多个小写字母的组合。[_]表示匹配一个下划线，[a-z]+表示匹配一个或多个小写字母。整个分组用括号括起来，并且后面有一个问号?，表示这个分组是可选的，即下划线和后面的小写字母可以出现0次或1次。

‌$‌：表示字符串的结尾。*/

    /* 要匹配字符串'/小写字母/小写字母_小写字母'，可以使用正则表达式  /^/[a-z]+(/[a-z]+_[a-z]+)?$/  (/^\/[a-z]+(\/[a-z]+_[a-z]+)?$/ )
‌开头必须是 /‌：正则表达式的开头必须是一个斜杠 /。
‌主体部分‌：
必须以一个或多个小写字母开头（[a-z]+）。
后面可以跟一个路径，路径的格式为：
一个斜杠 /。
后面跟着一个或多个小写字母（[a-z]+）。
接着是一个下划线 _。
最后再跟着一个或多个小写字母（[a-z]+）。
‌可选的路径部分‌：整个路径部分是可选的，即可以没有路径。
‌具体解释如下‌：

/^\/：匹配字符串的开头是一个斜杠 /。
[a-z]+：匹配一个或多个小写字母。
(\/[a-z]+_[a-z]+)?：这是一个可选的路径部分，格式为：
/：一个斜杠。
[a-z]+：一个或多个小写字母。
_：一个下划线。
[a-z]+：一个或多个小写字母。
‌应用场景‌：这个正则表达式可以用于匹配以斜杠 / 开头，后面跟着一个或多个小写字母，并且可选地包含一个由斜杠、下划线和字母组成的路径的字符串。例如，它可以匹配以下字符串：

/abc
/def_ghi
/jkl/mno_pqr

*/
        $rules =  [
            'menu_id' => 'required|regex:/^[0-9]+$/',
            'menu_name' => 'required|regex:/^[a-z]+([_][a-z]+)?$/',
            'menu_title' => 'required:[\u4e00-\u9fa5]',
            'menu_path' => 'required|regex:/^\/[a-z]+(\/[a-z]+_[a-z]+)?$/',
            'icon' => 'required|regex:/^[a-z]+$/',
            'business_level' => 'required|regex:/^[0-9]+$/',
            'parent_id' => 'required|regex:/^[0-9]+$/',
            'is_pulled' => 'required|regex:/^[0-9]+$/',
            'action' => 'required|regex:/^[a-z]+$/',

        ];
        return $rules;

    }

    /**
     *  提示信息
     */
    public function  messages(){
        return [
            'menu_id.required'=>'菜单id不能为空',
            'menu_id.regex'=>'菜单id格式错误',

            'menu_name.required'=>'路由Name不能为空',
            'menu_name.regex'=>'路由Name格式错误，示例：小写字母或小写字母_小写字母',

            'menu_title.required'=>'展示名称不能为空',
            'menu_title.regex'=>'展示名称格式错误，请输入中文字符',

            'menu_path.required'=>'路由Path不能为空',
            'menu_path.regex'=>'路由Path错误,示例：/小写字母/小写字母_小写字母',

            'icon.required'=>'icon不能为空',
            'icon.regex'=>'icon格式错误',

            'business_level.required'=>'业务层面不能为空',
            'business_level.regex'=>'业务层面格式错误',

            'parent_id.required'=>'父节点不能为空',
            'parent_id.regex'=>'父节点码格式错误',

            'is_pulled.required'=>'下架不能为空',
            'is_pulled.regex'=>'下架格式错误',

            'action.required'=>'操作代码不能为空',
            'action.regex'=>'操作代码格式错误',

        ];

    }
}
