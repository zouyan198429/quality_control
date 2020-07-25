<?php
// 人员
namespace App\Business\DB\QualityControl;

/**
 *
 */
class StaffHistoryDBBusiness extends StaffDBBusiness
{
    public static $model_name = 'QualityControl\StaffHistory';
    public static $table_name = 'staff_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
