<?php
// 帐号管理
namespace App\Business\DB\QualityControl;

use App\Services\Map\Map;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class StaffDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Staff';
    public static $table_name = 'staff';// 表名称


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
            'staff_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, StaffHistoryDBBusiness::$model_name
            , StaffHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['staff_id'], $forceIncVersion);
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     *   operate_type 可有 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班 8 修改：如更新接单人员经纬度[频繁]
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
            throws('真实姓名不能为空！');
        }

//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        if(isset($saveData['admin_username']) && empty($saveData['admin_username'])  ){
            throws('用户名不能为空!！');
        }

        // 是否批量操作标识 true:批量操作； false:单个操作 ---因为如果批量操作，有些操作就不能每个操作都执行，也要批量操作---为了运行效率
        // 有此下标就代表批量操作
        $isBatchOperate = false;
        if(isset($saveData['isBatchOperate'])){
            $isBatchOperate = true;
            unset($saveData['isBatchOperate']);
        }

        $staffExtendData = [];// 人员扩展信息--企业和个人会有一条记录
        $companyBillingConfigData = [];// 企业开票配置信息---企业和个人会有一条记录
        if(isset($saveData['staff_extend']) ){
            $staffExtendData = $saveData['staff_extend'];
            if(!is_array($staffExtendData)) $staffExtendData = [];
            unset($saveData['staff_extend']);
        }
        if(isset($saveData['company_billing_config']) ){
            $companyBillingConfigData = $saveData['company_billing_config'];
            if(!is_array($companyBillingConfigData)) $companyBillingConfigData = [];
            unset($saveData['company_billing_config']);
        }


        $operateType = $saveData['operate_type'] ?? 0;// 操作类型 1 提交申请修改信息-不用 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班-不用 7 下班-不用 8 修改-不用：如更新接单人员经纬度[频繁]
        if(isset($saveData['operate_type'])) unset($saveData['operate_type']);

        // 如果有经纬度信息
        /**
         *
        if(isset($saveData['latitude'])){
            $latitude = $saveData['latitude'] ?? ''; // 纬度
            $longitude = $saveData['longitude'] ?? ''; // 经度
//            if($latitude == '' || $longitude == '' || ($latitude == '0' && $longitude == '0') ){
//                throws('经纬度不能为空！');
//            }
            $hashs = Map::getGeoHashs($latitude, $longitude);
            $saveData['geohash'] = $hashs[0] ?? '';
            $saveData['geohash3'] = $hashs[3] ?? '';
            $saveData['geohash4'] = $hashs[4] ?? '';
            $saveData['geohash5'] = $hashs[5] ?? '';
            if(!is_numeric($latitude)) $latitude = 0;
            if(!is_numeric($longitude)) $longitude = 0;
            $saveData['lat'] = $latitude;
            $saveData['lng'] = $longitude;
        }
         *
         */

        // 查询手机号是否已经有企业使用--账号表里查
        // if( isset($saveData['mobile']) && (!empty($saveData['mobile'])) && static::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [], 1)){
        //     throws('手机号已存在！');
        // }
        // 用户名--唯一
        if( isset($saveData['admin_username']) && static::judgeFieldExist($company_id, $id ,"admin_username", $saveData['admin_username'], [],1)){
            throws('用户名已存在！');
        }
        // 相同的用户类型，手机号唯一
        if( isset($saveData['mobile'])){
            // 修改手机号时---必须要有 admin_type  拥有者类型1平台2老师4学生
            $admin_type = $saveData['admin_type'] ?? '';
            if(!is_numeric($admin_type) || !in_array($admin_type, [1,2,4])) throws('用户类型参数有误！');
        }

        if( isset($saveData['mobile']) && static::judgeFieldExist($company_id, $id ,"mobile", $saveData['mobile'], [['admin_type', $saveData['admin_type']]],1)){
            throws('手机号已存在！');
        }

        // 是否有图片资源
        $hasResource = false;

        $resourceIds = [];
        $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        if(isset($saveData['resourceIds'])){
            $hasResource = true;
            $resourceIds = $saveData['resourceIds'];
            unset($saveData['resourceIds']);
        }
        if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        if(isset($saveData['resource_id']))  unset($saveData['resource_id']);


        DB::beginTransaction();
        try {

//            // 省id历史
//            if( isset($saveData['province_id']) && $saveData['province_id'] > 0 ){
//                $saveData['province_id_history'] = CityDBBusiness::getIdHistory($saveData['province_id']);
//            }
//            // 市id历史
//            if( isset($saveData['city_id']) && $saveData['city_id'] > 0 ){
//                $saveData['city_id_history'] = CityDBBusiness::getIdHistory($saveData['city_id']);
//            }
//            // 区县id历史
//            if( isset($saveData['area_id']) && $saveData['area_id'] > 0 ){
//                $saveData['area_id_history'] = CityDBBusiness::getIdHistory($saveData['area_id']);
//            }

            $isModify = false;
            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

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
//                 if($modifAddOprate) static::addOprate($saveData, $operate_staff_id, $operate_staff_id_history, 1);
//                if(!in_array($operateType, [8])) static::addOprate($saveData, $operate_staff_id, $operate_staff_id_history, 1);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
//                if(!in_array($operateType, [8])) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }
            $logCount = '';
            // 6 上班
//            if($operateType == 6) $saveData['on_time'] = date("Y-m-d H:i:s",time());
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;
                $resultDatas = static::getInfo($id);
                $logCount = '新加';
            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                $resultDatas = static::getInfo($id);
                $logCount = '修改';
            }
            // 同步修改图片资源关系
