<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 报名学员.
 * Class CourseSignUpStaffController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseSignUpStaffController extends BasicController
{

    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人

    /**
     * 主页
     * @param Request $request
     * @param $class_id
     * @return View
     */
    public function index(Request $request): View
    {
        return $this->exeDoPublicFun($request, 1, 1, $this->view(__FUNCTION__),
            true, '', [], function (&$reDataArr) use ($request)
            {
                $class_item = CTAPICourseClassBusiness::getList($request, $this)['result']['data_list'];
                $reDataArr['classes'] = collect($class_item)->pluck('class_name','id');
                return $reDataArr;
            });
    }

    /**
     * 分配班级
     * @param Request $request
     * @param $class_id
     * @return View
     */
    public function assignClass(Request $request): View
    {
        return $this->exeDoPublicFun($request, 1, 1, $this->view(__FUNCTION__));
    }

    /**
     * 返回列表数据
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request)
    {
//        CTAPICourseOrderStaffBusiness::mergeRequest($request, $this);
//        dd($request);
        $item = CTAPICourseOrderStaffBusiness::getList($request, $this, 2 + 4, [], ['staff', 'company', 'class', 'order']);
        return $item;
    }

    /**
     * 返回列表数据
     * @param Request $request
     * @return mixed
     */
    public function classList(Request $request)
    {
        return CTAPICourseClassBusiness::getList($request, $this, 2 + 4, [], ['city']);
    }
}
