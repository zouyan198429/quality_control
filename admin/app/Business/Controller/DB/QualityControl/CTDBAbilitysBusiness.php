<?php
// 能力验证
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilitysBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Abilitys';
    public static $table_name = 'abilitys';// 表名称

}
