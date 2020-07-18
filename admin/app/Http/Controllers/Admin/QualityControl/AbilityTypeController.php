<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityTypeBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class AbilityTypeController extends BasicController
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
            return view('admin.QualityControl.AbilityType.index', $reDataArr);

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
//            $reDataArr['province_kv'] = CTAPIAbilityTypeBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIAbilityTypeBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('admin.QualityControl.AbilityType.select', $reDataArr);
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
                $info = CTAPIAbilityTypeBusiness::getInfoData($request, $this, $id, [], '', []);
            }
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            return view('admin.QualityControl.AbilityType.add', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/ability_type/ajax_info",
     *     tags={"大后台-能力验证-行业分类"},
     *     summary="行业分类--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlAbilityTypeAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_type_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_ability_type"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_type"}
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
        $info = CTAPIAbilityTypeBusiness::getInfoData($request, $this, $id, [], '', []);
        $resultDatas = ['info' => $info];
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @OA\Post(
     *     path="/api/admin/ability_type/ajax_save",
     *     tags={"大后台-能力验证-行业分类"},
     *     summary="行业分类--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlAbilityTypeAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_type_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_type"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $type_name = CommonRequest::get($request, 'type_name');
        $sort_num = CommonRequest::getInt($request, 'sort_num');

        $saveData = [
            'type_name' => $type_name,
            'sort_num' => $sort_num,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIAbilityTypeBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/ability_type/ajax_alist",
     *     tags={"大后台-能力验证-行业分类"},
     *     summary="行业分类--列表",
     *     description="行业分类--列表......",
     *     operationId="adminQualityControlAbilityTypeAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_type_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_ability_type"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_type"}
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
        return  CTAPIAbilityTypeBusiness::getList($request, $this, 2 + 4);
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
//        $result = CTAPIAbilityTypeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilityTypeBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilityTypeBusiness::importTemplate($request, $this);
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/ability_type/ajax_del",
     *     tags={"大后台-能力验证-行业分类"},
     *     summary="行业分类--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlAbilityTypeAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_type_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_type"}
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
        return CTAPIAbilityTypeBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIAbilityTypeBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilityTypeBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIAbilityTypeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIAbilityTypeBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
