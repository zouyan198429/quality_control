<?php
// 能力验证检测所用仪器
namespace App\Business\DB\QualityControl;

/**
 *
 */
class AbilityJoinItemsResultsInstrumentDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResultsInstrument';
    public static $table_name = 'ability_join_items_results_instrument';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
