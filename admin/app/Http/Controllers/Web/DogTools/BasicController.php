<?php
namespace App\Http\Controllers\Web\DogTools;


use App\Business\Controller\API\DogTools\CTAPIClassTeachersBusiness;
use App\Business\Controller\API\DogTools\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Tool;
use Illuminate\Http\Request;

class BasicController extends WorksController
{
    public static $ADMIN_TYPE = 2;// 类型1平台2老师4学生

    // 重购方法
    // 获取
    public function getUserInfo(){
        $staff_id = Tool::getSession($this->redisKey, $this->save_session,
            config('public.sessionKey'), config('public.sessionRedisTye'));
        if(!is_numeric($staff_id)) throws('登录失效，请重新登录！');
        $userInfo = $this->getStaffInfo($staff_id);
//        $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
        // 对数据进行有效性验证
        if(empty($userInfo) || count($userInfo) <= 0 || empty($userInfo)){
            $this->delUserInfo();
            throws('用户名信息不存在！');
        }

        if($userInfo['admin_type'] != 2 ){
            $this->delUserInfo();
            throws('非法访问！');
        }
        if($userInfo['account_status'] == 2 ){
            $this->delUserInfo();
            throws('用户已冻结！');
        }
        if($userInfo['open_status'] != 2 ){
            $this->delUserInfo();
            throws('用户非审核通过！');
        }
        return $userInfo;
    }

    // 根据用户id ，判断权限
    public function getStaffInfo($staff_id = 0){
        if(!is_numeric($staff_id)) throws('参数【staff_id】有误！');
        $userInfo = CTAPIStaffBusiness::getInfoDataBase(\request(), $this,'', $staff_id, [], '', 1);
        if(!is_array($userInfo) || empty($userInfo)) $userInfo = [];
        // 对数据进行有效性验证
//        if(empty($userInfo) || count($userInfo) <= 0 || empty($userInfo)){
//            $this->delUserInfo();
//            throws('用户名信息不存在！');
//        }
//
//        if($userInfo['admin_type'] != 2 ){
//            $this->delUserInfo();
//            throws('非法访问！');
//        }
//
//        if($userInfo['account_status'] == 2 ){
//            $this->delUserInfo();
//            throws('用户已冻结！');
//        }
//        if($userInfo['open_status'] != 2 ){
//            $this->delUserInfo();
//            throws('用户非审核通过！');
//        }
        return $userInfo;
    }

    // 班级权限操作--老师的
    // 判断是否可以操作-修改班级信息 --创建者 、班主任  可以操作班级信息，其它的人只能看
    // $class_id 班级id
    // powerNum 权限类型 1 查看权限[必当前班级的老师] 2 查看权限[必当前班级的老师且非冻结]
    //                   4 查看权限[必当前班老师且必须审核通过且非冻结]
    //                   8 修改权限[必创建者]  16 修改权限[必班主任]
    //                   32 修改权限[是创建者 或 班主任]
    //                   64 获得用户在当前班中的记录---数据为空也返回
    // $staff_id 用户id 如果为空，则是当前登录用户
    public function hasPowerOperateClasses($class_id, $powerNum = 0, $staff_id = 0){
        // if($powerNum <= 0) return [];
        if($staff_id <= 0) $staff_id = $this->user_id;
        // 获得老师在班级中老师的记录

        $queryParams = [
            'where' => [
                ['class_id', $class_id],
                // ['teacher_status', 1],
                ['staff_id', $staff_id],
            ],
            // 'limit' => 1
        ];
        $relations = '';
        $classTeacherInfo = CTAPIClassTeachersBusiness::getInfoQuery(\request(), $this, '', 0, 1, $queryParams, $relations);
        if(($powerNum & 64) == 64) return $classTeacherInfo;
        if(empty($classTeacherInfo)) throws('老师不存在、非本班老师、冻结老师<br/>不可查看本班数据！');
        if($powerNum <= 0) return $classTeacherInfo;
        if(($powerNum & 2) == 2  && $classTeacherInfo['teacher_status']  == 2){// 2 查看权限[是当前班级的老师且非冻结]
            throws('冻结状态，不可操作！');
        }
        if(($powerNum & 4) == 4 && ($classTeacherInfo['teacher_status']  == 2 || $classTeacherInfo['open_status']  != 2)){// 4 查看权限[是当前班老师且必须审核通过且非冻结]
            throws('冻结或非审核通过状态，不可操作！');
        }
        if(($powerNum & 8) == 8  && $classTeacherInfo['is_create_teacher']  != 1){// 8 修改权限[必创建者]
            throws('非创建者，不可操作！');
        }
        if(($powerNum & 16) == 16  && $classTeacherInfo['is_head_master']  != 1){// 16 修改权限[必班主任]
            throws('非班主任，不可操作！');
        }
        if(($powerNum & 32) == 32  && $classTeacherInfo['is_create_teacher']  != 1 && $classTeacherInfo['is_head_master']  != 1 ){// 8 修改权限[必创建者]
            throws('非创建者，也非班主任，不可操作！');
        }
        return $classTeacherInfo;
    }

    // 判断操作权限--根据用户id
    protected function judgePower(Request $request, $staff_id = 0){
        $userInfo = $this->getStaffInfo($staff_id);
        $this->judgeUserPower($request, $userInfo);
        return $userInfo;
    }

    // 判断操作权限--根据用户信息【一维数组】
    // 特殊 $class_id > 0时:代表$userInfo 是当前学生，判断当前学生是否是这个班的
    protected function judgeUserPower(Request $request, $userInfo = [], $class_id = 0){
        if(empty($userInfo) || count($userInfo) <= 0 || empty($userInfo)){
            throws('用户名信息不存在！');
        }

        // 判断类型是否正确 1平台2老师4学生
        if($userInfo['admin_type'] != static::$ADMIN_TYPE){
            throws('用户类型不一致！');
        }
        // 判断当前学生是否是这个班的
        if(is_numeric($class_id) && $class_id > 0 && $userInfo['class_id'] != $class_id) throws('当前学生非当前所属班级！');
        return true;
    }


}
