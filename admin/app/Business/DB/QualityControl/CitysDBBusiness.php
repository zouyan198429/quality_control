<?php
// 城市[一级分类]
namespace App\Business\DB\QualityControl;

/**
 *
 */
class CitysDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Citys';
    public static $table_name = 'citys';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
