<?php
// 能力验证检测标准物质
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinItemsSamplesStandardBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsSamplesStandard';
    public static $table_name = 'ability_join_items_samples_standard';// 表名称

}
