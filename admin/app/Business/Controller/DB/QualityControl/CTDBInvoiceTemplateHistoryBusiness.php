<?php
// 发票开票模板历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceTemplateHistoryBusiness extends CTDBInvoiceTemplateBusiness
{
    public static $model_name = 'QualityControl\InvoiceTemplateHistory';
    public static $table_name = 'invoice_template_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
