<?php
// 项目标准
namespace App\Business\DB\QualityControl;

/**
 *
 */
class ProjectStandardsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\ProjectStandards';
    public static $table_name = 'project_standards';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
