<?php
// 能力验证报名项表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinItemsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItems';
    public static $table_name = 'ability_join_items';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
