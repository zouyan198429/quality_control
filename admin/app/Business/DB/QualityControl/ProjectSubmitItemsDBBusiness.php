<?php
// 验证数据项
namespace App\Business\DB\QualityControl;

/**
 *
 */
class ProjectSubmitItemsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\ProjectSubmitItems';
    public static $table_name = 'project_submit_items';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
