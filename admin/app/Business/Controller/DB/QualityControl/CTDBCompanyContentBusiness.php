<?php
// 企业内容管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyContentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyContent';
    public static $table_name = 'company_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
