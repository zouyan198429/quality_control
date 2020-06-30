<?php

namespace App\Services\Request\API\Sites;

use App\Services\Request\API\APIBasicRequest;

/**
 *具体业务通用接口类--请求api--公有接口
 */
class APIQualityControlRequest extends APIBasicRequest // 如果是自己的数据库系统，可以继承此公用方法
{
    //  配置文件 public.apiRequestOptions 下标 ：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
    public static $optionsConfigKey = 'APIQualityControlOptions';
    public static $request_mode = 2;// 数据请求方式， 1 通过API获得数据； 2 访问本地数据库

    /**
     * 获得请求地址-- 子类必须重写此方法
     *
     * @return string 接口地址
     * @author zouyan(305463219@qq.com)
     */
    public static function getUrl(){
        return config('public.apiUrl');
    }

    /**
     * 需要单独处理的，请继承重写此方法
     * 对请求options参数进行一些格式化处理
     *
     * @return array options参数等
     * @author zouyan(305463219@qq.com)
     */
    public static function  resolveConfigOptions(&$sitesOptions){
        return $sitesOptions;
    }
}
