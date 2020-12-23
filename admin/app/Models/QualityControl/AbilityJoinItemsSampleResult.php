<?php
// 能力验证取样登记样品结果

namespace App\Models\QualityControl;

class AbilityJoinItemsSampleResult extends BasePublicModel
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

    public static $cacheVersion = 'V4';// 内容随意改[可0{空默认为0}开始自增]- 如果运行过程中，有直接对表记录进行修改，增加或修改字段名，则修改此值，使表记录的相关缓存过期。
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

    // public static $IntPriceFields = [];//[有则设置] 表中整型表示价格的字段数组 -- 一维数组，目的：方便统一把数据中的字段转浮点数或转整数

    // 自有属性
    // 0：都没有；
    // 1：有历史表 ***_history;
    // 2：有操作员工id 字段 operate_staff_id
    // 4：有操作员工历史id 字段 operate_staff_id_history
    // 8：有操作日期字段 created_at timestamp
    // 16：有更新日期字段 updated_at  timestamp
    // 32: 有历史表 ***_history; 且 此表实时记录主表数据 （实时数据[不会删除]  +  历史修改过程中的数据）--全表记录【所有记录及历史】--可追溯
    // 64: 有同步数据表 ***_doing;--业务进行表【轻量级表】，当业务进行中时，可直接操作进行表【提高数据操作的率】，
    //                  一旦业务完成，则删除进行表中的数据，原表作为原始数据使用
    //                  -- TODO 直接操作业务写到操作操作的底层 CommonDB 【存在就同步更新，不存在：业务已结束或不用进行表了】
    public static $ownProperty = (2 | 4 | 8 | 16);// (1 | 2 | 4 | 8 | 16);

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ability_join_items_sample_result';

    /**
     * 获取能力验证检测所用仪器-二维
     */
    public function abilityJoinItemsResultsInstruments()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinItemsResultsInstrument', 'result_id', 'id');
    }

    /**
     * 获取能力验证检测标准物质-二维
     */
    public function abilityJoinItemsResultsStandards()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinItemsResultsStandard', 'result_id', 'id');
    }

    /**
     * 获取所属能力验证报名项表--一维
     */
    public function abilityJoinItem()
    {
        return $this->belongsTo('App\Models\QualityControl\AbilityJoinItems', 'ability_join_item_id', 'id');
    }

    /**
     * 获取关联到的能力验证检测方法依据---一维
     */
    public function abilityJoinItemsResultsMethod()
    {
        return $this->hasOne('App\Models\RunBuy\AbilityJoinItemsResultsMethod', 'result_id', 'id');
    }
}
