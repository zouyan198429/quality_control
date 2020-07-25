<?php

namespace App\Business\DB;

use App\Business\BaseBusiness;
use App\Business\DB\QualityControl\ResourceDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Business\DB\QualityControl\StaffHistoryDBBusiness;
use App\Services\DB\CommonDB;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;


/**
 *
 */
class BaseDBBusiness extends BaseBusiness
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    /**
     * 获得模型对象
     *
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelObj(&$modelObj = null){
        CommonDB::getObjByModelName(static::$model_name, $modelObj);
        return $modelObj;
    }

    /**
     * 获得模型对象-- 通过名称
     *
     * @param string  $model_name 模型名称
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelObjByName($model_name, &$modelObj = null){
        CommonDB::getObjByModelName($model_name, $modelObj);
        return $modelObj;
    }

    /**
     * 获得模型的属性
     *
     * @param string  $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @return mixed 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAttr($attrName, $isStatic = 0, &$modelObj = null){
        static::getModelObj($modelObj );
        // return CommonDB::getAttr($modelObj, $attrName, $isStatic);
        return Tool::getAttr($modelObj, $attrName, $isStatic);
    }

    /**
     * 获得模型的自有属性
     *    // 自有属性
     *  // 0：都没有；
     *  // 1：有历史表 ***_history;
     *  // 2：有操作员工id 字段 operate_staff_id
     *  // 4：有操作员工历史id 字段 operate_staff_id_history
     *  // 8：有操作日期字段 created_at timestamp
     *  // 16：有更新日期字段 updated_at  timestamp
     * @return int 自有属性
     * @author zouyan(305463219@qq.com)
     */
    public static function getOwnProperty( &$modelObj = null){
        return static::getAttr('ownProperty', 1,$modelObj);
    }

    /**
     * 获得当前对象自有属性值
     *
     * @return int  当前对象自有属性值
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getOwnPropertyVal(){
        $modelProObj = null;
        $ownProperty = static::getOwnProperty($modelProObj);
        $modelProObj = null;// 释放
        return $ownProperty;
    }

    /**
     *  使用 list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId())
     *  获得当前对象
     *   $ownProperty  自有属性值
     *   $temNeedStaffIdOrHistoryId 是否需要获取操作员工id和历史id true:需要获取； false:不需要获取
     *
     * @return array [$ownProperty, boolean 返回值 true:需要获取； false:不需要获取]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getNeedStaffIdOrHistoryId(){
        // 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        $temNeedStaffIdOrHistoryId = false;
        $ownProperty = static::getOwnPropertyVal();
        // 2：   有操作员工id 字段 operate_staff_id
        // 4：或 有操作员工历史id 字段 operate_staff_id_history
        if( ($ownProperty & 2) == 2 || ($ownProperty & 4) == 4) $temNeedStaffIdOrHistoryId = true;
        return [$ownProperty, $temNeedStaffIdOrHistoryId];
    }

    /**
     * 调用模型方法
     *
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @return mixed 返回值
     * @author zouyan(305463219@qq.com)
     *
        // 获得表名称
        $tableName = LrChinaCityBusiness::exeMethod($request, $this, 'getTable', []);
     */
    public static function exeMethod($methodName, $params = [], &$modelObj = null){
        static::getModelObj($modelObj );
        // return CommonDB::exeMethod($modelObj, $methodName, $params);
        return Tool::exeMethod($modelObj, $methodName, $params);
    }

    /**
     * 新加
     *
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function create($dataParams = [], &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::create($modelObj, $dataParams);
    }

    /**
     * 查找记录,或创建新记录[没有找到] - $searchConditon +  $updateFields 的字段,
     *
     * @param obj $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @return obj $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function firstOrCreate(&$mainObj, $searchConditon, $updateFields)
    {
        // 主表
        static::getModelObj($mainObj );

        CommonDB::firstOrCreate($mainObj, $searchConditon, $updateFields );
        return  $mainObj;
    }

    /**
     * 已存在则更新，否则创建新模型--持久化模型，所以无需调用 save()-- $searchConditon +  $updateFields 的字段,
     *
     * @param obj $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return obj $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function updateOrCreate(&$mainObj, $searchConditon, $updateFields, $isCacheDataByCache = true)
    {
        // 主表
        static::getModelObj($mainObj );

        CommonDB::updateOrCreate($mainObj, $searchConditon, $updateFields, $isCacheDataByCache);
        return  $mainObj;
    }

    /**
     * 批量新加接口-data只能返回成功true:失败:false
     *
     * @param array $dataParams 一维或二维数组;只返回true:成功;false：失败
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function addBath($dataParams, &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        return CommonDB::insertData($modelObj, $dataParams);

    }

    /**
     * 批量新加接口-返回新创建的对象 一维[单条]或二维[多条]
     *
     * @param array $dataParams 一维或二维数组;只返回true:成功;false：失败
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function addBathObj($dataParams, &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        return CommonDB::eachAddData($modelObj, $dataParams, 2);

    }


    /**
     * 批量新加接口-data只能返回成功true:失败:false--里面也是一条一条加入的
     *
     * @param array $dataParams 需要新的数据-- 二维数组
     * @param string $primaryKey 默认自增列被命名为 id，如果你想要从其他“序列”获取ID
     * @return array 返回新加的主键值-一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static  function addBathByPrimaryKey($dataParams, $primaryKey = 'id', &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        $newIds = CommonDB::insertGetId($modelObj, $dataParams, $primaryKey);
        return $newIds;
    }

    /**
     * 修改接口--按条件修改
     *
     * @param array $dataParams 字段数组/json字符 --一维数组
     * @param array $queryParams 条件数组/json字符
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function save($dataParams, $queryParams, &$modelObj = null, $isCacheDataByCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::updateQuery($modelObj, $dataParams, $queryParams, $isCacheDataByCache);
    }

    /**
     * 修改接口--通过主键值数组修改
     *
     * @param array $dataParams 字段数组/json字符 --一维数组
     * @param array $ids 主键值数组数组
     * @param array $queryParamsSave 其它条件
     * @param string $primaryName 主键字段名称 ；默认 'id'
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveByIds($dataParams, $ids = [], $queryParamsSave = [], $primaryName = 'id', &$modelObj = null, $isCacheDataByCache = true)
    {
        // $queryParamsSave = [];
        $queryParamsSave['whereIn'][$primaryName] = $ids;

        return static::save($dataParams, $queryParamsSave, $modelObj, $isCacheDataByCache);
    }

    /**
     * 批量修改设置
     *
     * @param array $dataParams 主键及要修改的字段值 二维数组 数组/json字符
     * @param string $primaryKey 主键字段,默认为id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveBathById($dataParams, $primaryKey = 'id')
    {
        return CommonDB::batchSave(static::$model_name, $dataParams, $primaryKey);
    }

    /**
     * 通过id修改接口
     *
     * @param array $dataParams 字段数组/json字符 一维数组
     * @param string $id 主键id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public  static function saveById($dataParams, $id, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
         static::getModelObj($modelObj );
         return CommonDB::saveById($modelObj, $dataParams, $id);
    }

    /**
     * 根据条件删除接口
     *
     * @param int $company_id 公司id
     * @param string $Model_name model名称
     * @param array $queryParams 条件数组/json字符
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function del($queryParams, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::del($modelObj, $queryParams);
    }

    /**
     * 根据id删除接口
     *
     * @param int $ids 删除记录 id,单条记录或 多条[,号分隔]
     * @param object $modelObj 数据模型对象
     * @param string $fieldName 查询的字段名--表中的
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function deleteByIds($ids, &$modelObj = null, $fieldName = 'id', $valsSeparator = ',', $excludeVals = [0, '0', ''])
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::delByIds($modelObj, $ids, $fieldName, $valsSeparator, $excludeVals);
    }

    /**
     * 获得model所有记录-- 查询/所有记录分批获取[推荐],也可以获得总数量
     * 注意如果想要数组，记得 ->toArray()
     *
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数

    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
     //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
     //               'offset' => 0,//  $offset 偏移量-- 一般不用
      //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAllList($queryParams, $relations, &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        $obj = CommonDB::getAllModelDatas($modelObj, $queryParams, $relations, $isOpenCache);
//        if($reType == 2 && is_object($obj)){
//            return $obj->toArray();
//        }
        return $obj;
    }


    /**
     * 获得model记录-根据条件
     *
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数

    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
    //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
    //               'offset' => 0,//  $offset 偏移量-- 一般不用
    //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getList($queryParams, $relations, &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);

        static::getModelObj($modelObj );
        return CommonDB::getList($modelObj, $queryParams, $relations, $isOpenCache);
    }

    /**
     * 获得列表数据--根据ids
     *
     * @param string / array $ids  查询的id ,多个用逗号分隔, 或数组【一维】
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数--可以放其它条件---一般为空
    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
    //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
    //               'offset' => 0,//  $offset 偏移量-- 一般不用
    //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param string $primaryName 主键字段名称 ；默认 'id'
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return mixed object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getListByIds($ids, $queryParams = [], $relations = [], $primaryName = 'id', &$modelObj = null, $isOpenCache = true){
        if(empty($ids)) return [];
        if(is_array($ids))  $ids = implode(',', $ids);

        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
                if(!isset($queryParams['where'])) $queryParams['where'] = [];
                array_push($queryParams['where'], [$primaryName, $ids]);
            } else {
                $idArr = array_values(array_unique(explode(',', $ids)));// 去重，重按数字下标
                $queryParams['whereIn'][$primaryName] = Tool::arrClsEmpty($idArr);
            }
        }
        return static::getList($queryParams, $relations, $modelObj , $isOpenCache);
    }

    /**
     * 获得指定条件的多条数据-- 分页+总数量
     *
     * @param int 选填 $pagesize 每页显示的数量 [默认10]
     * @param int 选填 $total 总记录数,优化方案：传<=0传重新获取总数[默认0];=-5:只统计条件记录数量，不返回数据
     * @param string 选填 $queryParams 条件数组/json字符
     * @param string 选填 $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return array 数据
        $listData = [
            'pageSize' => $pagesize,
            'page' => $page,
            'total' => $total,
            'totalPage' => ceil($total/$pagesize),
            'dataList' => $requestData,
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getDataLimit($page = 1, $pagesize = 10, $total = 0, $queryParams = [], $relations = [], &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        // $page = 1;
        // $pagesize = 10;
        // $total = 10;
//        $queryParams = [
//            'where' => [
//                  ['id', '&' , '16=16'],
//                ['company_id', $company_id],
//                //['mobile', $keyword],
//                //['admin_type',self::$admin_type],
//            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
////            'select' => [
////                'id','company_id','type_name','sort_num'
////                //,'operate_staff_id','operate_staff_id_history'
////                ,'created_at'
////            ],
//            // 'orderBy' => ['id'=>'desc'],
//        ];

        /*
        if ($group_id > 0) {
            array_push($queryParams['where'], ['group_id', $group_id]);
        }

        if (!empty($keyword)) {
            array_push($queryParams['where'], ['real_name', 'like', '%' . $keyword . '%']);
        }
        $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
                array_push($queryParams['where'], ['id', $ids]);
            } else {
                $queryParams['whereIn']['id'] = explode(',', $ids);
            }
        }
        */
        // $relations = ''; $requestData =
        return CommonDB::getModelListDatas($modelObj, $page, $pagesize, $total, $queryParams, $relations, $isOpenCache);

    }

    /**
     * 获得 id=> 键值对 或 查询的数据
     *
     * @param array $kv ['key' => 'id字段', 'val' => 'name字段'] 或 [] 返回查询的数据
     * @param array $select 需要获得的字段数组   ['id', 'area_name'] ; 参数 $kv 不为空，则此参数可为空数组
     * @param array $queryParams 查询条件
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeyVals($kv = [], $select =[], $queryParams = [], &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
//        $areaCityList = $modelObj::select(['id', 'area_name'])
//            ->orderBy('sort_num', 'desc')->orderBy('id', 'desc')
//            ->where([
//                ['company_id', '=', $company_id],
//                ['area_parent_id', '=', $area_parent_id],
//            ])
//            ->get()->toArray();
//        if(!$is_kv) return $areaCityList;
//        return Tool::formatArrKeyVal($areaCityList, 'id', 'area_name');
        return CommonDB::getKeyVals($modelObj, $kv, $select, $queryParams, $isOpenCache);
    }

    //################可以用到单条缓存############开始##################################################
    /**
     * 根据id获得详情--主键缓存
     *
     * @param int $id
     * @param array $select 需要获得的字段数组   ['id', 'area_name'] ; 参数 $kv 不为空，则此参数可为空数组
     * @param string $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfo($id, $select = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        $requestData = CommonDB::getInfoById($modelObj, $id, $select, $relations, $isOpenCache);

        return  $requestData;
    }

    /**
     * 单条记录缓存处理
     * 根据主键缓存或次缓存，获得数据--参数为空，则返回空数组
     *  cacheDb:U:m:{email值}_{email值}  -> {id值}
     * @param array $paramsPrimaryVals 主键或主键相关缓存字段及值 刚好[字段不能多]用上缓存，不然就用不了缓存 [ '字段1' => '字段1的值','字段2' => '字段2的值'] ;为空，则返回空数组--注意字段是刚好[主键或主字段]，不能有多,顺序无所谓
     *                                  字段多了[超过缓存字段]会自动转为块级缓存
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed 获得的单 条记录对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByPrimaryCache($paramsPrimaryVals = [], $select = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::getInfoByPrimaryFields($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
    }
    //################可以用到单条缓存############结束##################################################

    /**
     * 根据条件获得详情 获得单条记录数据 1:返回一维数组,>1 返回二维数组
     *
     * @param string $pagesize 要获得的数据 1:
     * @param string $queryParams 条件数组/json字符
     * @param string $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByQuery($pagesize = 1, $queryParams = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        $requestData = CommonDB::getInfoByQuery($modelObj, $pagesize, $queryParams, $relations, $isOpenCache);

        return  $requestData;
    }

    /**
     * 根据条件，查询单条记录--从数据库
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => '字段2的值']
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByFieldVals($fieldVals = [], $select = []){
        if(empty($fieldVals)) return [];
        $queryParams = [
            'where' => [
                // ['my_order_no', $out_trade_no],
            ],
            /*
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        $where = [];
        foreach($fieldVals as $field => $val){
            $where[] = [$field, $val];
        }
        $queryParams['where'] = array_merge($queryParams['where'], $where);
        if(!empty($select)) $queryParams['select'] = $select;
        // 查询记录
        $dbDataInfo = static::getInfoByQuery(1, $queryParams, []);
        return $dbDataInfo;
    }

    /**
     * 自增自减接口,通过条件-data操作的行数
     *
     * @param string incDecField 增减字段
     * @param string incDecVal 增减值
     * @param string incDecType 增减类型 inc 增 ;dec 减[默认]
     * @param string $queryParams 条件数组/json字符
     * @param string modifFields 修改的其它字段 -没有，则传空数组json
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveDecIncByQuery($incDecField, $incDecVal = 1, $incDecType = 'inc', $queryParams = [], $modifFields = [], &$modelObj = null, $isCacheDataByCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::saveDecInc($modelObj, $incDecField, $incDecVal, $incDecType, $queryParams, $modifFields, $isCacheDataByCache);
    }

    /**
     * 自增自减接口,通过数组[二维]-data操作的行数数组
     *
     * @param int $company_id 公司id
     * @param string $dataParams 条件数组/json字符
        $dataParams = [
            [
                'Model_name' => 'model名称',
                'primaryVal' => '主键字段值',
                'incDecType' => '增减类型 inc 增 ;dec 减[默认]',
                'incDecField' => '增减字段',
                'incDecVal' => '增减值',
                'modifFields' => '修改的其它字段 -没有，则传空数组',
            ],
        ];
    如:
        [
            [
                'Model_name' => 'CompanyProSecurityLabel',
                'primaryVal' => '7',
                'incDecType' => 'inc',
                'incDecField' => 'validate_num',
                'incDecVal' => '2',
                'modifFields' => [],
            ],
            [
                'Model_name' => 'CompanyProSecurityLabel',
                'primaryVal' => '9',
                'incDecType' => 'inc',
                'incDecField' => 'validate_num',
                'incDecVal' => '1',
                'modifFields' => [
                    'status' => 1,
                ],
            ],
        ];
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveDecIncByArr($dataParams)
    {
        return CommonDB::saveDecIncBatchByPrimaryKey($dataParams );
    }

    /**
     * 同步修改关系接口
     *
     * @param string $Model_name model名称
     * @param int $id
     * @param string/array $synces 同步关系数组/json字符  格式 [ '关系方法名' =>[关系id,...],...可多个....]
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function sync($id, $synces, &$modelObj = null)
    {
        static::getModelObj($modelObj );
        return CommonDB::sync($modelObj, $id, $synces);
    }

    /**
     * 移除关系接口
     *
     * @param string $Model_name model名称
     * @param int $id
     * @param string/array $detaches 移除关系数组/json字符 空：移除所有，id数组：移除指定的
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function detach($id, $detaches, &$modelObj = null)
    {
        static::getModelObj($modelObj );
        return CommonDB::detach($modelObj, $id, $detaches);;
    }

    /**
     * 获得表的主键字段
     *
     * @return array  缓存键的主键字段-- 一维数组,也可能是空
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryKey(&$mainObj = null)
    {
        // 主表
        static::getModelObj($mainObj );
        $primaryKey = CommonDB::getPrimaryKey($mainObj);
        return $primaryKey;
    }

    /**
     * 根据表对象名称获得表的主键字段
     *
     * @param string $objName 表对象名称
     * @return array 缓存键的主键字段-- 一维数组,也可能是空
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryKeyByObjName($objName, &$obj)
    {
        static::getModelObjByName($objName, $obj);

        $primaryKey = CommonDB::getPrimaryKey($obj);
        return $primaryKey;
    }


    /**
     * 根据主表id，获得对应的历史表id
     *
     * @param Request $request
     * @param mixed $mId 主表对象主键值
     * @param string $historyObjName 历史表对象名称
     * @param string $historyTable 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上) 格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
         [
            'company_id' => $company_id,
            'subject_id' => $main_id,
        ]
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] 格式 ['字段1','字段2' ... ]
     * @return  int 历史表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getHistoryId(&$mainObj, $mId, $historyObjName, $historyTable, &$historyObj, $historySearch = [], $ignoreFields = [] )
    {
        // 主表
        static::getModelObj($mainObj );

        // 历史表
        static::getModelObjByName($historyObjName, $historyObj);

        CommonDB::getHistory($mainObj, $mId, $historyObj, $historyTable, $historySearch, $ignoreFields);

        return  $historyObj->id;
    }


    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param obj $mainObj 主表对象
     * @param mixed $mId 主表对象主键值
     * @param obj $historyObj 历史表对象
     * @param string $historyTable 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上)格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
         [
            'company_id' => $company_id,
            'staff_id' => $main_id,
        ];
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] - 必须会有 [历史表中对应主表的id字段] 格式 ['字段1','字段2' ... ]
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistoryOrUpdateVersion(&$mainObj, $mId, $historyObjName, $historyTable, &$historyObj, $historySearch = [], $ignoreFields = [], $forceIncVersion = 0)
    {
        // 主表
        static::getModelObj($mainObj );

        // 历史表
        static::getModelObjByName( $historyObjName, $historyObj);


        $diffDataArr = CommonDB::compareHistoryOrUpdateVersion($mainObj, $mId,
            $historyObj, $historyTable, $historySearch, $ignoreFields,
            $forceIncVersion);

        return  $diffDataArr;
    }

    //************************基类扩展出来的公用方法*******************************************************************************
    /**
     * 判断后机号是否已经存在 true:已存在;false：不存在
     *
     * @param int $company_id id
     * @param int $id id
     * @param string $fieldName 需要判断的字段名 mobile  admin_username  work_num
     * @param string $fieldVal 当前要判断的值
     * @param array $otherWhere 其它查询条件 --二维数组
        [
        //  ['company_id', $company_id],
        [$fieldName,$fieldVal],
        // ['admin_type',self::$admin_type],
        ]
     * @param int $reType 返回类型 1:布尔型 2:当前存在的记录 [没有，则为空数组[]]
     * @return  mixed boolean/array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeFieldExist($company_id, $id, $fieldName, $fieldVal, $otherWhere = [], $reType = 1){
        // $company_id = $controller->company_id;
        $queryParams = [
            'where' => [
               // ['id', 100],
                //  ['company_id', $company_id],
                [$fieldName,$fieldVal],
                // ['admin_type',self::$admin_type],
            ],
            // 'limit' => 1
        ];
        if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
        if( is_numeric($id) && $id >0){
            array_push($queryParams['where'],['id', '<>' ,$id]);
        }

        $infoData = static::getInfoByQuery(1, $queryParams, []);
        // if(is_object($infoData))  $infoData = $infoData->toArray();
        if(empty($infoData)){//  || count($infoData)<=0
            if(($reType & 2) ==2) return [];
            return false;
        }
        if(($reType & 2) ==2) return $infoData;
        return true;
    }

    /**
     * 获得操作人员历史id
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @param int $operate_staff_id 操作人id
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function getStaffHistoryId($operate_staff_id = 0){
        $staffDBObj = null ;
        $staffHistoryDBObj = null ;
        $operate_staff_id_history = StaffDBBusiness::getHistoryId($staffDBObj, $operate_staff_id, StaffHistoryDBBusiness::$model_name
            , StaffHistoryDBBusiness::$table_name, $staffHistoryDBObj, ['staff_id' => $operate_staff_id], []);
        return $operate_staff_id_history;
    }

    /**
     * 数据加入操作人员历史id
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @param int $operate_type 操作类型 主要用1，2-一般不用，一般会在使用之前判断是不是应该用此来获取
     *                              1 [默认]必须要获得[下面代码也要用] $operate_staff_id_history 操作人历史id;--肯定要获取到
     *                              2 当前对象有这个字段就获取或只有调用的地方会用到 $operate_staff_id_history 操作人历史id;--不一定要获取
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function addOprate(&$saveData, $operate_staff_id = 0, &$operate_staff_id_history = 0, $operate_type = 1){
        if(!is_numeric($operate_staff_id) || $operate_staff_id <= 0) return $saveData;

        // 获得当前对象自有属性值
        $ownProperty = static::getOwnPropertyVal();

        // 判断是否需要获取 $operate_staff_id_history 操作人历史id
        $needStaffIdHistory = true;

        // 2 [默认]当前对象有这个字段就获取或只有调用的地方会用到
        // 且 没有有操作员工历史id 字段 operate_staff_id_history
        if( ($operate_type & 2) == 2 && ($ownProperty & 4) != 4) $needStaffIdHistory = false;

        // 进行优化 ：如果传入值　<= 0 且 源数据是一维数组  且有 历史下标值 且值 > 0 , 则直接可以使用此值，不用再去查询了
        if($operate_staff_id_history <= 0 && isset($saveData['operate_staff_id_history']) && is_numeric($saveData['operate_staff_id_history']) && $saveData['operate_staff_id_history'] > 0) $operate_staff_id_history = $saveData['operate_staff_id_history'];

        if ($needStaffIdHistory && $operate_staff_id_history <= 0) $operate_staff_id_history = static::getStaffHistoryId($operate_staff_id);

        // 加入操作人员信息
        $oprateArr = [
            // 'operate_staff_id' => $operate_staff_id,// $controller->operate_staff_id,
            // 'operate_staff_id_history' => $operate_staff_id_history,// $controller->operate_staff_id_history,
        ];
        // 2：有操作员工id 字段 operate_staff_id
        if( ($ownProperty & 2) == 2) $oprateArr['operate_staff_id'] = $operate_staff_id;
        // 4：有操作员工历史id 字段 operate_staff_id_history
        if( ($ownProperty & 4) == 4) $oprateArr['operate_staff_id_history'] = $operate_staff_id_history;

        // 为空值，直接返回
        if(empty($oprateArr)) return $saveData;

        $isMultiArr = false; // true:二维;false:一维
        foreach($saveData as $k => $v){
            if(is_array($v)){
                $isMultiArr = true;
            }
            break;
        }
        if($isMultiArr){ //二维

            foreach($saveData as $k => $v){
                $v = array_merge($v, $oprateArr);
                $saveData[$k] = $v;
            }
        }else{// 一维
            $saveData = array_merge($saveData, $oprateArr);
        }
        return $saveData;
    }

    // 判断权限-----开始
    // 判断权限 ,返回当前记录[可再进行其它判断], 有其它主字段的，可以重新此方法
    /**
     * 判断权限-- 注意用具体的**DBBusiness来调
     *
     * @param int $id id ,多个用,号分隔 为0或''时，可以用条件参数$relations来查询
     * @param array $judgeArr 需要判断的下标[字段名]及值 一维数组
     * @param int $companyId 企业id
     * @param array $otherWhere 其它查询条件 --二维数组
     * @param json/array $relations 要查询的与其它表的关系
        [
            //  ['company_id', $company_id],
            [$fieldName,$fieldVal],
            // ['admin_type',self::$admin_type],
        ]
     * @return array 一维数组[单条] 二维数组[多条]
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePower($id = 0, $judgeArr = [] , $company_id = '', $otherWhere = [], $relations = ''){
        // $this->InitParams($request);
//        if(empty($model_name)){
//            $model_name = $this->model_name;
//        }
        $dataList = [];
        $isSingle = true;// 是否单条记录 true:是;false：否
        if (strpos($id, ',') === false) { // 单条
            // 获得当前记录
            // $dataList[] =  static::getinfoApi($model_name, '', $relations, $company_id , $id, $notLog);
            if($id != '' &&  $id!= 0){
                $dataList[] =  static::getInfo($id, [], $relations);
            }else{
                $queryParams =  [
                    'where' => [
                        //['company_id', $company_id],
                        //['mobile', $keyword],
                    ],
//                    'select' => [
//                        'id','company_id','type_name','sort_num'
//                    ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
                $dataList[] = static::getInfoByQuery(1, $queryParams, $relations);
            }

        }else{
            $isSingle = false;
            $queryParams =  [
                'where' => [
                    //['company_id', $company_id],
                    //['mobile', $keyword],
                ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
                // 'orderBy' => ['id'=>'desc'],
            ];
//            if($company_id != ''){
//                array_push($queryParams['where'],['company_id', $company_id]);
//            }
            $ids = explode(',',$id);
            foreach($ids as $k => $tem_id){
                if($id == '' &&  $id == 0) unset($ids[$k]);
            }
            if(!empty($ids))  $queryParams['whereIn']['id'] = $ids;

            if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
            // $dataList = static::ajaxGetAllList($model_name, [], $company_id,$queryParams ,$relations, $notLog );
            $dataList = static::getAllList($queryParams, $relations);
        }
        foreach($dataList as $infoData){
            static::judgePowerByObj($infoData, $judgeArr);
        }
        return $isSingle ? $dataList[0] : $dataList;
    }

    public static function judgePowerByObj($infoData, $judgeArr = [] ){
        if(empty($infoData)){
            // throws('记录不存!', $this->source);
            throws('记录不存!');
        }
        foreach($judgeArr as $field => $val){
            if(!isset($infoData[$field])){
                // throws('字段[' . $field . ']不存在!', $this->source);
                throws('字段[' . $field . ']不存在!');
            }
            if( $infoData[$field] != $val ){
                // throws('没有操作此记录权限!信息字段[' . $field . ']', $this->source);
                throws('没有操作此记录权限!信息字段[' . $field . ']');
            }
        }
    }

    // 判断权限-----结束


    /**
     * 根据id获得详情及history id信息; 有历史功能的主表使用，注意在具体的类中需要定义 getIdHistory 方法，才能正常使用
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param string $relations 关系数组/json字符
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoHistoryId($company_id, &$id, $operate_staff_id = 0, $relations = [])
    {
        if(!is_array($relations))  $relations = [];
        $info = [];
        if(!empty($relations)){
            $info = static::getInfo($id, [], $relations);
        }

        $mainDBObj = null ;
        $historyDBObj = null ;
        $historyId = static::getIdHistory($id, $mainDBObj, $historyDBObj);
        if(empty($info)) $info = $mainDBObj;
        $info['history_id'] = $historyId ;
        $info['now_state'] = 0;// 最新的试题 0没有变化 ;1 已经删除  2 试卷不同
        return $info;
    }

    /**
     * 保存图片资源关系
     *
     * @param int  $company_id 企业id
     * @param int $id 主表记录id
     * @param array $resourceIds 关系表id数组
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史 默认 0
     * @param array $otherData 其它参数数组 - 一维
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveResourceSync($id, $resourceIds = [], $operate_staff_id = 0, $operate_staff_id_history = 0, $otherData = []){
        // 加入company_id字段
        $syncResourceArr = [];
        $temArr =  [
            // 'company_id' => $company_id,
//                    'operate_staff_id' => $operate_staff_id,
//                    'operate_staff_id_history' => $operate_staff_id_history,
        ];
        $temArr = array_merge($temArr, $otherData);// 其它参数合并进来
         // 加入操作人员信息
        static::addOprate($temArr, $operate_staff_id,$operate_staff_id_history, 1);
        foreach($resourceIds as $resourceId){
            if(empty($resourceId)) continue;
            $syncResourceArr[$resourceId] = $temArr;
            // 资源id 历史 resource_id_history
            $syncResourceArr[$resourceId]['resource_id_history'] = ResourceDBBusiness::getIdHistory($resourceId);
        }
        // 为空，则是移除关系
        if(empty($syncResourceArr)){// 解除关系
            $syncParams =[
                'siteResources' => $syncResourceArr,//标签
            ];
            return static::detach($id, $syncParams);
        }else{// 绑定关系
            $syncParams =[
                'siteResources' => $syncResourceArr,//标签
            ];
            return static::sync($id, $syncParams);
        }
    }

    /**
     * 删除图片资源关系
     *
     * @param int  $company_id 企业id
     * @param int $id 主表记录id
     * @param array $resourceIds 关系表id数组  空：移除所有，id数组：移除指定的
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function delResourceDetach($id, $resourceIds = []){
        $syncParams =[
            'siteResources' => $resourceIds,//标签
        ];
        return static::detach($id, $syncParams);
    }

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~开始~~~~~~~~~~~~~~~~~~~

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
//        return static::getHistoryId($mainDBObj, $mainId, CityHistoryDBBusiness::$model_name
//            , CityHistoryDBBusiness::$table_name, $historyDBObj, ['city_table_id' => $mainId], []);
        return 0;
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param mixed $mId 主表对象主键值
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistory($id = 0, $forceIncVersion = 0, &$mainDBObj = null, &$historyDBObj = null){
        // 判断版本号是否要+1
        $historySearch = [
            //  'company_id' => $company_id,
            'city_table_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        // return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CityHistoryDBBusiness::$model_name
        //    , CityHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['city_table_id'], $forceIncVersion);
        return [];
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        DB::beginTransaction();
        try {
            $isModify = false;

            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * replaceById 方法操作前可执行的一些操作,数据判重等----具体的表，如有需要，请重写此方法
     *
     * @param array $saveData 请求的原数据
     * @param int  $company_id 企业id
     * @param int $id id 成功后返回的id
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPIPre(&$saveData = [], $company_id = 0, $id = 0, $operate_staff_id = 0, &$operate_staff_id_history = 0, $modifAddOprate = 0){

    }

    /**
     * replaceById 方法操作成功后可执行的一些操作----具体的表，如有需要，请重写此方法
     *
     * @param boolean $isModify true:修改操作； false:新加操作
     * @param object $modelObj 主数据对象
     * @param array $saveData 请求的原数据
     * @param int  $company_id 企业id
     * @param int $id id 成功后返回的id
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPISucess($isModify = false, $modelObj = null, $saveData = [], $company_id = 0, $id = 0, $operate_staff_id = 0, $operate_staff_id_history = 0, $modifAddOprate = 0){

    }

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~结束~~~~~~~~~~~~~~~~~~~

    /**
     * 生成单号
     *
     * @param int $company_id 企业id
     * @param int $user_id 当前用户
     * @param int  $orderType 要保存或修改的数组 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金
     * @return  int
     * @author zouyan(305463219@qq.com)
     */
    public static function createSn($company_id , $user_id, $orderType = 1){
        // $company_id = $controller->company_id;
        // $user_id = $controller->user_id ?? '';
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
}
