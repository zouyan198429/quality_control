<?php
// 能力验证操作日志
namespace App\Business\DB\QualityControl;

use Carbon\Carbon;

/**
 *
 */
class AbilityJoinLogsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinLogs';
    public static $table_name = 'ability_join_logs';// 表名称

//    public static function aaa(){

        // $currentNow = Carbon::now();
        // $currentNow->toDateString()
        // 工单操作日志
//        $Record = [
//            'admin_type' => $staff_id,
//            'staff_id' => $on_line,
//            'ability_join_id' => $on_line,
//            'ability_join_item_id' => $on_line,
//            'content' => $logContent, // 操作内容
//            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id,
//            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,
//        ];
//        static::create($Record);
//    }
}
