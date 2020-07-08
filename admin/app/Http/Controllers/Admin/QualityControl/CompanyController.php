<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyController extends StaffController
{
    public static $ADMIN_TYPE = 2;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'Company';// 视图文件夹名称

}
