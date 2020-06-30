<?php

namespace App\Http\Controllers\Web\DogTools;

use App\Business\Controller\API\DogTools\CTAPIClassesBusiness;
use App\Business\Controller\API\DogTools\CTAPIClassTeachersBusiness;
use App\Business\Controller\API\DogTools\CTAPITeacherRolesBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class ClassTeachersController extends BasicController
{
    // public static $ADMIN_TYPE = 2;// 类型1平台2老师4学生

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request, $class_id = 0)
    {
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        $reDataArr['class_id'] = $class_id;        // 我的班级id
        // 我在当前班级中的记录
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 64);
        $canModif = 2;// 是否可以修改记录 2：不可以修改操作 1 ：可以修改操作
        if(!empty($userClassTeacherInfo) && ($userClassTeacherInfo['is_head_master'] == 1 ||  $userClassTeacherInfo['is_create_teacher'] == 1)
            && $userClassTeacherInfo['teacher_status'] == 1 && $userClassTeacherInfo['open_status'] == 2)  $canModif = 1;
        $reDataArr['canModif'] = $canModif;
        return view('web.DogTools.ClassTeachers.index', $reDataArr);
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request, $class_id = 0)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $reDataArr['province_kv'] = CTAPIClassTeachersBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIClassTeachersBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('web.DogTools.ClassTeachers.select', $reDataArr);
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request, $class_id = 0, $id = 0)
    {
        // 是班主任老师或创建者 才能 修改及删除
        $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        $reDataArr['class_id'] = $class_id;
        // 创建者 或 班主任 可操作
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 32);
        $info = [
            'id'=>$id,
          //   'department_id' => 0,
        ];
        $operate = "添加";
        $staffInfo = [];
        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $extParams = [
                'handleKeyArr' => ['staff'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            ];
            $info = CTAPIClassTeachersBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // 判断记录是否是老师
            $staff_id = $info['staff_id'] ?? 0;
            $staffInfo = $this->judgePower($request, $staff_id);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        // 老师角色
        $reDataArr['roles_kv'] = CTAPITeacherRolesBusiness::getKv($request, $this);
        $reDataArr['defaultRole'] = $info['teacher_role_id'] ?? -1;// 默认

        // 状态
        $reDataArr['openStatus'] = CTAPITeacherRolesBusiness::$openStatusArr;
        $reDataArr['defaultOpenStatus'] = $info['open_status'] ?? -1;// 默认

        return view('web.DogTools.ClassTeachers.add', $reDataArr);
    }

    /**
     * @ OA\Get(
     *     path="/api/web/dogtools/class_teachers/ajax_info",
     *     tags={"前端-班级管理-班级老师"},
     *     summary="班级老师--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="webDogToolsClassTeachersAjax_info",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_class_teachers_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_DogTools_info_class_teachers"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_class_teachers"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPIClassTeachersBusiness::getInfoData($request, $this, $id, [], '', []);
//        $this->judgeUserPower($request, $info);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * @ OA\Post(
     *     path="/api/web/dogtools/class_teachers/ajax_save",
     *     tags={"前端-班级管理-班级老师"},
     *     summary="班级老师--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="webDogToolsClassTeachersAjax_save",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_class_teachers_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_class_teachers"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request, $class_id = 0)
    {
        $this->InitParams($request);
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        // 创建者 或 班主任 可操作
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 32);

        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $teacher_role_id = CommonRequest::getInt($request, 'teacher_role_id');
        $is_head_master = CommonRequest::getInt($request, 'is_head_master');
        $open_status = CommonRequest::getInt($request, 'open_status');
        if($is_head_master != 1) $is_head_master = 2;
        $userInfo = [];
        if($id > 0){
            $classTeacherinfo = CTAPIClassTeachersBusiness::getInfoData($request, $this, $id, [], '');
            // 判断记录是否是老师
            $staff_id = $classTeacherinfo['staff_id'] ?? 0;
            $userInfo = $this->judgePower($request, $staff_id);
        }
        $saveData = [
            // 'class_id' => $class_id,
            'teacher_role_id' => $teacher_role_id,
            'is_head_master' => $is_head_master,
            'open_status' => $open_status,
        ];
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIClassTeachersBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @ OA\Get(
     *     path="/api/web/dogtools/class_teachers/ajax_alist",
     *     tags={"前端-班级管理-班级老师"},
     *     summary="班级老师--列表",
     *     description="班级老师--列表......",
     *     operationId="webDogToolsClassTeachersAjax_alist",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_class_teachers_id_optional"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_DogTools_list_class_teachers"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_class_teachers"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request, $class_id = 0){
        $this->InitParams($request);
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        // 只有本班的老师  可以看到数据 -- 非冻结
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 2);

        $relations = [];//  ['siteResources']
        $extParams = [
            'handleKeyArr' => ['class', 'staff', 'teacherRole'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        ];
        CTAPIClassTeachersBusiness::mergeRequest($request, $this, [
            'class_id' => $class_id,// 类型1平台2老师4学生
        ]);
        return  CTAPIClassTeachersBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        $result = CTAPIClassTeachersBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        CTAPIClassTeachersBusiness::getList($request, $this, 1 + 0);
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        CTAPIClassTeachersBusiness::importTemplate($request, $this);
//    }


    /**
     * @ OA\Post(
     *     path="/api/web/dogtools/class_teachers/ajax_del",
     *     tags={"前端-班级管理-班级老师"},
     *     summary="班级老师--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="webDogToolsClassTeachersAjax_del",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_class_teachers_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_class_teachers"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request, $class_id = 0)
    {
        $this->InitParams($request);
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        // 创建者 或 班主任 可操作
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 32);
        // 获得用户信息
        $id = CommonRequest::getInt($request, 'id');

        $info = CTAPIClassTeachersBusiness::getInfoData($request, $this, $id, [], '', []);
        // 判断记录是否是老师
        $staff_id = $info['staff_id'] ?? 0;
        $staffInfo = $this->judgePower($request, $staff_id);
        // if($staffInfo['issuper'] == 1) throws('超级帐户不可删除!');
        // 创建班级的老师不可以删除
        if($info['is_create_teacher'] == 1) throws('创建老师不可删除！');
        return CTAPIClassTeachersBusiness::delAjax($request, $this);
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIClassTeachersBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIClassTeachersBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request, $class_id = 0){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIClassTeachersBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request, $class_id = 0)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIClassTeachersBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }
}
