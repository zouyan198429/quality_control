<?php
// 所属企业检验检测机构资质认定证书附表
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\CertificateSchedule;
use App\Services\DB\CommonDB;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class CertificateScheduleDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CertificateSchedule';
    public static $table_name = 'certificate_schedule';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


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


        $certificate_info = [];
        // CMA证书号
        $certificate_no = [];
        $has_certificate_no = false;// 是否有 false:没有 ； true:有
        if(Tool::getInfoUboundVal($saveData, 'certificate_no', $has_certificate_no, $certificate_no, 0)){

            $certificate_info = [
                'company_id' => $saveData['company_id'],
                'certificate_no' => $certificate_no,
                'ratify_date' => $saveData['ratify_date'] ?? '',
                'valid_date' => $saveData['valid_date'] ?? '',
                'addr' => $saveData['addr'] ?? '',
            ];
        }
        // 修改时 需要强制更新员工数量
        $forceCompanyNum =  false;
        $force_company_num = '';
        $companyNumIds = [];// 需要更新的企业id数组
        if(Tool::getInfoUboundVal($saveData, 'force_company_num', $forceCompanyNum, $force_company_num, 1)){
            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0 ){
                array_push($companyNumIds, $saveData['company_id']);
            }
        }
        // 是否批量操作标识 true:批量操作； false:单个操作 ---因为如果批量操作，有些操作就不能每个操作都执行，也要批量操作---为了运行效率
        // 有此下标就代表批量操作
        $isBatchOperate = false;
        $isBatchOperateVal = '';
        Tool::getInfoUboundVal($saveData, 'isBatchOperate', $isBatchOperate, $isBatchOperateVal, 1);

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
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
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
            , &$certificate_info, &$certificate_no, &$has_certificate_no
            , &$forceCompanyNum, &$force_company_num, &$companyNumIds, &$isBatchOperate, &$isBatchOperateVal ){


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
            // 有证书
            if($has_certificate_no){
                $certificate_info = array_merge($certificate_info, ['operate_staff_id' => $operate_staff_id, 'operate_staff_id_history' => $operate_staff_id_history]);

                // $certificate_id = CertificateDBBusiness::replaceById($certificate_info, $company_id, $operate_id, $operate_staff_id, $modifAddOprate);

                $certificateObj = null ;
                $searchConditon = [
                    'company_id' => $certificate_info['company_id'],
                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
                ];
                CertificateDBBusiness::updateOrCreate($certificateObj, $searchConditon, $certificate_info);
                $saveData['certificate_id'] = $certificateObj->id;// $certificate_id;
            }
            if(isset($saveData['category_name'])){
                $tem_category_name = trim($saveData['category_name']);
                if(strlen($tem_category_name) > 100) $tem_category_name = mb_substr($tem_category_name,0,100,'utf-8');
                $saveData['category_name_id'] = CertificateNamesDBBusiness::getNameId($tem_category_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['project_name'])){
                $tem_project_name = trim($saveData['project_name']);
                if(strlen($tem_project_name) > 100) $tem_project_name = mb_substr($tem_project_name,0,100,'utf-8');
                $saveData['project_name_id'] = CertificateNamesDBBusiness::getNameId($tem_project_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['three_name'])){
                $tem_three_name = trim($saveData['three_name']);
                if(strlen($tem_three_name) > 100) $tem_three_name = mb_substr($tem_three_name,0,100,'utf-8');
                $saveData['three_name_id'] = CertificateNamesDBBusiness::getNameId($tem_three_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['four_name'])){
                $tem_four_name = trim($saveData['four_name']);
                if(strlen($tem_four_name) > 100) $tem_four_name = mb_substr($tem_four_name,0,100,'utf-8');
                $saveData['four_name_id'] = CertificateNamesDBBusiness::getNameId($tem_four_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['param_name'])){
                $tem_param_name = trim($saveData['param_name']);
                if(strlen($tem_param_name) > 100) $tem_param_name = mb_substr($tem_param_name,0,100,'utf-8');
                $saveData['param_name_id'] = CertificateNamesDBBusiness::getNameId($tem_param_name, $operate_staff_id, $operate_staff_id_history);
            }

            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                if($forceCompanyNum){
                    $info_old = static::getInfo($id);
                    $tem_company_id = $info_old['company_id'];
                    if($tem_company_id > 0 && !in_array($tem_company_id, $companyNumIds)) array_push($companyNumIds, $tem_company_id);

                }
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                $resultDatas = static::getInfo($id);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            // 如果是新加，则记录注册记录
            if(!$isModify){
                // 如果是新加，所需要更新企业能力范围数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                if(!$isBatchOperate && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                    StaffDBBusiness::updateCertificateScheduleNum($resultDatas['company_id']);
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新企业能力范围数量
                StaffDBBusiness::updateCertificateScheduleNum($companyNumIds);
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * 根据企业id,获得企业的能力范围数
     *
     * @param int  $company_id 企业id
     * @return  mixed 能力范围数
     * @author zouyan(305463219@qq.com)
     */
    public static function getCertificateScheduleCount($company_id = 0){
        // 更新班级老师人数
//        $queryParams = [
//            'where' => [
//                ['company_id', $company_id],
//                ['admin_type', 4],
////                ['open_status', 2],
////                ['account_status',1],
//            ],
//            'count' => 0,
//            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//        ];
//        $queryParams = Tool::getParamQuery(['company_id' => $company_id], [], []);
//        $queryParams['count'] = 0;
//        $count = static::getAllList($queryParams, []);
//        return $count;
        return static::getDBFVFormatList(8, 1, ['company_id' => $company_id], false);
    }


    /**
     * 根据id删除--可批量删除
     * 删除员工--还需要重新统计企业的员工数
     * 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     *  [
     *       'organize_id' => 3,操作的企业id 可以为0：不指定具体的企业
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $organizeIds = [];

        // 获得需要删除的数据
//            $queryParams = [
//                'where' => [
////                ['company_id', $organize_id],
//                ['admin_type', $admin_type],
////                ['teacher_status',1],
//                ],
//                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//            ];
            $queryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($queryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            $dataListObj = static::getAllList($queryParams, []);
            // $dataListObj = static::getListByIds($id);

            $dataListArr = $dataListObj->toArray();
            if(empty($dataListArr)) throws('没有需要删除的数据');
            // 用户删除要用到的
             $organizeIds = array_values(array_unique(array_column($dataListArr,'company_id')));
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use( &$id, &$organize_id, &$organizeIds){


            // 删除主记录
//            $delQueryParams = [
//                'where' => [
//                    ['admin_type', $admin_type],
//                    ['issuper','<>', 1],
//                ],
//            ];
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更企业能力范围数
                    StaffDBBusiness::updateCertificateScheduleNum($organizeId);;
                }
            }
        });
        return $id;
    }

    /**
     * 导入数据
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importDatas($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0){
        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为 3072M 3G
        ini_set("max_execution_time", 0);
        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        // 对数据有效性进行校验
        $organize_id = array_values(array_unique(array_column($saveData, 'company_id')));
        foreach($organize_id as $tem_company_id){
            $staffInfo = StaffDBBusiness::getInfo($tem_company_id);
            if(empty($staffInfo)) throws('企业【' . $tem_company_id .'】不存在！');
        }
        $errsArr = [];// 错误数组
        // $saveArr = [];// 最终可以保存的数据
        foreach($saveData as $k => $info) {
            $recordErr = [];
            $company_id = $info['company_id'] ?? 0;
            $category_name = $info['category_name'] ?? '';// 类别
            $project_name = $info['project_name'] ?? '';// 产品
            $three_name = $info['three_name'] ?? '';// 三级
            $four_name = $info['four_name'] ?? '';// 四级
            $param_name = $info['param_name'] ?? '';// 项目
            $method_name = $info['method_name'] ?? '';// 标准（方法）名称
            $limit_range = $info['limit_range'] ?? '';// 限制范围
            $explain_text = $info['explain_text'] ?? '';// 说明
            if(!is_numeric($company_id) || $company_id <= 0) array_push($recordErr, '所属企业不能为空!');
           // if(empty($category_name) && empty($project_name) && empty($param_name)) array_push($recordErr, '类别、产品、项目不能都为空!');
            if(empty($method_name)) array_push($recordErr, '标准（方法）名称不能都为空!');

            if(!empty($recordErr)){
                array_push($errsArr,'第' . ($k + 1) . '条记录:<br/>' . implode('<br/>', $recordErr));
            }
            if(empty($category_name) && empty($project_name) && empty($three_name) && empty($four_name) && empty($param_name)){
                $queryParams = ['method_name' => $method_name ];
            }else{
                $queryParams = ['company_id' => $company_id, 'category_name' => $category_name, 'project_name' => $project_name, 'three_name' => $three_name, 'four_name' => $four_name, 'param_name' => $param_name];
            }
            $temInfo = [];// static::getDBFVFormatList(4, 1, $queryParams, false); --不用修改，都是插入新的记录
            $saveData[$k]['id'] = $temInfo['id'] ?? 0;
        }
        // 如果有错，则返回错误
        if(!empty($errsArr)) throws(implode('<br/>', $errsArr));

        CommonDB::doTransactionFun(function() use( &$saveData, &$organize_id, &$returnIds, &$temNeedStaffIdOrHistoryId, &$operate_staff_id, &$company_id, &$modifAddOprate, &$operate_staff_id_history ){
            // 对数据进行修改或新加
            // throws('对数据进行修改或新加');
            foreach($saveData as $k => $info){
                $id = $info['id'] ?? 0;
                if(isset($info['id'])) unset($info['id']);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);

                // 新加或更新
                static::replaceById($info, $company_id, $id, $operate_staff_id, $modifAddOprate);
                array_push($returnIds, $id);

            }
            // 添加日志
             CertificateImportLogDBBusiness::saveImportLog($saveData, $operate_staff_id, $operate_staff_id_history);
            // 根据企业id更企业能力范围数
            StaffDBBusiness::updateCertificateScheduleNum($organize_id);
        });
        return $returnIds;
    }

    public static function test(){
        $key = '标';
        // 获得数量

        $tableName = (new CertificateSchedule)->getTable();
        // DB::table($tableName)->selectRaw('price * ? as price_with_tax', [1.0825])->get();
//        $count = DB::table($tableName)->distinct()->count();
        $count = DB::table($tableName)->distinct('company_id')->count('company_id');
//        DB::table($tableName)->raw("distinct(company_id)")->count();
//        $count = DB::table(' ((select distinct company_id from certificate_schedule ))')->count();
        pr(456);
    }

}
