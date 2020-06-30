<?php
// 能力验证检测所用仪器
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinItemsSamplesInstrumentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsSamplesInstrument';
    public static $table_name = 'ability_join_items_samples_instrument';// 表名称

}
