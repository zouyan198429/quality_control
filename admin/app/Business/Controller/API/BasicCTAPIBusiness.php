<?php

namespace App\Business\Controller\API;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Request\CommonRequest;
use App\Services\Response\Data\CommonAPIFromBusiness;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;

class BasicCTAPIBusiness extends APIOperate
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $model_name = '';// 中间层 App\Business\API 下面的表名称 API\RunBuy\CountSenderRegAPI
    public static $table_name = '';// 表名称


    /**
     * 修改 Request的值
     *
     * @param array $params 需要修改的键值数组 ['foo' => 'bar', ....]
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function mergeRequest(Request $request, Controller $controller, $params = [])
    {
        // 合并输入，如果有相同的key，用户输入的值会被替换掉，否则追加到 input
         $request->merge($params);

        // 替换所有输入
        // $request->replace($params);
    }

    /**
     * 删除 Request的值
     *
     * @param array $params 需要修改的键值数组 ['foo', 'bar', ....]
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function removeRequest(Request $request, Controller $controller, $params = [])
    {
        foreach($params as $key){
            unset($request[$key]);
        }
    }


    /**
     * 生成单号
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int  $orderType 要保存或修改的数组 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金 8 邀请码
     * @return  int
     * @author zouyan(305463219@qq.com)
     */
    public static function createSn(Request $request, Controller $controller, $orderType = 1){
        $company_id = $controller->company_id;
        $user_id = $controller->user_id ?? '';
        $namespace = '';
        $prefix = $orderType;
        $midFix = '';
        $backfix = '';
        $length = 6;
        $expireNums = [];
        $needNum = 0;
        $dataFormat = '';
        switch ($orderType)
        {
            case 1:// 订单
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'order' . $userIdBack;
                $length = 4;
                $needNum = 1 + 2 + 8;
//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 2:// 2 退款订单
            case 3:// 3 支付跑腿费
            case 4:// 4 追加跑腿费
            case 5:// 5 冲值
            case 6:// 6 提现
            case 7:// 7 压金或保证金
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'orderRefund' . $userIdBack;
                $length = 2;// 总共一秒一万
                $needNum = 4 + 8;
                $dataFormat = 'ymdHis';

//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 8:// 8 邀请码
                $prefix = '';
                $midFix = '';
                $length = 2;// 总共一秒一万
                $needNum = 1 + 2 + 8;
                $dataFormat = 's';
                break;
            default:
        }
        $fixParams = [
            'prefix' => $prefix,// 前缀[1-2位] 可填;可写业务编号等
            'midFix' => $midFix,// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
            'backfix' => $backfix,// 后缀[1-2位] 可填;备用
            'expireNums' => $expireNums,// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
            'needNum' => $needNum,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
            'dataFormat' => $dataFormat, // needNum 值为 4时的日期格式  'YmdHis'
        ];
        return Tool::makeOrder($namespace , $fixParams, $length);
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~通用方法~~~~~~~~如果有特殊的不同，可以自己重写相关方法~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // ~~~~~~~~~~~~~~~~~列表开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * 获得列表数据--根据ids
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string / array $ids  查询的id ,多个用逗号分隔, 或数组【一维】
     * @param array $extParams 其它扩展参数 -- 其它条件或排序，放这个数组中。
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param mixed $relations 关系
     * @param string $idFieldName id的字段名称 默认 id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getListByIds(Request $request, Controller $controller, $ids = '', $extParams = [], $relations = '', $idFieldName = 'id', $notLog = 0){
        if(empty($ids)) return [];
        if(is_array($ids))  $ids = implode(',', $ids);
        $queryParams = [
            'where' => [
                //    ['company_id', $company_id],
                // ['operate_staff_id', $user_id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_history_id'
//                ,'created_at'
//            ],
//            'orderBy' => ['sort_num'=>'desc','id'=>'desc'],
//            'orderBy' => ['id'=>'desc'],
        ];// 查询条件参数

        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
//                array_push($queryParams['where'], [$idFieldName, $ids]);
                if(!isset($extParams['sqlParams']['where'])) $extParams['sqlParams']['where'] = [];
                array_push($extParams['sqlParams']['where'], [$idFieldName, $ids]);
            } else {
                $idArr = array_values(array_unique(explode(',', $ids)));// 去重，重按数字下标
//                $queryParams['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
//                $queryParams['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
                $extParams['sqlParams']['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
            }
        }
        // 没有传，则用false
        if(!isset($extParams['useQueryParams']))  $extParams['useQueryParams'] = false;
        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, $extParams, $notLog);
        $data_list = $result['result']['data_list'] ?? [];
        return $data_list;
    }


    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param string $queryParams 条件数组/json字符
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getList(Request $request, Controller $controller, $oprateBit = 2 + 4, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $defaultQueryParams = [
            'where' => [
//                ['company_id', $company_id],
//                //['mobile', $keyword],
            ],
//            'select' => [
//                'id','company_id','position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
        ];
        // 修改默认查询条件
        static::listDefaultQuery($request,  $controller, $defaultQueryParams, $notLog);
        // 查询条件参数
        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
        $isExport = 0;

        $useSearchParams = $extParams['useQueryParams'] ?? true;// 是否用来拼接查询条件，true:用[默认];false：不用
        // 其它sql条件[覆盖式]
        $sqlParams = $extParams['sqlParams'] ?? [];
        $sqlKeys = array_keys($sqlParams);
        foreach($sqlKeys as $tKey){
            // if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
            if(isset($sqlParams[$tKey]) )  $queryParams[$tKey] = $sqlParams[$tKey];
        }

        if($useSearchParams) {
            // $params = static::formatListParams($request, $controller, $queryParams);
//            $province_id = CommonRequest::getInt($request, 'province_id');
//            if($province_id > 0 )  array_push($queryParams['where'], ['city_ids', 'like', '' . $province_id . ',%']);

//            $is_active = CommonRequest::get($request, 'is_active');
//            if(is_numeric($is_active) )  array_push($queryParams['where'], ['is_active', '=', $is_active]);

            // 参数拼接
            static::joinListParams($request, $controller,$queryParams, $notLog);

            $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
            if (!empty($ids)) {
                if (strpos($ids, ',') === false) { // 单条
                    // array_push($queryParams['where'], ['id', $ids]);
                    array_push($queryParams['where'], [static::$primary_key, $ids]);
                } else {
                    // $queryParams['whereIn']['id'] = explode(',', $ids);
                    $queryParams['whereIn'][static::$primary_key] = explode(',', $ids);
                }
            }
            $isExport = CommonRequest::getInt($request, 'is_export'); // 是否导出 0非导出 ；1导出数据
            if ($isExport == 1) $oprateBit = 1;
        }
        // $relations = ['CompanyInfo'];// 关系
        // $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);

        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $data_list, $handleKeyArr);

        // 对查询结果进行for循环处理
        static::forFormatListData($request, $controller, $data_list, $notLog);
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($data_list, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        $result['data_list'] = $data_list;
        // 导出功能
        if($isExport == 1){
            // 导出操作
            static::exportListData($request, $controller, $data_list, $notLog);
//            $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//            ImportExport::export('','excel文件名称',$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
            die;
        }
        // 非导出功能
        return ajaxDataArr(1, $result, '');
    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param string $paramName 参数的名称
     * @param string $fieldName 查询的字段名--表中的
     * @param boolean $paramIsNum 参数的值是一个，且是数字类型  true:数字；false:非数字--默认
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param boolean $hasInIsMerge 如果In条件有值时  true:合并；false:用新值--覆盖 --默认
     * @param array $paramVals 最终的有效值-- 一维数组
     * @return  boolean true:有拼查询  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function joinParamQuery(Request $request, Controller $controller, &$queryParams, $paramName = '', $fieldName = '', $paramIsNum = false, $excludeVals = [0, '0', ''], $valsSeparator = ',', $hasInIsMerge = false, &$paramVals = null){
        $paramVals = $paramIsNum ? CommonRequest::getInt($request,$paramName) : trim(CommonRequest::get($request,$paramName));// 多个用逗号分隔,

        return Tool::appendParamQuery($queryParams, $paramVals, $fieldName, $excludeVals, $valsSeparator, $hasInIsMerge);
        // 如果想自己用，可以用出下的形式
//        $class_id = CommonRequest::get($request, 'class_id');
//        if(is_numeric($class_id) && $class_id > 0 )  array_push($queryParams['where'], ['class_id', '=', $class_id]);
    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param array $paramConfigs 可能的参数配置  -- 二维数组
     * @return  boolean true:  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function joinParamQueryByArr(Request $request, Controller $controller, &$queryParams, $paramConfigs = []){

//        $paramConfigs = [
//            [
//                // 必有下标
//            'paramName' => 'class_id', // 参数的名称 -- 必填
//           'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                // 可有下标
//           'paramIsNum' => false,// 参数的值是一个，且是数字类型  true:数字；false:非数字--默认 -- 选填
//           'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//           'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//           'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//           ]
//       ];

        if(empty($paramConfigs)) return false;
        foreach($paramConfigs as $k => $paramConfig){
            $paramName = $paramConfig['paramName'] ?? '';
            $fieldName = $paramConfig['fieldName'] ?? '';
            if(empty($paramName) || empty($fieldName)) continue;

            $paramIsNum = $paramConfig['paramIsNum'] ?? false;
            $excludeVals = $paramConfig['excludeVals'] ?? [0, '0', ''];
            $valsSeparator = $paramConfig['valsSeparator'] ?? ',';
            $hasInIsMerge = $paramConfig['hasInIsMerge'] ?? false;
            $paramVals = '';
            static::joinParamQuery($request, $controller, $queryParams, $paramName, $fieldName, $paramIsNum, $excludeVals, $valsSeparator, $hasInIsMerge, $paramVals);
            $paramConfigs[$k]['paramVals'] = $paramVals;
        }
        return true;
    }

    /**
     * 获得列表数据时，查询条件的Like参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParamsLike(Request $request, Controller $controller, &$queryParams, $notLog = 0){
        // 有可能关键字不用like查询，所以单独写，每一个子类都写此代码
        $field = CommonRequest::get($request, 'field');
        $keyWord = CommonRequest::get($request, 'keyword');
        if (!empty($field) && !empty($keyWord)) {
            array_push($queryParams['where'], [$field, 'like', '%' . $keyWord . '%']);
        }

    }

    /**
     * 获得列表数据时，查询条件的默认的查询条件[比如可以修改select、where、orderby等]--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function listDefaultQuery(Request $request, Controller $controller, &$queryParams, $notLog = 0){
//        $select = [];
//        if(!empty($select)) $queryParams['select'] = $select;
//        或
//        $queryParams['select'] = [];
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

        // TODO 自己的参数查询拼接在这里
        // 方式一  --- 自己拼接
        // $type_id = CommonRequest::get($request, 'type_id');
        // if(is_numeric($type_id) )  array_push($queryParams['where'], ['type_id', '=', $type_id]);

        // 方式二 --- 单个拼接--封装
        // static::joinParamQuery($request, $controller, $queryParams, 'class_id', 'class_id', true, [0, '0', ''], ',', false);

        // 方式三 ---  批量拼接 -- 封装

//        $paramConfigs = [
//            [
//                'paramName' => 'class_id', // 参数的名称 -- 必填
//                'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                'paramIsNum' => false,// 参数的值是一个，且是数字类型  true:数字；false:非数字--默认 -- 选填
//                'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//                'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//                'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//            ],
//        ];
//        static::joinParamQueryByArr($request, $controller, $queryParams, $paramConfigs);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 获得列表数据时，对查询结果进行for循环处理--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function forFormatListData(Request $request, Controller $controller, &$data_list, $notLog = 0){
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
    }

    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function exportListData(Request $request, Controller $controller, &$data_list, $notLog = 0){
//            $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//            ImportExport::export('','excel文件名称',$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
    }

    /**
     * 数据通过自定义方法格式化---如果有格式化，肯定会重写里面的handleDataFormat 方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---一维/二维数组
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
    public static function handleData(Request $request, Controller $controller, &$data_list, $handleKeyArr){
        if(empty($handleKeyArr) || !is_array($handleKeyArr)) return true;
        if(empty($data_list) || (!is_array($data_list) && !is_object($data_list))) return true;

        $data_list = Tool::objectToArray($data_list);

        // 如果是一维数组，则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);

        // 对数据的具体格式化操作
        static::handleDataFormat($request, $controller, $data_list, $handleKeyArr, $isMulti);

        if(!$isMulti) $data_list = $data_list[0] ?? [];
        return true;
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

        /**
         *
        $typeKVArr = [];
        $contentKVArr = [];
        $resourceDataArr = [];
        //        if(!empty($data_list) ){
        // 获得分类名称
        if(in_array('templateType', $handleKeyArr)){
        $typeIdArr = array_values(array_filter(array_column($data_list,'type_id')));// 资源id数组，并去掉值为0的
        // kv键值对
        if(!empty($typeIdArr)) $typeKVArr = Tool::formatArrKeyVal(CTAPITemplateTypeBusiness::getListByIds($request, $controller, $typeIdArr), 'id', 'type_name');
        if(!$isNeedHandle && !empty($typeKVArr)) $isNeedHandle = true;
        }

        // 获得内容 templateContent
        if(in_array('templateContent', $handleKeyArr)){
        $idsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
        // kv键值对
        if(!empty($idsArr)) $contentKVArr = Tool::formatArrKeyVal(CTAPITemplateContentBusiness::getListByIds($request, $controller, $idsArr, [], [], 'template_id'), 'template_id', 'template_content');
        if(!$isNeedHandle && !empty($contentKVArr)) $isNeedHandle = true;
        }

        // 处理图片

        if(in_array('siteResources', $handleKeyArr)){
        $resourceIdArr = array_values(array_filter(array_column($data_list,'resource_id')));// 资源id数组，并去掉值为0的
        if(!empty($resourceIdArr)) $resourceDataArr = Tool::arrUnderReset(CTAPIResourceBusiness::getResourceByIds($request, $controller, $resourceIdArr), 'id', 2);// getListByIds($request, $controller, implode(',', $resourceIdArr));
        if(!$isNeedHandle && !empty($resourceDataArr)) $isNeedHandle = true;
        }

        //        }
         *
         */
        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
        //    return true;
        // }

        /**
         *
        foreach($data_list as $k => $v){
        //            // 公司名称
        //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
        //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);

        // 分类名称
        if(in_array('templateType', $handleKeyArr)){
        $data_list[$k]['type_name'] = $typeKVArr[$v['type_id']] ?? '';
        }

        // 获得内容
        if(in_array('templateContent', $handleKeyArr)){
        $data_list[$k]['content'] = $contentKVArr[$v['id']] ?? '';
        }

        // 资源url
        if(in_array('siteResources', $handleKeyArr)){
        // $resource_list = [];
        $resource_list = $resourceDataArr[$v['resource_id']] ?? [];
        if(isset($v['site_resources'])){
        Tool::resourceUrl($v, 2);
        $resource_list = Tool::formatResource($v['site_resources'], 2);
        unset($data_list[$k]['site_resources']);
        }
        $data_list[$k]['resource_list'] = $resource_list;
        }
        }
         *
         */

        // 重写结束
        return true;
    }
    // ~~~~~~~~~~~~~~~~~详情开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * 根据id获得单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id id
     * @param array $selectParams 查询字段参数--一维数组
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoData(Request $request, Controller $controller, $id, $selectParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIDogToolsRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoDataBase($request, $controller,'', $id, $selectParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $info, $handleKeyArr);

        // 格式化数据
        static::formatInfoData($request, $controller,$info, $notLog);

        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }

    /**
     * 获得详情数据时，对查询结果进行格式化操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info 详情数据 --- 一维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function formatInfoData(Request $request, Controller $controller, &$info, $notLog = 0){

    }

    /**
     * 根据条件获得一条详情记录 - 一维
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoDataByQuery(Request $request, Controller $controller, $company_id, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIDogToolsRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoByQuery($request, $controller,'', $company_id, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $info, $handleKeyArr);

        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }


    /**
     * 根据条件获得一条详情记录 - pagesize 1:返回一维数组,>1 返回二维数组  -- 推荐有这个按条件查询详情
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param int $pagesize 想获得的记录数量 1 , 2 。。 默认1
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getLimitDataQuery(Request $request, Controller $controller, $company_id, $pagesize = 1, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        $infoList = static::getInfoQuery($request, $controller,'', $company_id, $pagesize, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($infoList, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $infoList, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $infoList, $handleKeyArr);

        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($infoList, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $infoList;
    }

    /**
     * 格式化列表查询条件-暂不用
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $queryParams 条件数组/json字符
     * @return  array 参数数组 一维数据
     * @author zouyan(305463219@qq.com)
     */
//    public static function formatListParams(Request $request, Controller $controller, &$queryParams = []){
//        $params = [];
//        $title = CommonRequest::get($request, 'title');
//        if(!empty($title)){
//            $params['title'] = $title;
//            array_push($queryParams['where'],['title', 'like' , '%' . $title . '%']);
//        }
//
//        $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
//        if (!empty($ids)) {
//            $params['ids'] = $ids;
//            if (strpos($ids, ',') === false) { // 单条
//                array_push($queryParams['where'],['id', $ids]);
//            }else{
//                $queryParams['whereIn']['id'] = explode(',',$ids);
//                $params['idArr'] = explode(',',$ids);
//            }
//        }
//        return $params;
//    }

    /**
     * 获得当前记录前/后**条数据--二维数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 当前记录id
     * @param int $nearType 类型 1:前**条[默认]；2后**条 ; 4 最新几条;8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
     * @param int $limit 数量 **条
     * @param int $offset 偏移数量
     * @param string $queryParams 条件数组/json字符
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据 - 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getNearList(Request $request, Controller $controller, $id = 0, $nearType = 1, $limit = 1, $offset = 0, $queryParams = [], $relations = '', $extParams = [], $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 前**条[默认]
        $defaultQueryParams = [
            'where' => [
                //  ['company_id', $company_id],
//                ['id', '>', $id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc','id'=>'desc'],
//            'orderBy' => ['id'=>'asc'],
            'limit' => $limit,
            'offset' => $offset,
            // 'count'=>'0'
        ];
        // 默认的查询条件[比如可以修改select、where、orderby等]-
        static::nearDefaultQuery($request, $controller, $defaultQueryParams, $notLog);

        if(($nearType & 1) == 1){// 前**条
            // $defaultQueryParams['orderBy'] = ['id'=>'asc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'asc'];
            // array_push($defaultQueryParams['where'],['id', '>', $id]);
            array_push($defaultQueryParams['where'],[static::$primary_key, '>', $id]);
        }

        if(($nearType & 2) == 2){// 后*条
            // array_push($defaultQueryParams['where'],['id', '<', $id]);
            array_push($defaultQueryParams['where'],[static::$primary_key, '<', $id]);
            // $defaultQueryParams['orderBy'] = ['id'=>'desc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'desc'];
        }

        if(($nearType & 4) == 4){// 4 最新几条
            // $defaultQueryParams['orderBy'] = ['id'=>'desc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'desc'];
        }

        if(($nearType & 8) == 8){// 8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
            $defaultQueryParams['count'] = 0;
        }

        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
        $temFormatData = [
            'formatDataUbound' => $extParams['formatDataUbound'] ?? [],// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法
        ];
        if(isset($extParams['handleKeyArr'])) $temFormatData['handleKeyArr'] = $extParams['handleKeyArr'] ?? [];

        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, $temFormatData, $notLog);
        // 格式化数据
        $data_list = $result['result']['data_list'] ?? [];
//        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据 -- 已经在getList方法中处理过
        if($nearType == 1) $data_list = array_reverse($data_list); // 相反;
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
//        $result['result']['data_list'] = $data_list;
        return $data_list;
    }

    /**
     * 获得当前记录前/后**条数据-数据时，查询条件的默认的查询条件[比如可以修改select、where、orderby等]--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function nearDefaultQuery(Request $request, Controller $controller, &$queryParams, $notLog = 0){
//        $select = [];
//        if(!empty($select)) $queryParams['select'] = $select;
//        或
//        $queryParams['select'] = [];
    }

    /**
     * 导入模版
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 初始数据  -- 二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplate(Request $request, Controller $controller, $data_list = [], $notLog = 0)
    {
        static::importTemplateExcel($request, $controller, $data_list, $notLog);
        die;
    }

    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 初始数据  -- 二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplateExcel(Request $request, Controller $controller, $data_list = [], $notLog = 0){
//        $data_list = [];
//        $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//        ImportExport::export('','员工导入模版',$data_list,1, $headArr, 0, ['sheet_title' => '员工导入模版']);
    }


    /**
     * 删除单条数据--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delAjax(Request $request, Controller $controller, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
         $id = CommonRequest::getInt($request, 'id');
        // 调用删除接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        static::exeDBBusinessMethodCT($request, $controller, '',  'delById', $apiParams, $company_id, $notLog);
        return ajaxDataArr(1, $id, '');
//        return static::delAjaxBase($request, $controller, '', $notLog);

    }


    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'judgeDataKey' => '',// 数据验证的下标 add: 添加；modify:修改; replace:新加或修改等
     *   ];
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    /*
     *
    public static function replaceById(Request $request, Controller $controller, $saveData, &$id, $extParams = [], $modifAddOprate = false, $notLog = 0){
        // $tableLangConfig = static::getLangModelsDBConfig('',  1);
        $company_id = $controller->company_id;
        // 验证数据
        $judgeType = ($id > 0) ? 4 : 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        // $mustFields = [];//
        $judgeDataKey = $extParams['judgeDataKey'] ?? '';
        static::judgeDataThrowsErr($judgeType, $saveData, $mustFields, $judgeDataKey, 1, "<br/>", ['request' => $request, 'controller' => $controller , 'id' => $id]);

        if($id > 0){
            // 判断权限
//            $judgeData = [
//                'company_id' => $company_id,
//            ];
//            $relations = '';
//            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
            if($modifAddOprate) static::addOprate($request, $controller, $saveData);

        }else {// 新加;要加入的特别字段
            $addNewData = [
                //  'company_id' => $company_id,
            ];
            $saveData = array_merge($saveData, $addNewData);
            // 加入操作人员信息
            static::addOprate($request, $controller, $saveData);
        }
        // 新加或修改
        return static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
    }
     *
     */

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'judgeDataKey' => '',// 数据验证的下标 add: 添加；modify:修改; replace:新加或修改等
     *   ];
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById(Request $request, Controller $controller, $saveData, &$id, $extParams = [], $modifAddOprate = false, $notLog = 0){
        // $tableLangConfig = static::getLangModelsDBConfig('',  1);
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['goods_name']) && empty($saveData['goods_name'])  ){
//            throws('商品名称不能为空！');
//        }
        // 特殊的验证


        // 验证数据
        $judgeType = ($id > 0) ? 4 : 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        // $mustFields = [];
        //if(!empty($judgeDataKey)){
        //    $errMsgs = static::specialJudgeKey($mustFields, $saveData, $judgeDataKey, ['request' => $request, 'controller' => $controller , 'id' => $id]);
        //    if(!empty($errMsgs)) throws(implode('<br/>', $errMsgs));
        //}
        // static::judgeDBDataThrowErr($judgeType,$saveData, $mustFields, 1);
        $judgeDataKey = $extParams['judgeDataKey'] ?? '';
        static::judgeDataThrowsErr($judgeType, $saveData, $mustFields, $judgeDataKey, 1, "<br/>", ['request' => $request, 'controller' => $controller , 'id' => $id]);

        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        $methodName = $extParams['methodName'] ?? 'replaceById';
        $id = static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        // 操作成功后，可进行一些操作
        static::replaceByIdAPISucess($request, $controller, $apiParams, $id, $judgeType);

        return $id;
//        $isModify = false;
//        if($id > 0){
//            $isModify = true;
//            // 判断权限
////            $judgeData = [
////                'company_id' => $company_id,
////            ];
////            $relations = '';
////            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
//            if($modifAddOprate) static::addOprate($request, $controller, $saveData);
//
//        }else {// 新加;要加入的特别字段
//            $addNewData = [
//               // 'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//            // 加入操作人员信息
//            static::addOprate($request, $controller, $saveData);
//        }
//        // 新加或修改
//        $result =  static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
//        if($isModify){
//            // 判断版本号是否要+1
//            $historySearch = [
//                //  'company_id' => $company_id,
//                'goods_id' => $id,
//            ];
//            static::compareHistoryOrUpdateVersion($request, $controller, '' , $id, ShopGoodsHistoryAPIBusiness::$model_name
//                , 'shop_goods_history', $historySearch, ['goods_id'], 1, $company_id);
//        }
//        return $result;
    }

    /**
     * replaceById 方法操作成功后可执行的一些操作----具体的表，如有需要，请重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $apiParams 请求的原数据
     * @param int $id id 成功后返回的id
     * @param array $judgeType 2 新建数据 ；4 修改数据
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPISucess(Request $request, Controller $controller, $apiParams = [], $id = 0, $judgeType = 2){

    }

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

                // $id = $extParams['id'] ?? 0;
                $id = $extParams[static::$primary_key] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ***********导入***开始************************************************************
    /**
     * 批量导入
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function import(Request $request, Controller $controller, $saveData , $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 参数
        $requestData = [
            'company_id' => $company_id,
            'staff_id' =>  $controller->user_id,
            'admin_type' =>  $controller->admin_type,//self::$admin_type,
            'save_data' => $saveData,
        ];
        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
        // 生成带参数的测试get请求
        // $requestTesUrl = splicQuestAPI($url , $requestData);
        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
    }

    /**
     * 批量导入员工--通过文件路径
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $fileName 文件全路径
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function importByFile(Request $request, Controller $controller, $fileName = '', $notLog = 0){
        // $fileName = 'staffs.xlsx';
        $dataStartRow = 1;// 数据开始的行号[有抬头列，从抬头列开始],从1开始
        // 需要的列的值的下标关系：一、通过列序号[1开始]指定；二、通过专门的列名指定;三、所有列都返回[文件中的行列形式],$headRowNum=0 $headArr=[]
        $headRowNum = 1;//0:代表第一种方式，其它数字：第二种方式; 1开始 -必须要设置此值，$headArr 参数才起作用
        // 下标对应关系,如果设置了，则只获取设置的列的值
        // 方式一格式：['1' => 'name'，'2' => 'chinese',]
        // 方式二格式: ['姓名' => 'name'，'语文' => 'chinese',]
        $headArr = [
            '县区' => 'department',
            '归属营业厅或片区' => 'group',
            '姓名或渠道名称' => 'channel',
            //'姓名' => 'real_name',
            '工号' => 'work_num',
            '职务' => 'position',
            '手机号' => 'mobile',
            '性别' => 'sex',
        ];
//        $headArr = [
//            '1' => 'name',
//            '2' => 'chinese',
//            '3' => 'maths',
//            '4' => 'english',
//        ];
        try{
            $dataArr = ImportExport::import($fileName, $dataStartRow, $headRowNum, $headArr);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        }
        return self::import($request, $controller, $dataArr, $notLog);
    }

    // ***********导入***结束************************************************************

    // ***********获得kv***开始************************************************************
    // 根据父id,获得子数据kv数组
    public static function getCityByPid(Request $request, Controller $controller, $parent_id = 0, $notLog = 0){
        $company_id = $controller->company_id;
        $kvParams = ['key' => 'id', 'val' => 'type_name'];
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                    ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }

    // 根据父id,获得子数据kv数组
    /**
     * 数据kv数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $kvParams 键值对 ，为空：默认为['key' => 'id', 'val' => 'type_name']
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *   ];
     * @param array $orderBy 排序，默认为static::$orderBy，
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 键值对一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getListKV(Request $request, Controller $controller, $kvParams = [], $extParams = [], $orderBy = [], $notLog = 0){
        $company_id = $controller->company_id;
        // $kvParams = ['key' => 'id', 'val' => 'type_name'];
        if(empty($kvParams)) $kvParams = ['key' => 'id', 'val' => 'type_name'];
        if(empty($orderBy)) $orderBy = static::$orderBy;
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                // ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => $orderBy,// static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        // 其它sql条件[覆盖式]
        $sqlParams = $extParams['sqlParams'] ?? [];
        $sqlKeys = array_keys($sqlParams);
        foreach($sqlKeys as $tKey){
            // if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
            if(isset($sqlParams[$tKey]) )  $queryParams[$tKey] = $sqlParams[$tKey];
        }
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }
    // ***********获得kv***结束************************************************************

    // ***********通过组织条件获得kv***开始************************************************************
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据[一维的键=>值数组]
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildListKeyVal(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $parentData = static::getChildList($request, $controller, $pid, $oprateBit, $notLog);
        $department_list = $parentData['result']['data_list'] ?? [];
        return Tool::formatArrKeyVal($department_list, 'id', 'city_name');
    }
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildList(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $queryParams = [
            'where' => [
//                ['company_id', $company_id],
                ['parent_id', $pid],
            ],
            'select' => [
                'id','city_name','sort_num'
                //,'operate_staff_id','operate_staff_history_id'
            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc','id'=>'asc'],
        ];// 查询条件参数
        // $relations = ['CompanyInfo'];// 关系
        $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);
        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
        $result['data_list'] = $data_list;
        return ajaxDataArr(1, $result, '');
    }
    // ***********通过组织条件获得kv***结束************************************************************


}
