<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Models\QualityControl\Course;
use App\Models\QualityControl\CourseContent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class CourseController
 * @package App\Http\Controllers\Admin\QualityControl
 */
class CourseController extends BasicController
{

    /**
     * 返回状态成功
     */
    public const STATUS_SUCCESS = 1;

    /**
     * 返回状态错误
     */
    public const STATUS_ERROR = 0;

    /**
     * 视图命名空间
     * @var string Namespace.
     */
    public $view_namespace = 'admin.QualityControl.Course.';

    /**
     * 课程列表页
     * @return View
     */
    public function index(): View
    {
        return view($this->view_namespace . 'index');
    }

    /**
     * 课程创建页面
     * @return View
     */
    public function create(): View
    {
        return view($this->view_namespace . 'create');
    }

    /**
     * 保存数据
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $data = $request->all();
        $course_item = [
            'course_name'     => $data['course_name'],
            'resource_id'     => (int) $data['resource_id'],
            'resource_ids'    => $data['resource_id'],
            'price_member'    => $data['price_member'],
            'price_general'   => $data['price_general'],
            'status_online'   => $data['status_online'],
            'explain_remarks' => $data['explain_remarks'],
        ];
        $course_content = ['course_content' => $data['course_content']];
        DB::beginTransaction();
        try {
            $course = Course::create($course_item);
            $course->courseContent()->create($course_content);
            DB::commit();
            return $this->msgOut();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        DB::rollBack();
        return $this->msgOut(null, self::STATUS_ERROR);
    }

    /**
     * 返回列表数据
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxList(Request $request): JsonResponse
    {
        $data = CTAPICourseBusiness::getList($request, $this);
        return response()->json($data);
    }

    /**
     * 输出结果
     * @param Collection|array $data
     * @param int $status
     * @return JsonResponse
     */
    public function msgOut($data = [], int $status = self::STATUS_SUCCESS): JsonResponse
    {
        return response()->json(ajaxDataArr($status, $data));
    }
}
