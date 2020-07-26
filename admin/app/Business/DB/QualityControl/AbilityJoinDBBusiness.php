<?php
// 能力验证报名主表
namespace App\Business\DB\QualityControl;

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
        $ability_join_items = [];
        $has_join_item = false;// 是否有能力验证报名项修改 false:没有 ； true:有
        if(isset($saveData['ability_join_items'])){
            $ability_join_items = $saveData['ability_join_items'];
            unset($saveData['ability_join_items']);
            $has_join_item = true;
        }

        $currentNow = Carbon::now()->toDateTimeString();

        $modelObj = null;
        DB::beginTransaction();
        try {
            $isModify = false;

<<<<<<< HEAD
            $ability_code = '';
            if($id <= 0){
=======
            $ability_code = $saveData['ability_code'] ?? '';
            // 没有单号，则重新生成
            if(empty($ability_code)){// $id <= 0
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
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
<<<<<<< HEAD
=======
                    'join_year' => Carbon::now()->year,
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
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

            // 如果有能力验证报名项 修改
            if($has_join_item){
                $joinItemsListArr = [];
                $joinItemsIds = [];
                if($isModify){// 是修改
                    // 获得所有的方法标准
                    $queryParams = [
                        'where' => [
//                ['company_id', $organize_id],
                            ['ability_join_id', $id],
//                ['teacher_status',1],
                        ],
                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                    ];
                    $joinItemsDataListObj = AbilityJoinItemsDBBusiness::getAllList($queryParams, []);
                    $joinItemsListArr = $joinItemsDataListObj->toArray();
                    if(!empty($joinItemsListArr)) $joinItemsIds = array_values(array_unique(array_column($joinItemsListArr,'id')));
                }

                if(!empty($ability_join_items)){
                    $appendArr = [
                        'operate_staff_id' => $operate_staff_id,
                        'operate_staff_id_history' => $operate_staff_id_history,
                    ];
                    // 新加时
<<<<<<< HEAD
                    if(!$isModify){
                        $appendArr = array_merge($appendArr, [
                            'ability_join_id' => $id,
                            'ability_code' => $ability_code,
                            'join_time' => $currentNow,
                        ]);
                    }
=======
//                    if(!$isModify){
//                        $appendArr = array_merge($appendArr, [
//                            'ability_join_id' => $id,
//                            'ability_code' => $ability_code,
//                            'join_time' => $currentNow,
//                        ]);
//                    }
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
                    // Tool::arrAppendKeys($ability_join_items, $appendArr);
                    foreach($ability_join_items as $k => $join_item_info){
                        $join_item_id = $join_item_info['id'] ?? 0;
                        if(isset($join_item_info['id'])) unset($join_item_info['id']);
<<<<<<< HEAD
                        Tool::arrAppendKeys($join_item_info, $appendArr);
=======

                        Tool::arrAppendKeys($join_item_info, $appendArr);
                        if($join_item_id <= 0 ) Tool::arrAppendKeys($join_item_info, [
                            'ability_join_id' => $id,
                            'ability_code' => $ability_code,
                            'join_time' => $currentNow,
                        ]);
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
                        AbilityJoinItemsDBBusiness::replaceById($join_item_info, $company_id, $join_item_id, $operate_staff_id, $modifAddOprate);
                        // 移除当前的id
                        $recordUncode = array_search($join_item_id, $joinItemsIds);
                        if($recordUncode !== false) unset($joinItemsIds[$recordUncode]);// 存在，则移除

                    }
                }
                if($isModify && !empty($joinItemsIds)) {// 是修改 且不为空
                    // 删除记录
                    // AbilityJoinItemsDBBusiness::deleteByIds($joinItemsIds);
                    AbilityJoinItemsDBBusiness::delById($company_id, $joinItemsIds, $operate_staff_id, $modifAddOprate);
                }
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

}
