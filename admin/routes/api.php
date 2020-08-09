<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->post ('user/register', 'App\Api\Controllers\UserController@register');// 测试

    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        // $api->post('decode', 'AccountController@decode');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之前在这里写api
        // $api->post('login', 'AccountController@login');
        $api->get('users/{id}', 'UserController@show');
    });
    $api->group(["namespace" => "App\Http\Controllers"], function ($api) {
        // 接口文档swaggler测试
        $api->get('show','OASController@show');
        $api->get('hello','OASController@hello');

        $api->get('/test', 'IndexController@test');// 测试
        // jwt测试
        $api->post('login', 'ApiJWTController@login');
        $api->post('register', 'ApiJWTController@register');
        $api->post('testaa', 'ApiJWTController@testaa');
        $api->post('testbb', 'ApiJWTController@testbb');

        //$api->group(['middleware' => 'auth.jwt'], function () {
        //    $api->get('logout', 'ApiJWTController@logout');
        //    $api->get('usera', 'ApiJWTController@getAuthUser');
        //
        //    $api->get('products', 'ProductController@index');
        //    $api->get('products/{id}', 'ProductController@show');
        //    $api->post('products', 'ProductController@store');
        //    $api->put('products/{id}', 'ProductController@update');
        //    $api->delete('products/{id}', 'ProductController@destroy');
        //});
        // 原文链接：https://blog.csdn.net/qq_37788558/article/details/91886363
        // 然后在标头请求中添加“Authorization：Bearer {token}”
        //$api->group(['prefix' => 'auth'], function () {
        //    $api->post('login', 'Auth\JwtAuthController@login');
        //    $api->post('logout', 'Auth\JwtAuthController@logout');
        //    $api->post('refresh', 'Auth\JwtAuthController@refresh');
        //    $api->post('me', 'Auth\JwtAuthController@me');
        //});

        // 文件上传 any(
        // $api->post('file/upload', 'IndexController@upload');
        $api->post('upload', 'UploadController@index');
        // $api->post('upload/test', 'UploadController@test');
        // excel
        $api->get('excel/test','ExcelController@test');
        $api->get('excel/export','ExcelController@export'); // 导出
        $api->get('excel/import','ExcelController@import'); // 导入
        $api->get('excel/import_test','ExcelController@import_test'); // 导入 - 测试

        // ------后台

        // 验证码 -- ok
//        $api->get('admin/ajax_captcha', 'Admin\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('admin/ajax_captcha_verify', 'Admin\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('admin/ajax_captcha', 'Admin\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('admin/ajax_captcha_verify', 'Admin\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('admin/ajax_send_mobile_vercode', 'Admin\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('admin/ajax_mobile_code_verify', 'Admin\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('admin/ajax_login', 'Admin\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('admin/ajax_login_sms', 'Admin\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('admin/ajax_password_save', 'Admin\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('admin/ajax_info_save', 'Admin\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        // 上传图片
        $api->post('admin/upload', 'Admin\QualityControl\UploadController@index');
        $api->post('admin/upload/ajax_del', 'Admin\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 系统管理员
        $api->any('admin/staff/ajax_alist', 'Admin\QualityControl\StaffController@ajax_alist');//ajax获得列表数据
        $api->any('admin/staff/ajax_del', 'Admin\QualityControl\StaffController@ajax_del');// 删除
        $api->post('admin/staff/ajax_save', 'Admin\QualityControl\StaffController@ajax_save');// 新加/修改
        $api->post('admin/staff/ajax_get_child', 'Admin\QualityControl\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/staff/ajax_get_areachild', 'Admin\QualityControl\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/staff/ajax_import_staff','Admin\QualityControl\StaffController@ajax_import'); // 导入员工

        $api->post('admin/staff/import', 'Admin\QualityControl\StaffController@import');// 导入excel
        $api->post('admin/staff/ajax_get_ids', 'Admin\QualityControl\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/staff/ajax_open', 'Admin\QualityControl\StaffController@ajax_open');// 审核操作(通过/不通过)
        $api->any('admin/staff/ajax_frozen', 'Admin\QualityControl\StaffController@ajax_frozen');// 操作(冻结/解冻)

        // 企业帐号管理
        $api->any('admin/company/ajax_alist', 'Admin\QualityControl\CompanyController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company/ajax_del', 'Admin\QualityControl\CompanyController@ajax_del');// 删除
        $api->post('admin/company/ajax_save', 'Admin\QualityControl\CompanyController@ajax_save');// 新加/修改
        $api->post('admin/company/ajax_get_child', 'Admin\QualityControl\CompanyController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company/ajax_get_areachild', 'Admin\QualityControl\CompanyController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company/ajax_import_staff','Admin\QualityControl\CompanyController@ajax_import'); // 导入员工

        $api->post('admin/company/import', 'Admin\QualityControl\CompanyController@import');// 导入excel
        $api->post('admin/company/ajax_get_ids', 'Admin\QualityControl\CompanyController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/company/ajax_open', 'Admin\QualityControl\CompanyController@ajax_open');// 审核操作(通过/不通过)
        $api->post('admin/company/ajax_frozen', 'Admin\QualityControl\CompanyController@ajax_frozen');// 操作(冻结/解冻)

        // 个人帐号管理
        $api->any('admin/user/ajax_alist', 'Admin\QualityControl\UserController@ajax_alist');//ajax获得列表数据
        $api->any('admin/user/ajax_del', 'Admin\QualityControl\UserController@ajax_del');// 删除
        $api->post('admin/user/ajax_save', 'Admin\QualityControl\UserController@ajax_save');// 新加/修改
        $api->post('admin/user/ajax_get_child', 'Admin\QualityControl\UserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/user/ajax_get_areachild', 'Admin\QualityControl\UserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/user/ajax_import_staff','Admin\QualityControl\UserController@ajax_import'); // 导入员工

        $api->any('admin/user/import', 'Admin\QualityControl\UserController@import');// 导入excel
        $api->post('admin/user/ajax_get_ids', 'Admin\QualityControl\UserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('admin/user/ajax_sign', 'Admin\QualityControl\UserController@ajax_sign');// 授权人审核操作(通过/不通过)
        $api->any('admin/user/ajax_open', 'Admin\QualityControl\UserController@ajax_open');// 审核操作(通过/不通过)
        $api->post('admin/user/ajax_frozen', 'Admin\QualityControl\UserController@ajax_frozen');// 操作(冻结/解冻)

        // 行业[一级分类]
        $api->any('admin/industry/ajax_alist', 'Admin\QualityControl\IndustryController@ajax_alist');//ajax获得列表数据
        $api->post('admin/industry/ajax_del', 'Admin\QualityControl\IndustryController@ajax_del');// 删除
        $api->post('admin/industry/ajax_save', 'Admin\QualityControl\IndustryController@ajax_save');// 新加/修改
        $api->post('admin/industry/ajax_get_child', 'Admin\QualityControl\IndustryController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/industry/ajax_get_areachild', 'Admin\QualityControl\IndustryController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/industry/ajax_import_staff','Admin\QualityControl\IndustryController@ajax_import'); // 导入员工

        $api->post('admin/industry/import', 'Admin\QualityControl\IndustryController@import');// 导入excel
        $api->post('admin/industry/ajax_get_ids', 'Admin\QualityControl\IndustryController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 城市[一级分类]
        $api->any('admin/citys/ajax_alist', 'Admin\QualityControl\CitysController@ajax_alist');//ajax获得列表数据
        $api->post('admin/citys/ajax_del', 'Admin\QualityControl\CitysController@ajax_del');// 删除
        $api->post('admin/citys/ajax_save', 'Admin\QualityControl\CitysController@ajax_save');// 新加/修改
        $api->post('admin/citys/ajax_get_child', 'Admin\QualityControl\CitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/citys/ajax_get_areachild', 'Admin\QualityControl\CitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/citys/ajax_import_staff','Admin\QualityControl\CitysController@ajax_import'); // 导入员工

        $api->post('admin/citys/import', 'Admin\QualityControl\CitysController@import');// 导入excel
        $api->post('admin/citys/ajax_get_ids', 'Admin\QualityControl\CitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 老师登录验证码 验证码
        $api->any('admin/sms_code/ajax_alist', 'Admin\QualityControl\SmsCodeController@ajax_alist');//ajax获得列表数据
        $api->post('admin/sms_code/ajax_del', 'Admin\QualityControl\SmsCodeController@ajax_del');// 删除
        $api->post('admin/sms_code/ajax_save', 'Admin\QualityControl\SmsCodeController@ajax_save');// 新加/修改
        $api->post('admin/sms_code/ajax_get_child', 'Admin\QualityControl\SmsCodeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_code/ajax_get_areachild', 'Admin\QualityControl\SmsCodeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/sms_code/ajax_import_staff','Admin\QualityControl\SmsCodeController@ajax_import'); // 导入员工

        $api->post('admin/sms_code/import', 'Admin\QualityControl\SmsCodeController@import');// 导入excel
        $api->post('admin/sms_code/ajax_get_ids', 'Admin\QualityControl\SmsCodeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 资质证书类型[一级分类]
        $api->any('admin/company_certificate_type/ajax_alist', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_alist');//ajax获得列表数据
        $api->any('admin/company_certificate_type/ajax_del', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_del');// 删除
        $api->post('admin/company_certificate_type/ajax_save', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_save');// 新加/修改
        $api->post('admin/company_certificate_type/ajax_get_child', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_certificate_type/ajax_get_areachild', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_certificate_type/ajax_import_staff','Admin\QualityControl\CompanyCertificateTypeController@ajax_import'); // 导入员工

        $api->post('admin/company_certificate_type/import', 'Admin\QualityControl\CompanyCertificateTypeController@import');// 导入excel
        $api->post('admin/company_certificate_type/ajax_get_ids', 'Admin\QualityControl\CompanyCertificateTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证行业分类[一级分类]
        $api->any('admin/ability_type/ajax_alist', 'Admin\QualityControl\AbilityTypeController@ajax_alist');//ajax获得列表数据
        $api->any('admin/ability_type/ajax_del', 'Admin\QualityControl\AbilityTypeController@ajax_del');// 删除
        $api->post('admin/ability_type/ajax_save', 'Admin\QualityControl\AbilityTypeController@ajax_save');// 新加/修改
        $api->post('admin/ability_type/ajax_get_child', 'Admin\QualityControl\AbilityTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/ability_type/ajax_get_areachild', 'Admin\QualityControl\AbilityTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/ability_type/ajax_import_staff','Admin\QualityControl\AbilityTypeController@ajax_import'); // 导入员工

        $api->post('admin/ability_type/import', 'Admin\QualityControl\AbilityTypeController@import');// 导入excel
        $api->post('admin/ability_type/ajax_get_ids', 'Admin\QualityControl\AbilityTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证
        $api->any('admin/abilitys/ajax_alist', 'Admin\QualityControl\AbilitysController@ajax_alist');//ajax获得列表数据
        $api->post('admin/abilitys/ajax_del', 'Admin\QualityControl\AbilitysController@ajax_del');// 删除
        $api->post('admin/abilitys/ajax_save', 'Admin\QualityControl\AbilitysController@ajax_save');// 新加/修改
        $api->post('admin/abilitys/ajax_get_child', 'Admin\QualityControl\AbilitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/abilitys/ajax_get_areachild', 'Admin\QualityControl\AbilitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/abilitys/ajax_import_staff','Admin\QualityControl\AbilitysController@ajax_import'); // 导入员工

        $api->post('admin/abilitys/import', 'Admin\QualityControl\AbilitysController@import');// 导入excel
        $api->post('admin/abilitys/ajax_get_ids', 'Admin\QualityControl\AbilitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证--报名管理
        $api->any('admin/ability_join/ajax_alist', 'Admin\QualityControl\AbilityJoinController@ajax_alist');//ajax获得列表数据
//        $api->post('admin/ability_join/ajax_del', 'Admin\QualityControl\AbilityJoinController@ajax_del');// 删除
//        $api->post('admin/ability_join/ajax_save', 'Admin\QualityControl\AbilityJoinController@ajax_save');// 新加/修改
//        $api->post('admin/ability_join/ajax_get_child', 'Admin\QualityControl\AbilityJoinController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_join/ajax_get_areachild', 'Admin\QualityControl\AbilityJoinController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('admin/ability_join/ajax_import_staff','Admin\QualityControl\AbilityJoinController@ajax_import'); // 导入员工

//        $api->post('admin/ability_join/import', 'Admin\QualityControl\AbilityJoinController@import');// 导入excel
//        $api->post('admin/ability_join/ajax_get_ids', 'Admin\QualityControl\AbilityJoinController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/ability_join/ajax_save_sample', 'Admin\QualityControl\AbilityJoinController@ajax_save_sample');// 保存取样
        // 企业能力附表
        $api->any('admin/company_schedule/ajax_alist', 'Admin\QualityControl\CompanyScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_schedule/ajax_del', 'Admin\QualityControl\CompanyScheduleController@ajax_del');// 删除
        $api->post('admin/company_schedule/ajax_save', 'Admin\QualityControl\CompanyScheduleController@ajax_save');// 新加/修改
        $api->post('admin/company_schedule/ajax_get_child', 'Admin\QualityControl\CompanyScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_schedule/ajax_get_areachild', 'Admin\QualityControl\CompanyScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_schedule/ajax_import_staff','Admin\QualityControl\CompanyScheduleController@ajax_import'); // 导入员工

        $api->post('admin/company_schedule/import', 'Admin\QualityControl\CompanyScheduleController@import');// 导入excel
        $api->post('admin/company_schedule/ajax_get_ids', 'Admin\QualityControl\CompanyScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业能力附表
        $api->any('admin/company_new_schedule/ajax_alist', 'Admin\QualityControl\CompanyNewScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('admin/company_new_schedule/ajax_del', 'Admin\QualityControl\CompanyNewScheduleController@ajax_del');// 删除
        $api->any('admin/company_new_schedule/ajax_save', 'Admin\QualityControl\CompanyNewScheduleController@ajax_save');// 新加/修改
        $api->any('admin/company_new_schedule/ajax_excel_save', 'Admin\QualityControl\CompanyNewScheduleController@ajax_excel_save');// 上传excel
        $api->post('admin/company_new_schedule/ajax_get_child', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('admin/company_new_schedule/ajax_get_areachild', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('admin/company_new_schedule/ajax_import_staff','Admin\QualityControl\CompanyNewScheduleController@ajax_import'); // 导入员工

        $api->post('admin/company_new_schedule/import', 'Admin\QualityControl\CompanyNewScheduleController@import');// 导入excel
        $api->post('admin/company_new_schedule/ajax_get_ids', 'Admin\QualityControl\CompanyNewScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('admin/company_new_schedule/up_word', 'Admin\QualityControl\CompanyNewScheduleController@up_word');// 上传word地址
        $api->post('admin/company_new_schedule/up_pdf', 'Admin\QualityControl\CompanyNewScheduleController@up_pdf');// 上传pdf地址
        $api->post('admin/company_new_schedule/up_excel', 'Admin\QualityControl\CompanyNewScheduleController@up_excel');// 上传excel地址

        // 企业后台 company
        // 验证码 -- ok
//        $api->get('company/ajax_captcha', 'WebFront\Company\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('company/ajax_captcha_verify', 'WebFront\Company\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('company/ajax_captcha', 'WebFront\Company\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('company/ajax_captcha_verify', 'WebFront\Company\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('company/ajax_send_mobile_vercode', 'WebFront\Company\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('company/ajax_mobile_code_verify', 'WebFront\Company\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('company/ajax_login', 'WebFront\Company\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('company/ajax_login_sms', 'WebFront\Company\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('company/ajax_password_save', 'WebFront\Company\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('company/ajax_info_save', 'WebFront\Company\QualityControl\IndexController@ajax_info_save');// 修改设置--ok
        $api->any('company/ajax_basic_save', 'WebFront\Company\QualityControl\IndexController@ajax_basic_save');// 修改基本信息设置--ok

        // 上传图片
        $api->post('company/upload', 'WebFront\Company\QualityControl\UploadController@index');
        $api->post('company/upload/ajax_del', 'WebFront\Company\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 个人帐号管理
        $api->any('company/user/ajax_alist', 'WebFront\Company\QualityControl\UserController@ajax_alist');//ajax获得列表数据
        $api->any('company/user/ajax_del', 'WebFront\Company\QualityControl\UserController@ajax_del');// 删除
        $api->post('company/user/ajax_save', 'WebFront\Company\QualityControl\UserController@ajax_save');// 新加/修改
        $api->post('company/user/ajax_get_child', 'WebFront\Company\QualityControl\UserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
        $api->post('company/user/ajax_get_areachild', 'WebFront\Company\QualityControl\UserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
        $api->post('company/user/ajax_import_staff','WebFront\Company\QualityControl\UserController@ajax_import'); // 导入员工

        $api->any('company/user/import', 'WebFront\Company\QualityControl\UserController@import');// 导入excel
        $api->post('company/user/ajax_get_ids', 'WebFront\Company\QualityControl\UserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('company/user/ajax_open', 'WebFront\Company\QualityControl\UserController@ajax_open');// 审核操作(通过/不通过)
        $api->post('company/user/ajax_frozen', 'WebFront\Company\QualityControl\UserController@ajax_frozen');// 操作(冻结/解冻)

        // 能力验证
        $api->any('company/abilitys/ajax_alist', 'WebFront\Company\QualityControl\AbilitysController@ajax_alist');//ajax获得列表数据
        $api->post('company/abilitys/ajax_join_save', 'WebFront\Company\QualityControl\AbilitysController@ajax_join_save');// 报名
        $api->any('company/abilitys/ajax_company_extend', 'WebFront\Company\QualityControl\AbilitysController@ajax_company_extend');// 获得企业扩展信息
        $api->any('company/abilitys/ajax_schedule_num', 'WebFront\Company\QualityControl\AbilitysController@ajax_schedule_num');// 获得企业上传的能力附表pdf数量
//        $api->post('company/abilitys/ajax_del', 'WebFront\Company\QualityControl\AbilitysController@ajax_del');// 删除
//        $api->post('company/abilitys/ajax_save', 'WebFront\Company\QualityControl\AbilitysController@ajax_save');// 新加/修改
//        $api->post('company/abilitys/ajax_get_child', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/abilitys/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/abilitys/ajax_import_staff','WebFront\Company\QualityControl\AbilitysController@ajax_import'); // 导入员工
//
//        $api->post('company/abilitys/import', 'WebFront\Company\QualityControl\AbilitysController@import');// 导入excel
//        $api->post('company/abilitys/ajax_get_ids', 'WebFront\Company\QualityControl\AbilitysController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证--报名管理
        $api->any('company/ability_join/ajax_alist', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_alist');//ajax获得列表数据
//        $api->post('company/ability_join/ajax_del', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_del');// 删除
//        $api->post('company/ability_join/ajax_save', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_save');// 新加/修改
//        $api->post('company/ability_join/ajax_get_child', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join/ajax_import_staff','WebFront\Company\QualityControl\AbilityJoinController@ajax_import'); // 导入员工

//        $api->post('company/ability_join/import', 'WebFront\Company\QualityControl\AbilityJoinController@import');// 导入excel
//        $api->post('company/ability_join/ajax_get_ids', 'WebFront\Company\QualityControl\AbilityJoinController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 能力验证--项目管理
        $api->any('company/ability_join_item/ajax_alist', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_alist');//ajax获得列表数据
//        $api->post('company/ability_join_item/ajax_del', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_del');// 删除
//        $api->post('company/ability_join_item/ajax_save', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_save');// 新加/修改
//        $api->post('company/ability_join_item/ajax_get_child', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join_item/ajax_get_areachild', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/ability_join_item/ajax_import_staff','WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_import'); // 导入员工

//        $api->post('company/ability_join_item/import', 'WebFront\Company\QualityControl\AbilityJoinItemsController@import');// 导入excel
//        $api->post('company/ability_join_item/ajax_get_ids', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->any('company/ability_join_item/ajax_save_result_sample', 'WebFront\Company\QualityControl\AbilityJoinItemsController@ajax_save_result_sample');// 新加/修改 -- 数据提交

        // 企业能力附表
        $api->any('company/company_schedule/ajax_alist', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('company/company_schedule/ajax_del', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_del');// 删除
//        $api->post('company/company_schedule/ajax_save', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_save');// 新加/修改
//        $api->post('company/company_schedule/ajax_get_child', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/company_schedule/ajax_get_areachild', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/company_schedule/ajax_import_staff','WebFront\Company\QualityControl\CompanyScheduleController@ajax_import'); // 导入员工

        $api->post('company/company_schedule/import', 'WebFront\Company\QualityControl\CompanyScheduleController@import');// 导入excel
//        $api->post('company/company_schedule/ajax_get_ids', 'WebFront\Company\QualityControl\CompanyScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        // 企业能力附表
        $api->any('company/company_new_schedule/ajax_alist', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_alist');//ajax获得列表数据
        $api->post('company/company_new_schedule/ajax_del', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_del');// 删除
        $api->post('company/company_new_schedule/ajax_save', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_save');// 新加/修改
        $api->any('company/company_new_schedule/ajax_excel_save', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_excel_save');// 上传excel
//        $api->post('company/company_new_schedule/ajax_get_child', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//        $api->post('company/company_new_schedule/ajax_get_areachild', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//        $api->post('company/company_new_schedule/ajax_import_staff','WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_import'); // 导入员工

        $api->post('company/company_new_schedule/import', 'WebFront\Company\QualityControl\CompanyNewScheduleController@import');// 导入excel
//        $api->post('company/company_new_schedule/ajax_get_ids', 'WebFront\Company\QualityControl\CompanyNewScheduleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

        $api->post('company/company_new_schedule/up_word', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_word');// 上传word地址
        $api->post('company/company_new_schedule/up_pdf', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_pdf');// 上传pdf地址
        $api->post('company/company_new_schedule/up_img', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_img');// 上传图片地址
        $api->post('company/company_new_schedule/up_excel', 'WebFront\Company\QualityControl\CompanyNewScheduleController@up_excel');// 上传excel地址

        // 用户中心 user
        // 验证码 -- ok
//        $api->get('user/ajax_captcha', 'WebFront\User\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('user/ajax_captcha_verify', 'WebFront\User\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('user/ajax_captcha', 'WebFront\User\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('user/ajax_captcha_verify', 'WebFront\User\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码--注册 -- ok
        $api->any('user/reg/ajax_send_mobile_vercode', 'WebFront\User\QualityControl\SMSRegController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('user/reg/ajax_mobile_code_verify', 'WebFront\User\QualityControl\SMSRegController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        // 手机验证码--登录 -- ok
        $api->any('user/ajax_send_mobile_vercode', 'WebFront\User\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('user/ajax_mobile_code_verify', 'WebFront\User\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
        $api->any('user/ajax_login', 'WebFront\User\QualityControl\IndexController@ajax_login');// 登陆--ok
        $api->any('user/ajax_login_sms', 'WebFront\User\QualityControl\IndexController@ajax_login_sms');// 登陆-手机短信验证码--ok
        $api->post('user/ajax_password_save', 'WebFront\User\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
        $api->any('user/ajax_info_save', 'WebFront\User\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        // 上传图片
        $api->post('user/upload', 'WebFront\User\QualityControl\UploadController@index');
        $api->post('user/upload/ajax_del', 'WebFront\User\QualityControl\UploadController@ajax_del');// 根据id删除文件

        // 前台 web
        // 验证码 -- ok
//        $api->get('web/ajax_captcha', 'WebFront\Web\QualityControl\IndexController@ajax_captcha');// api生成验证码
//        $api->post('web/ajax_captcha_verify', 'WebFront\Web\QualityControl\IndexController@ajax_captcha_verify');// api生成验证码-验证
        $api->get('web/ajax_captcha', 'WebFront\Web\QualityControl\CaptchaController@ajax_captcha');// api生成验证码--ok
        $api->post('web/ajax_captcha_verify', 'WebFront\Web\QualityControl\CaptchaController@ajax_captcha_verify');// api生成验证码-验证--ok

        // 手机验证码 -- ok
        $api->any('web/ajax_send_mobile_vercode', 'WebFront\Web\QualityControl\SMSController@ajax_send_mobile_vercode');// 发送手机验证码--ok
        $api->any('web/ajax_mobile_code_verify', 'WebFront\Web\QualityControl\SMSController@ajax_mobile_code_verify');// 发送手机验证码-验证--ok

        //// 登陆
//        $api->any('web/ajax_login', 'WebFront\Web\QualityControl\IndexController@ajax_login');// 登陆--ok
//        $api->post('web/ajax_password_save', 'WebFront\Web\QualityControl\IndexController@ajax_password_save');// 修改密码--ok
//        $api->any('web/ajax_info_save', 'WebFront\Web\QualityControl\IndexController@ajax_info_save');// 修改设置--ok

        // 登录 注册
        $api->any('web/ajax_reg', 'WebFront\Web\QualityControl\HomeController@ajax_reg');// 注册
        $api->any('web/ajax_perfect_company', 'WebFront\Web\QualityControl\HomeController@ajax_perfect_company');// 注册-补充企业资料
        $api->any('web/ajax_perfect_user', 'WebFront\Web\QualityControl\HomeController@ajax_perfect_user');// 注册-补充用户资料

        $api->any('web/ajax_login_company', 'WebFront\Web\QualityControl\HomeController@ajax_login_company');// 登陆----为登录测试  补充资料用
        $api->any('web/ajax_login_user', 'WebFront\Web\QualityControl\HomeController@ajax_login_user');// 登陆----为登录测试  补充资料用
        $api->any('web/company_ajax_alist', 'WebFront\Web\QualityControl\HomeController@company_ajax_alist');// 登陆----为登录测试  补充资料用--获得企业列表信息

        // 上传图片
        $api->post('web/upload', 'WebFront\Web\QualityControl\UploadController@index');
        $api->post('web/upload/ajax_del', 'WebFront\Web\QualityControl\UploadController@ajax_del');// 根据id删除文件

//        Route::middleware('auth:api')->get('/user', function (Request $request) {
//            return $request->user();
//        });
        $api->group(['middleware' => 'auth:api'], function ($api) {
            $api->get('/user', function (Request $request) {
                return $request->user();
            });
        });
    });
});

//// 接口文档swaggler测试
//Route::get('show','BaseController@show');
//Route::get('hello','BaseController@hello');

/**
 *
// jwt测试
Route::post('login', 'ApiJWTController@login');
Route::post('register', 'ApiJWTController@register');
Route::post('testaa', 'ApiJWTController@testaa');
Route::post('testbb', 'ApiJWTController@testbb');

//Route::group(['middleware' => 'auth.jwt'], function () {
//    Route::get('logout', 'ApiJWTController@logout');
//    Route::get('usera', 'ApiJWTController@getAuthUser');
//
//    Route::get('products', 'ProductController@index');
//    Route::get('products/{id}', 'ProductController@show');
//    Route::post('products', 'ProductController@store');
//    Route::put('products/{id}', 'ProductController@update');
//    Route::delete('products/{id}', 'ProductController@destroy');
//});
// 原文链接：https://blog.csdn.net/qq_37788558/article/details/91886363
// 然后在标头请求中添加“Authorization：Bearer {token}”
//Route::group(['prefix' => 'auth'], function () {
//    Route::post('login', 'Auth\JwtAuthController@login');
//    Route::post('logout', 'Auth\JwtAuthController@logout');
//    Route::post('refresh', 'Auth\JwtAuthController@refresh');
//    Route::post('me', 'Auth\JwtAuthController@me');
//});

// 文件上传 any(
// Route::post('file/upload', 'IndexController@upload');
Route::post('upload', 'UploadController@index');
// Route::post('upload/test', 'UploadController@test');
// excel
Route::get('excel/test','ExcelController@test');
Route::get('excel/export','ExcelController@export'); // 导出
Route::get('excel/import','ExcelController@import'); // 导入
Route::get('excel/import_test','ExcelController@import_test'); // 导入 - 测试


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
 *
 */


/*
Route::post('file/upload', function(\Illuminate\Http\Request $request) {
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
        $photo = $request->file('photo');
        $extension = $photo->extension();
        //$store_result = $photo->store('photo');
        $store_result = $photo->storeAs('photo', 'test.jpg');
        $output = [
            'extension' => $extension,
            'store_result' => $store_result
        ];
        print_r($output);exit();
    }
    exit('未获取到上传文件或上传过程出错');
});
*/

//  将Passport集成到您的Laravel API https://justlaravel.com/integrate-passport-laravel-api/
//Route::get('/users', 'DetailController@index')->middleware('auth:api');


// laravelpassport实现API认证（Laravel5.6）---authuser+jwt格式token的登陆状态
// https://segmentfault.com/a/1190000017560443?utm_source=tag-newest
//
//Route::group(['namespace' => 'Api'], function () {
//    // 登录
//    Route::post('login', 'LoginController@login');
//    // 注册
//    Route::post('register', 'LoginController@register');
//    Route::group(['middleware' => 'auth:api'], function () {
//        // 用户信息
//        Route::get('user', 'LoginController@read');
//    });
//});

// https://learnku.com/docs/laravel/5.6/passport/1380
// 将授权码转换为访问令牌
// 路由 /oauth/token 返回的 JSON 响应中会包含 access_token 、refresh_token 和 expires_in 属性。
// expires_in 属性包含访问令牌的有效期（单位：秒）。
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjgwOTAzYmEwNGI4MjlhMDliY2MwY2E5ZTZhY2MxZGU0MTljNzk1YTU1NTk3YjZjZDcxOWMxMThiMzRjMGNlMmFjZGZjNWVkOWM2ZWQ0NzlkIn0.eyJhdWQiOiI1IiwianRpIjoiODA5MDNiYTA0YjgyOWEwOWJjYzBjYTllNmFjYzFkZTQxOWM3OTVhNTU1OTdiNmNkNzE5YzExOGIzNGMwY2UyYWNkZmM1ZWQ5YzZlZDQ3OWQiLCJpYXQiOjE1NzE2Njg3NTEsIm5iZiI6MTU3MTY2ODc1MSwiZXhwIjoxNTcyOTY0NzUxLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.i7h1RTkrstkKOUMwaGTjzDsvo5sptYNZV_z2e8Pt3feTvU1pwBKHy-c7c48TST99PZ5MrG9ZQsrn5TnkuUkmuIxRC55Of-FkJUtADWFyxiO0LB-YJFp5pOp7-qBoTmWDWObo1nv58yX2eiy-0aTEN8i8VWcoYI-y_oJbMaYpsBA6eWZ9qGRL06L3K5ZEyN-I_5wtb03fy9QMsZiOohqPv0qcTs7Hr9BQ26PH0vQBxI07UxQq-fV57oNpyiGNT9_GFiWjHlwE7As-n_Y3UzDEUUk5YfSABQaeu5qhCYye-KOVuAFxHTnlnMh9yX36XcAy85WYigMhe2QIgCgu-WspHRafDgQvd8SYn6hObJX4fF_lZE8NSxKTYp0jrpSVWJIVXw31umiz2HBGJ6awAcb07B2o4Asa4LJIvx2bMc3okyAdIxUEsZaGM6zLOW8KD22o8tsDqLlv-D5SbEpycIUqyaCg56HzjuxlMzcYxA27S7qeTrmn403Y6cEWZ-rqod2UzuPbMJeLx7XqUReNAo27--6c2u3zsY9Gglo3dc42cKD7REeQeu2QbXSyiDi0YRWXFHtw-Rglm9Yzu37Chiy4ipSHBTYiI5oMOJ8vQdo_JEe3K0fVNO3aKbMJvnP33dZ0t8tufvGbVumnV4TT9RTunvsEUtUN8Dt04kDMyoXppuI",
 *      "refresh_token": "def502003d4792e10b8a83421e37f1044aacd9c4d34fe3df889236b89547385b891926c78664c87ea7aab5682c8e20f90cec41d22e79d421a4fcd238ca23010b7e007361c4e4121d2817ab86ab6289e6fae97049358c10c5baec98a78d466fa05c93f921bf413530d4487409b66ec980c3e41a9521a35df6512eab113b5c52ac913f17d3e02a4f10ddb8352efd544ea9c445eb7767ddc9d3ae3b70e8cd447eebe6fac7827b1a78f53e270b403ddb5da1fb74a386e7b678a65592ec0c4f082e0013d5ee7944f91bef2c74c3b1af5f52439506dbdf5ab7b25de20994d9ec9498f752ba22cb1a01aebcac8f4e6b74e2eb2112bf11795018ff0f4af0d6ef88c5b7a913fe0bc4955c422142ba824eedf2087326d99beaf75fe821e131e360840eacf49d886eb4c325f6900e073cf473462d4df8379b5707040a051052681d797b0c5724cd1d41a32d27c4170433dddc3f239cd75f0640be6783b9acb8aa0c620b2c9577"
 *  }
 *
 */
//Route::get('/auth/callback', function (Request $request) {
//    $http = new GuzzleHttp\Client;
//
//    $response = $http->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'authorization_code',
//            'client_id' => 5,//'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',// 'client-secret',
//            'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//            'code' => $request->code,
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});

// 隐式授权令牌回调
/**
 * 有个问题：为什么返回的地址参数是#而不是?号
 *  http://runbuy.admin.cunwo.net/api/auth/callback/implicit#access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIyMmZiY2E4ZGNmOGQ3NzAwNTMxOGI2YjlkYjM3YWIzZmZlODg5YjFiZjQwZTYzYTU3ZTQ3ZjFkZjlmZGMyNWQwNzU2MjdlYzhmMDM5NWE3In0.eyJhdWQiOiI1IiwianRpIjoiMjIyZmJjYThkY2Y4ZDc3MDA1MzE4YjZiOWRiMzdhYjNmZmU4ODliMWJmNDBlNjNhNTdlNDdmMWRmOWZkYzI1ZDA3NTYyN2VjOGYwMzk1YTciLCJpYXQiOjE1NzE3MDUxMjcsIm5iZiI6MTU3MTcwNTEyNywiZXhwIjoxNTczMDAxMTI3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.dNXslW8QHo7iQTOWTIQ3h0RXpL7PqUHdyE1QAzr-0osDCuoNQbHhWq2pcXJxCwJnO1YeNlsiBswkBkBb5QcR9UYJNL6ZmnbVBsJxxRBOY2TdPVd26bxDdHN0g3tBLEt4OB5uNT20fDBdBQPU9nAF3hEqBhEpN3kJiKmR4E0QsNKn65nobXKhhjTg4cuuopT2ZK7J1VNQQryIb4IOgDUNIGR-qb_gYqoi6J5son9wtAHmI72nz1zG7gitdt_yV1VYdkGx3fSsfL3qt0HDaflhBdi4BEL-KSZgmy3rgHO5TNx5idszDoHzpwzxuFEIhzUZoMpg5Nj-vjiqFYmZ6XUOPEhBq5V77n1h4Hvpj9xXNH3ckO7VsAy6wsHol0hjDWER-WeOmyakT2mADYgtixcinmW7ZYJEcHhRAwyBTA-rY8iVz013NHsVIJbocntdNdpvvuQc3Crqu1CnKorBYPZjsfI15vISE8UDRYC6z7MNYPV5XrJ9QvI_PFnOWL6jDnIieAjT_wB-BbBNpFQytOwjRQL5wIJXMHcon-SPqkPm41Dlt2nHjepPtsViHZXwyJpHL3ZvxwICYmpFE4Vtr5wRapQeeirnwYPOoBcbMTurSS9J3WNqhuEkkxCX90q29tI6R_u38eFz8LFmedZoI4LrZTo7mZCysZroonuy6LAcLic&token_type=Bearer&expires_in=1296000
 *
 */
//Route::get('/auth/callback/implicit', function (Request $request) {
//    pr($request->all());
//});
// 刷新令牌
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI0NjQyMzQyNzRjN2FlYWFiYmZlMjRiZGVkZjEyZjQzZmQ4YWUwOThkZjA5ODM3ZDE0MWIzYzgxYjhjZWFjZjViNjgzYTBkOTUyNTIyZTgwIn0.eyJhdWQiOiI1IiwianRpIjoiYjQ2NDIzNDI3NGM3YWVhYWJiZmUyNGJkZWRmMTJmNDNmZDhhZTA5OGRmMDk4MzdkMTQxYjNjODFiOGNlYWNmNWI2ODNhMGQ5NTI1MjJlODAiLCJpYXQiOjE1NzE2Njk3MTYsIm5iZiI6MTU3MTY2OTcxNiwiZXhwIjoxNTcyOTY1NzE2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.dhPgP6vEGU6bwrjiPpFJsTjyKH0sXUdwslAZgF-2NGbFFWf6FX5v-wPGZIXQE_qmVFgqP6Nj0To0uUZ4f4ngdUmlCbPVjiWG6UfaDCRKqKTYO41fhTBKtsJb3pE5tnes0hWp8tPQD_eThjLD48NpTDKRdo1OO_GYqVt9XPzz4jZIu2Piqx0t-BYHDiL4RwS0itrUAxo14cb2vhiZSrqW9XOH9QgeXCY5rcMfHqtOjqNU10-VwK-5C3qruSY843Uq9dtjv6iEmpHoyfi2fPjYDeaNZrPOErsFw8WwB6B_VQbyOX3jSBJ91ZcBrLf-Bi8nll9DltFNNiDcRiuVlRLK-S9aVt672vA0zV0AjFjzak5YuYOIRsplKoRpW6G830rG-1x1YTFpbfV91IH0CtE21VhNhzz0X0bWvGIuYkbULJS3Mwr7hzpCHDDcovVgQb08Q-M9i3FvpFQGWvQkTnJ8IA4EEDjbC2L24n48y5yzdnJ4T3d_Xbos0dI1gI4XVoP4qL8BuPiSUBKgFC5vHM3CgOt672sXMM7c59PzIRyE5ws6sBKEAGa5CDDDw8n11IzvsfsFwZ2a8KMjBNaxqQDWv64CB5h4qwwoQ2X2kPCVa6K-ZSweDqXVjiKZAv-Ank-3IF9kPer20_od1nkTlfNxcirzx_3IbEv7ClQEpMO7xXM",
 *       "refresh_token": "def50200984e3f8282410e29122a90f934ab9b1e54e389749ab2b1ff41d1df8b119562cf372bc564cef8c5e3e45169b979062d2a8c6e2dda9e8ddb6699d850453558e1b7f985ffef93e15bcd80b9ef5d7d3fb6b07df2bf15c9aeb0d9126db592d59da98ead84b4c538a5ac690de609aaa7f82a3d76d7d2c2b97929ab68c8ec5571e57e372f6336806501d4cc8687fad1d1dd692bea30fc3040b2d1ed488e8c655cc8ed6e5d5747b70666b7e34f37751cfb207d2c48aa3c054be89241756352ecf8c1a3999c38c1262410e458606190119d4455ca68fd13a3e11cee8b095b3ac8fa739f62cb06705104ea39409353ce7b9e1230b573ead483733c6167987b0d6e52e28191615ee3ddcbe3c291d7090d66ed7f6ec8f4cf74b96ea99cf756c7e87a73d11d1fa4be1aca2c2cf0e48f756ef74d421016b3f5dd336410dff4dc1d2693c501aca32bf185defaf9fdaf8d6b1b11a983479918074bfd8438c17ae662769c88"
 *   }
 */
//Route::get('/auth/refreshToken', function (Request $request) {
//    $http = new GuzzleHttp\Client;
//
//    $response = $http->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'refresh_token',
//            'refresh_token' => 'def502003d4792e10b8a83421e37f1044aacd9c4d34fe3df889236b89547385b891926c78664c87ea7aab5682c8e20f90cec41d22e79d421a4fcd238ca23010b7e007361c4e4121d2817ab86ab6289e6fae97049358c10c5baec98a78d466fa05c93f921bf413530d4487409b66ec980c3e41a9521a35df6512eab113b5c52ac913f17d3e02a4f10ddb8352efd544ea9c445eb7767ddc9d3ae3b70e8cd447eebe6fac7827b1a78f53e270b403ddb5da1fb74a386e7b678a65592ec0c4f082e0013d5ee7944f91bef2c74c3b1af5f52439506dbdf5ab7b25de20994d9ec9498f752ba22cb1a01aebcac8f4e6b74e2eb2112bf11795018ff0f4af0d6ef88c5b7a913fe0bc4955c422142ba824eedf2087326d99beaf75fe821e131e360840eacf49d886eb4c325f6900e073cf473462d4df8379b5707040a051052681d797b0c5724cd1d41a32d27c4170433dddc3f239cd75f0640be6783b9acb8aa0c620b2c9577',
//            'client_id' => 5,//'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',// 'client-secret',
//            'scope' => '',
//            // 'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//            // 'code' => $request->code,
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});


// 客户端凭据授权令牌
//客户端凭据授权适用于机器到机器的认证。例如，你可以在通过 API 执行维护任务中使用此授权。
//要使用这种授权，你首先需要在 app/Http/Kernel.php 的 $routeMiddleware 变量中添加新的中间件：
/*
 *
 *   {
 *        "token_type": "Bearer",
 *        "expires_in": 1296000,
 *        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZlMGMxMTkzMTNhZDk0NDc0NGM0NjQ4MjBlMzgyMjM3M2NkMGFjMTAxYjUzZjg3YjU0NDczMGM2Y2QyMDQyY2U4ZjkxYTM1ZTcyNGMyM2MwIn0.eyJhdWQiOiI1IiwianRpIjoiZmUwYzExOTMxM2FkOTQ0NzQ0YzQ2NDgyMGUzODIyMzczY2QwYWMxMDFiNTNmODdiNTQ0NzMwYzZjZDIwNDJjZThmOTFhMzVlNzI0YzIzYzAiLCJpYXQiOjE1NzE3MDcxMTMsIm5iZiI6MTU3MTcwNzExMywiZXhwIjoxNTczMDAzMTEzLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.pz_dIe_gsgRYOsEpTKizIGI41rXAobm60_SHWgvy1SK28-0gypNV0PFyBjLM8sVJYez8cAZd2gAGrFmAaeB8Z9q64tiWj2I-FTKx5yggNohzMA0T9wu9P-m0YDX4NVCz1ZWAGrSAlPH4Qxtjrof6N-GibL-APXinXE-cGv6P-SW-yYeMlqw7EYkWBglJ28cTH4ZQ8fp7aBm7FvILdKetVpt2vBqLsl-UNckDqob3nie6skdHVcZUZoXrRN_fzYGP0sxrK_Y6AjnkcwidqHZWyjBLgqdIU_ErK_OVKGW4yDmmEo17mvxj2uF6nzbIQwDK78Mjq9rgwWeb3K53MrkRKYgFXRT7qBgHl3S4L8i4bNfupOMCQeAU3NrB1iE3Ko2kjX9ZAS93cO0mLihXBA0XhpIZnYexQAMzEPdMeeFBAyLH6VeoQVePRDXRwj0BqzLcwznDr0DeQUzD1qQ8AHCSUhlRZTGWjUJxPd8SEN4xUb40LfnEKbEd6PZJy5cwM7tLMs7xcFrfyJ1e-hpOcFK0wQdXD1xUU4IKopTZypMg8GE5fJ2-QW3HwLPM3hEJ_Hi1VMSfkqA9ksiudmNcYKXqIYrtTa58XFbIfxgMdafV7Z1zXNzWNZtiIGL3LPm-Ccy7-bUz3oisvX-LMFPWzF4SYqx1oD8MFjNPxQYg_bSVPS8"
 *    }
 *
 */
//Route::get('/auths/client_credentials', function(Request $request) {
//    $guzzle = new GuzzleHttp\Client;
//    $response = $guzzle->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'client_credentials',
//            'client_id' => 5,// 'client-id',
//            'client_secret' => 'cJN5b2CXvSovrUdQsqJLLOYNM3vKjkaNsSYFqrBm',//'client-secret',
//            'scope' => '',// 'your-scope',
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//    // return json_decode((string) $response->getBody(), true)['access_token'];
//});// ->middleware('client');

// 使用 Laravel Passport 处理 API 认证 https://juejin.im/post/5d8ed3536fb9a04e0925f9dd
//Route::group([
//    'prefix' => 'auth'
//], function () {
//    Route::post('login', 'AuthController@login');
//    Route::post('signup', 'AuthController@signup');
//
//    Route::group([
//        'middleware' => 'auth:api'
//    ], function() {
//        Route::get('logout', 'AuthController@logout');
//        Route::get('user', 'AuthController@user');
//    });
//});

//使用 Laravel Passport 为你的 REST API 增加用户认证功能
//https://zhuanlan.zhihu.com/p/64902443

//Route::post('login', 'PassportaaController@login');
//Route::post('register', 'PassportaaController@register');
//
//Route::middleware('auth:api')->group(function () {
//    Route::get('user', 'PassportaaController@details');
//
//    Route::resource('products', 'ProductaaController');
//});

//laravel5.4 使用Dingo/Api v2.0+Passport实现api认证
//https://blog.csdn.net/qq_20455399/article/details/79262002

//Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');
//
//Route::group(['middleware' => 'auth:api'], function(){
//    Route::post('details', 'API\UserController@details');
//});

// 创建路由(router/api.php) 验证为：auth中间件，guards为api
// http://www.manongjc.com/article/106150.html
//Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');
//
//Route::group(['middleware' => 'auth:api'], function(){
//    Route::post('details', 'API\UserController@details');
//});
