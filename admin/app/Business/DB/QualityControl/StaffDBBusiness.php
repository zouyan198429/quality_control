<?php
// 帐号管理
namespace App\Business\DB\QualityControl;

use App\Services\Map\Map;
use App\Services\Tool;
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
            throws('用户名不能为空！');
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

            // 如果是新增的学生，更新所在班级中的人数
            if(!$isModify && (isset($saveData['admin_type']) && $saveData['admin_type'] == 4 )  ){
                $class_id = $saveData['class_id'] ?? 0;
                if(!is_numeric($class_id) || $class_id <= 0) throws('学生所属班级参数有误！');
                $classInfo = ClassesDBBusiness::getInfo($class_id);
                if(empty($classInfo)) throws('学生所属班级不存在！');
                // 方式一
                // 更新班级学生人数
                $classData = [
                    'student_num' => $classInfo['student_num'] + 1,
                ];
                ClassesDBBusiness::saveById($classData, $class_id);

                // 根据班级id更新班级学生人数-- 方式二
                // ClassesDBBusiness::updateStudentsNum($class_id);
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
    public static function replaceByIdWX($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
        if (isset($paramsData['mini_openid']) && empty($paramsData['mini_openid'])) {
            throws('小程序 openid不能为空！');
        }

        // 查询存在的 mini_openid
        if(is_numeric($id) &&  $id <= 0 &&  isset($saveData['mini_openid']) ){
            $otherWhere = [];
            if(isset($saveData['admin_type'])  && $saveData['admin_type'] > 0 ) array_push($otherWhere, ['admin_type', $saveData['admin_type']]) ;
            $wx_unionid = $saveData['wx_unionid'] ?? '';
            $wx_unionid = trim($wx_unionid);
            if(!empty($wx_unionid)){
                $temOtherWhere = $otherWhere;

                // if( isset($saveData['wx_unionid']) ) array_push($temOtherWhere, ['wx_unionid', $saveData['wx_unionid']]);
                $info = static::judgeFieldExist($company_id, 0 ,"wx_unionid", $wx_unionid
                    , $temOtherWhere,2);

                // 如果是空，则按mini_openid再查一下
                if( empty($info) ){
                    array_push($temOtherWhere, ['wx_unionid', '']);// 是空的，也要加，因为索引
                    $info = static::judgeFieldExist($company_id, 0 ,"mini_openid", $saveData['mini_openid']
                        , $temOtherWhere,2);
                }

            }else{// 为空
                // if( isset($saveData['wx_unionid']) ) array_push($otherWhere, ['wx_unionid', $saveData['wx_unionid']]);// 是空的，也要加，因为索引
                array_push($otherWhere, ['wx_unionid', '']);// 是空的，也要加，因为索引
                $info = static::judgeFieldExist($company_id, 0 ,"mini_openid", $saveData['mini_openid']
                    , $otherWhere,2);
            }

            if(!empty($info)) $id = $info['id'];
        }

        if($id <= 0 && isset($saveData['admin_type']) && $saveData['admin_type'] == 32){
            $saveData['open_status'] = 1;// 审核状态1待审核2审核通过3审核未通过--32快跑人员用
            // 如果是app登录
            if($saveData['admin_type'] == 32){
                $nickName = $saveData['nickname'] ?? '';
                if ( empty($nickName) ) throws('新用户昵称不能为空！');
                // if (isset($saveData['avatar_url']) && empty($saveData['avatar_url'])) throws('新用户头像不能为空！');
            }
        }
        $res = static::replaceById($saveData, $company_id,$id, $operate_staff_id, $modifAddOprate);
        return $res;
    }

    /**
     * 更新老师有效的班级数
     *
     * @param int  $class_id 班级id
     * @param int  $type_num 数量类型 1 创建的班级 2 所属的班级
     * @return  mixed 有效的老师人数
     * @author zouyan(305463219@qq.com)
     */
    public static function updateClassesNum($staff_id = 0, $type_num = (1 | 2)){
        if(!is_numeric($type_num) || $type_num <= 0 ) return true;
        $teacherCountArr = ClassTeachersDBBusiness::getClassesCount($staff_id, $type_num);

        $saveData = [];
        if(($type_num & 2) == 2) $saveData['class_num'] = $teacherCountArr['class_num'];
        if(($type_num & 1) == 1) $saveData['create_class_num'] = $teacherCountArr['create_class_num'];
        if(empty($saveData)) return true;

        // 更新老师的班级数
        DB::beginTransaction();
        try {
            static::saveById($saveData, $staff_id);
        } catch ( \Exception $e) {
            DB::rollBack();
            throws($e->getMessage());
            // throws($e->getMessage());
        }
        DB::commit();
        return true;
    }

    /**
     * 根据id删除--可批量删除
     *
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $admin_type 类型1平台2老师4学生
     * @param int $class_id 操作的班级id 可以为0：不指定具体的班级
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $admin_type = 0, $class_id = 0, $modifAddOprate = 0){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $class_id = $info['class_id'];
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $dataListArr = [];
        $classIds = [];
        $staffIds = [];

         // 获得需要删除的数据
        if($admin_type == 2 || $admin_type == 4){

            $queryParams = [
                'where' => [
//                ['class_id', $class_id],
                ['admin_type', $admin_type],
//                ['teacher_status',1],
                ],
                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
            ];
            Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($queryParams, $class_id, 'class_id', [0, '0', ''], ',', false);

            $dataListObj = static::getAllList($queryParams, []);
            // $dataListObj = static::getListByIds($id);

            $dataListArr = $dataListObj->toArray();
            // 学生删除要用到的
            if($admin_type == 4) $classIds = array_values(array_unique(array_column($dataListArr,'class_id')));
            // 老师删除要用到的
            if($admin_type == 2) $staffIds = array_values(array_unique(array_column($dataListArr,'id')));
        }

        DB::beginTransaction();
        try {

            // 删除主记录
            $delQueryParams = [
                'where' => [
                    ['admin_type', $admin_type],
                ],
            ];
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($delQueryParams, $class_id, 'class_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
            // 更新班级老师人数
//            ClassesDBBusiness::updateTeacherNum($class_id);
//            // 更新老师拥有的班级数量
//            StaffDBBusiness::updateClassesNum($staff_id, (1 | 2));


            // 如果删除的是老师，则删除班级老师记录；减少班级老师数量
            if($admin_type == 2 && !empty($staffIds)){
                // 获得班级老师记录
                $classTeachersArr = ClassTeachersDBBusiness::getClassTeachersByStaffIds($staffIds)->toArray();

                // 获得老师记录相关的班级Id
                $classTeacherClassIds = array_values(array_unique(array_column($classTeachersArr,'class_id')));
                $classTeacherIds = array_values(array_unique(array_column($classTeachersArr,'id')));
                // 删除老师记录
                ClassTeachersDBBusiness::deleteByIds($classTeacherIds);
                // 更新班级的老师人数
                foreach($classTeacherClassIds as $classId){
                    // 根据班级id更新班级老师人数
                    ClassesDBBusiness::updateTeacherNum($classId);
                }

            }
            // 如果删除的是学生，则需要重新统计班级的学生数量
            if($admin_type == 4 && !empty($classIds)){
                foreach($classIds as $classId){
                    // 根据班级id更新班级学生人数
                    ClassesDBBusiness::updateStudentsNum($classId);
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
     * 根据班级id,获得班级的学生人数
     *
     * @param int  $class_id 班级id
     * @return  mixed 学生人数
     * @author zouyan(305463219@qq.com)
     */
    public static function getStudentsCount($class_id = 0){
        // 更新班级老师人数
        $queryParams = [
            'where' => [
                ['class_id', $class_id],
                ['admin_type', 4],
//                ['open_status', 2],
//                ['teacher_status',1],
            ],
            'count' => 0,
            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
        ];
        $studentsCount = static::getAllList($queryParams, []);
        return $studentsCount;
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param int  $class_id 学生的班级id
     * @param int  $admin_type 类型1平台2老师4学生
     * @param array $saveData 要导入的数组 -- 二维数组
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importStudents($class_id, $admin_type, $saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0){
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        // 性别
        $sex_arr = [
            '0' => '未知',
            '1' => '男',
            '2' => '女',
        ];

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        foreach($saveData as $k => $info) {
            $real_name = $info['real_name'] ?? '';// 用户名
            if(empty($real_name)){
                throws('姓名不能为空!');
            }
        }

        foreach($saveData as $k => $info) {
            $temStaffId = 0;
            $student_number = $info['student_number'] ?? '';
            $real_name = $info['real_name'] ?? '';// 用户名
            $sex = $info['sex'] ?? '';// 性别
            $sex_id = array_search($sex, $sex_arr);
            if($sex_id === false) $sex_id = 0;

            $temStaff = [
                'admin_type' => $admin_type,
                'class_id' => $class_id,
                //'real_name' => $real_name,
               // 'student_number' => $student_number,
                'sex' => $sex_id,
            ];
            $info = array_merge($info, $temStaff);
            // 学号为空，则是新加
            if(empty($student_number)){
                static::replaceById($info, $company_id, $temStaffId, $operate_staff_id, $modifAddOprate);
                array_push($returnIds, $temStaffId);
                continue;
            }

            // 加入操作人员信息
            if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);

            // 查
            $queryParams = [
                'where' => [
                    // ['id', '&' , '4=4'],
                    ['admin_type', '=' ,$admin_type],
                    ['class_id', '=' ,$class_id],
                    ['student_number', $student_number],
                    //['mobile', $keyword],
                    //['admin_type',self::$admin_type],
                ],
                //            'select' => [
                //                'id','company_id','type_name','sort_num'
                //                //,'operate_staff_id','operate_staff_history_id'
                //                ,'created_at'
                //            ],
                // 'orderBy' => ['id'=>'desc'],
            ];
            $relations = [];
            $requestData = static::getDataLimit(1, 1, 1, $queryParams , $relations);
            // $total = $requestData['total'] ?? 0;
            $dataList = $requestData['dataList'] ?? [];
            $staffObj = $dataList[0] ?? [];// 具体数据对象
            if(!empty($staffObj)) {// 有记录，则修改数据
                $temStaffId = $staffObj->id;
//                foreach($info as $t_k => $t_v){
//                    // if(in_array($t_k, ['admin_username', 'admin_password'])) continue; //不修改用户名和密码
//                    $staffObj->{$t_k} = $t_v;
//                }
//                $staffObj->save();
            }else{// 没有记录,则新加
                // $staffObj = static::create($temStaff);
                // static::replaceById($info, $company_id, $temStaffId, $operate_staff_id, $modifAddOprate);
            }
            // 新加或更新
            static::replaceById($info, $company_id, $temStaffId, $operate_staff_id, $modifAddOprate);
            // $staff_id = $staffObj->id;
            array_push($returnIds, $temStaffId);
        }
        return $returnIds;
    }

}
