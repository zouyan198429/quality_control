<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 报名企业.
 * Class CourseSignUpStaffController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseSignUpCompanyController extends BasicController
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
     * 返回列表数据
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request)
    {
        CTAPICourseOrderStaffBusiness::mergeRequest($request, $this);
        return CTAPICourseOrderBusiness::getList($request, $this, 2 + 4, [], ['company']);
    }

    /**
     * 更新订单
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        CTAPICourseOrderStaffBusiness::mergeRequest($request, $this);
        $order_id = (int) $request->order_id;
        $order_item = ['pay_status' => 2];
        $item = CTAPICourseOrderBusiness::replaceById($request, $this, $order_item, $order_id, ['company']);
        return $item;
    }
}