//            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 2, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
//            }
            if($hasResource){
                // 查询当前企业的营业执照记录id
                $queryCompanyCertificateParams = [
                    'where' => [
                        'company_id' => $id,
                        'type_id' => 5,
                        //['admin_type',self::$admin_type],
                    ],
                    //            'select' => [
                    //                'id','company_id','type_name','sort_num'
                    //                //,'operate_staff_id','operate_staff_history_id'
                    //                ,'created_at'
                    //            ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                $infoCompanyCertificateData = CompanyCertificateDBBusiness::getInfoByQuery(1, $queryCompanyCertificateParams, []);
                // if(is_object($infoData))  $infoData = $infoData->toArray();
                // 记录不存在，是新加，则必须要用帐号和密码
                $companyCertificateId = $infoCompanyCertificateData['id'] ?? 0;
                // 修改营业执照
                $companyCertificateData = [
                    'company_id' => $id,
                    'type_id' => 5,
                    'resource_id' => $resourceIds[0] ?? 0,// 第一个图片资源的id
                    'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                    'resourceIds' => $resourceIds,// 此下标为图片资源关系
                    'operate_staff_id' => $operate_staff_id,
                    'operate_staff_id_history' => $operate_staff_id_history,
                ];
                CompanyCertificateDBBusiness::replaceById($companyCertificateData, $company_id, $companyCertificateId, $operate_staff_id, $modifAddOprate);
            }

//            if($isModify && !in_array($operateType, [8]) && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
//                static::compareHistory($id, 1);
//            }

            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            // $operateType = $saveData['operate_type'] ?? 0;// 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻  6 上班 7 下班 8 修改：如更新接单人员经纬度[频繁]
//            $city_site_id = $resultDatas->city_site_id;
//            $on_line = $resultDatas->on_line;// 是否上班 1下班2上班
//            switch ($operateType)
//            {
////                case 1://  1 提交申请修改信息 ;
////                    $logCount = '修改信息，提交审核';
////                    if($on_line == 2)  StaffRecordOnlineDBBusiness::saveRecord($id, $city_site_id, $operate_staff_id , $operate_staff_id_history, 1, '修改信息，提交审核：下班');
////                    break;
//                case 2:// 2 审核通过
//                    $logCount = '审核通过';
//                    break;
//                case 3://  3 审核不通过
//                    $logCount = '审核不通过';
//                    if(isset($saveData['open_fail_reason'])) $logCount .= '；原因:' .  $saveData['open_fail_reason'];
//                    break;
//                case 4:// 4 冻结
//                    $logCount = '冻结';
//                    if(isset($saveData['frozen_fail_reason'])) $logCount .= '；原因:' .  $saveData['frozen_fail_reason'];
////                    if($on_line == 2)  StaffRecordOnlineDBBusiness::saveRecord($id, $city_site_id, $operate_staff_id , $operate_staff_id_history, 1, '冻结操作：下班');
//                    break;
//                case 5:// 5 解冻
//                    $logCount = '解冻';
//                    break;
////                case 6://  6 上班
////                    $logCount = '上班';
////                    StaffRecordOnlineDBBusiness::saveRecord($id, $city_site_id, $operate_staff_id , $operate_staff_id_history, 2, $logCount);
////                    break;
////                case 7:// 7 下班
////                    $logCount = '下班';
////                    StaffRecordOnlineDBBusiness::saveRecord($id, $city_site_id, $operate_staff_id , $operate_staff_id_history, 1, $logCount);
////                    break;
//                case 8:// 8 修改：如更新接单人员经纬度[频繁]
//                    break;
//                default:
//            }
            // if(is_numeric($operateType) && $operateType > 0);
//            if(!in_array($operateType, [8])) StaffRecordDBBusiness::saveLog($id , $operate_staff_id , $operate_staff_id_history, $logCount);// 保存操作记录

            // 如果是新加，则记录注册记录
            if(!$isModify){
                $currentNow = Carbon::now();
                $regLogData = [
                    'admin_type' => $resultDatas['admin_type'],
                    'staff_id' => $id,
                    'log_at' => date('Y-m-d H:i:s'),
                    'count_date' => $currentNow->toDateString(),
                    'count_year' => $currentNow->year,
                    'count_month' => $currentNow->month,
                    'count_day' => $currentNow->day,
                ];
                $regLogId = 0;
                RegLogDBBusiness::replaceById($regLogData, $company_id, $regLogId, $operate_staff_id, $modifAddOprate);
                // 是企业或个人
                if(in_array($resultDatas['admin_type'], [2, 4])){
                    // 人员扩展信息
                    $staffExtendData = array_merge($staffExtendData, [
                        'admin_type' => $resultDatas['admin_type'],
                        'staff_id' => $resultDatas['id'],
                    ]);
                    $staffExtendId = 0;
                    StaffExtendDBBusiness::replaceById($staffExtendData, $company_id, $staffExtendId, $operate_staff_id, $modifAddOprate);

                    // 企业开票配置信息
                    // 暂时只有企业有
                    if($resultDatas['admin_type'] == 2){
                        $companyBillingConfigData = array_merge($companyBillingConfigData, [
                            'admin_type' => $resultDatas['admin_type'],
                            'staff_id' => $resultDatas['id'],
                        ]);
                        $companyBillingConfigId = 0;
                        CompanyBillingConfigDBBusiness::replaceById($companyBillingConfigData, $company_id, $companyBillingConfigId, $operate_staff_id, $modifAddOprate);
                    }

                }
                // 如果是新加人员，且人员有所属企业，所需要更新企业员工数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                if(!$isBatchOperate && $resultDatas['admin_type'] == 4 && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                    static::updateStaffNum($resultDatas['company_id']);
                }
            }
            // 如果是修改信息
            if($isModify){

            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $resultDatas;
    }

    /**
     * 小程序  根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
//    public static function replaceByIdWX($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0)
//    {
//        if (isset($paramsData['mini_openid']) && empty($paramsData['mini_openid'])) {
//            throws('小程序 openid不能为空！');
//        }
//
//        // 查询存在的 mini_openid
//        if(is_numeric($id) &&  $id <= 0 &&  isset($saveData['mini_openid']) ){
//            $otherWhere = [];
//            if(isset($saveData['admin_type'])  && $saveData['admin_type'] > 0 ) array_push($otherWhere, ['admin_type', $saveData['admin_type']]) ;
//            $wx_unionid = $saveData['wx_unionid'] ?? '';
//            $wx_unionid = trim($wx_unionid);
//            if(!empty($wx_unionid)){
//                $temOtherWhere = $otherWhere;
//
//                // if( isset($saveData['wx_unionid']) ) array_push($temOtherWhere, ['wx_unionid', $saveData['wx_unionid']]);
//                $info = static::judgeFieldExist($company_id, 0 ,"wx_unionid", $wx_unionid
//                    , $temOtherWhere,2);
//
//                // 如果是空，则按mini_openid再查一下
//                if( empty($info) ){
//                    array_push($temOtherWhere, ['wx_unionid', '']);// 是空的，也要加，因为索引
//                    $info = static::judgeFieldExist($company_id, 0 ,"mini_openid", $saveData['mini_openid']
//                        , $temOtherWhere,2);
//                }
//
//            }else{// 为空
//                // if( isset($saveData['wx_unionid']) ) array_push($otherWhere, ['wx_unionid', $saveData['wx_unionid']]);// 是空的，也要加，因为索引
//                array_push($otherWhere, ['wx_unionid', '']);// 是空的，也要加，因为索引
//                $info = static::judgeFieldExist($company_id, 0 ,"mini_openid", $saveData['mini_openid']
//                    , $otherWhere,2);
//            }
//
//            if(!empty($info)) $id = $info['id'];
//        }
//
//        if($id <= 0 && isset($saveData['admin_type']) && $saveData['admin_type'] == 32){
//            $saveData['open_status'] = 1;// 审核状态1待审核2审核通过3审核未通过--32快跑人员用
//            // 如果是app登录
//            if($saveData['admin_type'] == 32){
//                $nickName = $saveData['nickname'] ?? '';
//                if ( empty($nickName) ) throws('新用户昵称不能为空！');
//                // if (isset($saveData['avatar_url']) && empty($saveData['avatar_url'])) throws('新用户头像不能为空！');
//            }
//        }
//        $res = static::replaceById($saveData, $company_id,$id, $operate_staff_id, $modifAddOprate);
//        return $res;
//    }

    /**
     * 更新企业有效的员工数
     *
     * @param int  / array $company_ids 企业id  多个时为一维数组或逗号分隔的字符串
     * @param int  $count_type 统计的类型 1 所有的【默认】； 2 正常的【不含冻结的】
     * @return  mixed 员工人数
     * @author zouyan(305463219@qq.com)
     */
    public static function updateStaffNum($company_ids = 0, $count_type = 1){
        // 没有需要处理的
        if(!Tool::formatOneArrVals($company_ids)) return true;
        // 更新企业的员工人数
        DB::beginTransaction();
        try {
            foreach($company_ids as $company_id){
                $staffCount = static::getStaffCount($company_id, $count_type);

                $updateFields = [
                    'staff_num' => $staffCount,
                ];
                $searchConditon = [
                    'admin_type' => 2,
                    'staff_id' => $company_id,
                ];
                $mainObj = null;
                StaffExtendDBBusiness::firstOrCreate($mainObj, $searchConditon, $updateFields );
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * 根据企业id,获得企业的员工人数
     *
     * @param int  $company_id 企业id
     * @param int  $count_type 统计的类型 1 所有的【默认】； 2 正常的【不含冻结的】
     * @return  mixed 员工人数
     * @author zouyan(305463219@qq.com)
     */
    public static function getStaffCount($company_id = 0, $count_type = 1){
        // 更新班级老师人数
        $queryParams = [
            'where' => [
                ['company_id', $company_id],
                ['admin_type', 4],
//                ['open_status', 2],
//                ['account_status',1],
            ],
            'count' => 0,
            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
        ];
        if(($count_type & 2) == 2){
            if(!isset($queryParams['where'])) $queryParams['where'] = [];
             array_push($queryParams['where'], ['account_status', 1]) ;
        }
        $staffCount = static::getAllList($queryParams, []);
        return $staffCount;
    }

    /**
     * 根据id删除--可批量删除
     * 删除员工--还需要重新统计企业的员工数
     * 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $admin_type 类型1平台2企业4个人-- 要操作的数据的
     * @param int $organize_id 操作的企业id 可以为0：不指定具体的企业
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $admin_type = 0, $organize_id = 0, $modifAddOprate = 0){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $dataListArr = [];
        $organizeIds = [];
        $staffIds = [];

         // 获得需要删除的数据
        if($admin_type == 2 || $admin_type == 4){

            $queryParams = [
                'where' => [
//                ['company_id', $organize_id],
                ['admin_type', $admin_type],
//                ['teacher_status',1],
                ],
                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
            ];
            Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($queryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);

            $dataListObj = static::getAllList($queryParams, []);
            // $dataListObj = static::getListByIds($id);

            $dataListArr = $dataListObj->toArray();
            if(empty($dataListArr)) throws('没有需要删除的数据');
            // 用户删除要用到的
            if($admin_type == 4) $organizeIds = array_values(array_unique(array_column($dataListArr,'company_id')));
            // 企业删除要用到的
            if($admin_type == 2) $staffIds = array_values(array_unique(array_column($dataListArr,'id')));
            // 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
            if($admin_type == 2) {
                $formatDataList = [];
                foreach($staffIds as $temStaffId){
                    // 获得企业的员工数量
                    $staffCount = static::getStaffCount($temStaffId, 1);
                    if(!is_numeric($staffCount) || $staffCount > 0){
                        // 有必要才格式化数据
                        if(empty($formatDataList)) $formatDataList = Tool::arrUnderReset($dataListArr, 'id', 1);
                        throws('企业【' . ($formatDataList[$temStaffId]['company_name'] ?? '') . '】有员工【' . $staffCount . '人】，不可进删除操作！<br/>如果确实要删除，请先删除或解绑员工！');
                    }

                }
            }
        }

        DB::beginTransaction();
        try {

            // 删除主记录
            $delQueryParams = [
                'where' => [
                    ['admin_type', $admin_type],
                    ['issuper','<>', 1],
                ],
            ];
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if($admin_type == 4 && !empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更企业员工人数
                    static::updateStaffNum($organizeId);;
                }
            }
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return $id;
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param int  $organize_id 用户的企业id , 1平台2企业 : 都为0  4个人：可能是所属企业；0没有所属企业
     * @param int  $admin_type 类型1平台2企业4个人
     * @param array $saveData 要导入的数组 -- 二维数组
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importStaffs($organize_id, $admin_type, $saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0){
        if(in_array($admin_type, [1, 2])) $organize_id = 0;
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        $selModelObj = static::getModelObj();
        // 拥有者类型1平台2企业4个人
        $adminTypeArr = Tool::getAttr($selModelObj, 'adminTypeArr', 0);
        // 是否完善资料1待完善2已完善
        $isPerfectArr = Tool::getAttr($selModelObj, 'isPerfectArr', 0);
        // 是否超级帐户2否1是
        $issuperArr = Tool::getAttr($selModelObj, 'issuperArr', 0);
        // 审核状态1待审核2审核通过4审核不通过
        $openStatusArr = Tool::getAttr($selModelObj, 'openStatusArr', 0);
        // 状态 1正常 2冻结
        $accountStatusArr = Tool::getAttr($selModelObj, 'accountStatusArr', 0);
        // 性别0未知1男2女
        $sexArr = Tool::getAttr($selModelObj, 'sexArr', 0);
        // 企业--是否独立法人1独立法人 2非独立法人
        $companyIsLegalPersionArr = Tool::getAttr($selModelObj, 'companyIsLegalPersionArr', 0);
        // 企业--企业类型1检测机构、2生产企业
        $companyTypeArr = Tool::getAttr($selModelObj, 'companyTypeArr', 0);
        // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、
        //  9其它机构、10民办非企业单位、11个体 、12工会法人
        $companyPropArr = Tool::getAttr($selModelObj, 'companyPropArr', 0);
        // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
        $companyPeoplesNumArr = Tool::getAttr($selModelObj, 'companyPeoplesNumArr', 0);
        // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
        $companyGradeArr = Tool::getAttr($selModelObj, 'companyGradeArr', 0);
        if(!in_array($admin_type, array_keys($adminTypeArr))) throws('参数[admin_type]有误！');


        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        // 新加时，加入特定的值---如果没有此下标是，默认的一维数组
        $addArr = [
            'admin_type' => $admin_type,
            'company_id' => $organize_id,
            'is_perfect' => 2,
            'issuper' => 2,
            'company_is_legal_persion' => 0,// 企业--是否独立法人1独立法人 2非独立法人
            'company_type' => 0,// 企业类型1检测机构、2生产企业
            'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
        ];
        // 如果是超级管理员，不能改的值
        $superDataArr = [
            'is_perfect' => 2,// 是否完善资料1待完善2已完善
            'issuper' => 1,// 是否超级帐户2否1是
            'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
            'account_status' => 1,// 状态 1正常 2冻结
        ];
        // 对数据有效性进行校验
        $errsArr = [];// 错误数组
        $saveArr = [];// 最终可以保存的数据
        foreach($saveData as $k => $info) {
            $recordErrText = '';
            switch($admin_type){
                case 1:
                    $real_name = $info['real_name'] ?? '';// 用户名
                    if(empty($real_name)) $recordErrText .= '姓名不能为空!<br/>';
                    $sex = $info['sex'] ?? '';// 性别
                    $sex = array_search($sex, $sexArr);
                    if($sex === false) $recordErrText .= '性别有效值[' . implode('、', $sexArr) . ']!<br/>';// $sex_id = 0;
                    $mobile = $info['mobile'] ?? '';
                    $tel = $info['tel'] ?? '';
                    $qq_number = $info['qq_number'] ?? '';
                    $admin_username = $info['admin_username'] ?? '';
                    $admin_password = $info['admin_password'] ?? '';
                    $open_status = $info['open_status'] ?? '';
                    $open_status = array_search($open_status, $openStatusArr);
                    if($open_status === false) $recordErrText .= '审核状态有效值[' . implode('、', $openStatusArr) . ']!<br/>';
                    $account_status = $info['account_status'] ?? '';
                    $account_status = array_search($account_status, $accountStatusArr);
                    if($account_status === false) $recordErrText .= '冻结状态有效值[' . implode('、', $accountStatusArr) . ']!<br/>';
                    $valiDateParam = [
                        ["var_name" => "real_name" ,"input" => $real_name,"require"=>"true","validator"=>"length","min"=>"1","max"=>"20","message"=>'用户名长度为1~ 20个字符！'],
                        // ["var_name" => "mobile" ,"input" => $mobile,"require"=>"true", "validator"=>"", "message"=>'手机不能为空！'],
                        ["var_name" => "mobile" ,"input"=>$mobile,"require"=>"true","validator"=>"mobile","message"=>'手机号格式有误！'],
//                        ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！'],
//                        ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！'],
                        ["var_name" => "tel" ,"input" => $tel,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'座机电话长度为6~ 20个字符！'],
                        ["var_name" => "qq_number" ,"input" => $qq_number,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'QQ\email\微信长度为6~ 20个字符！'],
                    ];
                    if(!empty($admin_username)) array_push($valiDateParam, ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！']);
                    if(!empty($admin_password)) array_push($valiDateParam, ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！']);

                    $errMsgArr = Tool::dataValid($valiDateParam, 2);
                    if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
                        $recordErrText .= implode('<br/>', $errMsgArr['errMsg']);
                        array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                        continue 2;
                    }
                    // 没有错；判断记录是否已经存在
                    // 查
                    $queryParams = [
                        'where' => [
                            // ['id', '&' , '4=4'],
                            ['admin_type', '=' ,$admin_type],
                            // ['class_id', '=' ,$class_id],
                           // ['student_number', $student_number],
                            ['mobile', $mobile],
                            //['admin_type',self::$admin_type],
                        ],
                        //            'select' => [
                        //                'id','company_id','type_name','sort_num'
                        //                //,'operate_staff_id','operate_staff_history_id'
                        //                ,'created_at'
                        //            ],
                        // 'orderBy' => ['id'=>'desc'],
                    ];
                    $tem_organize_id = $organize_id;
                    Tool::appendParamQuery($queryParams, $tem_organize_id, 'company_id', [0, '0', ''], ',', false);
                    $infoData = static::getInfoByQuery(1, $queryParams, []);
                    // if(is_object($infoData))  $infoData = $infoData->toArray();
                    // 记录不存在，是新加，则必须要用帐号和密码
                    $id = $infoData['id'] ?? 0;
                    $temSaveArr = [
                        'id' => $id,
                        'admin_type' => $admin_type,
                        'company_id' => $organize_id,
                        'real_name' => $real_name,
                        'sex' => $sex,
                        'mobile' => $mobile,
                        'tel' => $tel,
                        'qq_number' => $qq_number,
//                        'admin_username' => $admin_username,
//                        'admin_password' => $admin_password,
                        'open_status' => $open_status,
                        'account_status' => $account_status,
                    ];
                    if(!empty($admin_username)) $temSaveArr['admin_username'] = $admin_username;
                    if(!empty($admin_password)) $temSaveArr['admin_password'] = $admin_password;
                    // 记录不存在--新加
                    if(empty($infoData)){//  || count($infoData)<=0
                        // 新加必须有帐号信息
                        $valiDateParam = [
                            ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！'],
                            ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！'],
                        ];
                        $errMsgArr = Tool::dataValid($valiDateParam, 2);
                        if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
                            $recordErrText .= implode('<br/>', $errMsgArr['errMsg']);
                            array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                            continue 2;
                        }
                        // 手机号不能重
                        if( static::judgeFieldExist($company_id, $id ,"mobile", $mobile, [['admin_type', $admin_type]],1)){
                            $recordErrText .= '手机号已存在！';
                            array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                            continue 2;
                        }
                        // 新加时，加入特定的值
                        $temSaveArr = array_merge($addArr, $temSaveArr);

                    }else{
                        // 如果是超级管理员，则不能修改某些数据
                        $issuper = $infoData['issuper'] ?? 2;
                        if($issuper == 1)  $temSaveArr = array_merge($temSaveArr, $superDataArr);
                    }
                    // 且用户名不能重
                    // 用户名--唯一
                    if( !empty($admin_username) && static::judgeFieldExist($company_id, $id ,"admin_username", $admin_username, [],1)){
                        $recordErrText .= '用户名已存在！';
                        array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                        continue 2;
                    }
                    array_push($saveArr, $temSaveArr);

                    break;
                case 2:
                    $aaa = '';
                    break;
                case 4:
                    $real_name = $info['real_name'] ?? '';// 用户名
                    if(empty($real_name)) $recordErrText .= '姓名不能为空!<br/>';
                    $sex = $info['sex'] ?? '';// 性别
                    $sex = array_search($sex, $sexArr);
                    if($sex === false) $recordErrText .= '性别有效值[' . implode('、', $sexArr) . ']!<br/>';// $sex_id = 0;
                    $mobile = $info['mobile'] ?? '';
                    $email = $info['email'] ?? '';
                    $qq_number = $info['qq_number'] ?? '';
                    $id_number = $info['id_number'] ?? '';
                    $addr = $info['addr'] ?? '';
                    $admin_username = $info['admin_username'] ?? '';
                    $admin_password = $info['admin_password'] ?? '';
                    $open_status = $info['open_status'] ?? '';
                    $open_status = array_search($open_status, $openStatusArr);
                    if($open_status === false) $recordErrText .= '审核状态有效值[' . implode('、', $openStatusArr) . ']!<br/>';
                    $account_status = $info['account_status'] ?? '';
                    $account_status = array_search($account_status, $accountStatusArr);
                    if($account_status === false) $recordErrText .= '冻结状态有效值[' . implode('、', $accountStatusArr) . ']!<br/>';
                    $valiDateParam = [
                        ["var_name" => "real_name" ,"input" => $real_name,"require"=>"true","validator"=>"length","min"=>"1","max"=>"20","message"=>'用户名长度为1~ 20个字符！'],
                        // ["var_name" => "mobile" ,"input" => $mobile,"require"=>"true", "validator"=>"", "message"=>'手机不能为空！'],
                        ["var_name" => "mobile" ,"input"=>$mobile,"require"=>"true","validator"=>"mobile","message"=>'手机号格式有误！'],
//                        ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！'],
//                        ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！'],
                        ["var_name" => "email" ,"input" => $email,"require"=>"false","validator"=>"email","message"=>'email格式有误！'],
                        ["var_name" => "qq_number" ,"input" => $qq_number,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'QQ\email\微信长度为6~ 20个字符！'],
                        ["var_name" => "id_number" ,"input" => $id_number,"require"=>"false","validator"=>"length","min"=>"14","max"=>"20","message"=>'身份证号长度为14~ 20个字符！'],
                        ["var_name" => "addr" ,"input" => $addr,"require"=>"false","validator"=>"length","min"=>"2","max"=>"100","message"=>'身份证号长度为2~ 100个字符！'],
                    ];
                    if(!empty($admin_username)) array_push($valiDateParam, ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！']);
                    if(!empty($admin_password)) array_push($valiDateParam, ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"false","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！']);

                    $errMsgArr = Tool::dataValid($valiDateParam, 2);
                    if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
                        $recordErrText .= implode('<br/>', $errMsgArr['errMsg']);
                        array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                        continue 2;
                    }
                    // 没有错；判断记录是否已经存在
                    // 查
                    $queryParams = [
                        'where' => [
                            // ['id', '&' , '4=4'],
                            ['admin_type', '=' ,$admin_type],
                            // ['class_id', '=' ,$class_id],
                            // ['student_number', $student_number],
                            ['mobile', $mobile],
                            //['admin_type',self::$admin_type],
                        ],
                        //            'select' => [
                        //                'id','company_id','type_name','sort_num'
                        //                //,'operate_staff_id','operate_staff_history_id'
                        //                ,'created_at'
                        //            ],
                        // 'orderBy' => ['id'=>'desc'],
                    ];
                    $tem_organize_id = $organize_id;
                    Tool::appendParamQuery($queryParams, $tem_organize_id, 'company_id', [0, '0', ''], ',', false);
                    $infoData = static::getInfoByQuery(1, $queryParams, []);
                    // if(is_object($infoData))  $infoData = $infoData->toArray();
                    // 记录不存在，是新加，则必须要用帐号和密码
                    $id = $infoData['id'] ?? 0;
                    $temSaveArr = [
                        'id' => $id,
                        'admin_type' => $admin_type,
                        'company_id' => $organize_id,
                        'real_name' => $real_name,
                        'sex' => $sex,
                        'mobile' => $mobile,
                        'email' => $email,
                        'qq_number' => $qq_number,
                        'id_number' => $id_number,
                        'addr' => $addr,
//                        'admin_username' => $admin_username,
//                        'admin_password' => $admin_password,
                        'open_status' => $open_status,
                        'account_status' => $account_status,
                    ];
                    if(!empty($admin_username)) $temSaveArr['admin_username'] = $admin_username;
                    if(!empty($admin_password)) $temSaveArr['admin_password'] = $admin_password;
                    // 记录不存在--新加
                    if(empty($infoData)){//  || count($infoData)<=0
                        // 新加必须有帐号信息
//                        $valiDateParam = [
//                            ["var_name" => "admin_username" ,"input" => $admin_username,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'用户名长度为6~ 20个字符！'],
//                            ["var_name" => "admin_password" ,"input" => $admin_password,"require"=>"true","validator"=>"length","min"=>"6","max"=>"20","message"=>'登录密码长度为6~ 20个字符！'],
//                        ];
//                        $errMsgArr = Tool::dataValid($valiDateParam, 2);
//                        if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
//                            $recordErrText .= implode('<br/>', $errMsgArr['errMsg']);
//                            array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
//                            continue 2;
//                        }
                        // 手机号不能重
                        if( static::judgeFieldExist($company_id, $id ,"mobile", $mobile, [['admin_type', $admin_type]],1)){
                            $recordErrText .= '手机号已存在！';
                            array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                            continue 2;
                        }
                        $temSaveArr = array_merge($addArr, $temSaveArr );

                    }else{
                        // 如果是超级管理员，则不能修改某些数据
                        $issuper = $infoData['issuper'] ?? 2;
                        if($issuper == 1)  $temSaveArr = array_merge($temSaveArr, $superDataArr);
                    }
                    // 且用户名不能重
                    // 用户名--唯一
                    if( !empty($admin_username) && static::judgeFieldExist($company_id, $id ,"admin_username", $admin_username, [],1)){
                        $recordErrText .= '用户名已存在！';
                        array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
                        continue 2;
                    }
                    array_push($saveArr, $temSaveArr);

                    break;
                default:
                    break;
            }
            if(!empty($recordErrText)){
                array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
            }
        }
        // 如果有错，则返回错误
        if(!empty($errsArr)) throws(implode('<br/>', $errsArr));
        // 对数据进行修改或新加
        foreach($saveArr as $k => $info){
            $id = $info['id'] ?? 0;
            if(isset($info['id'])) unset($info['id']);
            // 加入操作人员信息
            if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);

            // 新加或更新
            static::replaceById($info, $company_id, $id, $operate_staff_id, $modifAddOprate);
            array_push($returnIds, $id);

        }
        return $returnIds;
    }

    /**
     * 根据id审核通过或不通过单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int  $admin_type 类型1平台2企业4个人
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $open_status 操作 状态 2审核通过     4审核不通过
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  mixed array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function openStatusById($company_id, $admin_type = 0, $organize_id = 0, $id = 0, $open_status = 2, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($open_status, [2,4])) throws('参数【open_status】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        $updateData = [
            'open_status' => $open_status
        ];
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        DB::beginTransaction();
        try {
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);
            $saveQueryParams = [
                'where' => [
                    ['open_status', 1], // 自由点，让他都可以改 ，就注释掉
                    ['issuper', '<>' , 1],
                    ['admin_type', $admin_type],
                ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],

                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            // 加入 id
            Tool::appendParamQuery($saveQueryParams, $id, 'id');
            Tool::appendParamQuery($saveQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            $modifyNum = static::save($updateData, $saveQueryParams);
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return $modifyNum;
    }


    /**
     * 根据id审核通过或不通过单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int  $admin_type 类型1平台2企业4个人
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $account_status 操作 状态 1正常--解冻操作； 2冻结--冻结操作
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function accountStatusById($company_id, $admin_type = 0, $organize_id = 0, $id = 0, $account_status = 1, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($account_status, [1, 2])) throws('参数【account_status】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        $updateData = [
            'account_status' => $account_status
        ];
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        DB::beginTransaction();
        try {
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);
            $saveQueryParams = [
                'where' => [
                   //  ['account_status', 1],
                    ['admin_type', $admin_type],
                    ['issuper', '<>', 1],
                ],
//                            'select' => [
//                                'id','title','sort_num','volume'
//                                ,'operate_staff_id','operate_staff_id_history'
//                                ,'created_at' ,'updated_at'
//                            ],

                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
            ];
            $oldAccountStatus = 1;// -- 可以冻结状态
            // 解冻操作
            if($account_status == 1) $oldAccountStatus = 2; // --可以解冻状态
            Tool::appendParamQuery($saveQueryParams, $oldAccountStatus, 'account_status');
            // 加入 id
            Tool::appendParamQuery($saveQueryParams, $id, 'id');
            Tool::appendParamQuery($saveQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            // pr($saveQueryParams);
            $modifyNum = static::save($updateData, $saveQueryParams);
        } catch ( \Exception $e) {
            DB::rollBack();
//            throws('操作失败；信息[' . $e->getMessage() . ']');
            throws($e->getMessage());
        }
        DB::commit();
        return $modifyNum;
    }
}
