<?php
// 能力验证单次结果
namespace App\Business\DB\QualityControl;

/**
 *
 */
class AbilityJoinItemsResultsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResults';
    public static $table_name = 'ability_join_items_results';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
