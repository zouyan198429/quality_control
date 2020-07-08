<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// welcome
//Route::get('/welcome', 'IndexController@welcome');
Route::get('/', 'IndexController@index');// 首页
//Route::get('/aaa', function () {
//    echo 'aaa';
//    // return view('welcome');
//});
//Route::view('clients', 'clients');

 Route::get('/test', 'IndexController@test');// 测试
//Route::get('/test2', 'IndexController@test2');// 测试
// Route::get('/', 'IndexController@index');// 首页-- 用这个

//Route::get('reg', 'IndexController@reg');// 注册
//Route::get('login', 'IndexController@login');// 登陆
//Route::get('logout', 'IndexController@logout');// 注销
//Route::get('404', 'IndexController@err404');// 404错误


// layuiAdmin
Route::get('layui/index', 'Layui\IndexController@index');// index.html
Route::get('layui/iframe/layer/iframe', 'Layui\Iframe\LayerController@iframe');// iframe/layer/iframe.html layer iframe 示例
Route::get('layui/system/about', 'Layui\SystemController@about');// system/about.html 版本信息 --***
Route::get('layui/system/get', 'Layui\SystemController@get');// system/get.html 授权获得 layuiAdmin --***
Route::get('layui/system/more', 'Layui\SystemController@more');// system/more.html 更多面板的模板 --***
Route::get('layui/system/theme', 'Layui\SystemController@theme');// system/theme.html 主题设置模板 --***
// 主页
Route::get('layui/home/console', 'Layui\HomeController@console');// 控制台 home/console.html
Route::get('layui/home/homepage1', 'Layui\HomeController@homepage1');// 主页一 home/homepage1.html
Route::get('layui/home/homepage2', 'Layui\HomeController@homepage2');// 主页二 home/homepage2.html
// 组件
Route::get('layui/component/laytpl/index', 'Layui\Component\LaytplController@index');// component/laytpl/index.html  模板引擎  --***
// 栅格
Route::get('layui/component/grid/list', 'Layui\Component\GridController@list');// 等比例列表排列 component/grid/list.html
Route::get('layui/component/grid/mobile', 'Layui\Component\GridController@mobile');// 按移动端排列 component/grid/mobile.html
Route::get('layui/component/grid/mobile-pc', 'Layui\Component\GridController@mobilePc');// 移动桌面端组合 component/grid/mobile-pc.html
Route::get('layui/component/grid/all', 'Layui\Component\GridController@all');// 全端复杂组合 component/grid/all.html
Route::get('layui/component/grid/stack', 'Layui\Component\GridController@stack');// 低于桌面堆叠排列 component/grid/stack.html
Route::get('layui/component/grid/speed-dial', 'Layui\Component\GridController@speedDial');// 九宫格 component/grid/speed-dial.html

Route::get('layui/component/button/index', 'Layui\Component\ButtonController@index');// 按钮  component/button/index.html
// 表单
Route::get('layui/component/form/element', 'Layui\Component\FormController@element');// 表单元素 component/form/element.html
Route::get('layui/component/form/group', 'Layui\Component\FormController@group');// 表单组合 component/form/group.html

Route::get('layui/component/nav/index', 'Layui\Component\NavController@index');// 导航  component/nav/index.html
Route::get('layui/component/tabs/index', 'Layui\Component\TabsController@index');// 选项卡 component/tabs/index.html
Route::get('layui/component/progress/index', 'Layui\Component\ProgressController@index');// 进度条 component/progress/index.html
Route::get('layui/component/panel/index', 'Layui\Component\PanelController@index');// 面板 component/panel/index.html
Route::get('layui/component/badge/index', 'Layui\Component\BadgeController@index');// 徽章 component/badge/index.html
Route::get('layui/component/timeline/index', 'Layui\Component\TimelineController@index');// 时间线 component/timeline/index.html
Route::get('layui/component/anim/index', 'Layui\Component\AnimController@index');// 动画 component/anim/index.html
Route::get('layui/component/auxiliar/index', 'Layui\Component\AuxiliarController@index');// 辅助 component/auxiliar/index.html
// 通用弹层
Route::get('layui/component/layer/list', 'Layui\Component\LayerController@list');// 功能演示 component/layer/list.html
Route::get('layui/component/layer/special-demo', 'Layui\Component\LayerController@specialDemo');// 特殊示例 component/layer/special-demo.html
Route::get('layui/component/layer/theme', 'Layui\Component\LayerController@theme');// 风格定制 component/layer/theme.html
// 日期时间
Route::get('layui/component/laydate/index', 'Layui\Component\LaydateController@index');// component/laydate/index.html  日期组件 --***
Route::get('layui/component/laydate/demo1', 'Layui\Component\LaydateController@demo1');// 功能演示一 component/laydate/demo1.html
Route::get('layui/component/laydate/demo2', 'Layui\Component\LaydateController@demo2');// 功能演示二 component/laydate/demo2.html
Route::get('layui/component/laydate/theme', 'Layui\Component\LaydateController@theme');// 设定主题 component/laydate/theme.html
Route::get('layui/component/laydate/special-demo', 'Layui\Component\LaydateController@specialDemo');// 特殊示例 component/laydate/special-demo.html

