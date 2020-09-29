<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Http\Resources\CourseOrderBusinessResource;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 报名单位.
 * Class CourseClassManagementController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseOrderController extends BasicController
{

    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人

    /**
     * 报名单位
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
     * 返回列表数据
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request)
    {
        $item = CTAPICourseOrderBusiness::getList($request, $this, 2 + 4, [], ['staff', 'company']);
        $item['result']['data_list'] = CourseOrderBusinessResource::collection($item['result']['data_list']);
        return $item;
    }
}
