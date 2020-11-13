<?php

namespace App\Http\Controllers\Admin\QualityControl\API;

use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Http\Controllers\Admin\QualityControl\BasicController as ParentBasicController;
use Illuminate\Http\Request;

class BasicController extends ParentBasicController
{
    public static $ALLOW_BROWSER_OPEN = true;// 微信内支付：调试用开关，true:所有浏览器都能开； false:只有微信内浏览器
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    // 应用的 AppKey：公匙 =>  http://coolaf.com/tool/rd
    public static $appConfig = [
        '1' => [
            'appId' => 'tJEdrjOU7RvD9UXZ',// AppID：应用的唯一标识
            // 'appKey' => '',// AppKey：公匙（相当于账号） API公钥
            'appSecret' => '06DpogbTRxis1Q1YBNHJfyhKWNYCERW7',//  AppSecret：私匙（相当于密码）API密钥
        ]
    ];

    // 根据 appid 获得应用的密钥
    public static function getAppInfo($appId){
        $appConfigInfo = [];
        foreach(static::$appConfig as $tConfig){
            if($tConfig['appId'] == $appId) {
                $appConfigInfo = $tConfig;
                break;
            }
        }
        if(empty($appConfigInfo)){

        }
        return $appConfigInfo;
    }

}
