<?php
// 行业[一级分类]
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBIndustryBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Industry';
    public static $table_name = 'industry';// 表名称

}
