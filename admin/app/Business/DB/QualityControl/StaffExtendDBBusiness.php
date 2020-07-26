<?php
// 人员扩展信息
namespace App\Business\DB\QualityControl;

/**
 *
 */
class StaffExtendDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\StaffExtend';
    public static $table_name = 'staff_extend';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
