<?php
// 能力验证报名主表
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AbilityJoinDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoin';
    public static $table_name = 'ability_join';// 表名称
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
            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
            // 保存前的处理
            static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);

            // 能力验证报名项
            $logContent = '';
            if(isset($saveData['ability_join_items'])){
                $logContent = '能力验证' . (($id > 0) ? '新报名' : '追加报名') . ';';
                $logContent .= '数据：' . json_encode($saveData);
            }
            $ability_join_items = [];
            $has_join_item = false;// 是否有能力验证报名项修改 false:没有 ； true:有
            if(Tool::getInfoUboundVal($saveData, 'ability_join_items', $has_join_item, $ability_join_items, 1)){

            }

            $currentNow = Carbon::now()->toDateTimeString();

            $modelObj = null;
            //************************************************************
            $isModify = false;

            $ability_code = $saveData['ability_code'] ?? '';
            // 没有单号，则重新生成
            if(empty($ability_code)){// $id <= 0
                $ability_code = AbilityCodeDBBusiness::getAbilityCode();// 单号 生成  2020NLYZ0001
            }

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
                $addNewData = [
                    // 'company_id' => $company_id,
                    'ability_code' => $ability_code,// 单号 生成  2020NLYZ0001
                    'join_year' => Carbon::now()->year,
                    'join_time' => $currentNow,
                    'status' => 1,
                    'passed_num' => 0,
                    'is_print' => 1,
                    'is_grant' => 1,
                ];
                $saveData = array_merge($saveData, $addNewData);
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

            // 如果有能力验证报名项 修改/添加---不做删除操作
            if($has_join_item){
                $sample_result_ids = AbilityJoinItemsDBBusiness::updateByDataList(['ability_join_id' => $id]
                    , [
                        'ability_join_id' => $id,
                        'ability_code' => $ability_code,
                        'join_time' => $currentNow,
                    ]
                    , $ability_join_items, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, [
                        'del' => [// 删除用
                            'del_type' => 2,// 4,// 2,// 删除方式  1：批量删除 static::deleteByIds($dbIds) [默认] 2: 多次单个删除：static::delById($company_id, $tem_id, $operate_staff_id, $modifAddOprate, $extendParams);
                            'extend_params' => [],// 删除的扩展参数 一维数组  del_type = 2时：用
                        ],
                    ]);

                // 记录报名日志
                // 获得操作人员信息
                $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
                AbilityJoinLogsDBBusiness::saveAbilityJoinLog($operateInfo['admin_type'], $operate_staff_id, $id, 0, $logContent, $operate_staff_id, $operate_staff_id_history);
            }
