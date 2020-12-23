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
