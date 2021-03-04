<?php

namespace App\Http\Controllers\Expert\QualityControl\Abilitys;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\AbilityJoinItems;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class AbilityJoinItemsController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @param int $ability_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request, $ability_id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItems.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'expert.QualityControl.AbilitysAdmin.AbilityJoinItems.index', true
            , 'doListPage', ['ability_id' => $ability_id], function (&$reDataArr) use ($request){

            });
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
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItems.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'expert.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function add(Request $request,$id = 0)
//    {
////        $reDataArr = [];// 可以传给视图的全局变量数组
////        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
////            // 正常流程的代码
////
////            $this->InitParams($request);
////            // $reDataArr = $this->reDataArr;
////            $reDataArr = array_merge($reDataArr, $this->reDataArr);
////            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItems.add', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'expert.QualityControl.AbilitysAdmin.AbilityJoinItems.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 查看--数据上报
     *
     * @param Request $request
     * @param int $ability_id 报名附表 项目相关表id
     * @param int $item_id 报名项id
     * @param int $retry_no 测试的序号
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sample_result_info(Request $request, $ability_id = 0, $item_id = 0, $retry_no = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$item_id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('company.QualityControl.AbilityJoinItems.sample_result', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        return $this->exeDoPublicFun($request, 0, 8, 'expert.QualityControl.AbilitysAdmin.AbilityJoinItems.sample_result_info', true
            , '', [], function (&$reDataArr) use ($request, &$ability_id, &$item_id, &$retry_no){


                if(!is_numeric($item_id) || $item_id <= 0){
                    throws('参数[item_id]有误！');
                }
                $operate = "数据上报";
                $reDataArr['operate'] = $operate;
                $reDataArr['ability_id'] = $ability_id;
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    // 'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info_updata', 'project_submit_items_list'], []),// , 'join_items'
                ];


                $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $item_id, [], '', $extParams);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }

//                $user_info = $this->user_info;
//                if($info['admin_type'] != $user_info['admin_type'] || $info['staff_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
//
//                if(!in_array($info['status'], [2]) || !in_array($info['is_sample'], [2])) throws('非已取样状态，不可进行此操作');
                $this->infoItemResult($request, $reDataArr, $info, $retry_no);

            });

    }

    /**
     * 公用方法
     * 对单次测试数据进行相关表获取---通过 retry_no  测试序号【冗余】 0正常测 1补测1 2 补测2 .....
     *
     * @param Request $request
     * @param array $reDataArr 可以传参到前端视图的变量数组
     * @param int $id 报名附表 项目相关表id
     * @param int $info 当前报名项详情
     * @param int $retry_no 测试序号【冗余】 0正常测 1补测1 2 补测2 .....
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function infoItemResult(Request $request, &$reDataArr, &$info, $retry_no = 0){
        if(!is_numeric($retry_no) || $retry_no < 0) $retry_no = 0;
        $info['retry_no'] = $retry_no;
        $relationFormatConfigs = CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info_updata', 'project_submit_items_list'], []);// , 'join_items'
        CTAPIAbilityJoinItemsBusiness::formatRelationList( $request, $this, $info, $relationFormatConfigs);

        // 所用仪器
        $results_instrument_list = $info['join_item_reslut_info_updata']['results_instrument_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_instrument_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_instrument_list'])) $info['join_item_reslut_info_updata']['results_instrument_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_instrument_list'], ['id'=> 0]);
        }
        // 检测标准物质
        $results_standard_list = $info['join_item_reslut_info_updata']['results_standard_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_standard_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_standard_list'])) $info['join_item_reslut_info_updata']['results_standard_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_standard_list'], ['id'=> 0]);
        }

        // 检测方法依据
        $results_method_list = $info['join_item_reslut_info_updata']['results_method_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_method_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_method_list'])) $info['join_item_reslut_info_updata']['results_method_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_method_list'], ['id'=> 0]);
        }

        // pr($info);
        $reDataArr['info'] = $info;
    }

    /**
     * @ OA\Get(
     *     path="/api/expert/ability_join_items/ajax_info",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_info",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_ability_join_items"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
//        $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');
//
////        $id = CommonRequest::getInt($request, 'id');
////        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
////        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
////
////        });
//    }

    /**
     * @ OA\Post(
     *     path="/api/expert/ability_join_items/ajax_save",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_save",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
////        $this->InitParams($request);
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::getInt($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $type_name = CommonRequest::get($request, 'type_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//                $saveData = [
//                    'type_name' => $type_name,
//                    'sort_num' => $sort_num,
//                ];
//
////        if($id <= 0) {// 新加;要加入的特别字段
////            $addNewData = [
////                // 'account_password' => $account_password,
////            ];
////            $saveData = array_merge($saveData, $addNewData);
////        }
//                $extParams = [
//                    'judgeDataKey' => 'replace',// 数据验证的下标
//                ];
//                $resultDatas = CTAPIAbilityJoinItemsBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * @ OA\Get(
     *     path="/api/expert/ability_join_items/ajax_alist",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--列表",
     *     description="能力验证报名项--列表......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_alist",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_optional"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_ability_join_items"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request, $ability_id = 0){
//        $this->InitParams($request);
//        return  CTAPIAbilityJoinItemsBusiness::getList($request, $this, 2 + 4);

        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request, &$ability_id){

            $mergeParams = [
                'ability_id' => $ability_id,
            ];
            CTAPIAbilityJoinItemsBusiness::mergeRequest($request, $this, $mergeParams);

            $handleKeyConfigArr = ['company_info', 'ability_info'];
            $isExport = CommonRequest::getInt($request, 'is_export'); // 是否导出 0非导出 ；1导出数据
            if ($isExport == 1) $oprateBit = 1;
            if($isExport == 1){// 如果是导出
                // array_push($handleKeyConfigArr, 'company_info');
            }

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            return  CTAPIAbilityJoinItemsBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
        });
    }

    /**
     * 选择短信模板页面
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sms_send(Request $request)
    {
        return $this->exeDoPublicFun($request, 34359738368, 8, 'admin.QualityControl.SmsTemplate.sms_send', true
            , '', [], function (&$reDataArr) use ($request){
                $sms_operate_type = 1;// 操作类型 1 发送短信  ; 2测试发送短信
                $reDataArr['sms_operate_type'] = $sms_operate_type;
                // 设置参数
                $mergeParams = [// template_id 与 module_id 二选一
                    // 'sms_template_id' => 1,// 短信模板id;--可为0 ；
                    'sms_module_id' => 1,// 短信模块id
                ];
                CTAPISmsTemplateBusiness::mergeRequest($request, $this, $mergeParams);

                $smsMobileFieldKV = ['mobile' => '手机号'];// 可以发送短信的手机号字段
                $smsMobileField = 'mobile';// 默认的发送短信的手机号字段
                $reDataArr['smsMobileFieldKV'] = $smsMobileFieldKV;
                $reDataArr['defaultSmsMobileField'] = $smsMobileField;
                CTAPISmsTemplateBusiness::smsSend($request,  $this, $reDataArr);
            });
    }

    /**
     * ajax发送手机短信
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_sms_send(Request $request){
        return $this->exeDoPublicFun($request, 68719476736, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
//                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
//                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            return CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
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
//        $result = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//        return $this->exeDoPublicFun($request, 4294967296, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $result = CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//            $data_list = $result['result']['data_list'] ?? [];
//            $ids = implode(',', array_column($data_list, 'id'));
//            return ajaxDataArr(1, $ids, '');
//        });
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
//        CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
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
//        CTAPIAbilityJoinItemsBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @ OA\Post(
     *     path="/api/expert/ability_join_items/ajax_del",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_del",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_del(Request $request)
//    {
////        $this->InitParams($request);
////        return CTAPIAbilityJoinItemsBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            return CTAPIAbilityJoinItemsBusiness::delAjax($request, $this);
//        });
//    }

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
//        $childKV = CTAPIAbilityJoinItemsBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilityJoinItemsBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//        return $this->exeDoPublicFun($request, 8589934592, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $parent_id = CommonRequest::getInt($request, 'parent_id');
//            // 获得一级城市信息一维数组[$k=>$v]
//            $childKV = CTAPIRrrDdddBusiness::getCityByPid($request, $this, $parent_id);
//            // $childKV = CTAPIRrrDdddBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//            return  ajaxDataArr(1, $childKV, '');
//        });
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
///
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $fileName = 'staffs.xlsx';
//            $resultDatas = CTAPIRrrDdddBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
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
//        $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    // **************公用方法**********************开始*******************************

    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];
        $ability_id = $extendParams['params']['ability_id'];
        $reDataArr['ability_id'] = $ability_id;

        $abilityInfo = $this->getAbilityInfo($ability_id);
        $reDataArr['info'] = $abilityInfo;

        // 拥有者类型1平台2企业4个人
        $reDataArr['adminType'] =  AbilityJoinItems::$adminTypeArr;
        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 是否补测 0正常测 1补测1 2 补测2 .....
        $reDataArr['retryNo'] =  AbilityJoinItems::$retryNoArr;
        $reDataArr['defaultRetryNo'] = -1;// 列表页默认

        // 状态
        $reDataArr['status'] =  AbilityJoinItems::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认

        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $reDataArr['resultStatus'] =  AbilityJoinItems::$resultStatusArr;
        $reDataArr['defaultResultStatus'] = -1;// 列表页默认

        // 是否取样1待取样--未取 2已取样--已取
        $reDataArr['isSample'] =  AbilityJoinItems::$isSampleArr;
        $reDataArr['defaultIsSample'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
        // $user_info = $this->user_info;
        $id = $extendParams['params']['id'] ?? 0;

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//        $info = [
//            'id'=>$id,
//            //   'department_id' => 0,
//        ];
//        $operate = "添加";
//
//        if ($id > 0) { // 获得详情数据
//            $operate = "修改";
//            $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', []);
//        }
//        // $reDataArr = array_merge($reDataArr, $resultDatas);
//        $reDataArr['info'] = $info;
//        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
