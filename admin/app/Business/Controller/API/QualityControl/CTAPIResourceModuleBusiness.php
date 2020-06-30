<?php
// 资源模块使用表
namespace App\Business\Controller\API\QualityControl;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIResourceModuleBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\ResourceModuleAPI';
    public static $table_name = 'resource_module';// 表名称

}
