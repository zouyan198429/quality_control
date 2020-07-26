<?php
// 登录验证码
namespace App\Business\DB\QualityControl;

/**
 *
 */
class SmsCodeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\SmsCode';
    public static $table_name = 'sms_code';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
