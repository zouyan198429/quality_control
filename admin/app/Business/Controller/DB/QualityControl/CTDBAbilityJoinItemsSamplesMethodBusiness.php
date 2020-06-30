<?php
// 能力验证检测方法依据
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityJoinItemsSamplesMethodBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsSamplesMethod';
    public static $table_name = 'ability_join_items_samples_method';// 表名称

}