Route::get('layui/component/table/static', 'Layui\Component\TableController@static');// 静态表格 component/table/static.html
// 数据表格
Route::get('layui/component/table/index', 'Layui\Component\TableController@index');// component/table/index.html  表格 --***
Route::get('layui/component/temp', 'Layui\Component\TableController@temp');// component/temp.html  简单用法 - 数据表格 --***
Route::get('layui/component/table/simple', 'Layui\Component\TableController@simple');// 简单数据表格 component/table/simple.html
Route::get('layui/component/table/auto', 'Layui\Component\TableController@auto');// 列宽自动分配 component/table/auto.html
Route::get('layui/component/table/data', 'Layui\Component\TableController@data');// 赋值已知数据 component/table/data.html
Route::get('layui/component/table/tostatic', 'Layui\Component\TableController@tostatic');// 转化静态表格 component/table/tostatic.html
Route::get('layui/component/table/page', 'Layui\Component\TableController@page');// 开启分页 component/table/page.html
Route::get('layui/component/table/resetPage', 'Layui\Component\TableController@resetPage');// 自定义分页 component/table/resetPage.html
Route::get('layui/component/table/toolbar', 'Layui\Component\TableController@toolbar');// 开启头部工具栏 component/table/toolbar.html
Route::get('layui/component/table/totalRow', 'Layui\Component\TableController@totalRow');// 开启合计行 component/table/totalRow.html
Route::get('layui/component/table/height', 'Layui\Component\TableController@height');// 高度最大适应 component/table/height.html
Route::get('layui/component/table/checkbox', 'Layui\Component\TableController@checkbox');// 开启复选框 component/table/checkbox.html
Route::get('layui/component/table/radio', 'Layui\Component\TableController@radio');// 开启单选框 component/table/radio.html
Route::get('layui/component/table/cellEdit', 'Layui\Component\TableController@cellEdit');// 开启单元格编辑 component/table/cellEdit.html
Route::get('layui/component/table/form', 'Layui\Component\TableController@form');// 加入表单元素 component/table/form.html
Route::get('layui/component/table/style', 'Layui\Component\TableController@style');// 设置单元格样式 component/table/style.html
Route::get('layui/component/table/fixed', 'Layui\Component\TableController@fixed');// 固定列 component/table/fixed.html
Route::get('layui/component/table/operate', 'Layui\Component\TableController@operate');// 数据操作 component/table/operate.html
Route::get('layui/component/table/parseData', 'Layui\Component\TableController@parseData');// 解析任意数据格式 component/table/parseData.html
Route::get('layui/component/table/onrow', 'Layui\Component\TableController@onrow');// 监听行事件 component/table/onrow.html
Route::get('layui/component/table/reload', 'Layui\Component\TableController@reload');// 数据表格的重载 component/table/reload.html
Route::get('layui/component/table/initSort', 'Layui\Component\TableController@initSort');// 设置初始排序 component/table/initSort.html
Route::get('layui/component/table/cellEvent', 'Layui\Component\TableController@cellEvent');// 监听单元格事件 component/table/cellEvent.html
Route::get('layui/component/table/thead', 'Layui\Component\TableController@thead');// 复杂表头 component/table/thead.html
// 分页
Route::get('layui/component/laypage/index', 'Layui\Component\LaypageController@index');// component/laypage/index.html  通用分页组件 --***
Route::get('layui/component/laypage/demo1', 'Layui\Component\LaypageController@demo1');// 功能演示一 component/laypage/demo1.html
Route::get('layui/component/laypage/demo2', 'Layui\Component\LaypageController@demo2');// 功能演示二 component/laypage/demo2.html
// 上传
Route::get('layui/component/upload/index', 'Layui\Component\UploadController@index');// component/upload/index.html 上传 --***
Route::get('layui/component/upload/demo1', 'Layui\Component\UploadController@demo1');// 功能演示一 component/upload/demo1.html
Route::get('layui/component/upload/demo2', 'Layui\Component\UploadController@demo2');// 功能演示二 component/upload/demo2.html

