<?php

namespace App\Http\Controllers\Web\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
// use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Captcha\CaptchaCode;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends BasicRegController
{
    public function test(Request $request){
        $extParams['sqlParams']['whereIn']['id'] = 123;
        pr($extParams);
            $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        //pr($this->getUserInfo());
        //die;
        pr($this->user_id);
        echo '1111';
    }

    /**
     * 登录页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login(Request $request)
    {
        // $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        return view('web.QualityControl.home.login', $reDataArr);
    }

    /**
     * 注册页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function reg(Request $request)
    {
        // $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        return view('web.QualityControl.home.reg', $reDataArr);
    }

    /**
     * 完善企业资料页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function perfect_company(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $info = $this->user_info;
        if(empty($info)){
//            $reDataArr['errorMsg'] = '您还没有注册！';
//            $reDataArr['isShowBtn'] = (1 | 2);// 1:显示“回到首页”；2：显示“返回上页”
//            return $this->errorView($reDataArr, 'error');
            return redirect('web/login');
        }
        $reDataArr['info'] = $info;
        // 获得城市KV值
        $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
        $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

        // 所属行业
        $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
        $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

        // 企业--企业类型1检测机构、2生产企业
        $reDataArr['companyType'] =  CTAPIStaffBusiness::$companyTypeArr;
        $reDataArr['defaultCompanyType'] = $info['company_type'] ?? -1;// 列表页默认状态

        // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
        $reDataArr['companyProp'] =  CTAPIStaffBusiness::$companyPropArr;
        $reDataArr['defaultCompanyProp'] = $info['company_prop'] ?? -1;// 列表页默认状态

        // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
        $reDataArr['companyPeoples'] =  CTAPIStaffBusiness::$companyPeoplesNumArr;
        $reDataArr['defaultCompanyPeoples'] = $info['company_peoples_num'] ?? -1;// 列表页默认状态
        return view('web.QualityControl.home.perfect_company', $reDataArr);
    }

    /**
     * 完善个人资料页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function perfect_user(Request $request)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $info = $this->user_info;
        if(empty($info)){
//            $reDataArr['errorMsg'] = '您还没有注册！';
//            $reDataArr['isShowBtn'] = (1 | 2);// 1:显示“回到首页”；2：显示“返回上页”
//            return $this->errorView($reDataArr, 'error');
            return redirect('web/login');
        }
        $reDataArr['info'] = $info;
        // 获得城市KV值
        $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
        $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

        return view('web.QualityControl.home.perfect_user', $reDataArr);
    }

    /**
     * @OA\Post(
     *     path="/api/web/ajax_reg",
     *     tags={"前端-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行注册",
     *     operationId="webIndexAjax_reg",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_repass"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_key"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_code"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff_login"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_multi_brands"}
     */
    /**
     * ajax保存数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_reg(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        $admin_type = CommonRequest::getInt($request, 'admin_type');
        if(!in_array($admin_type, [2, 4])) throws('帐户类型无效！');
        $regInitData = [
            'issuper' => 2,
            'company_type' => 0,// 企业类型1检测机构、2生产企业
            'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
            'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
            'account_status' => 1// 状态 1正常 2冻结
        ];
        // 企业和用户需要审核
        if(in_array($admin_type, [2, 4])) $regInitData['open_status'] = 1;

        $userInfo = CTAPIStaffBusiness::loginCaptchaCode($request, $this,$admin_type, 1, 1, 2, $regInitData);
        // 保存注册信息
        $preKey = CommonRequest::get($request, 'preKey');// 0 小程序 1后台[默认]
        if(!is_numeric($preKey)) $preKey = 1;
        $redisKey = $this->setUserInfo($userInfo['id'], $preKey);
        $userInfo['redisKey'] = $redisKey;
        return ajaxDataArr(1, $userInfo, '');

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
         $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('web/login');
    }

    /**
     * ajax保存数据--注册-补充企业资料
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_perfect_company(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // 判断是否就是登录的用户
        if($id != $this->user_info['id']) throws('参数[id]有误！');
        if($this->user_info['admin_type'] != 2)  throws('非企业用户不可进行此操作！');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $company_name = CommonRequest::get($request, 'company_name');
        $company_credit_code = CommonRequest::get($request, 'company_credit_code');
        $company_is_legal_persion = CommonRequest::getInt($request, 'company_is_legal_persion');
        if($company_is_legal_persion != 1) $company_is_legal_persion = 2;
        $company_legal_credit_code = CommonRequest::get($request, 'company_legal_credit_code');
        $company_legal_name = CommonRequest::get($request, 'company_legal_name');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $company_type = CommonRequest::getInt($request, 'company_type');
        $company_prop = CommonRequest::get($request, 'company_prop');
        $addr = CommonRequest::get($request, 'addr');
        $zip_code = CommonRequest::get($request, 'zip_code');
        $fax = CommonRequest::get($request, 'fax');
        $email = CommonRequest::get($request, 'email');
        $company_legal = CommonRequest::get($request, 'company_legal');
        $company_peoples_num = CommonRequest::getInt($request, 'company_peoples_num');
        $company_industry_id = CommonRequest::getInt($request, 'company_industry_id');
        $company_certificate_no = CommonRequest::get($request, 'company_certificate_no');
        $company_contact_name = CommonRequest::get($request, 'company_contact_name');
        $company_contact_mobile = CommonRequest::get($request, 'company_contact_mobile');
        $company_contact_tel = CommonRequest::get($request, 'company_contact_tel');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $saveData = [
            'admin_type' => $this->user_info['admin_type'],
            'is_perfect' => 2,
            'company_name' => $company_name,
            'company_credit_code' => $company_credit_code,
            'company_is_legal_persion' => $company_is_legal_persion,
            'company_legal_credit_code' => $company_legal_credit_code,
            'company_legal_name' => $company_legal_name,
            'city_id' => $city_id,
            'company_type' => $company_type,
            'company_prop' => $company_prop,
            'addr' => $addr,
            'zip_code' => $zip_code,
            'fax' => $fax,
            'email' => $email,
            'company_peoples_num' => $company_peoples_num,
            'company_industry_id' => $company_industry_id,
            'company_certificate_no' => $company_certificate_no,
            'company_contact_name' => $company_contact_name,
            'company_contact_mobile' => $company_contact_mobile,
            'company_contact_tel' => $company_contact_tel,
        ];
        if($admin_username != '') $saveData['admin_username'] = $admin_username;
        if($admin_password != '' || $sure_password != ''){
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据--注册-补充用户资料
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_perfect_user(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // 判断是否就是登录的用户
        if($id != $this->user_info['id']) throws('参数[id]有误！');
        if($this->user_info['admin_type'] != 4)  throws('非用户不可进行此操作！');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $real_name = CommonRequest::get($request, 'real_name');
        $email = CommonRequest::get($request, 'email');
        $mobile = CommonRequest::get($request, 'mobile');
        $qq_number = CommonRequest::get($request, 'qq_number');
        $id_number = CommonRequest::get($request, 'id_number');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $addr = CommonRequest::get($request, 'addr');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $saveData = [
            'admin_type' => $this->user_info['admin_type'],
            'is_perfect' => 2,
            'real_name' => $real_name,
            'mobile' => $mobile,
            'qq_number' => $qq_number,
            'id_number' => $id_number,
            'city_id' => $city_id,
            'addr' => $addr,
        ];

        if($admin_username != '') $saveData['admin_username'] = $admin_username;
        if($admin_password != '' || $sure_password != ''){
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }
}
