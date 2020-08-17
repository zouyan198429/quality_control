<?php
// 能力验证单次结果
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\AbilityJoinItemsResults;
use App\Services\DB\CommonDB;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AbilityJoinItemsResultsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResults';
    public static $table_name = 'ability_join_items_results';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

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
        return CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate){
            //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
            //            throws('联系人不能为空！');
            //        }
            //
            //        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
            //            throws('手机不能为空！');
            //        }

            // 能力验证检测所用仪器
            $results_instrument = [];
            $has_results_instrument = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_instrument', $has_results_instrument, $results_instrument, 1);

            // 能力验证检测标准物质
            $results_standard = [];
            $has_results_standard = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_standard', $has_results_standard, $results_standard, 1);

            // 检测方法依据
            $results_method = [];
            $has_results_method = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_method', $has_results_method, $results_method, 1);

            // 能力验证取样登记表
            $items_samples = [];
            $has_items_samples = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'items_samples', $has_items_samples, $items_samples, 1);

            // 是否有图片资源
            $hasResource = false;
            $resourceIds = [];
            if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
                // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
            }
            $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
            // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
            // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);


            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
            // 保存前的处理
            static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            $modelObj = null;
            // ******************************************************************
            $isModify = false;

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

            // 能力验证检测所用仪器修改
            if($has_results_instrument){
                $results_instrument_ids = AbilityJoinItemsResultsInstrumentDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_instrument, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证检测标准物质修改
            if($has_results_standard){
                $results_standard_ids = AbilityJoinItemsResultsStandardDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_standard, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 检测方法依据修改
            if($has_results_method){
                $results_method_ids = AbilityJoinItemsResultsMethodDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_method, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证取样登记表修改
            if($has_items_samples){
                $items_samples_ids = AbilityJoinItemsSamplesDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $items_samples, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }


            // 同步修改图片资源关系
            if($hasResource){
                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
                // 更新图片资源表
                if(!empty($resourceIds)) {
                    $resourceArr = ['column_type' => 4, 'column_id' => $id];
                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
                }
            }
            // *********************************************************
            // 保存成功后的处理
            static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            return $id;
        });
    }

    /**
     * 根据id判断记录结果
     *
     * @param array $saveData 要保存或修改的数组 [ 'result_status' =>  2满意、4有问题、8不满意   16满意【补测满意】 ]
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeResultById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){
         $result_status = $saveData['result_status'] ?? 0;
//        if(!in_array($result_status, array_keys(AbilityJoinItemsResults::$resultStatusArr))) throws('参数【result_status】值无效！');
        $info = static::getInfo($id);
        if(empty($info)) throws('记录不存在！');
        $status = $info['status'] ?? 0;// 状态1已报名  2已取样  4已传数据   8已判定 16已完成
        $info_result_status = $info['result_status'] ?? 0;// 验证结果1待判定  2满意、4有问题、8不满意   16满意【补测满意】

        if($status != 4 || $info_result_status != 1) throws('非已传数据状态，不可进行此操作！');

        $retry_no = $info['retry_no'] ?? 0;
        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $resultStatus = AbilityJoinItemsResults::$resultStatusArr;
        if(isset($resultStatus[1])) unset($resultStatus[1]);// 去掉 1待判定
        if($retry_no == 0){
            if(isset($resultStatus[16])) unset($resultStatus[16]);// 去掉  16满意【补测满意】
        }else{
            if(isset($resultStatus[2])) unset($resultStatus[2]);// 去掉 2满意
        }
        if(!in_array($result_status, array_keys($resultStatus))) throws('请选择正确的验证结果');
        $result_status_text = $resultStatus[$result_status] ?? '';
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);
        // 具体操作
        // 有问题  不满意
        //     如果是第一次测试：
        //            项目表 abilitys   初测不满意数 first_fail_num + 1
        //            主报名表 abilityJoin 状态改为 status 2' => '补测待取样； 初测不满意数 first_fail_num + 1
        //            报名项表 ability_join_items  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        //                                          改为补测  retry_no =1 并生成新的 补测单次结果
        //            对当前的这条单次记录 ability_join_items_results    status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        // 如果是第二次补测
        //           项目表 abilitys   补测不满意数 repair_fail_num + 1 ; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
        //           主报名表 abilityJoin  判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
        //                                              补测不满意数 repair_fail_num + 1
        //            报名项表 ability_join_items status 状态改为 8 已判定 ； result_status ；是否评定  2 已评 judge_time；
        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        // 满意
        //     如果是第一次测试： 第二次补测  补测满意数 repair_success_num + 1
        //            项目表 abilitys  初测满意数 first_success_num + 1; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
        //            主报名表 abilityJoin 判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
        //                                  初测满意数 first_success_num + 1;
        //            报名项表 ability_join_items status  status 状态改为 8 已判定 ； result_status ；是否评定  2 已评 judge_time；
        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；

        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$operate_staff_id_history, &$modifAddOprate, &$result_status, &$retry_no, &$info, &$result_status_text){

            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            $ability_id = $info['ability_id'];// 所属能力验证
            $ability_join_id = $info['ability_join_id'];// 所属报名主表
            $ability_join_item_id = $info['ability_join_item_id'];// 所属能力验证报名项
            $admin_type = $info['admin_type'];// 类型1平台2企业4个人
            $staff_id = $info['staff_id'];// 所属人员id
            $currentNow = Carbon::now()->toDateTimeString();

            $abilityInfo = AbilitysDBBusiness::getInfo($ability_id);
            if(empty($abilityInfo)) throws('项目记录不存在！');
            // 8已结束 16 已取消【作废】)
            if(in_array($abilityInfo['status'], [8,16]))  throws('项目记录状态有误，不可进行此操作！');
            if(!in_array($abilityInfo['status'], [4]))throws('项目记录状态非进行中，不可进行此操作！');

            $joinInfo = AbilityJoinDBBusiness::getInfo($ability_join_id);
            if(empty($joinInfo)) throws('报名主记录不存在！');
            if(!in_array($joinInfo['status'],[4,8])) throws('报名主记录非进行中或部分评定状态，不可进行此操作！');

            $joinItemInfo = AbilityJoinItemsDBBusiness::getInfo($ability_join_item_id);
            if(empty($joinItemInfo)) throws('报名项记录不存在！');
            if(!in_array($joinItemInfo['status'],[2,4])) throws('报名项记录非进行中状态，不可进行此操作！');

            // 记录报名日志
            // 获得操作人员信息
            $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
            $logContent = '判定[' . $result_status_text . ']数据：' . json_encode($saveData);
            // $ability_join_id = $resultDatas['ability_join_id'] ?? 0;
            AbilityJoinLogsDBBusiness::saveAbilityJoinLog($operateInfo['admin_type'], $operate_staff_id, $ability_join_id, $ability_join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);

            // 4有问题、8不满意
            if(in_array($result_status, [4, 8])){
                switch($retry_no){
                    case 0:// 初测
                        // 项目表 abilitys   初测不满意数 first_fail_num + 1
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'first_fail_num', 1, 'inc');
                        //  补测企业数量 + 1
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'repair_num', 1, 'inc');
                        // 主报名表 abilityJoin retry_no 改为补测 is_sample 4  补测待取样 8 进行中[有评定]； 初测不满意数 first_fail_num + 1
                        AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'first_fail_num', 1, 'inc');
                        AbilityJoinDBBusiness::saveById(['retry_no' => 1, 'is_sample' => 4, 'status' => 8], $ability_join_id);
                        //            报名项表 ability_join_items  8 已判定 result_status ；judge_status 是否评定  2 已评 judge_time；
                        //
                        AbilityJoinItemsDBBusiness::saveById([
                            'retry_no' => 1,
                             'status' => 8,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                            'is_sample' => 4,
                             'submit_status' => 4,
                            // 'judge_status' => 4,
                        ], $ability_join_item_id);
                        // 生成新的补测单结果
                        $updateFields = [
                            'ability_join_item_id' => $ability_join_item_id,
                            'retry_no' => 1,
                            'admin_type' => $joinItemInfo['admin_type'],
                            'staff_id' => $joinItemInfo['staff_id'],
                            'ability_join_id' => $joinItemInfo['ability_join_id'],
                            'ability_code' => $joinItemInfo['ability_code'],
                            'contacts' => $joinItemInfo['contacts'],
                            'mobile' => $joinItemInfo['mobile'],
                            'tel' => $joinItemInfo['tel'],
                            'ability_id' => $joinItemInfo['ability_id'],
                            'join_time' => $joinItemInfo['join_time'],
                            'status' => 1,
                            'is_sample' => 1,
                            // 'sample_time' => null,
                            'submit_status' => 1,
                            // 'submit_time' => null,
                            'judge_status' => 1,
                            // 'judge_time' => null,
                            'result_status' => 1,
                            'resource_ids' => '',
                            'submit_remarks' => '',
                        ];
                        $searchConditon = [
                            'ability_join_item_id' => $ability_join_item_id,
                            'retry_no' => 1,
                        ];
                        $itemResultObj = null;
                        static::updateOrCreate($itemResultObj, $searchConditon, $updateFields);

                        // 记录报名日志--新建补测
                        // 获得操作人员信息
                        // $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
                        $logContent = '新建补测数据：' . json_encode(array_merge($updateFields, $searchConditon));
                        // $ability_join_id = $resultDatas['ability_join_id'] ?? 0;
                        AbilityJoinLogsDBBusiness::saveAbilityJoinLog($operateInfo['admin_type'], $operate_staff_id, $ability_join_id, $ability_join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);



                        //            对当前的这条单次记录 ability_join_items_results    status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                        static::saveById([
                            'status' => 16,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                        ],$id);
                        break;
                    case 1:// 补测
                        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                        static::saveById([
                            'status' => 16,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                        ],$id);

                        //            报名项表 ability_join_items status 状态改为 32 无证 ； result_status ；是否评定  8  已评[补测] judge_time；

                        AbilityJoinItemsDBBusiness::saveById([
                            'status' => 32,
                            'result_status' => $result_status,
                            'judge_status' => 8,
                            'judge_time' => $currentNow,
                            // 'judge_status' => 4,
                        ], $ability_join_item_id);

                        //           主报名表 abilityJoin  判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
                        //                                              补测不满意数 repair_fail_num + 1
                        AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'repair_fail_num', 1, 'inc');
                        // 企业报名的状态可能是
                        //     还有没有判定的 时  8 有判定
                        //     全判定时   没有一个满意的： 32 无证书  ； 有一个满意的：16  待发证

                        $tem_join_result_status  = 8;
                        // 获得正在处理的一条记录
                        // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                        $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                        $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                        if(empty($resultDoingInfo)){// 全都判定了
                            // 判断是否有一个满意的
                            // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                            $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [8,32]]]], []);
                            $resultSuccInfo = static::getInfoByQuery(1, $queryParams, []);
                            if(empty($resultSuccInfo)) $tem_join_result_status  = 32;// 没有一个满意的
                            if(!empty($resultSuccInfo))   $tem_join_result_status  = 16; // 肯定有一个满意的
                        }
                        AbilityJoinDBBusiness::saveById([
                            'status' => $tem_join_result_status
                        ], $ability_join_id);

                        // 整个项目的--- 判断是否还有未判定的项目
                        //           项目表 abilitys   补测不满意数 repair_fail_num + 1 ; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'repair_fail_num', 1, 'inc');
                        // 状态可能是
                        //    还有没有要判定的  有  : 状态不变
                        //                      没有（全都判定时）：  状态 不变  is_publish 2 待公布；
                        // 获得正在处理的一条记录
                        $queryParams = Tool::getParamQuery(['ability_id' => $ability_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                        $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                        if(empty($resultDoingInfo)){// 全都判定了
                            AbilitysDBBusiness::saveById([
                                'is_publish' => 2,
                            ], $ability_id);
                        }
                        break;
                    default:
                        break;
                }
            }else{// 2满意  16满意【补】
                $incFieldName = ($result_status == 2) ? 'first_success_num' : 'repair_success_num';
                // 对当前的这条单次记录 ability_join_items_results  status 8 待发证 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                static::saveById([
                    'status' => 8,
                    'result_status' => $result_status,
                    'judge_status' => 2,
                    'judge_time' => $currentNow,
                ],$id);

                // 报名项表 ability_join_items status  status 状态改为 16 待发证 ； result_status ；是否评定  2 已评 judge_time；

                AbilityJoinItemsDBBusiness::saveById([
                    'status' => 16,
                    'result_status' => $result_status,
                    'judge_status' => ($result_status == 2) ? 2 : 8,
                    'judge_time' => $currentNow,
                ], $ability_join_item_id);

                //            主报名表 abilityJoin 判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
                //                                  初测满意数 first_success_num + 1;

                AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, $incFieldName, 1, 'inc');
                // 企业报名的状态可能是
                //     还有没有判定的 时  8 有判定
                //     全判定时   16  待发证

                $tem_join_result_status  = 8;
                // 获得正在处理的一条记录
                // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                if(empty($resultDoingInfo)){
                    // 判断是否有一个满意的
                    $tem_join_result_status  = 16; // 肯定有一个满意的
                }
                AbilityJoinDBBusiness::saveById([
                    'status' => $tem_join_result_status
                ], $ability_join_id);
                // 满意项目数量 + 1
                AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'passed_num', 1, 'inc');

                //            项目表 abilitys  初测满意数 first_success_num + 1; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
                 AbilitysDBBusiness::fieldValIncDec($ability_id, $incFieldName, 1, 'inc');

                //    还有没有要判定的  有  : 状态不变
                //                      没有（全都判定时）：  状态 不变  is_publish 2 待公布；
                // 获得正在处理的一条记录
                $queryParams = Tool::getParamQuery(['ability_id' => $ability_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                if(empty($resultDoingInfo)){
                    AbilitysDBBusiness::saveById([
                        'is_publish' => 2,
                    ], $ability_id);
                }
            }
        });
        return $id;
    }

    /**
     * 根据项目id--获得指定的单个或多个状态的数量
     * @param mixed $status 单个或 多个：一维数组 ； 状态1已报名  2已取样  4已传数据   8已判定 16已完成
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @param int  $ability_id  项目id --可以为0：不参与查询
     * @param int  $admin_type  类型1平台2企业4个人--可以为0：不参与查询
     * @param int  $staff_id  所属人员id--可以为0：不参与查询
     * @return  mixed 数量
     * @author zouyan(305463219@qq.com)
     */
    public static function getCountNum($status = 1, $retry_no = 0, $ability_id = 0, $admin_type = 0, $staff_id = 0){
        $fieldValParams = [];
        if(is_numeric($retry_no) && $retry_no >= 0) $fieldValParams['retry_no'] = $retry_no;
        if(is_numeric($ability_id) && $ability_id > 0) $fieldValParams['ability_id'] = $ability_id;
        if(is_numeric($admin_type) && $admin_type > 0) $fieldValParams['admin_type'] = $admin_type;
        if(is_numeric($staff_id) && $staff_id > 0) $fieldValParams['staff_id'] = $staff_id;
        if(!empty($fieldValParams)) $queryParams = Tool::getParamQuery($fieldValParams, [], []);
        if(!empty($status)) Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);

        $queryParams['count'] = 0;
        return static::getAllList($queryParams, []);
    }

    /**
     * 根据项目id--获得指定的单个记录
     * @param mixed $status 单个或 多个：一维数组 ； 状态1已报名  2已取样  4已传数据   8已判定 16已完成
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @param int  $ability_id  项目id --可以为0：不参与查询
     * @param int  $admin_type  类型1平台2企业4个人--可以为0：不参与查询
     * @param int  $staff_id  所属人员id--可以为0：不参与查询
     * @return  mixed 数量
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoOne($status = 1, $retry_no = 0, $ability_id = 0, $admin_type = 0, $staff_id = 0){
        $fieldValParams = [];
        if(is_numeric($retry_no) && $retry_no >= 0) $fieldValParams['retry_no'] = $retry_no;
        if(is_numeric($ability_id) && $ability_id > 0) $fieldValParams['ability_id'] = $ability_id;
        if(is_numeric($admin_type) && $admin_type > 0) $fieldValParams['admin_type'] = $admin_type;
        if(is_numeric($staff_id) && $staff_id > 0) $fieldValParams['staff_id'] = $staff_id;
        if(!empty($fieldValParams)) $queryParams = Tool::getParamQuery($fieldValParams, [], []);
        if(!empty($status)) Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);
        $info = static::getInfoByQuery(1, $queryParams, []);
        return $info;
    }
}
