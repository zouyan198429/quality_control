<?php
// 老师登录验证码
namespace App\Business\API\QualityControl;


class SmsCodeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\SmsCode';
    public static $table_name = 'sms_code';// 表名称
}
