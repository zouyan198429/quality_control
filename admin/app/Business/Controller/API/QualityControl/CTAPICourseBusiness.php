<?php
//课程管理
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\OrderPayConfig;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICourseBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CourseAPI';
    public static $table_name = 'course';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    /**
     * 特殊的验证 关键字 -单个 的具体验证----具体的子类----重写此方法来实现具体的验证
     *
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param string $key 验证规则的关键字 -单个
     * @param array $tableLangConfig 多语言单个数据表配置数组--也就是表多语言的那个配置数组
     * @param array $extParams 其它扩展参数，
     * @return  array 错误：非空数组；正确：空数组
     * @author zouyan(305463219@qq.com)
     */
    public static function singleJudgeDataByKey(&$mustFields = [], &$judgeData = [], $key = '', $tableLangConfig = [], $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $errMsgs = [];// 错误信息的数组--一维数组，可以指定下标
        // if( (is_string($key) && strlen($key) <= 0 ) || (is_array($key))) return $errMsgs;
        switch($key){
            case 'add':// 添加；

                break;
            case 'modify':// 修改
                break;
            case 'replace':// 新加或修改

                $id = $extParams['id'] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ****表关系***需要重写的方法**********开始***********************************
    /**
     * 获得处理关系表数据的配置信息--重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $relationKeys
     * @param array $extendParams  扩展参数---可能会用
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigs(Request $request, Controller $controller, $relationKeys = [], $extendParams = []){
        if(empty($relationKeys)) return [];
        list($relationKeys, $relationArr) = static::getRelationParams($relationKeys);// 重新修正关系参数
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;
        // 关系配置
        $relationFormatConfigs = [
            // 下标 'relationConfig' => []// 下一个关系
            // 获得企业名称
//            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
//                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
//                , 1, 2
//                ,'','',
//                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
//                    static::getUboundRelation($relationArr, 'company_info'),
//                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
//                ['where' => [['admin_type', 2]]], '', []),
            // 获得详细内容
            'course_content' => CTAPICourseContentBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'course_id']
                , 1, 2
                ,'','',
                CTAPICourseContentBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_content'),
                    static::getUboundRelationExtendParams($extendParams, 'course_content')),
                [], '', []),
            // 获得封面图
            'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['resource_id' => 'id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list')),
                [], '', ['extendConfig' => ['listHandleKeyArr' => ['format_resource'], 'infoHandleKeyArr' => ['resource_list']]]),

            // 获得报名企业报名项-- 企业报名的
            'course_order_company' => CTAPICourseOrderBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'course_id']
                , 2, 2
                ,'','',
                CTAPICourseOrderBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'course_order_company'),
                    static::getUboundRelationExtendParams($extendParams, 'course_order_company')),
                ['where' => [
                    ['admin_type', $user_info['admin_type']],
                    ['company_id', $user_info['id']]
                ]], '', ['extendConfig' => ['infoHandleKeyArr' => ['judgeJoined']]]),
            // 获得报名学员报名项-- 学员报名的--
            'course_order_staff' => CTAPICourseOrderStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'course_id']
                , 2, 2
                ,'','', [
                    // 获得班级名称
                    'class_name' => CTAPICourseClassBusiness::getTableRelationConfigInfo($request, $controller
                        , ['class_id' => 'id']
                        , 1, 2
                        ,'','', [], [], '', []),
                ]
                , [], '', []),
        ];
        return Tool::formatArrByKeys($relationFormatConfigs, $relationKeys, false);
    }

    /**
     * 获得要返回数据的return_data数据---每个对象，重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $return_num 指定要获得的关系数据类型格式化后的数据 编号 1[占用：原数据] 2 4 8..
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigReturnData(Request $request, Controller $controller, $return_num = 0){
        $return_data = [];// 为空，则会返回对应键=> 对应的数据， 具体的 结构可以参考 Tool::formatConfigRelationInfo  $return_data参数格式

        if(($return_num & 1) == 1) {// 返回源数据--特别的可以参考这个配置
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => [], 'ubound_type' =>1];
        }

        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
            $one_field = ['key' => 'course_name', 'return_type' => 2, 'ubound_name' => 'course_name', 'split' => '、'];// 获得名称
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

    /**
     * 获得列表数据时，查询条件的参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParams(Request $request, Controller $controller, &$queryParams, $notLog = 0){
        // 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

        $resource_id = CommonRequest::getInt($request, 'resource_id');
        if($resource_id > 0 )  array_push($queryParams['where'], ['resource_id', '=', $resource_id]);

        $status_online = CommonRequest::get($request, 'status_online');
        if(strlen($status_online) > 0 && $status_online != 0)  Tool::appendParamQuery($queryParams, $status_online, 'status_online', [0, '0', ''], ',', false);

        $pay_config_id = CommonRequest::get($request, 'pay_config_id');
        if(strlen($pay_config_id) > 0 && $pay_config_id != 0)  Tool::appendParamQuery($queryParams, $pay_config_id, 'pay_config_id', [0, '0', ''], ',', false);

        $pay_method = CommonRequest::get($request, 'pay_method');
        if(strlen($pay_method) > 0 && $pay_method != 0)  Tool::appendCondition($queryParams, 'pay_method',  $pay_method . '=' . $pay_method, '&');

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 对单条数据关系进行格式化--具体的可以重写
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info  单条数据  --- 一维数组
     * @param array $temDataList  关系数据  --- 一维或二维数组 -- 主要操作这个数据到  $info 的特别业务数据
     *                              如果是二维数组：下标已经是他们关系字段的值，多个则用_分隔好的
     * @param array $infoHandleKeyArr 其它扩展参数，// 一维数组，单条 数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function infoRelationFormatExtend(Request $request, Controller $controller, &$info, &$temDataList, $infoHandleKeyArr, &$returnFields){
        // if(empty($info)) return $returnFields;
        // $returnFields[$tem_ubound_old] = $tem_ubound_old;
        if(in_array('resetPayMethod', $infoHandleKeyArr)){

            // 支付方式-实时处理
            OrderPayConfig::unionPayMethod($info, 'pay_method','pay_config_id');
            OrderPayConfig::getPayMethodText($info, 'pay_method');
//            $info['resource_list'] = $resource_list;
//            $returnFields['resource_list'] = 'resource_list';
        }

        return $returnFields;
    }
}
