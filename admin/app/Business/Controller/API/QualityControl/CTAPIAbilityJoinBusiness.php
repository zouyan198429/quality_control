<?php
//能力验证报名主表
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIAbilityJoinBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilityJoinAPI';
    public static $table_name = 'ability_join';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

<<<<<<< HEAD

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
         $user_info = $controller->user_info;
         $user_id = $controller->user_id;
         $user_type = $controller->user_type;
             // 关系配置
        $relationFormatConfigs = [
            // 获得企业名称 1:1
            'company_info' => CTAPIStaffBusiness::getPrimaryRelationConfigVal($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1, ['one_field' =>['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'],]// 企业名称
                ,[], ['where' => [['admin_type', 2]]], '', []),
            // 报名项目 1:n
            'join_items' => CTAPIAbilityJoinItemsBusiness::getPrimaryRelationConfigVal($request, $controller
                , ['id' => 'ability_join_id']
                , 2
                , [
                   //  'one_field' =>['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、']
                  ]
                ,[
                     // 下一级关系 报名项所属的项目 -- 的名称 1:1
                    'ability_info' => CTAPIAbilitysBusiness::getPrimaryRelationConfigVal($request, $controller
                        , ['ability_id' => 'id']
                        , 1, ['one_field' =>['key' => 'ability_name', 'return_type' => 2, 'ubound_name' => 'ability_name', 'split' => '、'],]// 项目名称  测试4
                        ,[], [], '', []),
                    // 下一级关系的  能力验证报名项-项目标准 1:n
                    'join_item_standards' => CTAPIAbilityJoinItemsStandardsBusiness::getPrimaryRelationConfigVal($request, $controller
                        , ['id' => 'ability_join_item_id']
                        , 2
                        , [
                            'old_data' => ['ubound_operate' => 1, 'ubound_name' => '','fields_arr' => [], 'ubound_keys' => ['project_standard_id'], 'ubound_type' =>1],
                            // 项目报名项的标准方法 中的 选中的 标准id 数组
                            'one_field' =>['key' => 'project_standard_id', 'return_type' => 1, 'ubound_name' => 'join_item_standard_ids', 'split' => ','] // [0,25]
                        ]
                        ,[
                            // 获得报名项选的方法对应的名称 1:1
                            'project_standard_info' => CTAPIProjectStandardsBusiness::getPrimaryRelationConfigVal($request, $controller
                                , ['project_standard_id' => 'id']
                                , 1, ['one_field' =>['key' => 'name', 'return_type' => 2, 'ubound_name' => 'project_standard_id_name', 'split' => '、'],]// 项目名称 方法1
                                ,[], [], '', []),
                        ], [], '', []),
                    // 下一级关系 报名项所属的项目 的标准【方法】 1:n
                    'project_standards_list' => CTAPIProjectStandardsBusiness::getPrimaryRelationConfigVal($request, $controller
                        , ['ability_id' => 'ability_id']
                        , 2, [
                            // [[ 'id' => 26,  'tag_name' => '方法2'], ... ]
                            'many_fields' =>[ 'ubound_name' => 'project_standards', 'fields_arr'=> ['id' => 'id', 'tag_name' => 'name'],'reset_ubound' => 2],// 是否重新排序下标 1：重新０.．． ]
                            'one_field' =>['key' => 'name', 'return_type' => 2, 'ubound_name' => 'project_standards_text', 'split' => '<br/>']// 方法2<br/>方法1
                        ]
                        ,[], [], '', []),
                ], [], '', []),
        ];
        return Tool::formatArrByKeys($relationFormatConfigs, $relationKeys, false);
    }

