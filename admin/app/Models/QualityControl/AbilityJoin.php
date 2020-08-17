<?php
// 能力验证报名主表

namespace App\Models\QualityControl;

class AbilityJoin extends BasePublicModel
{
    //****************数据据缓存**相关的***开始********************************************
//    public static $cachePre = 'cacheDB';// 缓存键最前面的关键字  cacheDb:U:{id值}_{email值}  中的 cacheDb
//    public static $separatoChar = '__';// 缓存相关的分隔符-主要是键;注意合法性，会作为redis键的一部分
//    public static $cacheTimeTableKey = 'Ttbl';// 缓存表更新时间时的缓存关键字
//    public static $cacheTimeBlockKey = 'Tblock';// 缓存块更新时间时的缓存关键字
//    public static $cacheTimeInfoKey = 'Tinfo';// 缓存表具体详情更新时间时的缓存关键字
//    public static $cacheInfoKey = 'info';// 缓存表具体详情数据的缓存关键字
//    public static $cachePrimaryValInfoKey = 'TpriVal';// 缓存表其它缓存字段缓存主键值的缓存关键字
//    public static $operateRedis = 2;// 操作 1 转为json 2 序列化 ; 3 不转换 ---最好用2 序列化，不然可能会有问题
//    public static $cacheExpire = 60 * 60 * 24 * 10;// 10 天 缓存的时间长度 ; 值<= 0时，会使用 public.DBDataCache.expire 配置

    // 1 缓存详情 2缓存块[确定没有用到关系的块，可以缓存]
    //  public.DBDataCache.cacheType 配置打开，且各模型也打开才会有对应缓存
    public static $cacheType = (1 | 2);// 0
    // 最大缓存数据行数，如果>此值的数据不缓存。; 值<= 0时，会使用 public.DBDataCache.maxCacheRows 配置
    public static $maxCacheRows = 0;

//    public static $cacheSimple = 'U';// 表名简写,为空，则使用表名

    public static $cacheVersion = 'V7';// 内容随意改[可0{空默认为0}开始自增]- 如果运行过程中，有直接对表记录进行修改，增加或修改字段名，则修改此值，使表记录的相关缓存过期。
    // $cacheExcludeFields 为空：则缓存所有字段值；排除字段可能是大小很大的字段，不适宜进行缓存
    public static $cacheExcludeFields = [];// 表字段中排除字段; 有值：要小心，如果想获取的字段有在排除字段中的，则不能使用缓存


//    public static $cachePrimaryFields = 'id';//格式 '字段a ' 或 一维数组 ['字段b','字段c',....] 为空，则通过 表的主键缓存，再没有就不缓存

    // 可作为单条记录缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段，缓存值为指向 id 字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    public static $cachePrimaryKeyFields = [];

    // 此属性有值；则是多情况（多种平台应该；如按城市分站）缓存，为空：系统/公用类别的缓存
    // 块数据缓存时，需要标记缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    // 为空，则表级缓存块
    // 有新下标加入或字段变动，所有缓存会自动失效。删除下标：不会影响已有缓存
    public static $cacheBlockFields = [];

    // 单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
    // 值[] 空时，会使用 public.DBDataCache.openCache 配置
//    public static $openCache = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 3,// 访问次数
//    ];
    // 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
    // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置
//    public static $extendExpire = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 8,// 访问次数
//        'maxExendNum' => 3,// 可延长3次
//    ];

    //****************数据据缓存**相关的***结束********************************************

    // 自有属性
    // 0：都没有；
    // 1：有历史表 ***_history;
    // 2：有操作员工id 字段 operate_staff_id
    // 4：有操作员工历史id 字段 operate_staff_id_history
    // 8：有操作日期字段 created_at timestamp
    // 16：有更新日期字段 updated_at  timestamp
    public static $ownProperty = (2 | 4 | 8 | 16);// (1 | 2 | 4 | 8 | 16);

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ability_join';

    // 拥有者类型1平台2企业4个人
    public static $adminTypeArr = [
        '1' => '平台',
        '2' => '企业',
        '4' => '个人',
    ];

    // 状态(1已报名;2已取样;4已完成【已验证结果】【如果有有问题、不满意 --还可以再取样--进入已取样状态--- 可以打印证书;
    // 8已完成--不可再修改【打印证书后，不可再操作或大后台点《公布结果》后子项都已完成状态】)
    public static $statusArr = [
        '1' => '已报名',// 初始状态
//        '2' => '补测中',
//        '2' => '补测待取样',
//        '4' => '已取样',// 不需要
        '4' => '进行中',// 进行中-- 初测取样开始---
        '8' => '有判定',// 进行中---有部分有评定【初测或补测】---进行中[有评定]

        '16' => '待发证',// 全部都有结果--都有评定了，有需要发证的---后面是发证流程--全评定
        '32' => '无证书',// 如果没有有要进行证书的 或  没有一个满意的直接进入-- 终极状态--已完成[无证书]
        '64' => '已发证书',// 发完证书的-- 终极状态---已完成[有证书]
    ];

    // 是否补测 0正常测 1补测1 2 补测2 .....
    public static $retryNoArr = [
        '0' => '初测',
        '1' => '补测',
//        '2' => '补测2',
//        '3' => '补测3',
    ];

    // 是否取样1待取样--未取 2已取样--已取; 4 补测待取样 ; 8 补测已取样
    public static $isSampleArr = [
        '1' => '待取样',
        '2' => '已取样',
        '4' => '待取样[补测]',
        '8' => '已取样[补测]',
    ];

    // 证书是否打印 1未打印 2 已打印
    public static $isPrintArr = [
        '1' => '未打印',
        '2' => '已打印',
    ];

    // 证书是否领取 1未领取 2 已领取
    public static $isGrantArr = [
        '1' => '未领取',
        '2' => '已领取',
    ];

    // 表里没有的字段
    protected $appends = ['admin_type_text', 'status_text', 'retry_no_text', 'is_sample_text', 'is_print_text', 'is_grant_text'];


    /**
     * 获取用户的类型文字
     *
     * @return string
     */
    public function getAdminTypeTextAttribute()
    {
        return static::$adminTypeArr[$this->admin_type] ?? '';
    }

    /**
     * 获取拥有者类型文字
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return static::$statusArr[$this->status] ?? '';
    }

    /**
     * 获取是否补测文字
     *
     * @return string
     */
    public function getRetryNoTextAttribute()
    {
        return static::$retryNoArr[$this->retry_no] ?? '';
    }

    /**
     * 获取是否取样文字
     *
     * @return string
     */
    public function getIsSampleTextAttribute()
    {
        return static::$isSampleArr[$this->is_sample] ?? '';
    }

    /**
     * 获取证书是否打印文字
     *
     * @return string
     */
    public function getIsPrintTextAttribute()
    {
        return static::$isPrintArr[$this->is_print] ?? '';
    }

    /**
     * 获取证书是否领取文字
     *
     * @return string
     */
    public function getIsGrantTextAttribute()
    {
        return static::$isGrantArr[$this->is_grant] ?? '';
    }

    /**
     * 获取能力验证操作日志-二维
     */
    public function abilityJoinLogs()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinLogs', 'ability_join_id', 'id');
    }

    /**
     * 获取能力验证报名项-二维
     */
    public function abilityJoinItems()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinItems', 'ability_join_id', 'id');
    }

    /**
     * 获取所属帐号--一维
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\QualityControl\Staff', 'staff_id', 'id');
    }

}
