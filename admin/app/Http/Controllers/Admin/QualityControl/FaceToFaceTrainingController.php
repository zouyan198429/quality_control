<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Abilitys;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

/**
 * Class FaceToFaceTrainingController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class FaceToFaceTrainingController extends BasicController
{
    /**
     * @var string Namespace.
     */
    public $namespace = 'admin.QualityControl.FaceToFaceTraining.';

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->execute('index');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->execute('create');
    }

    /**
     * @param string $view View name.
     * @return mixed
     */
    public function execute(string $view)
    {
        return $this->exeDoPublicFun(request(), 1, 1, $this->namespace . $view);
    }

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
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.Abilitys.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.Abilitys.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * 查看
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            if(!is_numeric($id) || $id <= 0){
                throws('参数[id]有误！');
            }
            $operate = "详情";
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list', 'project_submit_items_list'], []),
            ];
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            if(empty($info)) {
                throws('记录不存在！');
            }
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            return view('admin.QualityControl.Abilitys.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/abilitys/ajax_info",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlAbilitysAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_abilitys"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
        $resultDatas = ['info' => $info];
        return ajaxDataArr(1, $resultDatas, '');

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
    }


    /**
     * 保存数据
     * @param Request $request
     * @return mixed
     */
    public function ajax_save(Request $request)
    {
        dd($request);
        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $ability_name = CommonRequest::get($request, 'ability_name');
                $estimate_add_num = CommonRequest::getInt($request, 'estimate_add_num');
                $join_begin_date = CommonRequest::get($request, 'join_begin_date');
                $join_end_date = CommonRequest::get($request, 'join_end_date');
                $duration_minute = CommonRequest::getInt($request, 'duration_minute');

                // 方法标准
                $project_standard_ids = CommonRequest::get($request, 'project_standard_ids');// 值id数组
                if(is_string($project_standard_ids) || !is_array($project_standard_ids)) $project_standard_ids = explode(',', $project_standard_ids);

                $project_standard_names = CommonRequest::get($request, 'project_standard_names');// 值数组
                if(is_string($project_standard_names) || !is_array($project_standard_names)) $project_standard_names = explode(',', $project_standard_names);

                $project_standards = [];// 数组
                foreach ($project_standard_ids as $k => $temId){
                    array_push($project_standards,[
                        'id' => $temId,
                        'name' => $project_standard_names[$k],
                    ]);
                }

                // 验证数据项
                $submit_item_ids = CommonRequest::get($request, 'submit_item_ids');// 值id数组
                if(is_string($submit_item_ids) || !is_array($submit_item_ids)) $submit_item_ids = explode(',', $submit_item_ids);

                $submit_item_names = CommonRequest::get($request, 'submit_item_names');// 值数组
                if(is_string($submit_item_names) || !is_array($submit_item_names)) $submit_item_names = explode(',', $submit_item_names);

                $submit_items = [];// 数组
                foreach ($submit_item_ids as $k => $temId){
                    array_push($submit_items,[
                        'id' => $temId,
                        'name' => $submit_item_names[$k],
                    ]);
                }

                $saveData = [
                    'ability_name' => $ability_name,
                    'estimate_add_num' => $estimate_add_num,
                    'duration_minute' => $duration_minute,
                    'join_begin_date' => $join_begin_date,
                    'join_end_date' => $join_end_date,
                    'project_standards' => $project_standards,// 方法标准 - 数组
                    'submit_items' => $submit_items,// 验证数据项  - 数组
                ];
                // 开始报名前，可以增删改，后面就不可以修改、删除
                if($id > 0){
                    $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
                    if(empty($info)) throws('记录不存在！');
                    if($info['status'] != 1) throws('当前记录非【待开始】状态，不可修改！');
                }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIAbilitysBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
        });
    }


    /**
     * ajax保存数据 修改公布时间类型
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_publish(Request $request)
    {
//        $this->InitParams($request);

        return $this->exeDoPublicFun($request, 0, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                $publish_type = CommonRequest::getInt($request, 'publish_type');
                $publish_time = CommonRequest::get($request, 'publish_time');

                // 公布结果时间类型 1待指定  2立即公布 4  定时公布
                $publishType = Abilitys::$publishTypeArr;
                if(!in_array($publish_type, array_keys($publishType))) throws('请选择正确的公布类型！');
                $begin_time = date('Y-m-d H:i:s');
                if($publish_type != 4) $publish_time = $begin_time;
                if($publish_type == 4){
                    // 判断指定公布时间
                    Tool::judgeBeginEndDate($begin_time, $publish_time, 1 + 2 + 64 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '指定公布时间');

                }

                $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                $status = $info['status'];
                $is_publish = $info['is_publish'];
                if($status != 4 || $is_publish != 2) throws('非进行中状态或非待公布状态，不可进行此操作！');

                $saveData = [
                    'publish_type' => $publish_type,
                    'publish_time' => $publish_time,
                    // 'judge_complete' => 1,// 有此下标，会去判断状态应该是不是可以到完成状态
                ];
                if($publish_type == 2){// 2立即公布
                    $saveData['is_publish'] = 4;
                    $saveData['judge_complete'] = 1;// 有此下标，会去判断状态应该是不是可以到完成状态
                }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIAbilitysBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/abilitys/ajax_alist",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--列表",
     *     description="能力验证--列表......",
     *     operationId="adminQualityControlAbilitysAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_abilitys"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
//        $this->InitParams($request);
//
//        $relations = [];//  ['siteResources']
//        // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list', 'project_submit_items_list'], []),
//        ];
//
//        return  CTAPIAbilitysBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){


            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list', 'project_submit_items_list'], []),
            ];

            return  CTAPIAbilitysBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
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
//        $result = CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
//    }

    /**
     * 导出--报名的企业信息
     *
     * @param Request $request
     * @param int $ability_id 所属能力验证
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function export_join(Request $request, $ability_id = 0){
//        $this->InitParams($request);
//        CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request, &$ability_id){
            $mergeParams = [
                'ability_id' => $ability_id,
                'is_export' => 1,
            ];
            CTAPIAbilityJoinItemsBusiness::mergeRequest($request, $this, $mergeParams);
            $handleKeyConfigArr = ['company_info_all', 'ability_info'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $aa = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
            pr($aa);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/admin/abilitys/ajax_del",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlAbilitysAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
//        $this->InitParams($request);
//         $id = CommonRequest::getInt($request, 'id');
//        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
//        if(empty($info)) throws('记录不存在！');
//        if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
//        return CTAPIAbilitysBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $id = CommonRequest::getInt($request, 'id');
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
            if(empty($info)) throws('记录不存在！');
            if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
            return CTAPIAbilitysBusiness::delAjax($request, $this);
        });
    }

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
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 状态
        $reDataArr['status'] =  Abilitys::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认状态

        // 是否公布结果1未公布  2待公布 4  已公布
        $reDataArr['isPublish'] =  Abilitys::$isPublishArr;
        $reDataArr['defaultIsPublish'] = -1;// 列表页默认状态
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
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
        // $user_info = $this->user_info;
        $id = $extendParams['params']['id'] ?? 0;

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list', 'project_submit_items_list'], []),
            ];
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

    }
    // **************公用方法********************结束*********************************

}
