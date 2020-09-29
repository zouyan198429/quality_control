<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Resources\CourseOrderBusinessResource;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseOrderController extends BasicController
{
    public static $ADMIN_TYPE = 4;// 类型1平台2企业4个人

    public $controller_id = 0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     * @return View
     */
    public function index(): View
    {
        return view($this->view(__FUNCTION__));
    }

    /**
     * 报名信息显示
     *
     * @param Request $request
     * @param $course_id
     * @return mixed
     */
    public function show(Request $request, $course_id)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        $view = $this->view(__FUNCTION__);
        return Tool::doViewPages($this, $request,
            function () use($view, $request, &$course_id) {
                $info = CTAPICourseOrderBusiness::getInfoData($request, $this, $course_id, [], ['course', 'staff']);
                $info['staff'] = CourseOrderBusinessResource::collection($info['staff']);
                return view($view, compact('info'));
            }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     */
    public function ajaxList(Request $request){
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [],
            function () use ($request){
                $extParams = [
                    'relationFormatConfigs'=> CTAPICourseOrderBusiness::getRelationConfigs($request, $this, ['course']),
                ];
                return CTAPICourseOrderBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
        });
    }

    /**
     * 员工列表
     * @param Request $request
     * @param $course_id
     * @return mixed
     */
    public function staffList(Request $request, $course_id = 0) {
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [],
            function (&$reDataArr) use ($request, $course_id) {
                // $this->company_id = 1;
                $mergeParams = [
                    'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
                ];
                // 企业 的 个人--只能读自己的人员信息
                if($this->user_type == 2 && static::$ADMIN_TYPE == 4) {
                    $mergeParams['company_id'] = $this->own_organize_id;
                }
                CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

                $relations = ['staff'];//  ['siteResources']
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
                $query_condition = ['where' => ['course_order_id' => $course_id]];
                return CTAPICourseOrderStaffBusiness::getList($request, $this, 2 + 4, $query_condition, $relations, $extParams);
            });
    }
}
