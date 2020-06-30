<?php

namespace App\Http\Controllers\Web\DogTools;

use App\Business\Controller\API\DogTools\CTAPIClassesBusiness;
use App\Business\Controller\API\DogTools\CTAPIResourceBusiness;
use App\Business\Controller\API\DogTools\CTAPIStudentsBusiness;
use App\Business\Controller\API\DogTools\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class StudentsController extends BasicController
{
    public static $ADMIN_TYPE = 4;// 类型1平台2老师4学生

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
        $reDataArr['class_id'] = $class_id;
        // 我在当前班级中的记录
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 64);
        $canModif = 2;// 是否可以修改记录 2：不可以修改操作 1 ：可以修改操作
        if(!empty($userClassTeacherInfo) && ($userClassTeacherInfo['is_head_master'] == 1 ||  $userClassTeacherInfo['is_create_teacher'] == 1)
            && $userClassTeacherInfo['teacher_status'] == 1 && $userClassTeacherInfo['open_status'] == 2)  $canModif = 1;
        $reDataArr['canModif'] = $canModif;
        return view('web.DogTools.Students.index', $reDataArr);
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        $reDataArr['province_kv'] = CTAPIStaffBusiness::getCityByPid($request, $this,  0);
//        $reDataArr['province_kv'] = CTAPIStaffBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//        $reDataArr['province_id'] = 0;
//        return view('web.DogTools.Students.select', $reDataArr);
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

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $extParams = [
            //    'handleKeyArr' => [],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            ];
            $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // 判断记录是否是学生
            $this->judgeUserPower($request, $info, $class_id);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        // 性别
        $reDataArr['sexArr'] = CTAPIStaffBusiness::$sex;
        $reDataArr['defaultSex'] = $info['sex'] ?? -1;// 默认

        return view('web.DogTools.Students.add', $reDataArr);
    }

    /**
     * @ OA\Get(
     *     path="/api/web/dogtools/students/ajax_info",
     *     tags={"前端-学生管理-学生"},
     *     summary="学生--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="webDogToolsStudentsAjax_info",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_students_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_DogTools_info_students"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_students"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * @ OA\Post(
     *     path="/api/web/dogtools/students/ajax_save",
     *     tags={"前端-学生管理-学生"},
     *     summary="学生--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="webDogToolsStudentsAjax_save",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_students_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_students"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
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
        $real_name = CommonRequest::get($request, 'real_name');
        $student_number = CommonRequest::get($request, 'student_number');
        $sex = CommonRequest::getInt($request, 'sex');

        $userInfo = [];
        if($id > 0){
            $userInfo = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '');
            // 判断记录是否是学生
            $this->judgeUserPower($request, $userInfo, $class_id);
        }
        $saveData = [
            'admin_type' => static::$ADMIN_TYPE,
            'class_id' => $class_id,
            'real_name' => $real_name,
            'student_number' => $student_number,
            'sex' => $sex,
        ];

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @ OA\Get(
     *     path="/api/web/dogtools/students/ajax_alist",
     *     tags={"前端-学生管理-学生"},
     *     summary="学生--列表",
     *     description="学生--列表......",
     *     operationId="webDogToolsStudentsAjax_alist",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_students_id_optional"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_DogTools_list_students"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_students"}
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
          //  'handleKeyArr' => ['class', 'staff', 'teacherRole'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        ];
        CTAPIStaffBusiness::mergeRequest($request, $this, [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2老师4学生
            'class_id' => $class_id,// 所在班级
        ]);
        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIStaffBusiness::getList($request, $this, 1 + 0);
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
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIStaffBusiness::getList($request, $this, 1 + 0);
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import_template(Request $request, $class_id = 0){
        $this->InitParams($request);
        // 判断班级是否存在
//        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
//        $reDataArr['class_id'] = $class_id;
//        // 创建者 或 班主任 可操作
//        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 32);
        CTAPIStaffBusiness::importTemplate($request, $this);
    }


    /**
     * @ OA\Post(
     *     path="/api/web/dogtools/students/ajax_del",
     *     tags={"前端-学生管理-学生"},
     *     summary="学生--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="webDogToolsStudentsAjax_del",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_DogTools_students_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_DogTools_info_students"}
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

        return CTAPIStaffBusiness::delDatasAjax($request, $this, static::$ADMIN_TYPE, $class_id);
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIStaffBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIStaffBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import(Request $request, $class_id = 0)
    {
        $this->InitParams($request);
        // 判断班级是否存在
        $classInfo = CTAPIClassesBusiness::isExistClass($request, $this, $class_id);
        // 创建者 或 班主任 可操作
        $userClassTeacherInfo = $this->hasPowerOperateClasses($class_id, 32);

        CTAPIStaffBusiness::mergeRequest($request, $this, [
            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2老师4学生
            'class_id' => $class_id,// 所在班级
        ]);

        // 上传并保存文件
        $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 2);
        if($result['apistatus'] == 0) return $result;
        // 文件上传成功
        // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
        $fileName = Tool::getPath('public') . $result['result']['filePath'];
        $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
        $resourceId = $result['result']['id'] ?? 0;
         if($resourceId > 0){
             CTAPIStaffBusiness::mergeRequest($request, $this, [
                 'id' => $resourceId,
             ]);
             CTAPIResourceBusiness::delAjax($request, $this);
         }
        // 删除上传的文件
        // Tool::resourceDelFile(['resource_url' => $result['result']['filePath']]);
        return ajaxDataArr(1, $resultDatas, '');
    }
}
