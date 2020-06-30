<?php

namespace App\Http\Controllers\Web\DogTools;

use App\Business\Controller\API\DogTools\CTAPIStaffBusiness;
use App\Business\Controller\API\DogTools\CTAPITablesBusiness;
use App\Business\Controller\API\DogTools\CTAPITemplatesBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Captcha\CaptchaCode;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends BasicController
{
    public function test(){
         phpinfo();
//        $this->company_id = 1;
//        $cityList = CTAPILrChinaCityBusiness::getList($request, $this, 2 + 4, [], []);
//        pr($cityList);

        // 测试数据模型属性
//        $attr = APIRunBuyRequest::getAttrApi('RunBuy\LrChinaCity', 'status_arr', 0, 1 );
//        pr($attr);
        // 测试调用数据模型方法
//        $tableName = APIRunBuyRequest::exeMethodApi('RunBuy\LrChinaCity', 'getTable', [], 1 );
//        pr($tableName);

        // 获得数据中间层属性
//        $attr = APIRunBuyRequest::getBusinessAttrApi('RunBuy\LrChinaCity', 'attrTest', 1, 1 );
//        pr($attr);
        // 测试调用数据模型方法
//        $tableName = APIRunBuyRequest::exeBusinessMethodApi('RunBuy\LrChinaCity', 'testMethod', ['1111','222'], 1 );
//        pr($tableName);

    }

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        // 获得模板库
//        $relations = [];//  ['siteResources']
//        $extParams = [
//            'handleKeyArr' => ['templateType', 'siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//        ];
//        $templatesList = CTAPITemplatesBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
//        $reDataArr['templates'] = $templatesList['result']['data_list'] ?? [];
//        pr($templatesList);

        return view('web.index', $reDataArr);
    }

    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha(Request $request)
//    {
//        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default');// app('captcha')->create('default', true);
//
//        return ajaxDataArr(1, $captchaParams, '');
//    }

    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha_verify(Request $request)