=======
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
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

    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param boolean 原数据类型 true:二维[默认];false:一维
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
<<<<<<< HEAD
    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){

        // 重写开始

        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记

        //        if(!empty($data_list) ){
        // 企业信息
        $companyDataList = [];// 企业id 为下标 二维数组
        $companyKVList = [];// 企业id => 企业名称 的键值对
        if(in_array('company', $handleKeyArr)){
            $staffIdsArr = array_values(array_filter(array_column($data_list,'staff_id')));// 资源id数组，并去掉值为0的
            // 查询条件
//            $companyList = [];
//            if(!empty($staffIdsArr)){
//                // 获得企业信息
//                $companyQueryParams = [
//                    'where' => [
//                        // ['type_id', 5],
//                        //                //['mobile', $keyword],
//                    ],
//                    //            'select' => [
//                    //                'id','company_id','position_name','sort_num'
//                    //                //,'operate_staff_id','operate_staff_id_history'
//                    //                ,'created_at'
//                    //            ],
//                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
//                ];
//                Tool::appendParamQuery($companyQueryParams, $staffIdsArr, 'id', [0, '0', ''], ',', false);
//                $companyList = CTAPIStaffBusiness::getBaseListData($request, $controller, '', $companyQueryParams,[], 1,  1)['data_list'] ?? [];
//            }
            $extParams =[];
            $companyList =  CTAPIStaffBusiness::getFVFormatList( $request,  $controller,  ['id' => $staffIdsArr], false,[], $extParams);
            if(!empty($companyList)){
                $companyDataList = Tool::arrUnderReset($companyList, 'id', 1);
                $companyKVList = Tool::formatArrKeyVal($companyList, 'id', 'company_name');
            }
            if(!$isNeedHandle && !empty($companyDataList)) $isNeedHandle = true;
        }

        // 获得报名项
        $joinItemKeyDataList = [];// 报名主表 id 为下标 二维数组
        if(in_array('joinItems', $handleKeyArr)){
            $joinIdsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
            // 查询条件
//            $joinItemList = [];
//            if(!empty($joinIdsArr)){
                // 获得企业信息
//                $joinItemQueryParams = [
//                    'where' => [
//                        // ['type_id', 5],
//                        //                //['mobile', $keyword],
//                    ],
//                    //            'select' => [
//                    //                'id','company_id','position_name','sort_num'
//                    //                //,'operate_staff_id','operate_staff_id_history'
//                    //                ,'created_at'
//                    //            ],
//                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
//                ];
//                Tool::appendParamQuery($joinItemQueryParams, $joinIdsArr, 'ability_join_id', [0, '0', ''], ',', false);
//                // $joinItemList = CTAPIAbilityJoinItemsBusiness::getBaseListData($request, $controller, '', $joinItemQueryParams, [], 1,  1)['data_list'] ?? [];
//
//                $extParams = [
//                    'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                ];
//                $joinItemList = CTAPIAbilityJoinItemsBusiness::getList($request, $controller, 1, $joinItemQueryParams, [], $extParams)['result']['data_list'] ?? [];
//            }

            $extParams = [
                'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            ];
            // $businessName = 'App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness';
            $joinItemList =  CTAPIAbilityJoinItemsBusiness::getFVFormatList( $request,  $controller,  ['ability_join_id' => $joinIdsArr], false,[], $extParams);
            if(!empty($joinItemList)){
                $joinItemKeyDataList = Tool::arrUnderReset($joinItemList, 'ability_join_id', 2);
            }
            if(!$isNeedHandle && !empty($joinItemKeyDataList)) $isNeedHandle = true;

        }


        //        }
        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
        //    return true;
        // }

        foreach($data_list as $k => $v){
            //            // 公司名称
            //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
            //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);


            // 企业信息
            if(in_array('company', $handleKeyArr)){
                $data_list[$k]['company_info'] = $companyDataList[$v['staff_id']] ?? '';
                $data_list[$k]['company_name'] = $companyKVList[$v['staff_id']] ?? '';
            }
            // 获得报名项
            if(in_array('joinItems', $handleKeyArr)){
                $data_list[$k]['join_items'] = $joinItemKeyDataList[$v['id']] ?? '';
            }
        }

        // 重写结束
        return true;
    }
=======
//    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){
//
//        // 重写开始
//
//        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记
//
//        //        if(!empty($data_list) ){
//        // 企业信息
//        $companyDataList = [];// 企业id 为下标 二维数组
//        $companyKVList = [];// 企业id => 企业名称 的键值对
//        if(in_array('company', $handleKeyArr)){
//            $staffIdsArr = array_values(array_filter(array_column($data_list,'staff_id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $companyList = [];
////            if(!empty($staffIdsArr)){
////                // 获得企业信息
////                $companyQueryParams = [
////                    'where' => [
////                        // ['type_id', 5],
////                        //                //['mobile', $keyword],
////                    ],
////                    //            'select' => [
////                    //                'id','company_id','position_name','sort_num'
////                    //                //,'operate_staff_id','operate_staff_id_history'
////                    //                ,'created_at'
////                    //            ],
////                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
////                ];
////                Tool::appendParamQuery($companyQueryParams, $staffIdsArr, 'id', [0, '0', ''], ',', false);
////                $companyList = CTAPIStaffBusiness::getBaseListData($request, $controller, '', $companyQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//            $extParams =[];
//            $companyList =  CTAPIStaffBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['id' => $staffIdsArr], false,[], $extParams);
//            if(!empty($companyList)){
//                $companyDataList = Tool::arrUnderReset($companyList, 'id', 1);
//                $companyKVList = Tool::formatArrKeyVal($companyList, 'id', 'company_name');
//            }
//            if(!$isNeedHandle && !empty($companyDataList)) $isNeedHandle = true;
//        }
//
//        // 获得报名项
//        $joinItemKeyDataList = [];// 报名主表 id 为下标 二维数组
//        if(in_array('joinItems', $handleKeyArr)){
//            $joinIdsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $joinItemList = [];
////            if(!empty($joinIdsArr)){
//                // 获得企业信息
////                $joinItemQueryParams = [
////                    'where' => [
////                        // ['type_id', 5],
////                        //                //['mobile', $keyword],
////                    ],
////                    //            'select' => [
////                    //                'id','company_id','position_name','sort_num'
////                    //                //,'operate_staff_id','operate_staff_id_history'
////                    //                ,'created_at'
////                    //            ],
////                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
////                ];
////                Tool::appendParamQuery($joinItemQueryParams, $joinIdsArr, 'ability_join_id', [0, '0', ''], ',', false);
////                // $joinItemList = CTAPIAbilityJoinItemsBusiness::getBaseListData($request, $controller, '', $joinItemQueryParams, [], 1,  1)['data_list'] ?? [];
////
////                $extParams = [
////                    'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
////                ];
////                $joinItemList = CTAPIAbilityJoinItemsBusiness::getList($request, $controller, 1, $joinItemQueryParams, [], $extParams)['result']['data_list'] ?? [];
////            }
//
//            $extParams = [
//                'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            ];
//            // $businessName = 'App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness';
//            $joinItemList =  CTAPIAbilityJoinItemsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['ability_join_id' => $joinIdsArr], false,[], $extParams);
//            if(!empty($joinItemList)){
//                $joinItemKeyDataList = Tool::arrUnderReset($joinItemList, 'ability_join_id', 2);
//            }
//            if(!$isNeedHandle && !empty($joinItemKeyDataList)) $isNeedHandle = true;
//
//        }
//
//
//        //        }
//        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
//        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
//        //    return true;
//        // }
//
//        foreach($data_list as $k => $v){
//            //            // 公司名称
//            //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//
//
//            // 企业信息
//            if(in_array('company', $handleKeyArr)){
//                $data_list[$k]['company_info'] = $companyDataList[$v['staff_id']] ?? '';
//                $data_list[$k]['company_name'] = $companyKVList[$v['staff_id']] ?? '';
//            }
//            // 获得报名项
//            if(in_array('joinItems', $handleKeyArr)){
//                $data_list[$k]['join_items'] = $joinItemKeyDataList[$v['id']] ?? '';
//            }
//        }
//
//        // 重写结束
//        return true;
//    }
>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221

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

        $admin_type = CommonRequest::getInt($request, 'admin_type');
        if($admin_type > 0 )  array_push($queryParams['where'], ['admin_type', '=', $admin_type]);

        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if($staff_id > 0 )  array_push($queryParams['where'], ['staff_id', '=', $staff_id]);

        $status = CommonRequest::getInt($request, 'status');
        if($status > 0 )  array_push($queryParams['where'], ['status', '=', $status]);

//        $status = CommonRequest::get($request, 'status');
//        if(strlen($status) > 0 && $status != 0)  Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);

        $is_print = CommonRequest::getInt($request, 'is_print');
        if($is_print > 0 )  array_push($queryParams['where'], ['is_print', '=', $is_print]);

        $is_grant = CommonRequest::getInt($request, 'is_grant');
        if($is_grant > 0 )  array_push($queryParams['where'], ['is_grant', '=', $is_grant]);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

<<<<<<< HEAD
=======

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
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;
        // 关系配置
        $relationFormatConfigs = [
            // 获得企业名称 1:1
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1
                , 2// 企业名称
                ,'',''
                ,[], ['where' => [['admin_type', 2]]], '', []),
            // 报名项目 1:n -- 读取到报名项
            'join_items' => CTAPIAbilityJoinItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_id']
                , 2
                ,0
                ,'','',[
                    // 下一级关系 报名项所属的项目 -- 的名称 1:1
                    'ability_info' => CTAPIAbilitysBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_id' => 'id']
                        , 1, 2// 项目名称  测试4
                        ,'',''
                        ,[], [], '', []),
                    // 下一级关系的  能力验证报名项-项目标准 1:n
                    'join_item_standards' => CTAPIAbilityJoinItemsStandardsBusiness::getTableRelationConfigInfo($request, $controller
                        , ['id' => 'ability_join_item_id']
                        , 2
                        , 1 | 2
                        ,'',''
                        ,[
                            // 获得报名项选的方法对应的名称 1:1
                            'project_standard_info' => CTAPIProjectStandardsBusiness::getTableRelationConfigInfo($request, $controller
                                , ['project_standard_id' => 'id']
                                , 1, 2
                                ,'',''
                                ,[], [], '', []),
                        ], [], '', ['extendConfig' => ['listHandleKeyArr' => ['mergeZeroName']]]),
                    // 下一级关系 报名项所属的项目 的标准【方法】 1:n
                    'project_standards_list' => CTAPIProjectStandardsBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_id' => 'ability_id']
                        , 2, 4 | 8
                        ,'',''
                        ,[], [], '', []),
                ], [], '', []),
            // 报名项目 1:n -- 读取到报名项
            'join_items_save' => CTAPIAbilityJoinItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_id']
                , 2
                ,4
                ,'','',[
                    // 下一级关系的  能力验证报名项-项目标准 1:n
                    'join_item_standards' => CTAPIAbilityJoinItemsStandardsBusiness::getTableRelationConfigInfo($request, $controller
                        , ['id' => 'ability_join_item_id']
                        , 2
                        , 4
                        ,'',''
                        ,[
                        ], [], '', []),
                ], [], '', []),
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

//        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
//            $one_field = ['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'];// 获得名称
//            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
//            array_push($return_data['one_field'], $one_field);
//        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

>>>>>>> 03194bebf1bfe858d89f59f73d7fe347d2316221
}
