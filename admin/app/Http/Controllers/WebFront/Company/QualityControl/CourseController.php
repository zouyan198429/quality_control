<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Resources\CourseBusinessResource;
use App\Services\Tool;
use Arr;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends BasicController
{
    public static $ADMIN_TYPE = 4;// 类型1平台2企业4个人

    public $controller_id = 0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $view = $this->view(__FUNCTION__);
        return Tool::doViewPages($this, $request,
            function () use($view, $request) {
                return view($view);
            }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 填写报名信息
     *
     * @param Request $request
     * @param $course_id
     * @return mixed
     */
    public function form(Request $request, $course_id)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        $view = $this->view(__FUNCTION__);
        return Tool::doViewPages($this, $request,
            function () use($view, $request, $course_id) {
                $info = CTAPICourseBusiness::getInfoData($request, $this, $course_id);
                return view($view, compact('info'));
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 报名
     *
     * @param Request $request
     * @param $class_id
     * @return mixed
     */
    public function signUp(Request $request)
    {
        return $this->exeDoPublicFun($request, 4, 4, '', true, '', [],
            function () use ($request) {
                $id = 0;
                $data = $request->all();
                $course = CTAPICourseBusiness::getInfoData($request, $this, (int) $data['course_id']);
                $staffs_id = explode(',', $data['ids']);
                $company_grade = $this->getUserInfo()['company_grade'];
                if ($company_grade === 1) {
                    $price = $course['price_general'];
                } else {
                    $price = $course['price_member'];
                }
                $course_order_item = [
                    'tel'        => $data['tel'],
                    'price'      => $price,
                    'join_num'   => count($staffs_id),
                    'contacts'   => $data['contacts'],
                    'course_id'  => $data['course_id'],
                    'order_date' => now()->toDateString(),
                    'company_id' => $this->getUserInfo()['id'],
                ];
                $course_order = CTAPICourseOrderBusiness::replaceById($request, $this, $course_order_item, $id);

                // Create course order.
                $course_order_staff_item = [];
                foreach ($staffs_id as $staff_key => $staff_id) {
                    $course_order_staff_item[$staff_key] = [
                        'staff_id'        => $staff_id,
                        'course_id'       => $data['course_id'],
                        'company_id'      => $this->getUserInfo()['id'],
                        'course_order_id' => $course_order,
                    ];
                }
                $resultData = CTAPICourseOrderStaffBusiness::createBathByPrimaryKeyCTL($request, $this, null, $course_order_staff_item);
                return ajaxDataArr(1, $resultData, '');
            });
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request){
        $request->company_id = $this->getUserInfo()['id'];
        $extParams = [
            'relationFormatConfigs'=> CTAPICourseBusiness::getRelationConfigs($request, $this, ['resource_list']),
        ];
        $course_item = CTAPICourseBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
        $resource = CourseBusinessResource::collection($course_item['result']['data_list']);
        $course_item['result']['data_list'] = $resource;
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [],
            function () use ($course_item) {
                return $course_item;
        });
    }

    /**
     * 员工列表
     * @param Request $request
     * @return mixed
     */
    public function staffList(Request $request) {
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [],
            function (&$reDataArr) use ($request) {
                // $this->company_id = 1;
                $mergeParams = [
                    'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
                ];
                // 企业 的 个人--只能读自己的人员信息
                if($this->user_type == 2 && static::$ADMIN_TYPE == 4) {
                    $mergeParams['company_id'] = $this->own_organize_id;
                }
                CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

                $relations = [];//  ['siteResources']
                $handleKeyArr = [];
                $handleKeyConfigArr = [];
                if(static::$ADMIN_TYPE == 2){
                    array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
                    array_push($handleKeyConfigArr, 'industry_info');
                }
                if(in_array(static::$ADMIN_TYPE, [2, 4])){
                    $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
                    $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
                }
                if(static::$ADMIN_TYPE == 4){
                    array_push($handleKeyArr, 'company');
                    array_push($handleKeyConfigArr, 'company_info');
                }

                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
                ];
                return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
            });
    }
}