//    {
//        $captcha_code = CommonRequest::get($request, 'captcha_code');
//        $captcha_key = CommonRequest::get($request, 'captcha_key');
////        if(!captcha_api_check($captcha_code, $captcha_key)) {
////            Cache::forget($captcha_key);
////            return ajaxDataArr(0, null, '验证码错误');
////        }
////        Cache::forget($captcha_key);
//        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
//        return ajaxDataArr(1, ['data' => 1], '验证码正确');
//    }

    /**
     * 登陆
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login(Request $request)
    {
        $reDataArr = $this->reDataArr;
        return view('web.login', $reDataArr);
    }

    /**
     * 登陆--帐号密码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login_pd(Request $request)
    {
        $reDataArr = $this->reDataArr;
        return view('web.login_pd', $reDataArr);
    }

    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_captcha(Request $request)
    {
        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default');// app('captcha')->create('default', true);

        return ajaxDataArr(1, $captchaParams, '');
    }

    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_captcha_verify(Request $request)
    {
        $captcha_code = CommonRequest::get($request, 'captcha_code');
        $captcha_key = CommonRequest::get($request, 'captcha_key');
//        if(!captcha_api_check($captcha_code, $captcha_key)) {
//            Cache::forget($captcha_key);
//            return ajaxDataArr(0, null, '验证码错误');
//        }
//        Cache::forget($captcha_key);
        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
        return ajaxDataArr(1, ['data' => 1], '验证码正确');
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function password(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $user_info = $this->user_info;
//        $reDataArr = array_merge($reDataArr, $user_info);
//        return view('web.admin.password', $reDataArr);
//    }

    /**
     * 显示
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function info(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $user_info = $this->user_info;
//
//        $reDataArr['adminType'] =  CTAPIStaffBusiness::$adminType;
//        $reDataArr['defaultAdminType'] = $user_info['admin_type'] ?? 0;// 列表页默认状态
//        $reDataArr = array_merge($reDataArr, $user_info);
//        return view('web.admin.info', $reDataArr);
//    }

    /**
     * err404
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function err404(Request $request)
    {
        $reDataArr = $this->reDataArr;
        return view('404', $reDataArr);
    }

    /**
     * @ OA\Post(
     *     path="/api/web/ajax_login",
     *     tags={"前端-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行登录",
     *     operationId="webIndexAjax_login",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @ OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @ OA\Parameter(ref="#/components/parameters/Parameter_Object_RunBuy_captcha_captcha_key"),
     *     @ OA\Parameter(ref="#/components/parameters/Parameter_Object_RunBuy_captcha_captcha_code"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_RunBuy_info_staff_login"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * ajax保存数据
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,2, 1, 1);
    }

    public function ajax_mobile_reg_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,2, 2, 2);
    }
    /**
     * 注销
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function logout(Request $request)
    {
        // $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('web/login');
    }

    /**
     * ajax修改密码
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_password_save(Request $request)
//    {
//        $this->InitParams($request);
//        return CTAPIStaffBusiness::modifyPassWord($request, $this);
//    }

    /**
     * ajax 修改设置
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info_save(Request $request)
//    {
//        $this->InitParams($request);
//
//        $id = $this->user_id;
//        $company_id = $this->company_id;
//        $admin_username = CommonRequest::get($request, 'admin_username');
//        $mobile = CommonRequest::get($request, 'mobile');
//        $real_name = CommonRequest::get($request, 'real_name');
//        $sex = CommonRequest::getInt($request, 'sex');
//        $tel = CommonRequest::get($request, 'tel');
//        $qq_number = CommonRequest::get($request, 'qq_number');
//
//        $saveData = [
//            'admin_username' => $admin_username,
//            'mobile' => $mobile,
//            'real_name' => $real_name,
//            'sex' => $sex,
//            'gender' => $sex,
//            'tel' => $tel,
//            'qq_number' => $qq_number,
//        ];
//        $extParams = [
//            // 'judgeDataKey' => 'replace',// 数据验证的下标
//        ];
//        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 下载二维码
     *
     * @param Request $request
     * @param int $id id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down(Request $request,$id = 0)
//    {
//        $this->InitParams($request);
//        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
//        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
//
//        $info = CTAPITablesBusiness::getInfoData($request, $this, $id, ['id','table_name','has_qrcode','qrcode_url'], $relations, []);
//        $has_qrcode = $info['has_qrcode'] ?? 1;
//        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png
//        $qrcode_url_old = $info['qrcode_url_old'] ?? '';// /resource/company/1/images/qrcode/tables/1.png
//        if($has_qrcode != 2 ) die('记录不存在或未生成二维码');
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $res = DownFile::downFilePath(2, $publicPath . $qrcode_url_old);
//        if(is_string($res)) echo $res;
//    }

    /**
     * 下载网页打印机驱动
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down_drive(Request $request)
//    {
////        $this->InitParams($request);
//        // $this->source = 2;
////        $reDataArr = $this->reDataArr;
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $fileName = '/CLodopPrint_Setup_for_Win32NT.exe';
//        $res = DownFile::downFilePath(2, $publicPath . '/' . $fileName);
//        if(is_string($res)) echo $res;
//    }

    /**
     * @OA\Get(
     *     path="/api/web/ajax_send_mobile_vercode",
     *     tags={"手机号注册登录"},
     *     summary="发送手机注册验证码",
     *     description="验证码2分钟内有效，过期请重新发送。未注册有效手机号。限:10次/天；30次/月；50次/半年；",
     *     operationId="dogtoolsWebStaffAjax_send_mobile_vercode",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_result_data_int_object"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_send_mobile_vercode(Request $request)
    {
        $mobile = CommonRequest::get($request, 'mobile');
        $countryCode = '86';
        $templateParams = [];
        // 发送手机验证码
        CTAPIStaffBusiness::sendSMSCodeLimit($request, $this, 'reg', $mobile, $countryCode, $templateParams, 1);

        return ajaxDataArr(1, ['data' => 1], '');
    }

    /**
     * @OA\Post(
     *     path="/api/web/ajax_mobile_code_verify",
     *     tags={"手机号注册登录"},
     *     summary="发送手机注册验证码校验是否正确",
     *     description="验证码2分钟内有效，过期请重新发送。未注册有效手机号。限:10次/天；30次/月；50次/半年；",
     *     operationId="dogtoolsWebStaffAjax_mobile_code_verify",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_mobile_code"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_result_data_int_object"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_RunBuy_multi_brands"}
     */
    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_mobile_code_verify(Request $request)
    {
        $mobile = CommonRequest::get($request, 'mobile');
        $countryCode = '86';
        $mobile_vercode = CommonRequest::get($request, 'mobile_vercode');
        // 发送手机验证码验证有效性
        CTAPIStaffBusiness::sMSCodeVerify($request, $this, 'reg', $mobile, $countryCode,  $mobile_vercode, false);
        return ajaxDataArr(1, ['data' => 1], '');
    }

}
