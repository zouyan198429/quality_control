<?php
// 资源分类自定义[一级分类]
namespace App\Business\Controller\API\QualityControl;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIResourceTypeSelfBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\ResourceTypeSelfAPI';
    public static $table_name = 'resource_type_self';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
