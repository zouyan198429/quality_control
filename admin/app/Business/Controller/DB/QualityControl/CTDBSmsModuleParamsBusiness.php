<?php
// 短信模板所属模块参数
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBSmsModuleParamsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\SmsModuleParams';
    public static $table_name = 'sms_module_params';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
