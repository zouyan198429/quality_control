<?php
// 项目标准
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBProjectStandardsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\ProjectStandards';
    public static $table_name = 'project_standards';// 表名称

}
