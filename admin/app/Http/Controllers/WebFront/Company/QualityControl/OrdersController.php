<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Business\Controller\API\QualityControl\CTAPIInvoiceBuyerBusiness;
use App\Business\Controller\API\QualityControl\CTAPIInvoiceProjectTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIInvoiceTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayConfigBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayMethodBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrdersBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Orders;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class OrdersController extends BasicController
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
//            return view('company.QualityControl.Orders.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.Orders.index', true
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
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $reDataArr['province_kv'] = CTAPIOrdersBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIOrdersBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('company.QualityControl.Orders.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'company.QualityControl.RrrDddd.select', true
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
////            return view('company.QualityControl.Orders.add', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.Orders.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 详情页
     *
     * @param Request $request
     * @param int $company_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
        return $this->exeDoPublicFun($request, 17179869184, 1,'company.QualityControl.Orders.info', false
            , '', ['id' => $id], function (&$reDataArr) use ($request, &$id){
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    'relationFormatConfigs'=> CTAPIOrdersBusiness::getRelationConfigs($request, $this,
                        [
                            'company_name' => '',
                            'pay_company_name' => '',
                            'pay_name' => '',
                            'invoice_template_name' => '',
                            'invoice_buyer_name' => '',
                        ], []),
                    'listHandleKeyArr' => ['priceIntToFloat'],
                    ];
                $info = CTAPIOrdersBusiness::getFVFormatList( $request,  $this, 4, 1
                    , ['id' => $id, 'company_id' => $this->user_id], false, [], $extParams);
                $reDataArr['info'] = $info;
                // pr($reDataArr);
            });
    }

    /**
     * 电子发票
     *
     * @param Request $request
     * @param int $company_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function invoices(Request $request)
    {
        return $this->exeDoPublicFun($request, 0, 1,'company.QualityControl.Orders.invoices', false
            , '', [], function (&$reDataArr) use ($request){

                $id = CommonRequest::get($request, 'id');
                if(is_string($id)) $id = explode(',', $id);
                if(!is_array($id)) $id = [];
                if(empty($id)) throws('请选择要开电子发票的订单');
                $info = [
                    'id'=> implode(',', $id),
                    //   'department_id' => 0,
                ];

//                $course_id = CommonRequest::getInt($request, 'course_id');
//                $class_id = CommonRequest::getInt($request, 'class_id');
                $company_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');// 报名用户所属的企业id

                // 根据报名用户id,获得报名用户及支付信息
                list($dataList, $company_id, $company_name) = CTAPIOrdersBusiness::getInvoiceByIds($request, $this, $id, $company_id, 1);

                if(!is_numeric($company_id)  || $company_id <= 0){
                    throws('参数【企业id】有误！');
                }
                $reDataArr['info'] = $info;
                $reDataArr['data_list'] = $dataList;
                $reDataArr['company_id'] = $company_id;
                $reDataArr['company_name'] = $company_name;

                // 获得发票抬头KV值
                $reDataArr['invoice_buyer_kv'] = CTAPIInvoiceBuyerBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'gmf_mc'], [
                    'sqlParams' => ['where' => [['open_status', 1], ['company_id', $company_id]]]
                ]);
                $reDataArr['defaultInvoiceBuyer'] = $info['invoice_buyer_id'] ?? -1;// 默认
                // pr($reDataArr);
            });
    }

    /**
     * ajax保存数据--开电子发票
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_invoices_save(Request $request)
    {
//        $this->InitParams($request);

        return $this->exeDoPublicFun($request, 0, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                // throws('正在调试开发中');

                $id = CommonRequest::get($request, 'id');
                if(is_string($id)) $id = explode(',', $id);
                if(!is_array($id)) $id = [];
                if(empty($id)) throws('请选择要开电子发票的订单');

//                $course_id = CommonRequest::getInt($request, 'course_id');
//                $class_id = CommonRequest::getInt($request, 'class_id');
                $company_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');// 报名用户所属的企业id
                $invoice_buyer_id = CommonRequest::getInt($request, 'invoice_buyer_id');// 企业抬头

                // 根据报名用户id,获得报名用户及支付信息
                list($dataList, $company_id, $company_name) = CTAPIOrdersBusiness::getInvoiceByIds($request, $this, $id, $company_id, 1);

                if(!is_numeric($company_id)  || $company_id <= 0){
                    throws('参数【企业id】有误！');
                }
                $resultDatas = CTAPIOrdersBusiness::operateInvoiceBlueAjax($request, $this, $company_id, $id, $invoice_buyer_id);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/company/orders/ajax_info",
     *     tags={"企业后台-订单管理-收款订单"},
     *     summary="收款订单--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlOrdersAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_orders_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_orders"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_orders"}
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
//        $info = CTAPIOrdersBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Post(
     *     path="/api/company/orders/ajax_save",
     *     tags={"企业后台-订单管理-收款订单"},
     *     summary="收款订单--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlOrdersAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_orders_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_orders"}
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
//                $industry_name = CommonRequest::get($request, 'industry_name');
//                $simple_name = CommonRequest::get($request, 'simple_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//                $saveData = [
//                    'industry_name' => $industry_name,
//                    'simple_name' => $simple_name,
//                    'sort_num' => $sort_num,
//                ];
//                 // 价格转为整型
//                Tool::bathPriceCutFloatInt($saveData, Orders::$IntPriceFields, Orders::$IntPriceIndex, 1);
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
//                $resultDatas = CTAPIOrdersBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * @OA\Get(
     *     path="/api/company/orders/ajax_alist",
     *     tags={"企业后台-订单管理-收款订单"},
     *     summary="收款订单--列表",
     *     description="收款订单--列表......",
     *     operationId="companyQualityControlOrdersAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_orders_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_orders"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_orders"}
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
//        return  CTAPIOrdersBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 根据条件获得项目列表数据
            $mergeParams = [
                'company_id' => $this->user_id,
            ];
            CTAPIOrdersBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIOrdersBusiness::getRelationConfigs($request, $this,
                    [
                        'company_name' => '',
                        'pay_company_name' => '',
                        'pay_name' => '',
                        'invoice_template_name' => '',
                        'invoice_buyer_name' => '',
                    ], []),
                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            return  CTAPIOrdersBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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
//        $result = CTAPIOrdersBusiness::getList($request, $this, 1 + 0);
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
    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIOrdersBusiness::getList($request, $this, 1 + 0);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
            // 根据条件获得项目列表数据
            $mergeParams = [
                'company_id' => $this->user_id,
            ];
            CTAPIOrdersBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIOrdersBusiness::getRelationConfigs($request, $this,
                    [
                        'company_name' => '',
                        'pay_company_name' => '',
                        'pay_name' => '',
                        'invoice_template_name' => '',
                        'invoice_buyer_name' => '',
                    ], []),
                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            CTAPIOrdersBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIOrdersBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            $id = CommonRequest::getInt($request, 'id');
//            $info = CTAPIOrdersBusiness::getInfoDataBase($request, $this, '', $id, [], '', 1);
//            if(empty($info)) throws('记录不存在！');
//            // $user_info = $this->user_info;
//            if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/company/orders/ajax_del",
     *     tags={"企业后台-订单管理-收款订单"},
     *     summary="收款订单--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlOrdersAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_orders_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_orders"}
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
////        return CTAPIOrdersBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            return CTAPIOrdersBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIOrdersBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIOrdersBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPIOrdersBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIOrdersBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIOrdersBusiness::importByFile($request, $this, $fileName);
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

        // 获得收款帐号KV值
        $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name']);
        $reDataArr['defaultPayConfig'] = (!is_numeric($pay_config_id) || $pay_config_id <= 0 ) ? -1 : $pay_config_id;// 默认

        // 收款开通类型(1现金、2微信支付、4支付宝)
        $pay_method = CommonRequest::getInt($request, 'pay_method');
        $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
        $reDataArr['defaultPayMethod'] = (!is_numeric($pay_method) || $pay_method <= 0 ) ? -1 : $pay_method;// 列表页默认状态
        // $reDataArr['payMethodDisable'] = OrderPayConfig::$payMethodDisable;// 不可用的--禁用

        // 获得发票开票模板KV值
        $reDataArr['invoice_template_kv'] = CTAPIInvoiceTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], []);// ['sqlParams' => ['where' => [['open_status', 1]]]]
        $reDataArr['defaultInvoiceTemplate'] = -1;// 默认

        // 获得发票商品项目模板KV值
//        $reDataArr['invoice_project_template_kv'] = CTAPIInvoiceProjectTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], []);// ['sqlParams' => ['where' => [['open_status', 1]]]]
//        $reDataArr['defaultInvoiceProjectTemplate'] = -1;// 默认

        // 获得发票抬头KV值
//        $reDataArr['invoice_buyer_kv'] = CTAPIInvoiceBuyerBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'gmf_mc'], ['sqlParams' => ['where' =>[['company_id' , $this->user_id]]]]);// ['sqlParams' => ['where' => [['open_status', 1]]]]
//        $reDataArr['defaultInvoiceBuyer'] = -1;// 默认

        // 订单类型1面授培训2会员年费
        $order_type = CommonRequest::getInt($request, 'order_type');
        $reDataArr['orderType'] =  Orders::$orderTypeArr;
        $reDataArr['defaultOrderType'] = (!is_numeric($order_type) || $order_type <= 0 ) ? -1 : $order_type;;// 列表页默认状态

        // 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
        $reDataArr['orderStatus'] =  Orders::$orderStatusArr;
        $reDataArr['defaultOrderStatus'] = -1;// 列表页默认状态

        // 是否退费0未退费1已退费2待退费
        $reDataArr['hasRefund'] =  Orders::$hasRefundArr;
        $reDataArr['defaultHasRefund'] = -1;// 列表页默认状态

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
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIOrdersBusiness::getRelationConfigs($request, $this,
                    [
                        'company_name' => '',
                        'pay_company_name' => '',
                        'pay_name' => '',
                        'invoice_template_name' => '',
                        'invoice_buyer_name' => '',
                    ], []),
                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            $info = CTAPIOrdersBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // $user_info = $this->user_info;
            if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        // 获得收款帐号KV值
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name']);
        $reDataArr['defaultPayConfig'] = $info['pay_config_id'] ?? -1;// 默认

        // 收款开通类型(1现金、2微信支付、4支付宝)
        $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
        $reDataArr['defaultPayMethod'] = $info['pay_method'] ?? -1;// 列表页默认状态

        // 获得发票开票模板KV值
        $reDataArr['invoice_template_kv'] = CTAPIInvoiceTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultInvoiceTemplate'] = $info['invoice_template_id'] ?? -1;// 默认

        // 获得发票商品项目模板KV值
//        $reDataArr['invoice_project_template_kv'] = CTAPIInvoiceProjectTemplateBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'template_name'], [
//            'sqlParams' => ['where' => [['open_status', 1]]]
//        ]);
//        $reDataArr['defaultInvoiceProjectTemplate'] = $info['invoice_project_template_id'] ?? -1;// 默认

        // 获得发票抬头KV值
//        $reDataArr['invoice_buyer_kv'] = CTAPIInvoiceBuyerBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'gmf_mc'], [
//            'sqlParams' => ['where' => [['open_status', 1],['company_id', $this->user_id]]]
//        ]);
//        $reDataArr['defaultInvoiceBuyer'] = $info['invoice_buyer_id'] ?? -1;// 默认

        // 订单类型1面授培训2会员年费
        $reDataArr['orderType'] =  Orders::$orderTypeArr;
        $reDataArr['defaultOrderType'] = $info['order_type'] ?? -1;// 列表页默认状态

        // 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
        $reDataArr['orderStatus'] =  Orders::$orderStatusArr;
        $reDataArr['defaultOrderStatus'] = $info['order_status'] ?? -1;// 列表页默认状态

        // 是否退费0未退费1已退费2待退费
        $reDataArr['hasRefund'] =  Orders::$hasRefundArr;
        $reDataArr['defaultHasRefund'] = $info['has_refund'] ?? -1;// 列表页默认状态

        $company_hidden = CommonRequest::getInt($request, 'company_hidden');
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
