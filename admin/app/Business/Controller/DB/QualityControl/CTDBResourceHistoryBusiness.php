<?php
// 资源历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBResourceHistoryBusiness extends CTDBResourceBusiness
{
    public static $model_name = 'QualityControl\ResourceHistory';
    public static $table_name = 'resource_history';// 表名称

}
