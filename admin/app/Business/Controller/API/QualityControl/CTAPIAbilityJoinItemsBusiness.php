<?php
//能力验证报名项表
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

class CTAPIAbilityJoinItemsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilityJoinItemsAPI';
    public static $table_name = 'ability_join_items';// 表名称

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
    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){

        // 重写开始

        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记

        //        if(!empty($data_list) ){
        // 获得能力验证名称
        $abilityDataList = [];// 能力id 为下标 二维数组
        $abilityKVList = [];// 能力id => 能力验证名称 的键值对
        if(in_array('ability', $handleKeyArr)){
            $abilityIdsArr = array_values(array_filter(array_column($data_list,'ability_id')));// 资源id数组，并去掉值为0的
            // 查询条件
            $abilityList = [];
            if(!empty($abilityIdsArr)){
                // 获得企业信息
                $abilityQueryParams = [
                    'where' => [
                        // ['type_id', 5],
                        //                //['mobile', $keyword],
                    ],
                    //            'select' => [
                    //                'id','company_id','position_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
                ];
                Tool::appendParamQuery($abilityQueryParams, $abilityIdsArr, 'id', [0, '0', ''], ',', false);
                $abilityList = CTAPIAbilitysBusiness::getBaseListData($request, $controller, '', $abilityQueryParams,[], 1,  1)['data_list'] ?? [];
            }
            if(!empty($abilityList)){
                $abilityDataList = Tool::arrUnderReset($abilityList, 'id', 1);
                $abilityKVList = Tool::formatArrKeyVal($abilityList, 'id', 'ability_name');
            }
            if(!$isNeedHandle && !empty($abilityDataList)) $isNeedHandle = true;
        }
        // 企业信息
        $companyDataList = [];// 企业id 为下标 二维数组
        $companyKVList = [];// 企业id => 企业名称 的键值对
        if(in_array('company', $handleKeyArr)){
            $staffIdsArr = array_values(array_filter(array_column($data_list,'staff_id')));// 资源id数组，并去掉值为0的
            // 查询条件
            $companyList = [];
            if(!empty($staffIdsArr)){
                // 获得企业信息
                $companyQueryParams = [
                    'where' => [
                        // ['type_id', 5],
                        //                //['mobile', $keyword],
                    ],
                    //            'select' => [
                    //                'id','company_id','position_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
                ];
                Tool::appendParamQuery($companyQueryParams, $staffIdsArr, 'id', [0, '0', ''], ',', false);
                $companyList = CTAPIStaffBusiness::getBaseListData($request, $controller, '', $companyQueryParams,[], 1,  1)['data_list'] ?? [];
            }
            if(!empty($companyList)){
                $companyDataList = Tool::arrUnderReset($companyList, 'id', 1);
                $companyKVList = Tool::formatArrKeyVal($companyList, 'id', 'company_name');
            }
            if(!$isNeedHandle && !empty($companyDataList)) $isNeedHandle = true;
        }

        // 获得报名的标准方法
        $joinItemStandardKeyDataList = [];// 报名主表 id 为下标 二维数组
        if(in_array('joinItemsStandards', $handleKeyArr)){
            $joinStandardIdsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
            // 查询条件
            $joinItemStandardList = [];
            if(!empty($joinStandardIdsArr)){
                // 获得企业信息
                $joinItemStandardQueryParams = [
                    'where' => [
                        // ['type_id', 5],
                        //                //['mobile', $keyword],
                    ],
                    //            'select' => [
                    //                'id','company_id','position_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_id_history'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
                ];
                Tool::appendParamQuery($joinItemStandardQueryParams, $joinStandardIdsArr, 'ability_join_item_id', [0, '0', ''], ',', false);
                // $joinItemStandardList = CTAPIAbilityJoinItemsBusiness::getBaseListData($request, $controller, '', $joinItemStandardQueryParams, [], 1,  1)['data_list'] ?? [];

                $extParams = [
                    'handleKeyArr' => ['project_standards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                ];
                $joinItemStandardList = CTAPIAbilityJoinItemsStandardsBusiness::getList($request, $controller, 1, $joinItemStandardQueryParams, [], $extParams)['result']['data_list'] ?? [];
            }
            if(!empty($joinItemStandardList)){
                $joinItemStandardKeyDataList = Tool::arrUnderReset($joinItemStandardList, 'ability_join_item_id', 2);

            }
            if(!$isNeedHandle && !empty($joinItemStandardKeyDataList)) $isNeedHandle = true;

            // 获得项目标准
            if(in_array('projectStandards', $handleKeyArr)){
                $abilityIdArr = array_values(array_filter(array_column($data_list,'ability_id')));// 资源id数组，并去掉值为0的
                $projectStandardsList = [];
                // 查询条件
                if(!empty($abilityIdArr)){
                    // 获得企业资质证书
                    $projectStandardsQueryParams = [
                        'where' => [
                            // ['type_id', 5],
                            //                //['mobile', $keyword],
                        ],
                        //            'select' => [
                        //                'id','company_id','position_name','sort_num'
                        //                //,'operate_staff_id','operate_staff_id_history'
                        //                ,'created_at'
                        //            ],
                        // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
                    ];
                    Tool::appendParamQuery($projectStandardsQueryParams, $abilityIdArr, 'ability_id', [0, '0', ''], ',', false);
                    $projectStandardsList = CTAPIProjectStandardsBusiness::getBaseListData($request, $controller, '', $projectStandardsQueryParams,[], 1,  1)['data_list'] ?? [];
                }
                if(!empty($projectStandardsList)) $projectStandardsArr = Tool::arrUnderReset($projectStandardsList, 'ability_id', 2);
                if(!$isNeedHandle && !empty($projectStandardsArr)) $isNeedHandle = true;
            }
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


            // 获得能力验证名称
            if(in_array('ability', $handleKeyArr)){
                $data_list[$k]['ability_info'] = $abilityDataList[$v['ability_id']] ?? '';
                $data_list[$k]['ability_name'] = $abilityKVList[$v['ability_id']] ?? '';
            }

            // 企业信息
            if(in_array('company', $handleKeyArr)){
                $data_list[$k]['company_info'] = $companyDataList[$v['staff_id']] ?? '';
                $data_list[$k]['company_name'] = $companyKVList[$v['staff_id']] ?? '';
            }
            // 获得报名的标准方法
            if(in_array('joinItemsStandards', $handleKeyArr)){
                $tem_arr = $joinItemStandardKeyDataList[$v['id']] ?? [];
                // $data_list[$k]['join_item_standards'] = $tem_arr;
                $data_list[$k]['join_item_standards'] = Tool::arrUnderReset($tem_arr, 'project_standard_id', 1);
                $data_list[$k]['join_item_standard_ids'] = array_values(array_column($tem_arr,'project_standard_id'));// 资源id数组，并去掉值为0的
            }

            // 获得项目标准
            if(in_array('projectStandards', $handleKeyArr)){
                //  [{'id': 0, 'tag_name': '标签名称'},..]
                $configArr = [];
                $temArr = $projectStandardsArr[$v['ability_id']] ?? [];
                foreach($temArr as $info){
                    array_push($configArr, [
                        'id' => $info['id'],
                        'tag_name' => $info['name'],
                    ]);
                }
                $data_list[$k]['project_standards'] = $configArr;
                $data_list[$k]['project_standards_text'] = implode('<br/>', Tool::getArrFields($temArr, 'name'));
            }

        }

        // 重写结束
        return true;
    }

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

        $ability_join_id = CommonRequest::getInt($request, 'ability_join_id');
        if($ability_join_id > 0 )  array_push($queryParams['where'], ['ability_join_id', '=', $ability_join_id]);

        $ability_id = CommonRequest::getInt($request, 'ability_id');
        if($ability_id > 0 )  array_push($queryParams['where'], ['ability_id', '=', $ability_id]);

        $status = CommonRequest::getInt($request, 'status');
        if($status > 0 )  array_push($queryParams['where'], ['status', '=', $status]);

//        $status = CommonRequest::get($request, 'status');
//        if(strlen($status) > 0 && $status != 0)  Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);


        $retry_no = CommonRequest::getInt($request, 'retry_no');
        if($retry_no >= 0 )  array_push($queryParams['where'], ['retry_no', '=', $retry_no]);

        $result_status = CommonRequest::getInt($request, 'result_status');
        if($result_status > 0 )  array_push($queryParams['where'], ['result_status', '=', $result_status]);

        $is_sample = CommonRequest::getInt($request, 'is_sample');
        if($is_sample > 0 )  array_push($queryParams['where'], ['is_sample', '=', $is_sample]);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

}
