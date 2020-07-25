<?php
// 能力验证报名项表
namespace App\Business\DB\QualityControl;

use App\Services\Tool;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AbilityJoinItemsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItems';
    public static $table_name = 'ability_join_items';// 表名称
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

        // 能力验证报名项-项目标准
        $ability_join_items_standards = [];
        $has_join_item_standards = false;// 是否有能力验证报名项-项目标准修改 false:没有 ； true:有
        if(isset($saveData['ability_join_items_standards'])){
            $ability_join_items_standards = $saveData['ability_join_items_standards'];
            unset($saveData['ability_join_items_standards']);
            $has_join_item_standards = true;
        }

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        DB::beginTransaction();
        try {
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

            // 如果有能力验证报名项-项目标准 修改
            if($has_join_item_standards){
                $joinItemsStandardListArr = [];
                $joinItemsStandardIds = [];
                if($isModify){// 是修改
                    // 获得所有的方法标准
                    $queryParams = [
                        'where' => [
//                ['company_id', $organize_id],
                            ['ability_join_item_id', $id],
//                ['teacher_status',1],
                        ],
                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                    ];
                    $joinItemsStandardDataListObj = AbilityJoinItemsStandardsDBBusiness::getAllList($queryParams, []);
                    $joinItemsStandardListArr = $joinItemsStandardDataListObj->toArray();
                    if(!empty($joinItemsStandardListArr)) $joinItemsStandardIds = array_values(array_unique(array_column($joinItemsStandardListArr,'id')));
                }

                if(!empty($ability_join_items_standards)){
                    $appendArr = [
                        'operate_staff_id' => $operate_staff_id,
                        'operate_staff_id_history' => $operate_staff_id_history,
                    ];
                    // 新加时
                    if(!$isModify){
                        $appendArr = array_merge($appendArr, [
                            'ability_join_item_id' => $id,
                        ]);
                    }
                    // Tool::arrAppendKeys($ability_join_items, $appendArr);
                    foreach($ability_join_items_standards as $k => $join_item_standard_info){
                        $join_item_standard_id = $join_item_standard_info['id'] ?? 0;
                        if(isset($join_item_standard_info['id'])) unset($join_item_standard_info['id']);
                        Tool::arrAppendKeys($join_item_standard_info, $appendArr);
                        AbilityJoinItemsStandardsDBBusiness::replaceById($join_item_standard_info, $company_id, $join_item_standard_id, $operate_staff_id, $modifAddOprate);

                    }
                    // 移除当前的id
                    $recordUncode = array_search($join_item_standard_id, $joinItemsStandardIds);
                    if($recordUncode !== false) unset($joinItemsStandardIds[$recordUncode]);// 存在，则移除
                }
                if($isModify && !empty($joinItemsStandardIds)) {// 是修改 且不为空
                    // 删除记录
                    AbilityJoinItemsStandardsDBBusiness::deleteByIds($joinItemsStandardIds);
                }
            }
            // 如果是加，则增加报名数量
            if(!$isModify){
                $ability_id = $saveData['ability_id'] ?? 0;
                if($ability_id > 0){
                    $queryParams = [
                        'where' => [
                            ['id', $ability_id],
                        ],
                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                    ];
                    AbilitysDBBusiness::saveDecIncByQuery('join_num', 1,  'inc', $queryParams, []);
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

    /**
     * 根据id删除--可批量删除
     * 删除员工--还需要重新统计企业的员工数
     * 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(is_string($id) && strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        if(empty($id)){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $dataListArr = [];
        $abilityIds = [];

        // 获得需要删除的数据
        $queryParams = [
            'where' => [
//                ['company_id', $organize_id],
              //  ['admin_type', $admin_type],
//                ['teacher_status',1],
            ],
            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
        ];
        Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);

        $dataListObj = static::getAllList($queryParams, []);
        // $dataListObj = static::getListByIds($id);

        $dataListArr = $dataListObj->toArray();
        if(empty($dataListArr)) throws('没有需要删除的数据');
        // 用户删除要用到的
        $abilityIds = array_values(array_unique(array_column($dataListArr,'ability_id')));

        DB::beginTransaction();
        try {

            // 删除主记录
            static::del($queryParams);
            // static::deleteByIds($id);
            // 如果是删除，则减少报名数量
            foreach($abilityIds as $ability_id){
                if($ability_id > 0){
                    $queryParams = [
                        'where' => [
                            ['id', $ability_id],
                        ],
                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                    ];
                    AbilitysDBBusiness::saveDecIncByQuery('join_num', 1,  'dec', $queryParams, []);
                }
            }

        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

}
