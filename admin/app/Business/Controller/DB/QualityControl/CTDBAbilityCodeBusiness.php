<?php
// 能力验证代码
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBAbilityCodeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\AbilityCode';
    public static $table_name = 'ability_code';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