//            if($has_join_item){
//                $joinItemsListArr = [];
//                $joinItemsIds = [];
//                if($isModify){// 是修改
//                    // 获得所有的方法标准
//                    $queryParams = [
//                        'where' => [
////                ['company_id', $organize_id],
//                            ['ability_join_id', $id],
////                ['teacher_status',1],
//                        ],
//                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                    ];
//                    $joinItemsDataListObj = AbilityJoinItemsDBBusiness::getAllList($queryParams, []);
//                    $joinItemsListArr = $joinItemsDataListObj->toArray();
//                    if(!empty($joinItemsListArr)) $joinItemsIds = array_values(array_unique(array_column($joinItemsListArr,'id')));
//                }
//
//                if(!empty($ability_join_items)){
//                    $appendArr = [
//                        'operate_staff_id' => $operate_staff_id,
//                        'operate_staff_id_history' => $operate_staff_id_history,
//                    ];
//                    // 新加时
////                    if(!$isModify){
////                        $appendArr = array_merge($appendArr, [
////                            'ability_join_id' => $id,
////                            'ability_code' => $ability_code,
////                            'join_time' => $currentNow,
////                        ]);
////                    }
//                    // Tool::arrAppendKeys($ability_join_items, $appendArr);
//                    foreach($ability_join_items as $k => $join_item_info){
//                        $join_item_id = $join_item_info['id'] ?? 0;
//                        if(isset($join_item_info['id'])) unset($join_item_info['id']);
//
//                        Tool::arrAppendKeys($join_item_info, $appendArr);
//                        if($join_item_id <= 0 ) Tool::arrAppendKeys($join_item_info, [
//                            'ability_join_id' => $id,
//                            'ability_code' => $ability_code,
//                            'join_time' => $currentNow,
//                        ]);
//                        AbilityJoinItemsDBBusiness::replaceById($join_item_info, $company_id, $join_item_id, $operate_staff_id, $modifAddOprate);
//                        // 移除当前的id
//                        $recordUncode = array_search($join_item_id, $joinItemsIds);
//                        if($recordUncode !== false) unset($joinItemsIds[$recordUncode]);// 存在，则移除
//                    }
//                }
//                if($isModify && !empty($joinItemsIds)) {// 是修改 且不为空
//                    // 删除记录
//                    // AbilityJoinItemsDBBusiness::deleteByIds($joinItemsIds);
//                    AbilityJoinItemsDBBusiness::delById($company_id, $joinItemsIds, $operate_staff_id, $modifAddOprate, []);
//                }
//            }
            // ***********************************************************
            // 保存成功后的处理
            static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            return $id;
        });
    }

    /**
     * 根据id新加或修改样品编号
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function sample_save($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

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
            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
            // 保存前的处理
            static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);

            // 样品编号
            $sample_num_data = [];
            $has_sample_num = false;// 样品编号 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'sample_num_data', $has_sample_num, $sample_num_data, 1);


            if(empty($sample_num_data)) return $id;

            $currentNow = Carbon::now()->toDateTimeString();

            $modelObj = null;
            // ******************************************************
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
                throws('操作记录参数有误！');
//                $addNewData = [
//                    // 'company_id' => $company_id,
//                    'ability_code' => $ability_code,// 单号 生成  2020NLYZ0001
//                    'join_year' => Carbon::now()->year,
//                    'join_time' => $currentNow,
//                    'status' => 1,
//                    'passed_num' => 0,
//                    'is_print' => 1,
//                    'is_grant' => 1,
//                ];
//                $saveData = array_merge($saveData, $addNewData);
//                // 加入操作人员信息
//                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            if($id <= 0){// 新加
//                $resultDatas = static::create($saveData,$modelObj);
//                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveData = array_merge($saveData,[
                    'status' => 4,
                    'sample_time' => $currentNow,
                ]);
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            $appendArr = [
                'operate_staff_id' => $operate_staff_id,
                'operate_staff_id_history' => $operate_staff_id_history,
            ];
            // 获得操作人员信息
            $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
            foreach($sample_num_data as $join_item_id => $sample_list){
                if(empty($sample_list)) continue;
                $join_item_info =  AbilityJoinItemsDBBusiness::getInfo($join_item_id);
                //  1已报名  2已取样  4已上传数据
                //   8已判定【如果有有问题、不满意 --还可以再取样--进入已取样状态】
                //   16已完成--不可再修改【打印证书后或大后台点《公布结果》】)
                $status = $join_item_info['status'];
                $retry_no = $join_item_info['retry_no'];// 是否补测 0正常测 1补测1 2 补测2 .....
                $result_status = $join_item_info['result_status'];// 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
                $is_sample = $join_item_info['is_sample'];// 是否取样1待取样--未取 2已取样--已取

                // 完成状态不可取样
                // 验证结果 2满意  16满意【补测满意】 不可取样
                if(in_array($status, [16]) || in_array($result_status, [2, 16])) continue;

                // 更新报名项目

                $saveItemData = array_merge([
                    'status' => 2,
                    'is_sample' => 2,
                    'sample_time' => $currentNow,
                ], $appendArr);
                $itemObj = null;
                $saveBoolen = AbilityJoinItemsDBBusiness::saveById($saveItemData, $join_item_id,$itemObj);

                // 能力验证单次结果
                $updateFields = array_merge([
                    'status' => 2,
                    'is_sample' => 2,
                    'sample_time' => $currentNow
                ], $appendArr);
                $searchConditon = [
                    'ability_join_item_id' => $join_item_id,
                    'retry_no' => $retry_no,
                ];
                $resultObj = null;
                AbilityJoinItemsResultsDBBusiness::updateOrCreate($resultObj, $searchConditon, $updateFields );
                $result_id = $resultObj->id;


                // 数据加入结果id
                Tool::arrAppendKeys($sample_list, [
                    'ability_join_item_id' => $join_item_id,
                    'retry_no' => $retry_no,
                    'result_id' => $result_id,
                    'sample_time' => $currentNow
                ]);

                // 记录报名日志
                $logContent = '取样操作：' . json_encode($sample_list);
                AbilityJoinLogsDBBusiness::saveAbilityJoinLog($operateInfo['admin_type'], $operate_staff_id, $id, $join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);
                // 获得已有的样品编号
//                $queryParams = [
//                    'where' => [
//                        ['ability_join_item_id', $join_item_id],
//                        ['retry_no', $retry_no],
//                        ['result_id',$result_id],
//                    ],
//                    // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                ];
                $queryParams = Tool::getParamQuery(['ability_join_item_id' => $join_item_id, 'retry_no' => $retry_no, 'result_id' => $result_id], [], []);
                $joinSampleDataListObj = AbilityJoinItemsSamplesDBBusiness::getAllList($queryParams, []);
                $joinSampleListArr = $joinSampleDataListObj->toArray();
                $joinSampleIds = [];
                if(!empty($joinSampleListArr)) $joinSampleIds = array_values(array_unique(array_column($joinSampleListArr,'id')));

                foreach($sample_list as $sample_info){
                    $updateFields = array_merge($sample_info,[
                        'sample_time' => $currentNow
                    ], $appendArr);
                    $searchConditon = [
                        'ability_join_item_id' => $join_item_id,
                        'retry_no' => $retry_no,
                        'result_id' => $result_id,
                        'serial_number' => $sample_info['serial_number'],
                        // 'sample_one' => $sample_info['sample_one'],
                    ];
                    $sampleObj = null;
                    AbilityJoinItemsSamplesDBBusiness::updateOrCreate($sampleObj, $searchConditon, $updateFields );
                    $temSampleId = $sampleObj->id;// 移除当前的id
                    if(!empty($joinSampleIds)){
                        $recordUncode = array_search($temSampleId, $joinSampleIds);
                        if($recordUncode !== false) unset($joinSampleIds[$recordUncode]);// 存在，则移除
                    }

                }
                // 删除多余的
                if(!empty($joinSampleIds)) {// 是修改 且不为空
                    // 删除记录
                    AbilityJoinItemsSamplesDBBusiness::deleteByIds($joinSampleIds);
                }

            }
            // ************************************************************
            // 保存成功后的处理
            static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            return $id;
        });
    }
}
