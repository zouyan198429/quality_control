<?php
// 帐号管理
namespace App\Business\Controller\API\QualityControl;

use App\Services\Captcha\CaptchaCode;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIStaffBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\StaffAPI';
    public static $table_name = 'staff';// 表名称

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    // 拥有者类型1平台2企业4个人
    public static $adminType = [
        '1' => '平台',
        '2' => '企业',
        '4' => '个人',
    ];

    // 是否完善资料1待完善2已完善
    public static $isPerfectArr = [
        '1' => '待完善',
        '2' => '已完善',
    ];

    // 是否超级帐户2否1是
    public static $issuper = [
        '2' => '普通帐户',
        '1' => '超级帐户',
    ];

    // 审核状态1待审核2审核通过4审核不通过
    public static $openStatus = [
        '1' => '待审核',
        '2' => '审核通过',
        '4' => '审核不通过',
    ];

    // 状态 1正常 2冻结
    public static $accountStatus = [
        '1' => '正常',
        '2' => '冻结',
    ];

    // 性别0未知1男2女
    public static $sex = [
        '0' => '未知',
        '1' => '男',
        '2' => '女',
    ];

    // 企业--是否独立法人1独立法人 2非独立法人
    public static $companyIsLegalPersionArr = [
        '1' => '独立法人',
        '2' => '非独立法人',
    ];

    // 企业--企业类型1检测机构、2生产企业
    public static $companyTypeArr = [
        '1' => '检测机构',
        '2' => '生产企业',
    ];

    // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
    public static $companyPropArr = [
        '1' => '企业法人',
        '2' => '企业非法人',
        '3' => '事业法人',
        '4' => '事业非法人',
        '5' => '社团法人',
        '6' => '社团非法人',
        '7' => '机关法人',
        '8' => '机关非法人',
        '9' => '其它机构',
        '10' => '民办非企业单位',
        '11' => '个体',
        '12' => '工会法人',
    ];

    // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
    public static $companyPeoplesNumArr = [
        '1' => '1-20',
        '2' => '20-100',
        '3' => '100-500',
        '4' => '500以上',
    ];

    // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
    public static $companyGradeArr = [
        '1' => '非会员',
        '2' => '会员',
        '4' => '理事',
        '8' => '常务理事',
        '16' => '理事长',
    ];

    /**
     * 手机验证码登录/注册
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 拥有者类型1平台2老师4学生 可以写操作
     * @param int $login_type 登录方式 1 用户名密码 [已经判断过验证码] ；2 手机验证码[已经判断过短信验证码]
     * @return  array 用户数组
     * @author zouyan(305463219@qq.com)
     */
    public static function loginRegMobileCode(Request $request, Controller $controller, $admin_type = 0, $login_type = 1){
        return static::login( $request,  $controller, $admin_type , $login_type);
    }

    /**
     * 登录--有验证码的
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 拥有者类型1平台2企业4个人 可以写操作
     * @param int $login_type 登录方式 1 用户名密码 ；2 手机验证码
     * @param int $checkType 需要判断验证的类型 1图形验证码 2手机验证码 -- 可以多种都需要 (1 | 2)
     * @param int $operateType 操作类型 1 注册登录[帐号不存在时，自动注册并登录；存在时登录]--注册并进行登录操作 2 注册【不进行登录操作】  4登录
     * @param array $regInitData 注册时的初始值 一维数组  如：['issuper' => 2,'open_status' => 2,'account_status' => 1] --最优先有效
     * @return  array 用户数组
     * @author zouyan(305463219@qq.com)
     */
    public static function loginCaptchaCode(Request $request, Controller $controller, $admin_type = 0, $login_type = 1, $checkType = 1, $operateType = 4, $regInitData = []){
        // 1图形验证码
        if( ($checkType & 1) == 1 ){
            $captcha_code = CommonRequest::get($request, 'captcha_code');
            $captcha_key = CommonRequest::get($request, 'captcha_key');
            CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, true, 1);
        }
        // 2手机验证码
        $mobile = '';
        $countryCode = '86';
        $mobile_vercode = '';
        if( ($checkType & 2) == 2 ){
            $mobile = CommonRequest::get($request, 'mobile');
            $mobile_vercode = CommonRequest::get($request, 'mobile_vercode');
            // 发送手机验证码验证有效性
            CTAPIStaffBusiness::sMSCodeVerify($request, $controller, 'reg', $mobile, $countryCode,  $mobile_vercode, true);
        }
        $user_id = 0;
        $result = ajaxDataArr(0, null, '登录失败！');// 默认为失败
        try {
            $result = static::login( $request,  $controller, $admin_type , $login_type, $user_id, $operateType, $regInitData);
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            throws($errMsg);
        }finally{
            //  登录成功， $user_id > 0 &&  且 2手机验证码 登录
            // 改为只要是手机验证码登录，都会去改验证码状态为已使用
            if( ($checkType & 2) == 2 && $mobile_vercode){
                // 修改验证码为已使用
                $smsQueryParams = [
                    'where' => [
                        // ['id', '&' , '16=16'],
                        ['country_code', $countryCode],
                        ['mobile', '=', $mobile],
                        ['sms_code', '=', $mobile_vercode],
                        ['sms_type', 1],
                        //['admin_type',self::$admin_type],
                    ],
                    'whereIn' => [ 'sms_status' => [1,2,8]]
                ];
                $smsUpdateData = [
                    'sms_status' => 4,
                    'staff_id' => $user_id
                ];
                CTAPISmsCodeBusiness::ModifyByQueyCTL($request, $controller, '', $smsUpdateData, $smsQueryParams, 1);
            }
        }
        return $result;
    }

    /**
     * 登录
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 拥有者类型1平台2企业4个人 可以写操作
     * @param int $login_type 登录方式 1 用户名密码 [已经判断过验证码] ；2 手机验证码[已经判断过短信验证码]
     * @param int $user_id 登录成功后的用户id 默认为：0
     * @param int $operateType 操作类型 1 注册登录[帐号不存在时，自动注册并登录；存在时登录]--注册并进行登录操作 2 注册【不进行登录操作】  4登录 -- 通过 用户类型
     * @param array $regInitData 注册时的初始值 一维数组  如：['issuper' => 2,'open_status' => 2,'account_status' => 1] --最优先有效
     * @return  mixed 用户数组
     * @author zouyan(305463219@qq.com)
     */
    public static function login(Request $request, Controller $controller, $admin_type = 0, $login_type = 1, &$user_id = 0, $operateType = 4, $regInitData = []){

        $preKey = CommonRequest::get($request, 'preKey');// 0 小程序 1后台[默认]
        if(!is_numeric($preKey)) $preKey = 1;

//        $preKey = Common::get($request, 'preKey');// 0 小程序 1后台
//        if(!is_numeric($preKey)){
//            $preKey = 1;
//        }
        // 数据验证 TODO
        // $company_id = config('public.company_id');
        $queryParams = [
            'where' => [
                // ['company_id',$company_id],
//                ['admin_username',$admin_username],
//                ['admin_password',md5($admin_password)],
            ],
//            'whereIn' => [
//                'admin_type' => array_keys(self::$adminType),
//            ],
            // 'select' => ['id','company_id','admin_username','real_name','admin_type'],
            // 'limit' => 1
        ];
        $pageParams = [
            'page' =>1,
            'pagesize' => 1,
            'total' => 1,
        ];

//        if($admin_type >= 64){
            // array_push($queryParams['where'], ['admin_type', '&' , '64=64']);
//        }else{
            array_push($queryParams['where'], ['admin_type', $admin_type]);
//        }

        $relations = [];// ['city', 'cityPartner', 'seller', 'shop'];
        $userInfo = [];
        $mobile = '';
        $admin_username = '';
        $admin_password = '';
        switch ($login_type) {
            case 2:// 2 手机验证码
                $mobile = CommonRequest::get($request, 'mobile');
                if(strlen($mobile) <= 0) throws('手机号不能为空！');
                if(strlen($mobile) < 11) throws('手机号长度不得少于11位！');
                // 根据手机号获得用户信息
                $mobileQueryParams = $queryParams;
                array_push($mobileQueryParams['where'], ['mobile', $mobile]);
                $userInfo = static::getInfoQuery($request, $controller, '', 0, 1, $mobileQueryParams, $relations, 1);
                // 如果是注册-- 用户信息已经存在
                if($operateType == 2 && !empty($userInfo)){
                    throws('此手机号已被注册！');
                }
                // 没有用户信息，则注册用户信息
                if(empty($userInfo) && in_array($operateType, [1, 2])){
                    $infoData = [
                        'admin_type' => $admin_type,
                        'issuper' => 2,
                        'open_status' => 2,
                        'account_status' => 1,
                        'mobile' => $mobile,
                    ];
                    // 加入指定字段及值
                    if(!empty($regInitData)) $infoData = array_merge($infoData, $regInitData);
                    $staff_id = 0;
                    $userInfo = static::replaceById($request, $controller, $infoData, $staff_id, [ 'judgeDataKey' => 'replace',], true);
                }
                // 注册操作--直接返回用户
                if($operateType == 2) return $userInfo;
                break;
            default:// 1 用户名密码
                $admin_username = CommonRequest::get($request, 'admin_username');
                $admin_password = CommonRequest::get($request, 'admin_password');
                if(strlen($admin_username) <= 0 || strlen($admin_password) <= 0) throws('用户名或密码不能为空！');
                if(strlen($admin_username) < 6) throws('用户名长度不得少于6位！');
                if(strlen($admin_password) < 6) throws('密码长度不得少于6位！');
                array_push($queryParams['where'], ['admin_username', $admin_username]);// 必须放在最上面，下面注册判断用户名是否存在要用此条件
                // 如果是注册：要判断用户名是否已经存在
                if($operateType == 2){
                    // 判断确认密码
                    $repass = CommonRequest::get($request, 'repass');
                    if(strlen($repass) < 6) throws('确认密码长度不得少于6位！');
                    if($admin_password != $repass) throws("密码与确认密码不一致！");
                    $userQueryParams = $queryParams;
                    $userCompareInfo = static::getInfoQuery($request, $controller, '', 0, 1, $userQueryParams, [], 1);
                    if(!empty($userCompareInfo)) throws('用户名已存在！');
                }
                array_push($queryParams['where'], ['admin_password', md5($admin_password)]);

                $userInfo = static::getInfoQuery($request, $controller, '', 0, 1, $queryParams, $relations, 1);
                // 如果是注册-- 用户信息已经存在
                if($operateType == 2 && !empty($userInfo)){
                    throws('用户名已存在！');
                }
                // 没有用户信息，则注册用户信息
                if(empty($userInfo) && in_array($operateType, [1, 2])){
                    $infoData = [
                        'admin_type' => $admin_type,
                        'issuper' => 2,
                        'open_status' => 2,
                        'account_status' => 1,
                        'admin_username' => $admin_username,
                        'admin_password' => $admin_password,
                    ];
                    // 加入指定字段及值
                    if(!empty($regInitData)) $infoData = array_merge($infoData, $regInitData);
                    $staff_id = 0;
                    $userInfo = static::replaceById($request, $controller, $infoData, $staff_id, [ 'judgeDataKey' => 'replace',], true);
                }
                // 注册操作--直接返回用户id
                if($operateType == 2) return $userInfo;

        }
        // 进行登录操作

        if(empty($userInfo) || count($userInfo) <= 0 || empty($userInfo)){
            throws('用户名信息不存在！');
        }

        // 以下是对用户信息的验证
        // $judge_type = 1;// 1登录 2操作是判断是否登录
        RelationDB::resolvingRelationData($userInfo, $relations);// 根据关系设置，格式化数据
        if($login_type == 1 && $admin_username != $userInfo['admin_username']) throws('用户名或密码有误！');
        if($login_type == 2 && $mobile != $userInfo['mobile']) throws('手机号有误！');
        if($userInfo['account_status'] == 2 ) throws('用户已冻结！');
        // 让企业和个的待审核状态可以先登录，再在首页做
        if($userInfo['open_status'] == 1 &&
            (
                ( in_array($userInfo['admin_type'], [2, 4]) && $userInfo['is_perfect'] == 2)  ||
                $userInfo['admin_type'] == 1
            )
         ) throws('审核中，请耐心等待！');
        if($userInfo['open_status'] == 4 ) throws('审核未通过！');
        if($userInfo['open_status'] != 2   &&
            (
                ( in_array($userInfo['admin_type'], [2, 4]) && $userInfo['is_perfect'] == 2) ||
                $userInfo['admin_type'] == 1
            )
        ) throws('非审核通过！');
//        $staffCity = $userInfo['city'] ?? [];// 城市分站
//        $staffCityPartner = $userInfo['city_partner'] ?? [];// 城市代理
//        $staffSeller = $userInfo['seller'] ?? [];// 商家
//        $staffShop = $userInfo['shop'] ?? [];// 店铺
        // 拥有者类型1平台2城市分站4城市代理8商家16店铺32快跑人员64用户
//        switch ($userInfo['admin_type'])
//        {
//            case 1:// 平台
//                break;
//            case 2:// 2老师
//                throws('老师不可以用帐号密码登录！');
//                break;
//            case 4:// 4学生
//                throws('学生不可以用帐号密码登录！');
////                if(empty($staffCityPartner))  throws('城市代理信息不存在！');
////                if($staffCityPartner['status'] != 1)  throws('不是审核通过状态！');
//                break;
////            case 8:// 商家
////                if(empty($staffSeller))  throws('商家信息不存在！');
////                if($staffSeller['status'] != 1)  throws('不是审核通过状态！');
////                break;
////            case 16:// 店铺
////                if(empty($staffShop))  throws('店铺信息不存在！');
////                if($staffShop['status'] != 1)  throws('不是审核通过状态！');
////                break;
////            case 32:// 快跑人员
////                break;
//            case 64:// 用户
//                break;
//            default:
//        }

//        $admin_type = $userInfo['admin_type'] ?? '';
//        if($admin_type != 2)  throws('您不是超级管理员，没有权限访问！');
        $firstlogintime = $userInfo['firstlogintime'] ?? '';
        $user_id = $userInfo['id'];

        $saveData = [
            'lastlogintime' => date('Y-m-d H:i:s',time()),
        ];
        if(empty($firstlogintime) || strlen($firstlogintime) <= 0 ) $saveData['firstlogintime'] = date('Y-m-d H:i:s',time());
        static::saveByIdApi($request, $controller,'', $userInfo['id'], $saveData, $userInfo['id'], 1);

        $userInfo['modifyTime'] = time();
        // 保存session
        // 存储数据到session...
//        if (!session_id()) session_start(); // 初始化session
//        $_SESSION['userInfo'] = $userInfo; //保存某个session信息

        // 保存session
        // 存储数据到session...
//        if (!session_id()) session_start(); // 初始化session
        // $_SESSION['userInfo'] = $userInfo; //保存某个session信息
//        $redisKey = $controller->setUserInfo($userInfo, $preKey);
        $redisKey = $controller->setUserInfo($userInfo['id'], $preKey);
        $userInfo['redisKey'] = $redisKey;
        return ajaxDataArr(1, $userInfo, '');
    }

    /**
     * 退出登录
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @author zouyan(305463219@qq.com)
     */
    public static function loginOut(Request $request, Controller $controller){
//        if(isset($_SESSION['userInfo'])){
//            unset($_SESSION['userInfo']); //保存某个session信息
//        }
        return $controller->delUserInfo();
    }

    /**
     * 修改密码
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @author zouyan(305463219@qq.com)
     */
    public static function modifyPassWord(Request $request, Controller $controller){

        // $id = Common::getInt($request, 'id');
        // Common::judgeEmptyParams($request, 'id', $id);
        $id = $controller->user_id;
        $company_id = $controller->company_id;
        $old_password = CommonRequest::get($request, 'old_password');// 旧密码，如果为空，则不校验
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');

        if (empty($admin_password) || $admin_password != $sure_password){
            return ajaxDataArr(0, null, '密码和确定密码不一致！');
        }

        $saveData = [
            'admin_password' => $admin_password,
        ];

        // 修改
        // 判断权限
//        $judgeData = [
//           // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        $relations = '';
//        static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations);
        // 如果有旧密码，则验证旧密码是否正确
        if(!empty($old_password)){
            $queryParams = [
                'where' => [
                    ['id',$id],
                    ['admin_password',md5($old_password)],
                ],
                // 'limit' => 1
            ];
            $relations = '';
            $infoData = static::getInfoQuery($request, $controller, '', $company_id, 1, $queryParams, $relations);
            if(empty($infoData)){
                return ajaxDataArr(0, null, '原始密码不正确！');
            }
            RelationDB::resolvingRelationData($infoData, $relations);// 根据关系设置，格式化数据
        }
        $resultDatas = static::saveByIdApi($request, $controller,'', $id, $saveData, $company_id);
        return ajaxDataArr(1, $resultDatas, '');
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

        $admin_type = CommonRequest::getInt($request, 'admin_type');
        if($admin_type > 0 )  array_push($queryParams['where'], ['admin_type', '=', $admin_type]);

        $is_perfect = CommonRequest::getInt($request, 'is_perfect');
        if($is_perfect > 0 )  array_push($queryParams['where'], ['is_perfect', '=', $is_perfect]);

        $account_status = CommonRequest::getInt($request, 'account_status');
        if(is_numeric($account_status) && $account_status > 0 )  array_push($queryParams['where'], ['account_status', '=', $account_status]);

        $open_status = CommonRequest::getInt($request, 'open_status');
        if(is_numeric($open_status) && $open_status > 0 )  array_push($queryParams['where'], ['open_status', '=', $open_status]);

        $issuper = CommonRequest::getInt($request, 'issuper');
        if(is_numeric($issuper) && $issuper > 0 )  array_push($queryParams['where'], ['issuper', '=', $issuper]);

        $sex = CommonRequest::get($request, 'sex');
        if(is_numeric($sex) && $sex >= 0 )  array_push($queryParams['where'], ['sex', '=', $sex]);

        $company_id = CommonRequest::getInt($request, 'company_id');
        if(is_numeric($company_id) && $company_id > 0 )  array_push($queryParams['where'], ['company_id', '=', $company_id]);

        $company_is_legal_persion = CommonRequest::getInt($request, 'company_is_legal_persion');
        if(is_numeric($company_is_legal_persion) && $company_is_legal_persion > 0 )  array_push($queryParams['where'], ['company_is_legal_persion', '=', $company_is_legal_persion]);

        $company_type = CommonRequest::getInt($request, 'company_type');
        if(is_numeric($company_type) && $company_type > 0 )  array_push($queryParams['where'], ['company_type', '=', $company_type]);

        $company_prop = CommonRequest::getInt($request, 'company_prop');
        if(is_numeric($company_prop) && $company_prop > 0 )  array_push($queryParams['where'], ['company_prop', '=', $company_prop]);

        $company_peoples_num = CommonRequest::getInt($request, 'company_peoples_num');
        if(is_numeric($company_peoples_num) && $company_peoples_num > 0 )  array_push($queryParams['where'], ['company_peoples_num', '=', $company_peoples_num]);

        $company_grade = CommonRequest::getInt($request, 'company_grade');
        if(is_numeric($company_grade) && $company_grade > 0 )  array_push($queryParams['where'], ['company_grade', '=', $company_grade]);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 删除单条数据--1平台管理员数据删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
//    public static function delAjax(Request $request, Controller $controller, $notLog = 0)
//    {
//        $company_id = $controller->company_id;
//
//        return static::delAjaxBase($request, $controller, '', $notLog);
//
//    }

    /**
     * 删除单条数据--2企业4个人 数据删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $adminType 类型1平台2老师4学生
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delDatasAjax(Request $request, Controller $controller, $adminType = 0, $organize_id = 0, $notLog = 0)
    {
        //  类型1平台2企业4个人
        if(!in_array($controller->user_type, [1, 2]))  throws('用户中心不可进行删除操作!');

        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $id = CommonRequest::get($request, 'id');
        if(is_array($id)) $id = implode(',', $id);
        // 如果是单条删除
        if(is_numeric($id) || (is_string($id) && strpos($id, ',') === false )){
            $info = $controller->judgePower($request, $id);
            if($info['issuper'] == 1) throws('超级帐户不可删除!');

        }
        // 调用删除接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'admin_type' => $adminType,
            'organize_id' => $organize_id,
            'modifAddOprate' => 1,
        ];
        static::exeDBBusinessMethodCT($request, $controller, '',  'delById', $apiParams, $company_id, $notLog);
        return ajaxDataArr(1, $id, '');
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     *    operate_type 可有 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
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
        if(!is_numeric($user_id) || $user_id <= 0) $user_id = 0;

        $real_name = $saveData['real_name'] ?? '';
        $mobile = $saveData['mobile'] ?? '';
        $admin_username = $saveData['admin_username'] ?? '';

        /*
         *
        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
            throws('真实姓名不能为空！');
        }

        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
            throws('手机不能为空！');
        }

        if(isset($saveData['admin_username']) && empty($saveData['admin_username'])  ){
            throws('用户名不能为空！');
        }
         *
         */
        // 验证数据
        $judgeType = ($id > 0) ? 4 : 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        // $mustFields = [];//
        $judgeDataKey = $extParams['judgeDataKey'] ?? '';
        static::judgeDataThrowsErr($judgeType, $saveData, $mustFields, $judgeDataKey, 1, "<br/>", ['request' => $request, 'controller' => $controller , 'id' => $id]);

        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 1,
        ];
        $methodName = 'replaceById';
