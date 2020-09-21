<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseContentBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Business\Controller\DB\QualityControl\CTDBCourseContentBusiness;
use App\Services\DB\CommonDB;
use App\Services\Request\CommonRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CourseClassController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseClassController extends BasicController
{

    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人

    /**
     * 培训班列表页
     * @return View
     */
    public function index(): View
    {
        return view($this->view(__FUNCTION__));
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

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []) {
        $id = $extendParams['params']['id'] ?? 0;
        if ($id > 0) {
            $course_class = CTAPICourseClassBusiness::getInfoData($request, $this, $id);
        } else {
            $course_class = [];
        }
        $reDataArr = [
            'id'          => $id,
            'info'        => $course_class,
            'cities'      => CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']),
            'role_num'    => 0,
            'defaultCity' => $course_class['city_id'] ?? -1,
        ];
        return;
    }
}
