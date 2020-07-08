<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class StaffController extends BasicController
{
    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'Staff';// 视图文件夹名称

    // 下面的只能判断操作的数据是这个栏目的数据

    // 判断操作权限--根据用户id
    public function judgePower(Request $request, $staff_id = 0){
        $userInfo = $this->getStaffInfo($staff_id);
        $this->judgeUserPower($request, $userInfo);
        return $userInfo;
    }

    // 判断操作权限--根据用户信息【一维数组】
    public function judgeUserPower(Request $request, $userInfo = []){
        if(empty($userInfo) || count($userInfo) <= 0 || empty($userInfo)){
            throws('用户名信息不存在！');
        }
        // 判断类型是否正确 1平台2老师4学生
        if($userInfo['admin_type'] != static::$ADMIN_TYPE){
            throws('用户类型不一致！');
        }
        return true;
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

        // 拥有者类型1平台2企业4个人
        $reDataArr['adminType'] =  CTAPIStaffBusiness::$adminType;
        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 是否完善资料1待完善2已完善
        $reDataArr['isPerfect'] =  CTAPIStaffBusiness::$isPerfectArr;
        $reDataArr['defaultIsPerfect'] = -1;// 列表页默认状态

        // 是否超级帐户2否1是
        $reDataArr['issuper'] =  CTAPIStaffBusiness::$issuper;
        $reDataArr['defaultIssuper'] = -1;// 列表页默认状态

        // 审核状态1待审核2审核通过4审核不通过
        $reDataArr['openStatus'] =  CTAPIStaffBusiness::$openStatus;
        $reDataArr['defaultOpenStatus'] = -1;// 列表页默认状态

        // 状态 1正常 2冻结
        $reDataArr['accountStatus'] =  CTAPIStaffBusiness::$accountStatus;
        $reDataArr['defaultAccountStatus'] = -1;// 列表页默认状态

        // 性别0未知1男2女
        $reDataArr['sex'] =  CTAPIStaffBusiness::$sex;
        $reDataArr['defaultSex'] = -1;// 列表页默认状态

        // 企业--是否独立法人1独立法人 2非独立法人
        $reDataArr['companyIsLegalPersion'] =  CTAPIStaffBusiness::$companyIsLegalPersionArr;
        $reDataArr['defaultCompanyIsLegalPersion'] = -1;// 列表页默认状态

        // 企业--企业类型1检测机构、2生产企业
        $reDataArr['companyType'] =  CTAPIStaffBusiness::$companyTypeArr;
        $reDataArr['defaultCompanyType'] = -1;// 列表页默认状态

        // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
        $reDataArr['companyProp'] =  CTAPIStaffBusiness::$companyPropArr;
        $reDataArr['defaultCompanyProp'] = -1;// 列表页默认状态

        // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
        $reDataArr['companyPeoples'] =  CTAPIStaffBusiness::$companyPeoplesNumArr;
        $reDataArr['defaultCompanyPeoples'] = -1;// 列表页默认状态

        // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
        $reDataArr['companyGrade'] =  CTAPIStaffBusiness::$companyGradeArr;
        $company_grade = CommonRequest::get($request, 'company_grade');
        if(strlen($company_grade) <= 0 ) $company_grade = -1;
        $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态

        return view('admin.QualityControl.' . static::$VIEW_NAME . '.index', $reDataArr);
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $reDataArr['province_kv'] = CTAPIStaffBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIStaffBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('admin.QualityControl.Staff.select', $reDataArr);
//    }

    /**
     * 添加
     * 大后台: 管理员、企业 、 个人 修改
     * 企业后台： 企业 、 个人 修改
     * 个人后台：个人 修改
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', []);
            $this->judgeUserPower($request, $info);
            // 判断是否有操作权限
            // 根据具体功能 ，加上或去掉要判断的下标
            $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
            if(!$this->batchJudgeRecordOperateAuth($info, $powerFields, 0, 0, 0, true)){
                $reDataArr['errorMsg'] = '您没有操作权限！';
                $reDataArr['isShowBtn'] = 0;// 1:显示“回到首页”；2：显示“返回上页”
                return $this->errorView($reDataArr, 'error');
            }
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        return view('admin.QualityControl.' . static::$VIEW_NAME . '.add', $reDataArr);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/staff/ajax_info",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlStaffAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');

        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', []);
        $this->judgeUserPower($request, $info);
        // 判断是否有操作权限
        // 根据具体功能 ，加上或去掉要判断的下标
        $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
        if(!$this->batchJudgeRecordOperateAuth($info, $powerFields, 0, 0, 0, true)){
            return ajaxDataArr(0, null, '您没有操作权限！');
        }
        $resultDatas = ['info' => $info];
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @OA\Post(
     *     path="/api/admin/staff/ajax_save",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlStaffAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
//        $account_status = CommonRequest::getInt($request, 'account_status');
        $mobile = CommonRequest::get($request, 'mobile');
        $tel = CommonRequest::get($request, 'tel');
        $qq_number = CommonRequest::get($request, 'qq_number');
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $userInfo = [];
        if($id > 0){
            $userInfo = $this->judgePower($request, $id);
            // 判断是否有操作权限
            // 根据具体功能 ，加上或去掉要判断的下标
            $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
            if(!$this->batchJudgeRecordOperateAuth($userInfo, $powerFields, 0, 0, 0, true)){
                return ajaxDataArr(0, null, '您没有操作权限！');
            }
        }

        $saveData = [
            'admin_type' => static::$ADMIN_TYPE,
            'real_name' => $real_name,
            'sex' => $sex,
//            'gender' => $sex,
//            'account_status' => $account_status,
            'mobile' => $mobile,
            'tel' => $tel,
            'qq_number' => $qq_number,
            'admin_username' => $admin_username,
        ];
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
     * @OA\Get(
     *     path="/api/admin/staff/ajax_alist",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--列表",
     *     description="帐号管理--列表......",
     *     operationId="adminQualityControlStaffAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
        $this->InitParams($request);
        // $this->company_id = 1;
        $mergeParams = [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
        ];
        // 企业 的 个人--只能读自己的人员信息
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
            $mergeParams['company_id'] = $this->own_organize_id;
        }
        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIStaffBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function export(Request $request){
        $this->InitParams($request);
        // $this->company_id = 1;
        $mergeParams = [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
        ];
        // 企业 的 个人--只能读自己的人员信息
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
            $mergeParams['company_id'] = $this->own_organize_id;
        }
        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

        CTAPIStaffBusiness::getList($request, $this, 1 + 0);
    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import_template(Request $request){
        $this->InitParams($request);
        // $this->company_id = 1;
        $mergeParams = [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
        ];
        // 企业 的 个人--只能读自己的人员信息
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
            $mergeParams['company_id'] = $this->own_organize_id;
        }
        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

        CTAPIStaffBusiness::importTemplate($request, $this);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/staff/ajax_del",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlStaffAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');
        $company_id = 0;
        // 删除的是个人， 是企业后台--操作的-- 企业只能删除自己的员工
        if(static::$ADMIN_TYPE == 4 && $this->user_type == 2){
            $company_id = $this->own_organize_id;
        }
        // 删除员工--还需要重新统计企业的员工数
        // 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
        if(in_array(static::$ADMIN_TYPE, [2, 4])){
            $organize_id = $this->organize_id;
            // 大后台--可以删除所有的员工；删除企业【无员工】
            // 企业后台 -- 删除员工，只能删除自己的员工；无删除企业
            // 个人后台--不可进行删除操作
            if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
            return CTAPIStaffBusiness::delDatasAjax($request, $this, static::$ADMIN_TYPE, $organize_id);
        }else{// 管理员 直接删除
            $delResult = CTAPIStaffBusiness::delByIds($request, $this, static::$ADMIN_TYPE, $id, $company_id, 0);
            return  ajaxDataArr(1, $delResult, '');
        }
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIStaffBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIStaffBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import(Request $request)
    {
        $this->InitParams($request);
        // $this->company_id = 1;
        // 企业 的 个人--只能读自己的人员信息
        $organize_id = 0;
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
            $organize_id = $this->own_organize_id;
        }
        $mergeParams = [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
            'company_id' => $organize_id,
        ];
        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

        // 上传并保存文件
        $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 2);
        if($result['apistatus'] == 0) return $result;
        // 文件上传成功
        // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
        $fileName = Tool::getPath('public') . $result['result']['filePath'];
        $resultDatas = [];
        try{
            $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        } finally {
            $resourceId = $result['result']['id'] ?? 0;
            if ($resourceId > 0) {
                CTAPIStaffBusiness::mergeRequest($request, $this, [
                    'id' => $resourceId,
                ]);
                CTAPIResourceBusiness::delAjax($request, $this);
            }
            // 删除上传的文件
            Tool::resourceDelFile(['resource_url' => $result['result']['filePath']]);
        }
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 子帐号管理-审核操作(通过/不通过)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_open(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $open_status = CommonRequest::getInt($request, 'open_status');// 操作类型 2审核通过     4审核不通过

        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::openAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $open_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * 子帐号管理-(冻结/解冻)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_frozen(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $account_status = CommonRequest::getInt($request, 'account_status');// 操作类型 状态 1正常--解冻操作； 2冻结--冻结操作
        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::accountStatusAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $account_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }
}