Route::get('layui/component/colorpicker/index', 'Layui\Component\ColorpickerController@index');// 颜色选择器 component/colorpicker/index.html
Route::get('layui/component/slider/index', 'Layui\Component\SliderController@index');// 滑块组件 component/slider/index.html
Route::get('layui/component/rate/index', 'Layui\Component\RateController@index');// 评分 component/rate/index.html
Route::get('layui/component/carousel/index', 'Layui\Component\CarouselController@index');// 轮播 component/carousel/index.html
Route::get('layui/component/flow/index', 'Layui\Component\FlowController@index');// 流加载 component/flow/index.html
Route::get('layui/component/util/index', 'Layui\Component\UtilController@index');// 工具 component/util/index.html
Route::get('layui/component/code/index', 'Layui\Component\CodeController@index');// 代码修饰 component/code/index.html

// 页面
Route::get('layui/template/personalpage', 'Layui\TemplateController@personalpage');// 个人主页 template/personalpage.html
Route::get('layui/template/addresslist', 'Layui\TemplateController@addresslist');// 通讯录 template/addresslist.html
Route::get('layui/template/caller', 'Layui\TemplateController@caller');// 客户列表 template/caller.html
Route::get('layui/template/goodslist', 'Layui\TemplateController@goodslist');// 商品列表 template/goodslist.html
Route::get('layui/template/msgboard', 'Layui\TemplateController@msgboard');// 留言板 template/msgboard.html
Route::get('layui/template/search', 'Layui\TemplateController@search');// 搜索结果 template/search.html
Route::get('layui/template/temp', 'Layui\TemplateController@temp');// template/temp.html --***


Route::get('layui/user/reg', 'Layui\UserController@reg');// 注册 user/reg.html
Route::get('layui/user/login', 'Layui\UserController@login');// 登入 user/login.html
Route::get('layui/user/forget', 'Layui\UserController@forget');// 忘记密码 user/forget.html

Route::get('layui/template/tips/404', 'Layui\Template\TipsController@err404');// 404页面不存在 template/tips/404.html
Route::get('layui/template/tips/error', 'Layui\Template\TipsController@error');// 错误提示 template/tips/error.html
// 百度一下 //www.baidu.com/
// layui官网 //www.layui.com/
// layuiAdmin官网 //www.layui.com/admin/
// 应用
//    内容系统
Route::get('layui/app/content/list', 'Layui\App\ContentController@list');// 文章列表 app/content/list.html
Route::get('layui/app/content/tags', 'Layui\App\ContentController@tags');// 分类管理 app/content/tags.html
Route::get('layui/app/content/comment', 'Layui\App\ContentController@comment');// 评论管理 app/content/comment.html
Route::get('layui/app/content/contform', 'Layui\App\ContentController@contform');// app/content/contform.html  评论管理 iframe 框 --***
Route::get('layui/app/content/listform', 'Layui\App\ContentController@listform');// app/content/listform.html  文章管理 iframe 框 --***
Route::get('layui/app/content/tagsform', 'Layui\App\ContentController@tagsform');// app/content/tagsform.html  分类管理 iframe 框
//    社区系统
Route::get('layui/app/forum/list', 'Layui\App\ForumController@list');// 帖子列表 app/forum/list.html
Route::get('layui/app/forum/replys', 'Layui\App\ForumController@replys');// 回帖列表 app/forum/replys.html
Route::get('layui/app/forum/listform', 'Layui\App\ForumController@listform');// app/forum/listform.html  帖子管理 iframe 框 --***
Route::get('layui/app/forum/replysform', 'Layui\App\ForumController@replysform');// app/forum/replysform.html  回帖管理 iframe 框 --***

Route::get('layui/app/message/index', 'Layui\App\MessageController@index');// 消息中心 app/message/index.html
Route::get('layui/app/message/detail', 'Layui\App\MessageController@detail');// app/message/detail.html  消息详情标题 --***

Route::get('layui/app/workorder/list', 'Layui\App\WorkorderController@list');// 工单系统 app/workorder/list.html
Route::get('layui/app/workorder/listform', 'Layui\App\WorkorderController@listform');// app/workorder/listform.html 工单管理 iframe 框

Route::get('layui/app/mall/category', 'Layui\App\MallController@category');// app/mall/category.html  分类管理 --***
Route::get('layui/app/mall/list', 'Layui\App\MallController@list');// app/mall/list.html  商品列表 --***
Route::get('layui/app/mall/specs', 'Layui\App\MallController@specs');// app/mall/specs.html  规格管理 --***
//  高级
//    LayIM 通讯系统
Route::get('layui/senior/im/index', 'Layui\Senior\ImController@index');// senior/im/index.html  LayIM 社交聊天 --***
//    Echarts集成
Route::get('layui/senior/echarts/line', 'Layui\Senior\EchartsController@line');// 折线图 senior/echarts/line.html
Route::get('layui/senior/echarts/bar', 'Layui\Senior\EchartsController@bar');// 柱状图 senior/echarts/bar.html
Route::get('layui/senior/echarts/map', 'Layui\Senior\EchartsController@map');// 地图  senior/echarts/map.html
// 用户
Route::get('layui/user/user/list', 'Layui\User\UserController@list');// 网站用户 user/user/list.html
Route::get('layui/user/user/userform', 'Layui\User\UserController@userform');// user/user/userform.html  网站用户 iframe 框

