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
//                dd($reDataArr);
                return $reDataArr;
            });
    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function create(Request $request, $id = 0)
    {
        $pageNum = ($id > 0) ? 64 : 16;
        $view = $this->view(__FUNCTION__);
        return $this->exeDoPublicFun($request, $pageNum, 1, $view, true, 'doInfoPage', ['id' => $id]);
    }

    /**
     * 保存数据
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [],
            function () use ($request, $id){
                $data = $request->all();
                $course_item = [
                    'city_id'    => (int) $data['city_id'],
                    'class_name' => $data['class_name'],
                    'remarks'    => $data['remarks'],
                ];
                $resultData = CTAPICourseClassBusiness::replaceById($request, $this, $course_item, $id, [], true);
                return ajaxDataArr(1, $resultData);
            });
    }

    /**
     * 返回列表数据
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request)
    {
        return CTAPICourseClassBusiness::getList($request, $this, 2 + 4, [], ['city']);
    }
}
