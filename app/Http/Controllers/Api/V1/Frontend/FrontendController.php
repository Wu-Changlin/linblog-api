<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;

// use App\Services\Backend\UserService;
use Illuminate\Http\Request;


// 前端页面相关
class FrontendController extends Controller
{
    // protected $userService;

    public function __construct()
    {

        // $this->userService = new UserService(); 

    }

    // 获取当前选择菜单页数据，非首页（首页没有下一页数据，需要在页面底部显示备案）和归档页（统计页面）
    public function getCurrentActivePageData(Request $request)
    {


        sendMSG(200,[],'getCurrentActivePageData');
        // return response()->json(['user' => $user], 201);
    }

    //获取首页数据（内容标签栏数据、博文列表数据（瀑布流组件））  
    public function getIndexPageData(Request $request)
    {


        sendMSG(200,[],'getIndexPageData');
        // return response()->json(['user' => $user], 201);
    }


    //获取归档页网站统计栏、标签统计栏、贡献统计栏数据

    public function getArchivesPageData(Request $request)
    {


        sendMSG(200,[],'getArchivesPageData');
        // return response()->json(['user' => $user], 201);
    }

    // 获取当前点击标签的页面数据
    public function getClickTagPageData()
    {
        sendMSG(200,[],'getClickTagPageData');
    }
  

    // 获取选中标签的下一页数据  
    public function getActiveTagNextPageData()
    {
        sendMSG(200,[],'getActiveTagNextPageData');
    }


    //获取log和菜单导航栏   // 获取网站配置（如网站标题、网站关键词、网站描述、底部备案、网站log）
    public function getLayoutLogOrMenuListData()
    {
        sendMSG(200,[],'getLayoutLogOrMenuListData');
    }


    //获取搜索关键字匹配所用数据源  提供一个获取数据的方法，用于搜索时显示下拉菜单数据
    public function getSearchKeywordMatchArticleListDataFunction()
    {
        sendMSG(200,[],'getSearchKeywordMatchArticleListDataFunction');
    }


    //获取搜索关键字匹配结果  
    public function getSearchKeywordMatchData()
    {
        sendMSG(200,[],'getSearchKeywordMatchData');
    }



    //获取搜索关键字匹配结果下一页数据

    public function getSearchKeywordMatchNextPageData()
    {
        sendMSG(200,[],'getSearchKeywordMatchNextPageData');
    }


    //获取博文详情数据

    public function getArticlePageData()
    {
        sendMSG(200,[],'getArticlePageData');
    }

    //获取点击年份的贡献数据（获取年贡献信息）

    public function getClickYearContributionData()
    {
        sendMSG(200,[],'getClickYearContributionData');
    }

    //获取选中日期贡献信息（由contribution_calendar子组件发到父组件的点击贡献图某日数据）
    public function clickContributionDay()
    {
        sendMSG(200,[],'clickContributionDay');
    }


    // 其他路由方法
}
