<?php

namespace App\Http\Resources;

use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Http\Controllers\WebFront\Company\QualityControl\CourseController;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseBusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $controller = new CourseController();
        $query_condition = ['where' => [
            'course_id' => $this['id'],
            'company_id' => $request->company_id,
        ]];
        $course_order = CTAPICourseOrderBusiness::getList($request, $controller, 2 + 4, $query_condition);
        $order_item = empty($course_order['result']['data_list']);
        if ($order_item) {
            $sign_up_status = '未报名';
        } else {
            $sign_up_status = '已经报名';
        }
        return [
            'id'                 => $this['id'],
            'resource'           => array_first($this['resource_list'])['resource_url_format'],
            'course_name'        => $this['course_name'],
            'sign_up_status'     => $sign_up_status,
            'status_online_text' => $this['status_online_text'],
        ];
    }
}
