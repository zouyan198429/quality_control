<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICompanyPunishBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyPunishController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.CompanyPunish.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.CompanyPunish.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

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
////        $reDataArr = [];// 可以传给视图的全局变量数组
////        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
////            // 正常流程的代码
////
////            $this->InitParams($request);
////            // $reDataArr = $this->reDataArr;
////            $reDataArr = array_merge($reDataArr, $this->reDataArr);
////            $reDataArr['province_kv'] = CTAPICompanyPunishBusiness::getCityByPid($request, $this,  0);
////            $reDataArr['province_kv'] = CTAPICompanyPunishBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
////            $reDataArr['province_id'] = 0;
////            return view('admin.QualityControl.CompanyPunish.select', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.CompanyPunish.select', true
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
    public function add(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $info = [
//                'id'=>$id,
//                //   'department_id' => 0,
//            ];
//            $operate = "添加";
//
//            if ($id > 0) { // 获得详情数据
//                $operate = "修改";
//                $info = CTAPICompanyPunishBusiness::getInfoData($request, $this, $id, [], '', []);
//            }
//            // $reDataArr = array_merge($reDataArr, $resultDatas);
//            $reDataArr['info'] = $info;
//            $reDataArr['operate'] = $operate;
//            return view('admin.QualityControl.CompanyPunish.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.CompanyPunish.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/company_punish/ajax_info",
     *     tags={"大后台-检验机构信息管理平台-机构处罚"},
     *     summary="机构处罚--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlCompanyPunishAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_punish_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_company_punish"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_punish"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPICompanyPunishBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Post(
     *     path="/api/admin/company_punish/ajax_save",
     *     tags={"大后台-检验机构信息管理平台-机构处罚"},
     *     summary="机构处罚--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlCompanyPunishAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_punish_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_punish"}
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
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $company_id = CommonRequest::getInt($request, 'company_id');
                $resource_name = CommonRequest::get($request, 'resource_name');

                // 文件资源
                // $resource_id = [];
                $resource_id = CommonRequest::get($request, 'resource_id');
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
                    'company_id' => $company_id,
                    'resource_name' => $resource_name,
                    // 'resource_id' => $resource_id[0] ?? 0,// 文件资源的id
                    'resource_ids' => $resource_ids,// 资源id串(逗号分隔-未尾逗号结束)
                    'resourceIds' => $resource_id,// 此下标为资源关系
                ];

                if($id <= 0) {// 新加;要加入的特别字段
                    //            $addNewData = [
                    //                // 'account_password' => $account_password,
                    //            ];
                    //            $saveData = array_merge($saveData, $addNewData);
                }else{
                    $info = CTAPICompanyPunishBusiness::getInfoData($request, $this, $id, [], '', []);
                    // 如果改变了所属企业,需要重新统计数
                    if(isset($saveData['company_id']) && $company_id != $info['company_id']) $saveData['force_company_num'] = 1;
                }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPICompanyPunishBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/company_punish/ajax_alist",
     *     tags={"大后台-检验机构信息管理平台-机构处罚"},
     *     summary="机构处罚--列表",
     *     description="机构处罚--列表......",
     *     operationId="adminQualityControlCompanyPunishAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_punish_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_company_punish"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_punish"}
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
//        return  CTAPICompanyPunishBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $handleKeyConfigArr = ['company_info' => '', 'resource_list' => ''];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICompanyPunishBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            return  CTAPICompanyPunishBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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
//        $result = CTAPICompanyPunishBusiness::getList($request, $this, 1 + 0);
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
////        $this->InitParams($request);
////        CTAPICompanyPunishBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//
//            $handleKeyConfigArr = ['company_info', 'resource_list'];
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICompanyPunishBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//            ];
//            CTAPICompanyPunishBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
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
////        $this->InitParams($request);
////        CTAPICompanyPunishBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPICompanyPunishBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/company_punish/ajax_del",
     *     tags={"大后台-检验机构信息管理平台-机构处罚"},
     *     summary="机构处罚--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlCompanyPunishAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_punish_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_punish"}
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
//        return CTAPICompanyPunishBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $organize_id = CommonRequest::getInt($request, 'company_id');// 可有此参数
            return CTAPICompanyPunishBusiness::delCustomizeAjax($request,  $this, $organize_id, [], '');
//            return CTAPICompanyPunishBusiness::delAjax($request, $this);
        });
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
//        $childKV = CTAPICompanyPunishBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPICompanyPunishBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPICompanyPunishBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPICompanyPunishBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPICompanyPunishBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-上传文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_file(Request $request)
    {
        $this->InitParams($request);
        // $this->company_id = 1;
        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');

        // 上传并保存文件
        return CTAPIResourceBusiness::filePlupload($request, $this, 1 | 2 | 8 | 16);
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
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
        $company_id = CommonRequest::getInt($request, 'company_id');
        $info = [];
        $company_hidden = 0;
        if(is_numeric($company_id) && $company_id > 0){
            // 获得企业信息
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
            $company_hidden = 1;
        }
        $reDataArr['info'] = $info;
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

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

        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $operate = "添加";

        // 如果是企业列表点《企业简介》
        $company_id = CommonRequest::getInt($request, 'company_id');
        if($id <= 0 && $company_id > 0){
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
        }

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $handleKeyConfigArr = ['company_info' => '', 'resource_list' => ''];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICompanyPunishBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $info = CTAPICompanyPunishBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $company_hidden = CommonRequest::getInt($request, 'company_hidden');
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