//        if(isset($saveData['mini_openid']))  $methodName = 'replaceByIdWX';
        $saveData = static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        /*
        // 查询手机号是否已经有企业使用--账号表里查
        if( isset($saveData['mobile']) && self::judgeFieldExist($request, $controller, $id ,"mobile", $saveData['mobile'], $notLog)){
            throws('手机号已存在！');
        }
        // 用户名
        if( isset($saveData['admin_username']) && self::judgeFieldExist($request, $controller, $id ,"admin_username", $saveData['admin_username'], $notLog)){
            throws('用户名已存在！');
        }

        $isModify = false;
        if($id > 0){
            $isModify = true;
            // 判断权限
//            $judgeData = [
//                'company_id' => $company_id,
//            ];
//            $relations = '';
//            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
            if($modifAddOprate) static::addOprate($request, $controller, $saveData);

        }else {// 新加;要加入的特别字段
//            $addNewData = [
//                'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
            // 加入操作人员信息
            static::addOprate($request, $controller, $saveData);
        }

        // 省id历史
        if( isset($saveData['province_id']) && $saveData['province_id'] > 0 ){
            $province_id = $saveData['province_id'];
            $province_id_history = CTAPICityBusiness::getHistoryId($request, $controller, '', $province_id, CityHistoryAPIBusiness::$model_name,
                CityHistoryAPIBusiness::$table_name, ['city_table_id' => $province_id], [], $company_id , $notLog);
            $saveData['province_id_history'] = $province_id_history;
        }
        // 市id历史
        if( isset($saveData['city_id']) && $saveData['city_id'] > 0 ){
            $city_id = $saveData['city_id'];
            $city_id_history = CTAPICityBusiness::getHistoryId($request, $controller, '', $city_id, CityHistoryAPIBusiness::$model_name,
                CityHistoryAPIBusiness::$table_name, ['city_table_id' => $city_id], [], $company_id , $notLog);
            $saveData['city_id_history'] = $city_id_history;
        }
        // 区县id历史
        if( isset($saveData['area_id']) && $saveData['area_id'] > 0 ){
            $area_id = $saveData['area_id'];
            $area_id_history = CTAPICityBusiness::getHistoryId($request, $controller, '', $area_id, CityHistoryAPIBusiness::$model_name,
                CityHistoryAPIBusiness::$table_name, ['city_table_id' => $area_id], [], $company_id , $notLog);
            $saveData['area_id_history'] = $area_id_history;
        }

        // 新加或修改
        $result = static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);

        if($isModify){
            // 判断版本号是否要+1
            $historySearch = [
               //  'company_id' => $company_id,
                'staff_id' => $id,
            ];
            static::compareHistoryOrUpdateVersion($request, $controller, '' , $id, StaffHistoryAPIBusiness::$model_name
                , 'staff_history', $historySearch, ['staff_id'], 1, $company_id);
        }
        */


        // 更新登陆缓存
        /**
         *
        $redisKey = ( is_null($controller->redisKey) || empty($controller->redisKey) ) ? '' : $controller->redisKey;
        if($id > 0 && $controller->user_id == $id){
            $userInfo = $controller->user_info;
            $userInfo = array_merge($userInfo, $saveData);
            if (!empty($redisKey)) $controller->delUserInfo();// 是小程序，则先删除登陆缓存
            // 保存session
            // 存储数据到session...
            if (!session_id()) session_start(); // 初始化session
            // $_SESSION['userInfo'] = $userInfo; //保存某个session信息
            $redisKey = $controller->setUserInfo($userInfo, -1);
        }else{
            $userInfo = $saveData;
        }
        return ['redisKey' => $redisKey, 'result' => $userInfo];
         *
         */
        return $saveData;
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

