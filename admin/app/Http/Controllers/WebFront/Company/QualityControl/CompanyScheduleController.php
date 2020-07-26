<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICompanyScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyScheduleController extends BasicController
{
    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('company.QualityControl.CompanySchedule.index', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
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
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $reDataArr['province_kv'] = CTAPICompanyScheduleBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPICompanyScheduleBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('company.QualityControl.CompanySchedule.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $info = [
                'id'=>$id,
                //   'department_id' => 0,
            ];
            $operate = "添加";

            if ($id > 0) { // 获得详情数据
                $operate = "修改";
                $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                // $user_info = $this->user_info;
                if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');

            }
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            return view('company.QualityControl.CompanySchedule.add', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Get(
     *     path="/api/company/quality_control/company_schedule/ajax_info",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlCompanyScheduleAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_company_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * @OA\Post(
     *     path="/api/company/quality_control/company_schedule/ajax_save",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlCompanyScheduleAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_save(Request $request)
//    {
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        // CommonRequest::judgeEmptyParams($request, 'id', $id);
//        $type_name = CommonRequest::get($request, 'type_name');
//        $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//        $saveData = [
//            'type_name' => $type_name,
//            'sort_num' => $sort_num,
//        ];
//
////        if($id <= 0) {// 新加;要加入的特别字段
////            $addNewData = [
////                // 'account_password' => $account_password,
////            ];
////            $saveData = array_merge($saveData, $addNewData);
////        }
//        $extParams = [
//            'judgeDataKey' => 'replace',// 数据验证的下标
//        ];
//        $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * @OA\Get(
     *     path="/api/company/quality_control/company_schedule/ajax_alist",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--列表",
     *     description="能力附表--列表......",
     *     operationId="companyQualityControlCompanyScheduleAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_company_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
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
        $user_info = $this->user_info;
        // 根据条件获得项目列表数据
        $mergeParams = [
            'company_id' => $this->user_id,
        ];
        CTAPICompanyScheduleBusiness::mergeRequest($request, $this, $mergeParams);
        $relations = [];//  ['siteResources']
        $extParams = [
<<<<<<< HEAD
            'handleKeyArr' => ['siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
=======
            // 'handleKeyArr' => ['siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPICompanyScheduleBusiness::getRelationConfigs($request, $this, ['company_info', 'resource_list'], []),
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
        ];
        return  CTAPICompanyScheduleBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
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
//        $result = CTAPICompanyScheduleBusiness::getList($request, $this, 1 + 0);
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
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPICompanyScheduleBusiness::getList($request, $this, 1 + 0);
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPICompanyScheduleBusiness::importTemplate($request, $this);
//    }


    /**
     * @OA\Post(
     *     path="/api/company/quality_control/company_schedule/ajax_del",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlCompanyScheduleAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
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
        $id = CommonRequest::getInt($request, 'id');
        $info = CTAPICompanyScheduleBusiness::getInfoDataBase($request, $this, '', $id, [], '', 1);
        if(empty($info)) throws('记录不存在！');
        // $user_info = $this->user_info;
        if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
        return CTAPICompanyScheduleBusiness::delAjax($request, $this);
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
//        $childKV = CTAPICompanyScheduleBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPICompanyScheduleBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPICompanyScheduleBusiness::importByFile($request, $this, $fileName);
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
        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

        $userInfo = $this->getStaffInfo($organize_id);
        if(empty($userInfo)) throws('企业记录不存在！');

        // 上传并保存文件
        $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 4);
        if($result['apistatus'] == 0) return $result;
        // 文件上传成功
        // /srv/www/dogtools/company/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
        $fileName = Tool::getPath('public') . $result['result']['filePath'];

        // 图片资源
        $resource_id = $result['result']['id'];// CommonRequest::get($request, 'resource_id');
        // 如果是字符，则转为数组
        if(is_string($resource_id) || is_numeric($resource_id)){
            if(strlen(trim($resource_id)) > 0){
                $resource_id = explode(',' ,$resource_id);
            }
        }
        if(!is_array($resource_id)) $resource_id = [];

        // 再转为字符串
        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        $saveData = [
            'company_id' => $organize_id,
            // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $id = 0;
        $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);

        return ajaxDataArr(1, $resultDatas, '');
    }
}
