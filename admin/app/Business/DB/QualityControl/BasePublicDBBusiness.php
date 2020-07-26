<?php

namespace App\Business\DB\QualityControl;

use App\Business\DB\BaseDBBusiness;
use App\Services\Tool;


/**
 *
 */
class BasePublicDBBusiness extends BaseDBBusiness
{
    public static $database_model_dir_name = 'QualityControl';// 对应的数据库模型目录名称
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
