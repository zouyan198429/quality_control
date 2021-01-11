<?php
// 收款订单
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Invoice\hydzfp\InvoiceHydzfp;
use App\Services\Tool;
use Carbon\Carbon;

/**
 *
 */
class OrdersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Orders';
    public static $table_name = 'orders';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 生成订单
     *
     * @param int  $company_id 企业id
     * @param array $createOrder 生成订单时，需要传入的参数
     *   $createOrder = [
     *      'company_id' => $company_id,
     *      'order_type' => 1,// 订单类型1面授培训2会员年费
     *      'pay_config_id' => $pay_config_id,// 收款帐号配置id
     *      'pay_method' => $pay_method,// 支付方式(1现金、2微信支付、4支付宝)
     *      'remarks' => $otherParams['remarks'] ?? '',// 订单备注
     *      'total_amount' => count($courseStaffList),// 商品数量-实际/实时
     *      'total_price' => $total_price,// 商品总价-实际/实时
     *      'total_price_discount' => $total_price_discount,// 商品下单时优惠金额
     *      'total_price_goods' => $total_price_goods,// $total_price - $total_price_discount,// 商品应付金额--平台按量结算值(商品总价-实际/实时 total_price －　商品下单时优惠金额　total_price_discount)
     *      'payment_amount' => $payment_amount,// 总支付金额
     *      'change_amount' => $change_amount,// 找零金额
     *      'invoice_template_id' => 0,// 发票开票模板id
     *      'invoice_template_id_history' => 0,// 发票开票模板id历史
     *  ];
     * @param string  $order_no 生成的订单号
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrder($company_id, $createOrder = [], &$order_no = '', $operate_staff_id = 0, $modifAddOprate = 0){

        $order_type = $createOrder['order_type'];
        $order_no = static::createSn($company_id , $operate_staff_id, 1 . '' . $order_type);
        // 处理订单
        $ordersInfo = [
            'company_id' => $createOrder['company_id'],
            'order_type' => $order_type,// 订单类型1面授培训2会员年费
            'pay_config_id' => $createOrder['pay_config_id'],// 收款帐号配置id
            'pay_method' => $createOrder['pay_method'],// 支付方式(1现金、2微信支付、4支付宝)
            'remarks' => $createOrder['remarks'] ?? '',// 订单备注
            'order_no' => $order_no,// 订单号
            'total_amount' => $createOrder['total_amount'],// 商品数量-实际/实时
            'total_price' => $createOrder['total_price'],// 商品总价-实际/实时
            'total_price_discount' => $createOrder['total_price_discount'],// 商品下单时优惠金额
            'total_price_goods' => $createOrder['total_price_goods'],// 商品应付金额--平台按量结算值(商品总价-实际/实时 total_price －　商品下单时优惠金额　total_price_discount)
            'payment_amount' => $createOrder['payment_amount'],// 总支付金额
            'change_amount' => $createOrder['change_amount'],// 找零金额
            'order_status' => 1,// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
            'order_time' => date('Y-m-d H:i:s'),// 下单时间
            // 'pay_time' => 'aaa',// 付款时间
            // 'sure_time' => 'aaa',// 确认时间
            // 'check_time' => 'aaa',// 完成时间
            // 'cancel_time' => 'aaa',// 作废时间(取消时间)
            'has_refund' => 0,// 是否退费0未退费1已退费2待退费
            'refund_price' => 0,// 退费-实际总退费
            'refund_price_frozen' => 0,// 退费冻结[申请时冻结，成功/失败时减掉]
            // 'refund_time' => 'aaa',// 退费时间-最后一次退款成功的时间
            'check_price' => $createOrder['total_price_goods'],// 真实收取费用（商品应付金额total_price_goods -  退费refund_price）
            'invoice_template_id' => $createOrder['invoice_template_id'] ?? 0,// 发票开票模板id
            'invoice_template_id_history' => $createOrder['invoice_template_id_history'] ?? 0,// 发票开票模板id历史

        ];
        $order_id = 0;
        static::replaceById($ordersInfo, $company_id, $order_id, $operate_staff_id, $modifAddOprate);
        return $order_id;
    }

    /**
     * 订单第三方支付--未生成支付单时-- 有一个主单，多个支付记录的可能
     *
     * @param int  $company_id 企业id
     * @param array $createOrder 生成订单时，需要传入的参数
     *   $createOrder = [
     *       'company_id' => $company_id,
     *       'operate_type' => 2,// 操作类型1用户操作2平台操作
     *       'pay_no' => 'aaa',// 支付单号(第三方)
     *      'pay_price' => 'aaa',// 支付费用
     *      'remarks' => '',// 备注
     *  ];
     * @param string  $order_no 生成的订单号
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array
     * [
     *   'order_pay_id' => $order_pay_id ,
     *   'pay_order_no' => $pay_order_no
     * ]
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrderPay($company_id, $createOrder = [], $order_no = '', $operate_staff_id = 0, $modifAddOprate = 0)
    {

        // 获得订单详情
        $orderInfo = static::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
        if(empty($orderInfo)) throws('订单【' . $order_no . '】不存在');
        if($orderInfo['order_status'] != 1) throws('订单【' . $order_no . '】非待支付状态，不可操作');
        // 支付订单--可能为空
//        $orderPayInfo = OrderPayDBBusiness::getDBFVFormatList(4, 1, ['order_no' => $order_no, 'pay_type' => 1, 'pay_status' => [1,2]]);
//        if(empty($orderPayInfo)) throws('订单【' . $order_no . '】支付订单记录已存在，不可操作');

        $order_pay_id = 0;
        $pay_order_no = [];
            // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$createOrder, &$order_no, &$operate_staff_id, &$modifAddOprate
            , &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderInfo, &$order_pay_id, &$pay_order_no){

            $pay_order_no = static::createSn($company_id , $operate_staff_id, 3 . '' . $orderInfo['order_type']);
            $ordersPayInfo = [
                'company_id' => $orderInfo['company_id'],
                'pay_order_no' => $pay_order_no,// 支付订单号
                'order_type' => $orderInfo['order_type'],// 订单类型1面授培训2会员年费
                'pay_config_id' => $orderInfo['pay_config_id'],// 收款帐号配置id
                'pay_method' => $orderInfo['pay_method'],// 支付方式(1现金、2微信支付、4支付宝)
                'order_no' => $order_no,// 订单号
                'pay_type' => 1,// 支付类型1付款2退款
                'operate_type' => $createOrder['operate_type'] ?? 2,// 操作类型1用户操作2平台操作
                'pay_no' => $createOrder['pay_no'],// 支付单号(第三方)
                'parent_pay_no' => '',// 父单号(第三方)-退款
                'pay_price' => $createOrder['pay_price'],// 支付费用
                'frozen_status' => 0,// 冻结状态0不用冻结1已冻结2已解冻
                'pay_status' => 2,// 状态1已关闭2付款中4成功8失败
                'remarks' => $createOrder['remarks'],// 备注
                'order_time' => date('Y-m-d H:i:s'),// 下单时间
                // 'sure_time' => 'aaa',// 确认时间
            ];
            OrderPayDBBusiness::replaceById($ordersPayInfo, $company_id, $order_pay_id, $operate_staff_id, $modifAddOprate);
            // 修改订单号
//            $updateData = [
//                'order_status' => 2,// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
//                'pay_time' => date('Y-m-d H:i:s'),// 付款时间
//            ];
//            $saveQueryParams = Tool::getParamQuery(['order_no' => $order_no],[], []);
//            static::save($updateData, $saveQueryParams);
        });
        return ['order_pay_id' => $order_pay_id , 'pay_order_no' => $pay_order_no];
    }


    /**
     * 订单完成支付
     *
     * @param int  $company_id 企业id
     * @param string  $order_no 生成的订单号
     * @param string  $pay_order_no 生成的支付订单号
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $order_no = '', $pay_order_no = '', $operate_staff_id = 0, $modifAddOprate = 0)
    {

        // 获得订单详情
        $orderInfo = static::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
        if(empty($orderInfo)) throws('订单【' . $order_no . '】不存在');
        if($orderInfo['order_status'] != 1) throws('订单【' . $order_no . '】非待支付状态，不可操作');
        // 支付订单--可能为空
        // $orderPayInfo = OrderPayDBBusiness::getDBFVFormatList(4, 1, ['order_no' => $order_no, 'pay_type' => 1, 'pay_status' => [1,2]]);

        // $pay_order_no = $orderPayInfo['pay_order_no'] ?? '';

        $order_flow_id = 0;
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$order_no, &$operate_staff_id, &$modifAddOprate
            , &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderInfo, &$pay_order_no, &$order_flow_id){
                // 修改订单号
                $updateData = [
                    'order_status' => 2,// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
                     'pay_time' => date('Y-m-d H:i:s'),// 付款时间
                ];
                $saveQueryParams = Tool::getParamQuery(['order_no' => $order_no],[], []);
                static::save($updateData, $saveQueryParams);

                $currentNow = Carbon::now();
                $ordersFlowInfo = [
                    'company_id' => $orderInfo['company_id'],
                    'order_type' => $orderInfo['order_type'],// 订单类型1面授培训2会员年费
                    'pay_config_id' => $orderInfo['pay_config_id'],// 收款帐号配置id
                    'pay_method' => $orderInfo['pay_method'],// 支付方式(1现金、2微信支付、4支付宝)
                    'order_no' => $order_no,// 订单号
                    'pay_order_no' => $pay_order_no,// 支付订单号
                    'pay_type' => 1,// 支付类型1付款2退款
                    'pay_price' => $orderInfo['check_price'],// 支付费用
                    'remarks' => '',// 备注
                    'count_date' => $currentNow->toDateString(),// 日期
                    'count_year' => $currentNow->year,// 年
                    'count_month' => $currentNow->month,// 月
                    'count_day' => $currentNow->day,// 日
                ];
                OrderFlowDBBusiness::replaceById($ordersFlowInfo, $company_id, $order_flow_id, $operate_staff_id, $modifAddOprate);
                // 相关记录的操作
                $order_type = $orderInfo['order_type'];
                switch($order_type){
                    case 1:// 1面授培训
                        CourseOrderStaffDBBusiness::finishPay($company_id, $order_no, $orderInfo, $operate_staff_id, $modifAddOprate);
                        break;
                    case 2:// 2会员年费
                        break;
                    default:
                        break;
                }
        });
        return $order_flow_id;
    }


    /**
     * 订单确认
     *
     * @param int  $company_id 企业id
     * @param string  $order_no 生成的订单号
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function orderSure($company_id, $order_no = '', $operate_staff_id = 0, $modifAddOprate = 0)
    {

        // 获得订单详情
        $orderInfo = static::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
        if(empty($orderInfo)) throws('订单【' . $order_no . '】不存在');
        if($orderInfo['order_status'] != 2) throws('订单【' . $order_no . '】未付款状态，不可操作');
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$order_no, &$operate_staff_id, &$modifAddOprate
            , &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderInfo){
            // 修改订单号
            $updateData = [
                'order_status' => 4,// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
                'sure_time' => date('Y-m-d H:i:s'),// 付款时间
            ];
            $saveQueryParams = Tool::getParamQuery(['order_no' => $order_no],[], []);
            static::save($updateData, $saveQueryParams);
        });
        return $order_no;
    }

    /**
     * 根据id确认 、 完成服务单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $operate_type 操作类型 1确认、2手动订单完成【对业务完成后才确认缴费的订单，进行手动完成】
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function operateStatusById($company_id, $organize_id = 0, $id = 0, $operate_type = 1, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($operate_type, [1,2])) throws('参数【operate_type】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        // 获得需要操作的数据
        $fieldValParams = ['id' => $id];
        // if(is_numeric($organize_id) && $organize_id > 0) $fieldValParams['company_id'] = $organize_id;
        $dataList = static::getDBFVFormatList(1, 1, $fieldValParams, false);
        if(empty($dataList))  return $modifyNum;// 没有要操作的记录，便不进行操作了

        $updateData = [];
        foreach($dataList as $tInfo){
            $order_no = $tInfo['order_no'];
            $order_status = $tInfo['order_status'];
            switch($operate_type) {
                case 1://  1确认
                    if(!in_array($order_status, [2])) throws('订单号【' . $order_no . '】,非待确认状态，不可进行此操作');
                    break;
                case 2://  2服务完成
                    if(!in_array($order_status, [4])) throws('订单号【' . $order_no . '】,非确认状态，不可进行此操作');
                    break;
                default:
                    break;
            }
        }

        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$operate_type, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$fieldValParams, &$updateData, &$dataList){
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);

            $date = date('Y-m-d');
            $dateTime = date('Y-m-d H:i:s');
            switch($operate_type) {
                case 1://  1确认
                    $updateData['order_status'] = 4;
                    $updateData['sure_time'] = $dateTime;
                    break;
                case 2://  2服务完成
                    $updateData['order_status'] = 8;
                    $updateData['check_time'] = $dateTime;
                    $orderNoArr = Tool::getArrFields($dataList, 'order_no');
                    $orderIsFinisArr = static::judgeOrderIsFinish($orderNoArr);
                    foreach($dataList as $temInfo){
                        $temOrderNo = $temInfo['order_no'];
                        $orderJudgeInfo = $orderIsFinisArr[$temOrderNo] ?? [];
                        $isFinish = $orderJudgeInfo['is_finish'] ?? true;
                        $errStr = $orderJudgeInfo['err_str'] ?? '';
                        if(!$isFinish){
                            throws($errStr);
                            break;
                        }
                    }
                    break;
                default:
                    break;
            }
            $saveQueryParams = Tool::getParamQuery($fieldValParams, [], []);
            $modifyNum = static::save($updateData, $saveQueryParams);
        });
        return $modifyNum;
    }

    /**
     * 根据订单id，获得订单信息并判断是否可以进行分班操作
     *
     * @param string $id 记录id，多个用逗号分隔 或一维数组
     * @param int $operateType 操作类型 1：电子发票[蓝票] 【默认】；2：电子发票[红票]
     * @return array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getOrdersAndJudge($id, $operateType = 1){
        // 获得需要操作的数据
        $fieldValParams = ['id' => $id];
        // if(is_numeric($organize_id) && $organize_id > 0) $fieldValParams['company_id'] = $organize_id;
        $dataList = static::getDBFVFormatList(1, 1, $fieldValParams, false);
        if(empty($dataList))  throws("订单信息不存在");// 没有要操作的记录，便不进行操作了

        foreach($dataList as $info){

            $tem_order_status = $info['order_status'];// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
            $tem_invoice_result = $info['invoice_result'];// 开票结果1待开票1已开蓝票2已红冲
            $tem_invoice_status = $info['invoice_status'];// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
            $tem_upload_status = $info['upload_status'];// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已红冲[不用]
            $tem_order_no = $info['order_no'];

            switch($operateType) {
                case 1:// 电子发票[蓝票]判断 【默认】
                    if(! (($tem_order_status & (2 | 4 | 8)) > 0 && $tem_invoice_status == 1 )){
                        throws('订单【' . $tem_order_no . '】非可开电子发票状态，不可以进行此操作');
                    }
                    break;
                case 2:// 电子发票[红票]
                    if(! $tem_invoice_status == 4 ){
                        throws('订单【' . $tem_order_no . '】非已开发票状态，不可以进行此操作');
                    }
                    break;
                default:
                    break;
            }
        }
        return $dataList;
    }

    /**
     * 根据id开电子发票【蓝票】单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id
     * @param string/array $id id 数组或字符串
     * @param int $invoice_buyer_id 开票抬头id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function operateInvoiceBlueById($company_id, $organize_id = 0, $id = 0, $invoice_buyer_id = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        // 获得订单列表
        $dataList = static::getOrdersAndJudge($id, 1);
        // 判断订单所属企业【同一企业，就可以开电子发票】
        $companyIds = Tool::getArrFields($dataList, 'company_id');
        if(count($companyIds) > 1){
            throws('不同的企业，不可以一起进行开票！请分别开票！');
        }
        $companyNames = Tool::getArrFields($dataList, 'company_name');
        if(is_numeric($organize_id) && $organize_id > 0 && !in_array($organize_id, $companyIds)){
            throws('您没有操作此记录的权限！');
        }

        // 收款帐号
        $payConfigIds = Tool::getArrFields($dataList, 'pay_config_id');
        if(count($payConfigIds) > 1){
            throws('不同的收款帐号，不可以一起进行开票！请分别开票！');
        }
        $pay_config_id = $payConfigIds[0];
        $payConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $pay_config_id]
            , false, '', []);
        if(empty($payConfigInfo)) throws('收款帐号记录不存在！');
        if(!in_array($payConfigInfo['open_status'], [1])) throws('收款帐号非开启状态！');

        // 发票开票模板
        $invoiceTemplateIds = Tool::getArrFields($dataList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1){
            throws('不同的【发票开票模板】，不可以一起进行开票！请分别开票！');
        }
        $invoice_template_id = $invoiceTemplateIds[0];
        $invoiceTemplateInfo = InvoiceTemplateDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_template_id]
            , false, '', []);
        if(empty($invoiceTemplateInfo)) throws('【发票开票模板】记录不存在！');
        if(!in_array($invoiceTemplateInfo['open_status'], [1])) throws('【发票开票模板】非开启状态！');

        // 获得销售方信息
        $invoiceSellerInfo = InvoiceSellerDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id]
            , false, '', []);
        if(empty($invoiceSellerInfo)) throws('【销售方开票信息】记录不存在！');
        if(!in_array($invoiceSellerInfo['open_status'], [1])) throws('【销售方开票信息】非开启状态！');

        // 获得购买方信息
        $invoiceBuyerInfo = InvoiceBuyerDBBusiness::getDBFVFormatList(4, 1, ['id' => $invoice_buyer_id]
            , false, '', []);
        if(empty($invoiceBuyerInfo)) throws('【购买方开票信息】记录不存在！');
        if($organize_id != $invoiceBuyerInfo['company_id'])  throws('您没有【购买方开票信息】操作此记录的权限！');
        if(!in_array($invoiceBuyerInfo['open_status'], [1])) throws('【购买方开票信息】非开启状态！');

        // 获得具体的商品信息
        $dataOrderTypeList = Tool::arrUnderReset($dataList, 'order_type', 2, '_');
        foreach($dataOrderTypeList as $tem_order_type => $tem_orderList){
            switch($tem_order_type) {// 订单类型1面授培训2会员年费
                case 1://  1面授培训
                    $tem_order_nos = Tool::getArrFields($tem_orderList, 'order_no');
                    // 获得订单的学员信息
                    $courseOrderStaffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $tem_order_nos], false);
                    if(empty($courseOrderStaffList)) throws('【面授培训】学员记录不存在！');

                    $invoiceProjectTemplateIds = Tool::getArrFields($courseOrderStaffList, 'invoice_project_template_id');
                    $invoiceProjectTemplateList = InvoiceProjectTemplateDBBusiness::getDBFVFormatList(1, 1, ['id' => $invoiceProjectTemplateIds], false);
                    if(empty($invoiceProjectTemplateList)) throws('【发票商品项目模板】记录不存在！');
                    // 按id格式化数据
                    $formatInvoiceProjectTemplateList = Tool::arrUnderReset($invoiceProjectTemplateList, 'id', 1, '_');


                    $projectTemplateCourserStaffList = Tool::arrUnderReset($courseOrderStaffList, 'invoice_project_template_id', 2, '_');
                    foreach($projectTemplateCourserStaffList as $tem_template_id => $tem_template_staff_list){
                        $temTemplateInfo = $formatInvoiceProjectTemplateList[$tem_template_id] ?? [];
                        if(empty($temTemplateInfo)) throws('【发票商品项目模板-' . $tem_template_id . '】记录不存在！');

                        foreach($tem_template_staff_list as $temInfo){

                        }

                    }


                    break;
                case 2://  2会员年费
                    break;
                default:
                    break;
            }

        }


        $invoice_service = $invoiceTemplateInfo['invoice_service'];// 开票服务商1沪友
        $order_num = '';

        // 发票配置沪友
        $invoiceConfigInfo = InvoiceConfigHydzfpDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id]
            , false, '', []);
        if(empty($invoiceConfigInfo)) throws('【发票配置信息】记录不存在！');


        // $companyConfig = static::$companyConfig;
        $data = [
            "data_resources" => "API",// 是	string	4	固定参数 “API”
            "nsrsbh" => $invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],// "1246546544654654",// 是	string	20	销售方纳税人识别号
            "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
            "order_num" => $order_num,// "1120521299480004",// "order_num_1474873042826",// 是	string	200	业务单据号；必须是唯一的
            "bmb_bbh" => "33.0", // "29.0",// 是	string	10	税收编码版本号，参数“29.0”，具体值请询问提供商-- ?
            "zsfs" => $invoiceTemplateInfo['zsfs'],// "0",// 是	string	2	征税方式 0：普通征税 1: 减按计增 2：差额征税
            "tspz" => $invoiceTemplateInfo['tspz'],// "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
            "xsf_nsrsbh" => $invoiceSellerInfo['xsf_nsrsbh'],// $companyConfig['tax_num'],// "1246546544654654",//是	string	20	销售方纳税人识别号
            "xsf_mc" => $invoiceSellerInfo['xsf_mc'],// $companyConfig['pay_company_name'],// "\t自贡市有限公司",// 是	string	100	销售方名称
            "xsf_dzdh" => $invoiceSellerInfo['xsf_dz'] . $invoiceSellerInfo['xsf_dh'],// "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",// 是	string	100	销售方地址、电话
            "xsf_yhzh" =>  $invoiceSellerInfo['xsf_yh'] . $invoiceSellerInfo['xsf_yhzh'],// "124654654123154",// 是	string	100	销售方开户行名称与银行账号
            "gmf_nsrsbh" => $invoiceBuyerInfo['gmf_nsrsbh'],//  "",// 否	string	100	购买方纳税人识别号(税务总局规定企业用户为必填项)
            "gmf_mc" => $invoiceBuyerInfo['gmf_mc'],//  "个人",// 是	string	100	购买方名称
            "gmf_dzdh" => $invoiceBuyerInfo['gmf_dz'] . $invoiceBuyerInfo['gmf_dh'],//  "",// 否	string	100	购买方地址、电话
            "gmf_yhzh" =>  $invoiceBuyerInfo['gmf_yh'] . $invoiceBuyerInfo['gmf_yhzh'],//  "",// 否	string	100	购买方开户行名称与银行账号
            "kpr" => $invoiceTemplateInfo['kpr'],// "开票员A",// 是	string	8	开票人
            "skr" =>  $invoiceTemplateInfo['skr'],// "",// 否	string	8	收款人
            "fhr" =>  $invoiceTemplateInfo['fhr'],// "",// 否	string	8	复核人
            "yfp_dm" =>  "",// 否	string	12	原发票代码
            "yfp_hm" =>  "",// 否	string	8	原发票号码
            // 是	string	#.##	价税合计;单位：元（2位小数） 价税合计=合计金额(不含税)+合计税额
            // 注意：不能使用商品的单价、数量、税率、税额来进行累加，最后四舍五入，只能是总合计金额+合计税额
            "jshj" =>  "1.00",
            "hjje" => "0.97",// "0.88",// 是	string	#.##	合计金额 注意：不含税，单位：元（2位小数）
            "hjse" =>  "0.03",// "0.12",// 是	string	#.##	合计税额单位：元（2位小数）
            "kce" =>  "",// 否	string	#.##	扣除额小数点后2位，当ZSFS为2时扣除额为必填项
            "bz" =>  $invoiceTemplateInfo['bz'],// "备注啊哈哈哈哈",// 否	string	100	备注 (长度100字符)
            // "kpzdbs" => "",// 否	string	20	已经失效，不再支持
            "jff_phone" => $invoiceBuyerInfo['jff_phone'],//  "",// "手机号",// 否	string	11	手机号，针对税控盒子主动交付，需要填写
            "jff_email" => $invoiceBuyerInfo['jff_email'],//  "",// "电子邮件",// 否	string	100	电子邮件，针对税控盒子主动交付，需要填写
            "common_fpkj_xmxx" => [
                [
                    "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
                    "spbm" => "3070201020000000000",// "",// 是	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
                    "zxbm" => "",// 否	string	20	自行编码(一般不建议使用自行编码)
                    "yhzcbs" => "0",// "",//否	string	2	优惠政策标识 0：不使用，1：使用
                    "lslbs" => "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
                    // 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，
                    // 具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
                    "zzstsgl" => "",// aa  bbb
                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
                    // 如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                    "xmmc" => "培训费",// "更具自身业务决定",// aa  bbb
                    "ggxh" => "",// 否	string	30	规格型号(折扣行请不要传)
                    "dw" => "",// 否	string	20	计量单位(折扣行请不要传)
                    "xmsl" => "1",// "",// 否	string	#.######	项目数量 小数点后6位,大于0的数字
                    "xmdj" => "1.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是含税单价,大于0的数字
                    "xmje" => "1.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）
                    "sl" => "0.03",// "0.13",// 是	string	#.##	税率 例1%为0.01
                    "se" => "0.03",// "0.12"// 是	string	#.##	税额 单位：元（2位小数）
                ]
            ]
        ];
        // A0001-开具蓝字发票
         $result = InvoiceHydzfp::ebiInvoiceHandleNewBlueInvoice($invoiceConfigInfo['open_id'], $invoiceConfigInfo['app_secret'], 0,  false);
    }

    /**
     * 根据【已确认状态】订单号，判断订单否否完成状态
     *
     * @param string  $orderNo 订单号 一维数组或多个用逗号分隔
     * @return  array 记录id值，数组 ['订单号' =》 [ 'is_finish' => 'true:已完成;false:未完成', 'err_str' => $errStr]]
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeOrderIsFinish($orderNo){
        $reDataList = [];
        $dataList = static::getDBFVFormatList(1, 1, ['order_no' => $orderNo], false);
        if(empty($dataList))  return $reDataList;// 没有要操作的记录，便不进行操作了

        $formatOrderList = Tool::arrUnderReset($dataList, 'order_type', 2, '_');
        foreach($formatOrderList as $order_type => $tOrderList){
            if(empty($tOrderList)) continue;
            switch($order_type) {
                case 1://  1面授培训
                    // 判断是否真的已经完成状态
                    $orderNoArr = Tool::getArrFields($tOrderList, 'order_no');
                    // 获得报名的学员列表
                    $courseStaffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $orderNoArr], false);
                    $formatCourseStaffList = Tool::arrUnderReset($courseStaffList, 'order_no', 2, '_');
                    // 获得班级列表
                    $classIds = Tool::getArrFields($courseStaffList, 'class_id');
                    $courseClassList = CourseClassDBBusiness::getDBFVFormatList(1, 1, ['id' => $classIds], false);
                    $formatCourseClassList = Tool::arrUnderReset($courseClassList, 'id', 1, '_');
                    foreach($tOrderList as $tInfo){
                        $order_no = $tInfo['order_no'];
                        $order_status = $tInfo['order_status'];
                        if(!in_array($order_status, [4])) {
                            $reDataList[$order_no] = [ 'is_finish' => false, 'err_str' => '订单【' . $order_no . '】非【已确认状态】，不可进行此操作！'];
                            continue;
                        }
                        // 判断每一个订单号是否都已经完成了
                        $isFinish = true;
                        $errStr = '';
                        $judgeClassIds = [];
                        $errArr = [];
                        $temCourseStaffs = $formatCourseStaffList[$order_no] ?? [];
                        foreach($temCourseStaffs as $tInfo){
                            $temClassId = $tInfo['class_id'];
                            $temStaffStatus = $tInfo['staff_status'];
                            if(isset($judgeClassIds[$temClassId]) || in_array($temStaffStatus, [4])) continue;// 已经判断过了 或已作废的

                            $judgeClassIds[$temClassId] = $temClassId;
                            $temClassInfo = $formatCourseClassList[$temClassId] ?? [];

                            if(empty($temClassInfo) && in_array($temStaffStatus, [1])) {
                                $errStr .= '订单【' . $order_no . '】有学员未分班【未完成课程】，不可进行此操作！';
                                continue;
                            }
                            $class_name = $temClassInfo['class_name'] ?? '';
                            $class_status = $temClassInfo['class_status'] ?? 0;
                            if(!in_array($class_status, [8])){
                                // throws('班级【' . $class_name . '】非结业状态，不可进行此操作！');

                                //$isFinish = false;
                                // $errStr = '班级【' . $class_name . '】非结业状态，不可进行此操作！';
                                array_push($errArr, $class_name);
                                // break;
                            }

                        }
                        if(!empty($errArr)){
                            $isFinish = false;
                            $errStr .= '班级【' . implode('、', $errArr) . '】非结业状态，不可进行此操作！';
                        }
                        if($errStr != '') $isFinish = false;
                        $reDataList[$order_no] = [ 'is_finish' => $isFinish, 'err_str' => $errStr];
                    }
                    break;
//                case 2://  2会员年费
//                    break;
                default:
                    foreach($tOrderList as $tInfo) {
                        $order_no = $tInfo['order_no'];
                        $order_status = $tInfo['order_status'];
                        if(!in_array($order_status, [4])) {
                            $reDataList[$order_no] = [ 'is_finish' => false, 'err_str' => '订单【' . $order_no . '】非【已确认状态】，不可进行此操作！'];
                            continue;
                        }
                        // 判断每一个订单号是否都已经完成了
                        $isFinish = true;
                        $errStr = '';
                        $reDataList[$order_no] = [ 'is_finish' => $isFinish, 'err_str' => $errStr];
                    }
                    break;
            }
        }
        return $reDataList;
    }
}