//                $id = $extParams['id'] ?? 0;
//                if($id > 0){
//
//                }


                if(isset($judgeData['real_name']) && empty($judgeData['real_name'])  ){
                    // throws('真实姓名不能为空！');
                    $errMsgs['real_name'] = $tableLangConfig['judge_err']['real_name_is_must'] ?? '';
                }

                if(isset($judgeData['mobile']) && empty($judgeData['mobile'])  ){
                    // throws('手机不能为空！');
                    $errMsgs['mobile'] = $tableLangConfig['judge_err']['mobile_is_must'] ?? '';
                }

                if(isset($judgeData['admin_username']) && empty($judgeData['admin_username'])  ){
                    // throws('用户名不能为空！');
                    $errMsgs['admin_username'] = $tableLangConfig['judge_err']['admin_username_is_must'] ?? '';
                }
                break;
            default:
                break;
        }
        return $errMsgs;
    }

    /**
     * 判断后机号是否已经存在 true:已存在;false：不存在
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id id
     * @param string $fieldName 需要判断的字段名 mobile  admin_username  work_num
     * @param string $fieldVal 当前要判断的值
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  boolean 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeFieldExist(Request $request, Controller $controller, $id ,$fieldName, $fieldVal, $notLog = 0){
        $company_id = $controller->company_id;
        $queryParams = [
            'where' => [
                //  ['company_id', $company_id],
                [$fieldName,$fieldVal],
                // ['admin_type',self::$admin_type],
            ],
            // 'limit' => 1
        ];
        if( is_numeric($id) && $id >0){
            array_push($queryParams['where'],['id', '<>' ,$id]);
        }

        $infoData =  static::getInfoQuery($request, $controller, '', $company_id, 1, $queryParams, '');
        if(empty($infoData) || count($infoData)<=0){
            return false;
        }
        return true;
    }

    /**
     * 根据id可有 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
     *  id ；operate_type ； reason
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function staffOperateById(Request $request, Controller $controller, $modifAddOprate = false, $notLog = 0){
        $id = CommonRequest::getInt($request, 'id');
        $operate_type = CommonRequest::getInt($request, 'operate_type');
        // operate_type 可有 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
        $reason = CommonRequest::get($request, 'reason');// 原因

        $staffInfo = static::getInfoData($request, $controller, $id, ['admin_type', 'open_status', 'account_status'
            , 'on_line', 'city_site_id', 'real_name', 'mobile'], '', [], $notLog);
        if(empty($staffInfo)) throws('记录不存在');

        $admin_type = $staffInfo['admin_type'] ?? 0;
        if($admin_type != 32) throws('非快跑人员');

        $open_status = $staffInfo['open_status'] ?? 0;// 审核状态1待审核2审核通过3审核未通过--32快跑人员用
        $account_status = $staffInfo['account_status'] ?? 0;// 状态 0正常 1冻结
        $on_line = $staffInfo['on_line'] ?? 0;

        switch ($operate_type)
        {
            case 1://  1 提交申请修改信息 ;
                break;
            case 2:// 2 审核通过
                if($open_status != 1) throws('非待审核状态!');
                if(empty($staffInfo['city_site_id']) || empty($staffInfo['real_name']) || empty($staffInfo['mobile'])) throws('所属城市或真实姓名或手机为空，不能审核通过!');
                $saveData = [
                    'open_status' => 2,
                    'open_fail_reason' => '',
                ];
                break;
            case 3://  3 审核不通过
                if($open_status != 1) throws('非待审核状态!');
                $saveData = [
                    'open_status' => 3,
                    'open_fail_reason' => $reason,
                ];
                break;
            case 4:// 4 冻结
                if($account_status != 0) throws('非解冻状态!');
                $saveData = [
                    'account_status' => 1,
                    'frozen_fail_reason' => $reason,
                ];
                break;
            case 5:// 5 解冻
                if($account_status != 1) throws('非冻结状态!');
                $saveData = [
                    'account_status' => 0,
                    'frozen_fail_reason' => '',
                ];
                break;
            case 6://  6 上班
                if($open_status != 2) throws('非审核通过状态!');
                if($account_status == 1) throws('冻结状态!');
                // if($on_line != 1) throws('非下班状态!');
                $saveData = [
                    'on_line' => 2,
                ];
                break;
            case 7:// 7 下班
                if($open_status != 2) throws('非审核通过状态!');
                if($account_status == 1) throws('冻结状态!');
                // if($on_line != 2) throws('非上班状态!');
                $saveData = [
                    'on_line' => 1,
                ];
                break;
            default:
        }
        // operate_type 可有 操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
        $saveData['operate_type'] = $operate_type;
        $extParams = [
            //  'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = static::replaceById($request, $controller, $saveData, $id, $extParams, $modifAddOprate, $notLog);
        return $resultDatas;
    }

    /**
     * 注册发送手机验证码
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param string $mobile 发送的手机号
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param array $templateParams 其它数据参数 --一维数组，短信模板替换用
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSMSCodeLimit(Request $request, Controller $controller, $sms_key = '', $mobile = "", $countryCode = '86', $templateParams = [], $notLog = 0){

        // 多语言配置自动验证数据
        $judgeDataItem = [
            'mobile' => $mobile,
        ];
        $judgeData = $judgeDataItem;
        $mustFields = ['mobile'];
        static::judgeDataThrowsErr(1, $judgeData, $mustFields, [], 1, "<br/>", ['request' => $request, 'controller' => $controller]);
        $mobile_vercode = '';
        $smsConfig = [];
        // 发送手机验证码[判断手机号是否可以发送验证码]
        $reslutSMS = LimitSMS::sendSMSLimit($countryCode, $mobile, $sms_key, $templateParams, $mobile_vercode, $smsConfig, 1);
        if(is_string($reslutSMS)){
            $errMsg = $reslutSMS;
            throws($errMsg);
        }
        // 记录发送短信记录
        $currentNow = Carbon::now();
        $smsData = [
            'staff_id' => (int)$controller->user_id,
            'country_code' => $countryCode,
            'mobile' => $mobile,
            'sms_code' => $mobile_vercode,
            'sms_type' => 1,
            'sms_status' => 1,
            'count_date' => $currentNow->toDateString(),
            'count_year' => $currentNow->year,
            'count_month' => $currentNow->month,
            'count_day' => $currentNow->day,
        ];
        $sms_id = 0;
        CTAPISmsCodeBusiness::replaceById($request, $controller, $smsData, $sms_id, [ 'judgeDataKey' => 'replace',], true);
        return true;
    }

    /**
     * 校验手机验证码
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param string $mobile 发送的手机号
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string $vercode 验证码
     * @param boolean $del_cache 通过验证是否删除缓存 true:删除：false:不删除
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function sMSCodeVerify(Request $request, Controller $controller, $sms_key = '', $mobile = "", $countryCode = '86', $vercode = '', $del_cache = false){

        // 多语言配置自动验证数据
        $judgeDataItem = [
            'mobile' => $mobile,
        ];
        $judgeData = $judgeDataItem;
        $mustFields = ['mobile'];
        static::judgeDataThrowsErr(1, $judgeData, $mustFields, [], 1, "<br/>", ['request' => $request, 'controller' => $controller]);

        // 发送手机验证码[判断手机号是否可以发送验证码]
        $reslutSMS = LimitSMS::codeVerify($countryCode, $mobile, $sms_key, $vercode, $del_cache, 1);
        if(is_string($reslutSMS)){
            $errMsg = $reslutSMS;
            throws($errMsg);
        }
        return true;
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
        $user_id = $controller->user_id;
        $organize_id = CommonRequest::get($request, 'company_id');
        $admin_type = CommonRequest::get($request, 'admin_type');
        // 调用批量添加接口
        // 调用新加或修改接口
        $apiParams = [
            'organize_id' => $organize_id,
            'admin_type' => $admin_type,
            'saveData' => $saveData,
            'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 1,
        ];
        $methodName = 'importStaffs';
//        if(isset($saveData['mini_openid']))  $methodName = 'replaceByIdWX';
        $result = static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        return $result;
        // 参数
//        $requestData = [
//            'company_id' => $company_id,
//            'staff_id' =>  $controller->user_id,
//            'admin_type' =>  $controller->admin_type,//self::$admin_type,
//            'save_data' => $saveData,
//        ];
//        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
//        // 生成带参数的测试get请求
//        // $requestTesUrl = splicQuestAPI($url , $requestData);
//        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');

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
//            '县区' => 'department',
//            '归属营业厅或片区' => 'group',
//            '姓名或渠道名称' => 'channel',
            '学号' => 'student_number',
            '姓名' => 'real_name',
//            '工号' => 'work_num',
//            '职务' => 'position',
//            '手机号' => 'mobile',
            '性别' => 'sex',
        ];
//        $headArr = [
//            '1' => 'name',
//            '2' => 'chinese',
//            '3' => 'maths',
//            '4' => 'english',
//        ];
        $admin_type = CommonRequest::get($request, 'admin_type');
        $headArr = [
            '姓名' => 'real_name',
            '性别[未知|男|女]' => 'sex',
            '手机[唯一]' => 'mobile',
            '座机电话' => 'tel',
            'QQ\email\微信' => 'qq_number',
            '用户名[唯一]' => 'admin_username',
            '登录密码' => 'admin_password',
            '审核状态[待审核|审核通过|审核不通过]' => 'open_status',
            '冻结状态[正常|冻结]' => 'account_status',
        ];
        switch($admin_type){
            case 2:
                break;
            case 4:
                break;
            default:
                break;
        }

        try{
            $dataArr = ImportExport::import($fileName, $dataStartRow, $headRowNum, $headArr);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        }
        return self::import($request, $controller, $dataArr, $notLog);
    }

    // ***********导入***结束************************************************************

    /**
     * 开启 批量 或 单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 类型1平台2企业4个人
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string $id 记录id，多个用逗号分隔
     * @param int $open_status 操作 状态 2审核通过     4审核不通过
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function openAjax(Request $request, Controller $controller, $admin_type = 1, $organize_id = 0, $id = 0, $open_status = 2, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'admin_type' => $admin_type,
            'organize_id' => $organize_id,
            'id' => $id,
            'open_status' => $open_status,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $modifyNum = static::exeDBBusinessMethodCT($request, $controller, '',  'openStatusById', $apiParams, $company_id, $notLog);
        return $modifyNum;
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 冻结/解冻批量 或 单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 类型1平台2企业4个人
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string $id 记录id，多个用逗号分隔
     * @param int $account_status 操作 状态 1正常--解冻操作； 2冻结--冻结操作
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function accountStatusAjax(Request $request, Controller $controller, $admin_type = 1, $organize_id = 0, $id = 0, $account_status = 2, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'admin_type' => $admin_type,
            'organize_id' => $organize_id,
            'id' => $id,
            'account_status' => $account_status,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $modifyNum = static::exeDBBusinessMethodCT($request, $controller, '',  'accountStatusById', $apiParams, $company_id, $notLog);

        return $modifyNum;
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 删除批量 或 单条数据---只能删除管理员
     * 大后台: 管理员、企业 、 个人删除
     * 企业后台： 个人删除
     * 个人后台：无删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $admin_type 类型1平台2企业4个人
     * @param string $id 记录id，多个用逗号分隔
     * @param int $company_id 企业id--如果删除的是个人，且是 企业操作的时
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delByIds(Request $request, Controller $controller, $admin_type = 1, $id = 0, $company_id = 0, $notLog = 0){

        // 如果是单条删除
        if(is_numeric($id) || (is_string($id) && strpos($id, ',') === false )){
            $info = $controller->judgePower($request, $id);
            if($info['issuper'] == 1) throws('超级帐户不可删除!');
        }
        if($admin_type != 1)  throws('只能对管理员进行操作!');
        // 根据Id删除数据
        // return CTAPIStaffBusiness::delAjax($request, $controller);
        // 根据条件删除数据
        $queryParams = [
            'where' => [
                // ['company_id', $company_id],
                ['admin_type', $admin_type],
                ['issuper', '<>', 1],
            ],
//            'select' => [
//                'id','company_id','real_name'
//            ],
            // 'orderBy' => ['sort_num'=>'desc','id'=>'desc'],
        ];
        // 加入 id
        Tool::appendParamQuery($queryParams, $id, 'id');
        // 删除的是个人， 是企业后台--操作的-- 企业只能删除自己的员工
        if($admin_type == 4 && $controller->user_type == 2){
            if(!isset($queryParams['where'])) $queryParams['where'] = [];
            array_push($queryParams['where'], ['company_id', $company_id]);// $controller->user_id
        }
        $delResult = CTAPIStaffBusiness::delRecordByQuery($request, $controller, '', $queryParams, 0);
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
            $admin_type = CommonRequest::get($request, 'admin_type');
            $headArr = ['admin_username'=>'用户名', 'issuper_text'=>'角色', 'real_name'=>'姓名', 'sex_text'=>'性别', 'mobile'=>'手机号',
                 'tel'=>'座机电话', 'qq_number'=>'QQ\email\微信', 'lastlogintime'=>'上次登录', 'created_at'=>'创建时间'];
            $fileName = '系统管理员';
            $sheetTitle = '系统管理员';
            switch($admin_type){
                case 2:
                    break;
                case 4:
                    break;
                default:
                    break;
            }
            ImportExport::export('',$fileName,$data_list,1, $headArr, 0, ['sheet_title' => $sheetTitle]);
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
        $data_list = [];
        $headArr = ['real_name'=>'姓名', 'sex'=>'性别[未知|男|女]', 'mobile'=>'手机[唯一]', 'tel'=>'座机电话', 'qq_number'=>'QQ\email\微信'
            , 'admin_username'=>'用户名[唯一]', 'admin_password'=>'登录密码', 'open_status'=>'审核状态[待审核|审核通过|审核不通过]'
            , 'account_status'=>'冻结状态[正常|冻结]'];

        $admin_type = CommonRequest::get($request, 'admin_type');
        $fileName = '系统管理员导入模版';
        $sheetTitle = '系统管理员';
        switch($admin_type){
            case 2:
                break;
            case 4:
                break;
            default:
                break;
        }
        ImportExport::export('',$fileName, $data_list,1, $headArr, 0, ['sheet_title' => $sheetTitle]);
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
        // 城市数据
        $cityArr = [];
        // 行业数据
        $industryArr = [];
        // 人员扩展信息数据
        $extendArr = [];

        //        if(!empty($data_list) ){
        // 获得所属城市
        if(in_array('city', $handleKeyArr)){
            $cityIdArr = array_values(array_filter(array_column($data_list,'city_id')));// 资源id数组，并去掉值为0的
            // 主键为下标的二维数组
            if(!empty($cityIdArr)) $cityArr = Tool::arrUnderReset(CTAPICitysBusiness::getListByIds($request, $controller, $cityIdArr), 'id', 1);
            if(!$isNeedHandle && !empty($cityArr)) $isNeedHandle = true;
        }
        // 获得所属行业
        if(in_array('industry', $handleKeyArr)){
            $industryIdArr = array_values(array_filter(array_column($data_list,'company_industry_id')));// 资源id数组，并去掉值为0的
            // 主键为下标的二维数组
            if(!empty($industryIdArr)) $industryArr = Tool::arrUnderReset(CTAPIIndustryBusiness::getListByIds($request, $controller, $industryIdArr), 'id', 1);
            if(!$isNeedHandle && !empty($industryArr)) $isNeedHandle = true;
        }
        // 获得人员扩展信息
        if(in_array('extend', $handleKeyArr)){
            $extendIdArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
            // 主键为下标的二维数组
            if(!empty($extendIdArr)) $extendArr = Tool::arrUnderReset(CTAPIStaffExtendBusiness::getListByIds($request, $controller, $extendIdArr, [], [], 'staff_id'), 'staff_id', 1);
            if(!$isNeedHandle && !empty($extendArr)) $isNeedHandle = true;
        }

        //        }

        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
//        if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
//            return true;
//        }

        foreach($data_list as $k => $v){
            //            // 公司名称
            //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
            //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);

            // 获得所属城市
            if(in_array('city', $handleKeyArr)){
                $data_list[$k]['city_info'] = $cityArr[$v['city_id']] ?? [];
                $data_list[$k]['city_name'] = $cityArr[$v['city_id']]['city_name'] ?? [];
            }

            // 获得所属行业
            if(in_array('industry', $handleKeyArr)){
                $data_list[$k]['industry_info'] = $industryArr[$v['company_industry_id']] ?? [];
                $data_list[$k]['industry_name'] = $industryArr[$v['company_industry_id']]['industry_name'] ?? [];

            }
            // 获得人员扩展信息
            if(in_array('extend', $handleKeyArr)){
                $data_list[$k]['extend_info'] = $extendArr[$v['id']] ?? [];

            }
        }

        // 重写结束
        return true;
    }

}
