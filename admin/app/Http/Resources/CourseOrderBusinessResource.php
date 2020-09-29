<?php

namespace App\Http\Resources;

use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Http\Controllers\WebFront\Company\QualityControl\CourseOrderController;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOrderBusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $order_staff = array_first($this['staff']);
//        dd($this);
        if ($order_staff && $order_staff['class_id']) {
            $controller = new CourseOrderController();
            $class = CTAPICourseClassBusiness::getInfoData($request, $controller, $order_staff['class_id']);
        }
        return [
            'company'    => $this['company']['company_name'],
            'join_num'   => $this['join_num'],
            'contacts'   => $this['contacts'],
            'tel'        => $this['tel'],
            'class'      => $class['class_name'] ?? null,
            'pay_status' => $this['pay_status_text'],
            'order_date' => $this['order_date'],
        ];
    }
}
