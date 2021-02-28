<?php
// 短信模板
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class SmsTemplateDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\SmsTemplate';
    public static $table_name = 'sms_template';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        // 模板关键字--唯一
        if( isset($saveData['template_key']) && static::judgeFieldExist($company_id, $id ,"template_key", $saveData['template_key'], [],1)){
            throws('模板关键字已存在！');
        }
        if( isset($saveData['template_code']) && isset($saveData['template_type']) && static::judgeFieldExist($company_id, $id ,"template_code", $saveData['template_code'], [['template_type', $saveData['template_type']]],1)){
            throws('模板ID已存在！');
        }
        // 如果有模板内容
        if( isset($saveData['template_content']) ){
            $template_content = $saveData['template_content'] ?? '';
            $paramsArr = Tool::getLabelArr($template_content, '{', '}');
            if(!empty($paramsArr)){
                $module_id = $saveData['module_id'] ?? 0;
                if($module_id <= 0){// 得新获取
                    if($id > 0){// 修改记录
                        $temInfo = static::getInfo($id);
                        if(empty($temInfo)) throws('记录不存在！');
                        $module_id = $temInfo['module_id'];
                    }else{// 新加记录
                        throws('参数module_id有误或不存在！');
                    }
                }
                // 获得模块参数
                $moduleInfo = SmsModuleDBBusiness::getInfo($module_id);
                if(empty($moduleInfo)) throws('模块记录不存在！');
                // 获得模块参数
                $paramsList = SmsModuleParamsDBBusiness::getDBFVFormatList(1, 1, ['module_id' => $module_id], false, [], []);
                if(!empty($paramsList)){
                    $param_codeArr = Tool::getArrFields($paramsList, 'param_code');
                    $paramsArr = array_diff($paramsArr, $param_codeArr);
                }

                if(!empty($paramsArr)){
                    // 获得通用的参数
                    $paramsCommonList = SmsModuleParamsCommonDBBusiness::getDBFVFormatList(1, 1, [], true, [], []);
                    if(!empty($paramsCommonList)){
                        $param_common_codeArr = Tool::getArrFields($paramsCommonList, 'param_code');
                        $paramsArr = array_diff($paramsArr, $param_common_codeArr);
                    }
                }
                if(!empty($paramsArr)) throws('模板内容参数[' . implode(',', $paramsArr) . ']不是有效参数，请修改！');
            }
        }

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify ){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }
}
