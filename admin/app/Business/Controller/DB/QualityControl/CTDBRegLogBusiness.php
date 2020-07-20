<?php
// 注册记录
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBRegLogBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\RegLog';
    public static $table_name = 'reg_log';// 表名称

}
