<?php
// 报名学员
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CourseOrderStaffDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CourseOrderStaff';
    public static $table_name = 'course_order_staff';// 表名称
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
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate,
            &$operate_staff_id_history, &$modelObj, &$isModify){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                $tem_company_id = $saveData['company_id'];
                $saveData['company_id_history'] = static::getStaffHistoryId($tem_company_id);;
            }
            if(isset($saveData['staff_id']) && is_numeric($saveData['staff_id']) && $saveData['staff_id'] > 0){
                $tem_staff_id = $saveData['staff_id'];
                $saveData['staff_id_history'] = static::getStaffHistoryId($tem_staff_id);;
            }
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


    /**
     * 根据id作废或取消作废单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $staff_status 操作 状态 1正常--取消作废操作； 4已作废--作废操作
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function staffStatusById($company_id, $organize_id = 0, $id = 0, $staff_status = 1, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($staff_status, [1, 4])) throws('参数【staff_status】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        $updateData = [
            'staff_status' => $staff_status
        ];
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;

//            $saveQueryParams = [
//                'where' => [
//                   //  ['staff_status', 1],
//                ],
////                            'select' => [
////                                'id','title','sort_num','volume'
////                                ,'operate_staff_id','operate_staff_id_history'
////                                ,'created_at' ,'updated_at'
////                            ],
//
//                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
//            ];
        $saveQueryParams = [];
        $oldStaffStatus = 1;// -- 可以作废状态
        // 取消作废操作
        if($staff_status == 1) $oldStaffStatus = 4; // --可以取消作废状态
        Tool::appendParamQuery($saveQueryParams, $oldStaffStatus, 'staff_status');
        // 加入 id
        Tool::appendParamQuery($saveQueryParams, $id, 'id');
        Tool::appendParamQuery($saveQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
        // pr($saveQueryParams);
        // 查询到要操作的记录
        $dataList = static::getAllList($saveQueryParams, [])->toArray();
        if(empty($dataList))  return $modifyNum;// 没有要操作的记录，便不进行操作了
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$staff_status, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$updateData, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$saveQueryParams, &$dataList){
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);
            foreach($dataList as $info){
                $courseId = $info['course_id'];
                  $courseOrderId = $info['course_order_id'];
                if($staff_status == 1) {// 取消作废
                    // 课程表
                    $queryParams = Tool::getParamQuery(['id' => $courseId], [], []);
                    CourseDBBusiness::saveDecIncByQuery('wait_class_num', 1,  'inc', $queryParams, []);
                    CourseDBBusiness::saveDecIncByQuery('cancel_num', 1,  'dec', $queryParams, []);
                    // 报名主表
                    $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                    CourseOrderDBBusiness::saveDecIncByQuery('cancel_num', 1,  'dec', $queryParams, []);
                }else{// 作废
                    // 课程表
                    $queryParams = Tool::getParamQuery(['id' => $courseId], [], []);
                    CourseDBBusiness::saveDecIncByQuery('wait_class_num', 1,  'dec', $queryParams, []);
                    CourseDBBusiness::saveDecIncByQuery('cancel_num', 1,  'inc', $queryParams, []);
                    // 报名主表
                    $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                    CourseOrderDBBusiness::saveDecIncByQuery('cancel_num', 1,  'inc', $queryParams, []);

                }
            }
            $modifyNum = static::save($updateData, $saveQueryParams);
//            DB::commit();
        });
        return $modifyNum;
    }
}
