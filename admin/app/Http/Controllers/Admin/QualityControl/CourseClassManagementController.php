<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 培训班管理.
 * Class CourseClassManagementController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseClassManagementController extends BasicController
{

    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人

    /**
     * 主页
     * @param Request $request
     * @param $class_id
     * @return View
     */
    public function index(Request $request, $class_id): View
    {
        return $this->exeDoPublicFun($request, 1, 1, $this->view(__FUNCTION__),
            true, '', [], function (&$reDataArr) use ($request, $class_id)
            {
                $reDataArr['info'] = CTAPICourseClassBusiness::getInfoData($request, $this, $class_id, [], ['city']);
                return $reDataArr;
            });
    }

    /**
     * 基础信息
     * @param Request $request
     * @param $class_id
     * @return View
     */
    public function basic(Request $request, $class_id): View
    {
        return $this->exeDoPublicFun($request, 1, 1, $this->view(__FUNCTION__),
            true, '', [], function (&$reDataArr) use ($request, $class_id)
            {
                $reDataArr['info'] = CTAPICourseClassBusiness::getInfoData($request, $this, $class_id, [], ['city']);
                return $reDataArr;
            });
    }
}
