<?php
// 资源分类自定义历史[一级分类]
namespace App\Business\DB\QualityControl;

/**
 *
 */
class ResourceTypeSelfHistoryDBBusiness extends ResourceTypeSelfDBBusiness
{
    public static $model_name = 'QualityControl\ResourceTypeSelfHistory';
    public static $table_name = 'resource_type_self_history';// 表名称
}