Route::get('layui/user/administrators/list', 'Layui\User\AdministratorsController@list');// 后台管理员 user/administrators/list.html
Route::get('layui/user/administrators/role', 'Layui\User\AdministratorsController@role');// 角色管理 user/administrators/role.html
Route::get('layui/user/administrators/adminform', 'Layui\User\AdministratorsController@adminform');// user/administrators/adminform.html 管理员 iframe 框
Route::get('layui/user/administrators/roleform', 'Layui\User\AdministratorsController@roleform');// user/administrators/roleform.html 角色管理 iframe 框

// 设置
//    系统设置
Route::get('layui/set/system/website', 'Layui\Set\SystemController@website');// 网站设置 set/system/website.html
Route::get('layui/set/system/email', 'Layui\Set\SystemController@email');// 邮件服务 set/system/email.html
//    我的设置
Route::get('layui/set/user/info', 'Layui\Set\UserController@info');// 基本资料 set/user/info.html
Route::get('layui/set/user/password', 'Layui\Set\UserController@password');// 修改密码 set/user/password.html
// 授权  //www.layui.com/admin/#get

// --- 质量认证认可协会
// -- 后台


// 首页
Route::get('admin/test', 'Admin\QualityControl\IndexController@test');// 测试
Route::get('admin/index', 'Admin\QualityControl\IndexController@index');// 首页--ok
Route::get('admin', 'Admin\QualityControl\IndexController@index');// --ok
Route::get('admin/login', 'Admin\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('admin/logout', 'Admin\QualityControl\IndexController@logout');// 注销--ok
Route::get('admin/password', 'Admin\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('admin/info', 'Admin\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('admin/down_drive', 'Admin\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

// 系统管理员
Route::get('admin/staff', 'Admin\QualityControl\StaffController@index');// 列表
Route::get('admin/staff/add/{id}', 'Admin\QualityControl\StaffController@add');// 添加
// Route::get('admin/staff/select', 'Admin\QualityControl\StaffController@select');// 选择-弹窗
Route::get('admin/staff/export', 'Admin\QualityControl\StaffController@export');// 导出
Route::get('admin/staff/import_template', 'Admin\QualityControl\StaffController@import_template');// 导入模版

// 企业帐号管理
Route::get('admin/company', 'Admin\QualityControl\CompanyController@index');// 列表
Route::get('admin/company/add/{id}', 'Admin\QualityControl\CompanyController@add');// 添加
// Route::get('admin/company/select', 'Admin\QualityControl\CompanyController@select');// 选择-弹窗
Route::get('admin/company/export', 'Admin\QualityControl\CompanyController@export');// 导出
Route::get('admin/company/import_template', 'Admin\QualityControl\CompanyController@import_template');// 导入模版

// 个从帐号管理
Route::get('admin/user', 'Admin\QualityControl\UserController@index');// 列表
Route::get('admin/user/add/{id}', 'Admin\QualityControl\UserController@add');// 添加
// Route::get('admin/user/select', 'Admin\QualityControl\UserController@select');// 选择-弹窗
Route::get('admin/user/export', 'Admin\QualityControl\UserController@export');// 导出
Route::get('admin/user/import_template', 'Admin\QualityControl\UserController@import_template');// 导入模版

// 行业[一级分类]
Route::get('admin/industry', 'Admin\QualityControl\IndustryController@index');// 列表
Route::get('admin/industry/add/{id}', 'Admin\QualityControl\IndustryController@add');// 添加
// Route::get('admin/industry/select', 'Admin\QualityControl\IndustryController@select');// 选择-弹窗
Route::get('admin/industry/export', 'Admin\QualityControl\IndustryController@export');// 导出
Route::get('admin/industry/import_template', 'Admin\QualityControl\IndustryController@import_template');// 导入模版

// 城市[一级分类]
Route::get('admin/citys', 'Admin\QualityControl\CitysController@index');// 列表
Route::get('admin/citys/add/{id}', 'Admin\QualityControl\CitysController@add');// 添加
// Route::get('admin/citys/select', 'Admin\QualityControl\CitysController@select');// 选择-弹窗
Route::get('admin/citys/export', 'Admin\QualityControl\CitysController@export');// 导出
Route::get('admin/citys/import_template', 'Admin\QualityControl\CitysController@import_template');// 导入模版

// 登录验证码 验证码
Route::get('admin/sms_code', 'Admin\QualityControl\SmsCodeController@index');// 列表
Route::get('admin/sms_code/add/{id}', 'Admin\QualityControl\SmsCodeController@add');// 添加
// Route::get('admin/sms_code/select', 'Admin\QualityControl\SmsCodeController@select');// 选择-弹窗
Route::get('admin/sms_code/export', 'Admin\QualityControl\SmsCodeController@export');// 导出
Route::get('admin/sms_code/import_template', 'Admin\QualityControl\SmsCodeController@import_template');// 导入模版

// 资质证书类型[一级分类]
Route::get('admin/company_certificate_type', 'Admin\QualityControl\CompanyCertificateTypeController@index');// 列表
Route::get('admin/company_certificate_type/add/{id}', 'Admin\QualityControl\CompanyCertificateTypeController@add');// 添加
// Route::get('admin/company_certificate_type/select', 'Admin\QualityControl\CompanyCertificateTypeController@select');// 选择-弹窗
Route::get('admin/company_certificate_type/export', 'Admin\QualityControl\CompanyCertificateTypeController@export');// 导出
Route::get('admin/company_certificate_type/import_template', 'Admin\QualityControl\CompanyCertificateTypeController@import_template');// 导入模版

// 能力验证行业分类[一级分类]
Route::get('admin/ability_type', 'Admin\QualityControl\AbilityTypeController@index');// 列表
Route::get('admin/ability_type/add/{id}', 'Admin\QualityControl\AbilityTypeController@add');// 添加
// Route::get('admin/ability_type/select', 'Admin\QualityControl\AbilityTypeController@select');// 选择-弹窗
Route::get('admin/ability_type/export', 'Admin\QualityControl\AbilityTypeController@export');// 导出
Route::get('admin/ability_type/import_template', 'Admin\QualityControl\AbilityTypeController@import_template');// 导入模版

// 企业后台 company
Route::get('company/login', 'Company\QualityControl\IndexController@login');// login.html 登录
Route::get('company/reg', 'Company\QualityControl\IndexController@reg');// 注册
Route::get('company/perfect_company', 'Company\QualityControl\IndexController@perfect_company');// 注册-补充企业资料
Route::get('company/user_company', 'Company\QualityControl\IndexController@user_company');// 注册-补充用户资料

// 首页
Route::get('company/test', 'Company\QualityControl\IndexController@test');// 测试
Route::get('company/index', 'Company\QualityControl\IndexController@index');// 首页--ok
Route::get('company', 'Company\QualityControl\IndexController@index');// --ok
Route::get('company/login', 'Company\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('company/logout', 'Company\QualityControl\IndexController@logout');// 注销--ok
Route::get('company/password', 'Company\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('company/info', 'Company\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('company/down_drive', 'Company\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

// 用户中心 user
Route::get('user/login', 'User\QualityControl\IndexController@login');// login.html 登录
Route::get('user/reg', 'User\QualityControl\IndexController@reg');// 注册
Route::get('user/perfect_company', 'User\QualityControl\IndexController@perfect_company');// 注册-补充企业资料
Route::get('user/user_company', 'User\QualityControl\IndexController@user_company');// 注册-补充用户资料

// 首页
Route::get('user/test', 'User\QualityControl\IndexController@test');// 测试
Route::get('user/index', 'User\QualityControl\IndexController@index');// 首页--ok
Route::get('user', 'User\QualityControl\IndexController@index');// --ok
Route::get('user/login', 'User\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('user/logout', 'User\QualityControl\IndexController@logout');// 注销--ok
Route::get('user/password', 'User\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('user/info', 'User\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('user/down_drive', 'User\QualityControl\IndexController@down_drive');// 下载网页打印机驱动


// 前台 web
Route::get('web/test', 'Web\QualityControl\HomeController@test');// 测试
Route::get('web/login', 'Web\QualityControl\HomeController@login');// login.html 登录
Route::get('web/reg', 'Web\QualityControl\HomeController@reg');// 注册
Route::get('web/perfect_company', 'Web\QualityControl\HomeController@perfect_company');// 注册-补充企业资料
Route::get('web/perfect_user', 'Web\QualityControl\HomeController@perfect_user');// 注册-补充用户资料
Route::get('web/logout', 'Web\QualityControl\HomeController@logout');// 注销--ok

// 首页
//Route::get('web/test', 'Web\QualityControl\IndexController@test');// 测试
//Route::get('web/index', 'Web\QualityControl\IndexController@index');// 首页--ok
//Route::get('web', 'Web\QualityControl\IndexController@index');// --ok
//Route::get('web/login', 'Web\QualityControl\IndexController@login');//login.html 登录--ok
//Route::get('web/logout', 'Web\QualityControl\IndexController@logout');// 注销--ok
//Route::get('web/password', 'Web\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
//Route::get('web/info', 'Web\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('web/down_drive', 'Web\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

// Auth::routes();
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
//注册用户点击验证邮箱
Route::get('email/verify/{token}', 'EmailController@verify')->name('email.verify');
//保护路由
// 路由中间件 可用于仅允许经过验证的用户访问指定路由。Laravel 附带了 verified 中间件，
// 它定义在 Illuminate\Auth\Middleware\EnsureEmailIsVerified。由于此中间件已在应用程序的 HTTP 内核中注册，
// 因此您需要做的就是将中间件附加到路由定义：
//Route::get('profile', function () {
//    // 只有验证过的用户可以进入
//})->middleware('verified');

//  将Passport集成到您的Laravel API https://justlaravel.com/integrate-passport-laravel-api/
// 以调用注册视图
//Route::get('/register', function(){
//    return view('auth.register');
//})->name('register');
////Route::get('/login', function(){
////    return view('auth.login');
////})->name('login');
//Route::post('/register', 'PassportController@register');

//Route::get('/testGetToken', function () {
//    $client = new GuzzleHttp\Client;
//    $response = $client->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'client_id' => 8,// the_client_id_obtained_when_registered_to_API,
//            'client_secret' => 'z4iTk0hTiEaz7amHC1FjdSTopfYG0sjJqlCmoLGd',// 'the_client_secret_obtained_when_registered_to_API',
//            'grant_type' => 'password',
//            'username' => 'dfsdfsd@qq.com',// 'dfsdfsd@qq.com',
//            'password' => '123456',
//            'scope' => '*',
//        ]
//    ]);
//
//    $auth = json_decode( (string) $response->getBody() );
//    $response = $client->get('http://runbuy.admin.cunwo.net/api/users', [
//        'headers' => [
//            'Authorization' => 'Bearer '.$auth->access_token,
//        ]
//    ]);
//    $details = json_decode( (string) $response->getBody() );
//    return view('testGetToken', ['details' => $details]);
//});

// https://learnku.com/docs/laravel/5.6/passport/1380
//请求令牌
//授权时的重定向
//Route::get('/redirect', function () {
//    $query = http_build_query([
//        'client_id' => 5,//'client-id',
//        'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//        'response_type' => 'code',
//        'scope' => '',
//    ]);
//    $redirectUrl = 'http://runbuy.admin.cunwo.net/oauth/authorize?' . $query;// 'http://your-app.com/oauth/authorize?'.$query
//    return redirect($redirectUrl);
//});
// 密码授权令牌
// 请求令牌
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJhNmVmMGZkN2U3NzJjYjk3ZGU4MjA0NGI2MzhiNjcyZTE3MTAzMTBlZmY3NWU3YWYyMzczNzg2NDRjOGNmNjE0MjVmZGQ2OGZlMzM2ODE4In0.eyJhdWQiOiIyIiwianRpIjoiMmE2ZWYwZmQ3ZTc3MmNiOTdkZTgyMDQ0YjYzOGI2NzJlMTcxMDMxMGVmZjc1ZTdhZjIzNzM3ODY0NGM4Y2Y2MTQyNWZkZDY4ZmUzMzY4MTgiLCJpYXQiOjE1NzE3MDA5MjAsIm5iZiI6MTU3MTcwMDkyMCwiZXhwIjoxNTcyOTk2OTIwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.Wnwxpe7RzdSkr7N71GQ28BLweFyX3OA8FPgTcj5tbWYKSlE1cYbZ0E7V1UhmWabSzeff6VL6R16srXMwKEYQdE4_JyTcxhOIaCM633gLphYzgLjXDAIJmuuwiaMyMq0QL1_mtyNPF_eLXHiC8I9uD5kXaxqaIw393Ed98Bo6glSVO62Eg3R_yFXrjncw0FNiC0kx02hD71C5NAMFrpEyCP1ATlJqEZAJG7qGJoPq1uoYFSLeKrFwpbtPMKokD2a-gDd3re6i4faO5mxplaESDztHHA6lN0toU5GdgpggCAXriEA4GioncGOUpkdS9QCNxQKt-2rSd4An1iWnVUhhEaF-BI4kzHmCtyGQjP-gyuDhkDQvgP_b7cOjuqmnbTZRWsd6wuX75vfCeUxIMiu1eLKdAX_xEx3WPbq62hZnMkyKaZmUY-wXmWCaWzdaOt4ANqvsWoZs31HBGUinUhPvQrAb18iEN2vsNdESNowicpoXrCzxzyFk67MHMegB8GvY8-sDykcobWvx4Dqp5VGXU701Vhb2Eti5MeFcJ4OpQgEh4xLgPBgoF29oXJuqdBGnCH-dDZUmY-yW2h6M-moU2_VIXztw-GP4tXvl7FOlOfpRXGo-SUWetZ9LDiyh2PAGCbYuckQqCNbqSnY3J0oFZ9yI1pWZdfEU7U0T9MPM8Gc",
 *      "refresh_token": "def5020041714f3b344600b69e10314ca79298e2f8a289e933bcbd9d429eb9623d97077554f511a80a35541bdd2ff74307f92f29a5c5b5fa1bb8e0cae82a423d3e74714534bd0b29ca27956a6d332b2b2098b0bc1dae9c23b39e30053f2e1fd02085e86b501bcbf93164d3374bcfb8104408b62d936a877b98e3fa32b1d8a4f3c1dd7b03db901e679dc9d67549c99a03634c8621cef21036922595f8b28fdcf8dd0cec4b14311cb9786f152564eee9ef90f484985855080e8003b816ccb6ab1c085799a7703943a83dd6f40748de3d96bfa6c17226cc78cba5ea50c276864ac737c6e99038279fd0de904a9e4ae2c7bbcc063cc0a4656a57ef046c955fa9b9b1a8241c48e8bbe5a97718bbd41e0578d7a8eecb97e5a394dbc89b4238d35f86e930ebaa5a4c124058baf87752cf6ce5d9d41664539bf2098d708b2d8c22ab15c3cc50bad558cd0cbff1cec8242481289113ec8a4846bf65f75a07eb40a96c06a108"
 *  }
 *
 */
//Route::get('/redirect/password', function () {$http = new GuzzleHttp\Client;
//    $client = new GuzzleHttp\Client;
//    $response = $client->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'password',
//            'client_id' => 2,// 'client-id',
//            'client_secret' => '0YyuRCVmLUhvOy2BHgGAaXDHPSlopXdYv2ff7yMi',// 'client-secret',
//            'username' => 'dsfsdfsddasd@qq.com',// 'taylor@laravel.com',
//            'password' => '123456',// 'my-password',
//            // 可以通过请求 scope 参数 * 来授权应用程序支持的所有范围的令牌。如果你的请求中包含 scope 为 * 的参数，
//            // 令牌实例上的 can 方法会始终返回 true。这种作用域的授权只能分配给使用 password 授权时发出的令牌：
//            'scope' => '',
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});

// 隐式授权令牌
// 隐式授权类似于授权码授权，但是它只将令牌返回给客户端而不交换授权码。
//  这种授权最常用于无法安全存储客户端凭据的 JavaScript 或移动应用程序。
// 通过调用 AuthServiceProvider 中的 enableImplicitGrant 方法来启用这种授权：
/**
 * 有个问题：为什么返回的地址参数是#而不是?号
 *  http://runbuy.admin.cunwo.net/api/auth/callback/implicit#access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIyMmZiY2E4ZGNmOGQ3NzAwNTMxOGI2YjlkYjM3YWIzZmZlODg5YjFiZjQwZTYzYTU3ZTQ3ZjFkZjlmZGMyNWQwNzU2MjdlYzhmMDM5NWE3In0.eyJhdWQiOiI1IiwianRpIjoiMjIyZmJjYThkY2Y4ZDc3MDA1MzE4YjZiOWRiMzdhYjNmZmU4ODliMWJmNDBlNjNhNTdlNDdmMWRmOWZkYzI1ZDA3NTYyN2VjOGYwMzk1YTciLCJpYXQiOjE1NzE3MDUxMjcsIm5iZiI6MTU3MTcwNTEyNywiZXhwIjoxNTczMDAxMTI3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.dNXslW8QHo7iQTOWTIQ3h0RXpL7PqUHdyE1QAzr-0osDCuoNQbHhWq2pcXJxCwJnO1YeNlsiBswkBkBb5QcR9UYJNL6ZmnbVBsJxxRBOY2TdPVd26bxDdHN0g3tBLEt4OB5uNT20fDBdBQPU9nAF3hEqBhEpN3kJiKmR4E0QsNKn65nobXKhhjTg4cuuopT2ZK7J1VNQQryIb4IOgDUNIGR-qb_gYqoi6J5son9wtAHmI72nz1zG7gitdt_yV1VYdkGx3fSsfL3qt0HDaflhBdi4BEL-KSZgmy3rgHO5TNx5idszDoHzpwzxuFEIhzUZoMpg5Nj-vjiqFYmZ6XUOPEhBq5V77n1h4Hvpj9xXNH3ckO7VsAy6wsHol0hjDWER-WeOmyakT2mADYgtixcinmW7ZYJEcHhRAwyBTA-rY8iVz013NHsVIJbocntdNdpvvuQc3Crqu1CnKorBYPZjsfI15vISE8UDRYC6z7MNYPV5XrJ9QvI_PFnOWL6jDnIieAjT_wB-BbBNpFQytOwjRQL5wIJXMHcon-SPqkPm41Dlt2nHjepPtsViHZXwyJpHL3ZvxwICYmpFE4Vtr5wRapQeeirnwYPOoBcbMTurSS9J3WNqhuEkkxCX90q29tI6R_u38eFz8LFmedZoI4LrZTo7mZCysZroonuy6LAcLic&token_type=Bearer&expires_in=1296000
 *
 */
//Route::get('/redirect/implicit', function () {
//    $query = http_build_query([
//        'client_id' => 5,// 'client-id',
//        'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback/implicit',//'http://example.com/callback',
//        'response_type' => 'token',
//        'scope' => '',
//    ]);
//
//    return redirect('http://runbuy.admin.cunwo.net/oauth/authorize?'.$query);
//});


// 客户端凭据授权令牌
//客户端凭据授权适用于机器到机器的认证。例如，你可以在通过 API 执行维护任务中使用此授权。
//要使用这种授权，你首先需要在 app/Http/Kernel.php 的 $routeMiddleware 变量中添加新的中间件：
// 客户端通过获得的下面的token来请求服务端的这个接口
/*
 *
 *   {
 *        "token_type": "Bearer",
 *        "expires_in": 1296000,
 *        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZlMGMxMTkzMTNhZDk0NDc0NGM0NjQ4MjBlMzgyMjM3M2NkMGFjMTAxYjUzZjg3YjU0NDczMGM2Y2QyMDQyY2U4ZjkxYTM1ZTcyNGMyM2MwIn0.eyJhdWQiOiI1IiwianRpIjoiZmUwYzExOTMxM2FkOTQ0NzQ0YzQ2NDgyMGUzODIyMzczY2QwYWMxMDFiNTNmODdiNTQ0NzMwYzZjZDIwNDJjZThmOTFhMzVlNzI0YzIzYzAiLCJpYXQiOjE1NzE3MDcxMTMsIm5iZiI6MTU3MTcwNzExMywiZXhwIjoxNTczMDAzMTEzLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.pz_dIe_gsgRYOsEpTKizIGI41rXAobm60_SHWgvy1SK28-0gypNV0PFyBjLM8sVJYez8cAZd2gAGrFmAaeB8Z9q64tiWj2I-FTKx5yggNohzMA0T9wu9P-m0YDX4NVCz1ZWAGrSAlPH4Qxtjrof6N-GibL-APXinXE-cGv6P-SW-yYeMlqw7EYkWBglJ28cTH4ZQ8fp7aBm7FvILdKetVpt2vBqLsl-UNckDqob3nie6skdHVcZUZoXrRN_fzYGP0sxrK_Y6AjnkcwidqHZWyjBLgqdIU_ErK_OVKGW4yDmmEo17mvxj2uF6nzbIQwDK78Mjq9rgwWeb3K53MrkRKYgFXRT7qBgHl3S4L8i4bNfupOMCQeAU3NrB1iE3Ko2kjX9ZAS93cO0mLihXBA0XhpIZnYexQAMzEPdMeeFBAyLH6VeoQVePRDXRwj0BqzLcwznDr0DeQUzD1qQ8AHCSUhlRZTGWjUJxPd8SEN4xUb40LfnEKbEd6PZJy5cwM7tLMs7xcFrfyJ1e-hpOcFK0wQdXD1xUU4IKopTZypMg8GE5fJ2-QW3HwLPM3hEJ_Hi1VMSfkqA9ksiudmNcYKXqIYrtTa58XFbIfxgMdafV7Z1zXNzWNZtiIGL3LPm-Ccy7-bUz3oisvX-LMFPWzF4SYqx1oD8MFjNPxQYg_bSVPS8"
 *    }
 *
 */
//Route::get('/user/client_credentials', function(Request $request) {
//   echo 'aaaa';
//})->middleware('client');


// Laravel+passport 实现API认证 --未验证
// https://blog.csdn.net/hhhzua/article/details/80170447
//Route::group([
//    'prefix'=>'/v1',
//    'middleware' => ['api'],
//], function () {
//    Route::post('user/login','Api\LoginController@login');
//    Route::post('user/register','Api\LoginController@register');
//});
//Route::group([
//    'prefix'=>'/v1',
//    'middleware' => ['auth:api'],
//    //指定需要传入有效访问令牌的 auth:api 中间件
//], function () {
//    Route::get('user/logout','Api\LoginController@logout');
//    Route::get('user/info','User\IndexController@getInfo');//显示个人信息
//});


