<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class UserController extends StaffController
{
    public static $ADMIN_TYPE = 4;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'User';// 视图文件夹名称

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        $company_id = CommonRequest::getInt($request, 'company_id');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $email = CommonRequest::get($request, 'email');
        $mobile = CommonRequest::get($request, 'mobile');
        $qq_number = CommonRequest::get($request, 'qq_number');
        $id_number = CommonRequest::get($request, 'id_number');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $addr = CommonRequest::get($request, 'addr');
        $is_perfect = CommonRequest::getInt($request, 'is_perfect');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        $userInfo = [];
        if($id > 0){
            $userInfo = $this->judgePower($request, $id);
            // 判断是否有操作权限
            // 根据具体功能 ，加上或去掉要判断的下标
            $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
            if(!$this->batchJudgeRecordOperateAuth($userInfo, $powerFields, 0, 0, 0, true)){
                return ajaxDataArr(0, null, '您没有操作权限！');
            }
        }
        $saveData = [
            'admin_type' => static::$ADMIN_TYPE,
            'is_perfect' => $is_perfect,
            'company_id' => $company_id,
            'real_name' => $real_name,
            'sex' => $sex,
            'mobile' => $mobile,
            'email' => $email,
            'qq_number' => $qq_number,
            'id_number' => $id_number,
            'city_id' => $city_id,
            'addr' => $addr,
        ];
        if(!empty($admin_username)) $saveData['admin_username'] = $admin_username;
        if($admin_password != '' || $sure_password != ''){
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

        if($id <= 0) {// 新加;要加入的特别字段
            $addNewData = [
                // 'account_password' => $account_password,
//                'is_perfect' => 1,
                'company_grade' => 0,// 新加的会员默认等级为非会员单位
                'issuper' => 2,
                'company_type' => 0,// 企业类型1检测机构、2生产企业
                'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
                'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
                'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
                'account_status' => 1// 状态 1正常 2冻结
            ];
            $saveData = array_merge($saveData, $addNewData);
        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

}
