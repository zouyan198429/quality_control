<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class UserController extends StaffController
{
    public static $ADMIN_TYPE = 4;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'User';// 视图文件夹名称

}
