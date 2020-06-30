<?php
namespace App\Http\Controllers\Admin\QualityControl;


use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Tool;

class BasicController extends WorksController
{

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

        if($userInfo['admin_type'] != 1 ){
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
//        if($userInfo['admin_type'] != 1 ){
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
}
