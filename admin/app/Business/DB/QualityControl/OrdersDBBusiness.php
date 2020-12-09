<?php
// 收款订单
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
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
            'order_type' => $order_type,// 订单类型1面授培训2会员年费
            'pay_config_id' => $createOrder['pay_config_id'],// 收款帐号配置id
            'pay_method' => $createOrder['pay_method'],// 支付方式(1现金、2微信支付、4支付宝)
            'remarks' => $createOrder['remarks'] ?? '',// 订单备注
            'order_no' => $order_no,// 订单号
            'total_amount' => $createOrder['total_amount'],// 商品数量-实际/实时
            'total_price' => $createOrder['total_price'],// 商品总价-实际/实时
            'total_price_discount' => $createOrder['total_price'],// 商品下单时优惠金额
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
                'pay_order_no' => $pay_order_no,// 支付订单号
                'order_type' => $orderInfo['order_type'],// 订单类型1面授培训2会员年费
                'pay_config_id' => $orderInfo['pay_config_id'],// 收款帐号配置id
                'pay_method' => $orderInfo['pay_method'],// 支付方式(1现金、2微信支付、4支付宝)
                'order_no' => $order_no,// 订单号
                'pay_type' => 1,// 支付类型1付款2退款
                'operate_type' => 2,// 操作类型1用户操作2平台操作
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
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $order_no = '', $operate_staff_id = 0, $modifAddOprate = 0)
    {

        // 获得订单详情
        $orderInfo = static::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
        if(empty($orderInfo)) throws('订单【' . $order_no . '】不存在');
        if($orderInfo['order_status'] != 1) throws('订单【' . $order_no . '】非待支付状态，不可操作');
        // 支付订单--可能为空
        $orderPayInfo = OrderPayDBBusiness::getDBFVFormatList(4, 1, ['order_no' => $order_no, 'pay_type' => 1, 'pay_status' => [1,2]]);

        $pay_order_no = $orderPayInfo['pay_order_no'] ?? '';

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

}
