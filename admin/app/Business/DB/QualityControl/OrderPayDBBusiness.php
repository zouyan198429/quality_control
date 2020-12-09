<?php
// 支付订单
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class OrderPayDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\OrderPay';
    public static $table_name = 'order_pay';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 订单完成支付
     *
     * @param int  $company_id 企业id
     * @param string  $pay_order_no 生成的支付订单号
     * @param string  $pay_no 支付单号(第三方)
     * @param array  $extendParams 扩展字段
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $pay_order_no, $pay_no = '', $extendParams = [], $operate_staff_id = 0, $modifAddOprate = 0){

        // 查询支付单
        $payInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
        if(empty($payInfo)) throws('订单支付记录不存在', 10);// return '订单支付记录不存在';// 1; //记录不存在

        $status = $payInfo->pay_status;// 付款状态  状态1已关闭2付款中4成功8失败
        if(in_array($status, [1,4])) throws('已关闭或已支付成功', 10);// return '已关闭或已支付成功';//  return 1;// 已关闭或成功

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$pay_order_no, &$pay_no, &$extendParams, &$operate_staff_id, &$modifAddOprate
            , &$payInfo, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history){
            $order_no = $payInfo['order_no'];
            // 修改订单号
            $updateData = array_merge($extendParams, [
                'pay_no' => $pay_no,
                'pay_status' => 4,// 状态1已关闭2待确认4成功8失败
                'sure_time' => date('Y-m-d H:i:s'),// 付款时间
            ]);
            $saveQueryParams = Tool::getParamQuery(['pay_order_no' => $pay_order_no],[], []);
            static::save($updateData, $saveQueryParams);
            OrdersDBBusiness::finishPay($company_id, $order_no, $operate_staff_id, $modifAddOprate);
        });

    }

    /**
     * 订单-支付--失败
     *
     * @param int  $company_id 企业id
     * @param string  $pay_order_no 生成的支付订单号
     * @param string  $pay_no 支付单号(第三方)
     * @param array  $extendParams 扩展字段
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int  记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function failPay($company_id, $pay_order_no, $pay_no = '', $extendParams = [], $operate_staff_id = 0, $modifAddOprate = 0){

        // 查询支付单
        $payInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
        if(empty($payInfo)) throws('订单支付记录不存在', 10);// return '订单支付记录不存在';// 1; //记录不存在

        $status = $payInfo->pay_status;// 付款状态  状态1已关闭2付款中4成功8失败
        if(in_array($status, [1,4])) throws('已关闭或已支付成功', 10);// return '已关闭或已支付成功';//  return 1;// 已关闭或成功

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$pay_order_no, &$pay_no, &$extendParams, &$operate_staff_id, &$modifAddOprate
            , &$payInfo, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history){
            // 修改订单号
            $updateData = array_merge($extendParams, [
                'pay_no' => $pay_no,
                'pay_status' => 8,// 状态1已关闭2待确认4成功8失败
                'sure_time' => date('Y-m-d H:i:s'),// 付款时间
            ]);
            $saveQueryParams = Tool::getParamQuery(['pay_order_no' => $pay_order_no],[], []);
            static::save($updateData, $saveQueryParams);
        });

    }

    /**
     * 支付回调--微信
     *
     * @param array $message 回调的参数
     *   {
     *      "appid": "wxcb82783fe211782f",
     *      "bank_type": "CFT",// 银行类型
     *      "cash_fee": "1",// 现金
     *      "fee_type": "CNY",// 币种
     *      "is_subscribe": "N",// 是否订阅
     *      "mch_id": "1527642191",
     *      "nonce_str": "5c8e67b1d9bc3",
     *      "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
     *      "out_trade_no": "119108029350007",
     *      "result_code": "SUCCESS",// 支付结果 FAIL:失败;SUCCESS:成功
     *      "return_code": "SUCCESS",// 表示通信状态: SUCCESS 成功
     *      "sign": "C6ACF2C7C8AF999048094ED2264F0ABC",
     *      "time_end": "20190317232919",// 交易时间
     *      "total_fee": "1",// 交易金额
     *      "trade_type": "JSAPI",// 交易类型
     *      "transaction_id": "4200000288201903177135850941"// 交易号
     *  }
     * @param array $queryMessage 商户订单号查询 结果
     *
     *商户订单号查询 结果
     *   {
     *       "return_code": "SUCCESS",
     *      "return_msg": "OK",
     *      "appid": "wxcb82783fe211782f",
     *      "mch_id": "1527642191",
     *      "nonce_str": "aA5oRYgVOf7osQv3",
     *      "sign": "DCD3A1790A8C4E1A4BBE2339E812AB3C",
     *      "result_code": "SUCCESS",
     *      "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
     *      "is_subscribe": "N",
     *      "trade_type": "JSAPI",
     *      "bank_type": "CFT",
     *      "total_fee": "1",
     *      "fee_type": "CNY",
     *      "transaction_id": "4200000288201903177135850941",
     *      "out_trade_no": "119108029350007",
     *      "attach": null,
     *      "time_end": "20190317232919",
     *      "trade_state": "SUCCESS",// 交易状态
     *      "cash_fee": "1",
     *      "trade_state_desc": "支付成功"
     *  }
     *
     * 交易状态
     *  SUCCESS—支付成功
     *  REFUND—转入退款
     *  NOTPAY—未支付
     *  CLOSED—已关闭
     *  REVOKED—已撤销（付款码支付）
     *  USERPAYING--用户支付中（付款码支付）
     *  PAYERROR--支付失败(其他原因，如银行返回失败)
     *  支付状态机请见下单API页面
     * @return  mixed string throws错误，请再通知我  正常返回 :不用通知我了
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXNotify($message, $queryMessage){
        // 查询订单
        $out_trade_no = $message['out_trade_no'] ?? '';// 我方单号--与第三方对接用
        $out_trade_no = trim($out_trade_no);
        if(empty($out_trade_no)) throws('参数out_trade_no不能为空!');
        $transaction_id = $message['transaction_id'] ?? '';// 第三方单号[有则填]
        $transaction_id = trim($transaction_id);
        if(empty($transaction_id)) throws('参数transaction_id不能为空!');
        // pr($wrInfo->toArray());
        // pr($message);
        // pr($queryMessage);

//            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
//                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
//            }

//            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
//
        $returnStr = '';
        if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
            Tool::lockDoSomething('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1 | 2 | 4, ':', ':'), Tool::getActionMethod(), __CLASS__, __FUNCTION__, $out_trade_no]),
                function()  use(&$returnStr, &$message, &$queryMessage, &$out_trade_no, &$transaction_id){//

                    CommonDB::doTransactionFun(function() use(&$returnStr, &$message, &$queryMessage, &$out_trade_no, &$transaction_id){
                        try {
                            if ($message['result_code'] === 'SUCCESS' && $queryMessage['trade_state'] === 'SUCCESS') {
                                static::finishPay(0, $out_trade_no, $transaction_id, [], 0, 0);

                                // 用户支付失败
                            } elseif ($message['result_code'] === 'FAIL') {
                                static::failPay(0, $out_trade_no, $transaction_id, [], 0, 0);
                            }
                        } catch ( \Exception $e) {
                            $errStr = $e->getMessage();
                            $errCode = $e->getCode();
                            if($errCode == 10 ){
                                $returnStr = $errStr;
                                return $returnStr;
                            }else{
                                //                    throws('操作失败；信息[' . $e->getMessage() . ']');
                                throws($errStr, $errCode);
                            }
                            // throws($e->getMessage());
                        }

                    });
                }, function($errDo){
                    // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                   // return null;
                    throws('操作失败，请稍后重试!');
                }, true, 1, 2000, 2000);

            return $returnStr;

        } else {
//            return '通信失败，请稍后再通知我';// $fail('通信失败，请稍后再通知我');
            throws('通信失败，请稍后再通知我');
        }
        return '';
//
//            $order->save(); // 保存订单

//            return true; // 返回处理完成
    }
}
