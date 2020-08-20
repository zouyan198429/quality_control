<?php
// 各个Business下的各数据类会用到的[基本]方法
namespace App\Business;


use App\Services\Tool;

class BaseBusiness
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 功能：获得models数据表说明内容
     *
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangModelsDBConfig($configUbound = 'fields', $errDo = 1){
        $tableLangConfig = static::getLangTableConfig($configUbound, 'models',$errDo);
        if(is_string($tableLangConfig)){
            $error = $tableLangConfig;// "没有配置信息！";
            if($errDo == 1) throws($error);
            return $error;
        }
        return $tableLangConfig;
    }

    /**
     * 功能：获得models数据库说明内容
     *
     * @param int $langType 数据库多语言文件的类型 1 总标识model的 8 当前数据库的 -- 只能一个一个的用
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangModelsConfig($langType = 8, $errDo = 1){
        $dbLangConfig = static::getLangDBConfig($langType,  'models', $errDo);
        if(is_string($dbLangConfig)){
            $error = $dbLangConfig;// "没有配置信息！";
            if($errDo == 1) throws($error);
            return $error;
        }
        return $dbLangConfig;
    }

    /**
     * 功能：获得数据表字段说明内容--主要用来做数据验证
     *
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $dbFileTag 数据表配置文件的标识
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangTableConfig($configUbound = 'fields', $dbFileTag = 'models', $errDo = 1){
        $dbDir = static::$database_model_dir_name;
        $fieldsConfig = Tool::getDBFieldsConfig(static::$table_name, $configUbound, $dbDir, $dbFileTag, $errDo);
        if(is_string($fieldsConfig)){
            $error = $fieldsConfig;// "没有配置信息！";
            if($errDo == 1) throws($error);
            return $error;
        }
        return $fieldsConfig;
    }

    /**
     * 功能：获得数据库说明内容
     *
     * @param int $langType 数据库多语言文件的类型 1 总标识model的 8 当前数据库的 -- 只能一个一个的用
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $dbFileTag 数据表配置文件的标识
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangDBConfig($langType = 8, $dbFileTag = 'models', $errDo = 1){
        $dbDir = static::$database_model_dir_name;
        $modelsDBConfig = Tool::getDBConfig($langType, static::$table_name, $dbDir, $dbFileTag, $errDo);
        if(is_string($modelsDBConfig)){
            $error = $modelsDBConfig;// "没有配置信息！";
            if($errDo == 1) throws($error);
            return $error;
        }
        return $modelsDBConfig;
    }
    // ~~~~~~~~~~~插入/修改数据时，数据进行验证~~~~~~~~开始~~~~~~~~~~~~~~~~~~~
    // 验证已经移入到数据操作层，更底层，但是这里可以提前作些验证

    /**
     * 功能：插入/修改数据时，对数据进行验证
     *
     * @param array $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
     * @param array $judgeData 待验证数据 一维/二维
     * @param array $mustFields 必填字段
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  throws 错误 boolean: true 成功 ; array 具体错误 一维/二维的[根数据的维数一样]
     *  [
     *      'firstErr' => $firstErr,// 第一条错误信息
     *       'errMsg' => $ItemErrMsg,// 所有错误数组 一维数组
     *       'varErrMsg' => $varErrMsg,// 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
     *  ]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function judgeDBData($judgeType = 1, &$judgeData = [], $mustFields = [], $configUbound = 'fields', $errDo = 1){
        $dbDir = static::$database_model_dir_name;// explode('\\', static::$model_name)[0] ?? 'RunBuy';
        $result = Tool::judgeInDBData($judgeType, $judgeData, $mustFields, static::$table_name, $configUbound, $dbDir, 'models', $errDo);
        if(is_string($result)){
            // $error = "没有配置信息！";
            if($errDo == 1) throws($result);
            return $result;
        }
        return $result;
    }

    // 对错误有错进行throws抛出 ,成功，则返回 true;有错，则throws 或返回
    // $reType 有错返回类型 1 throws错误， 2返回字符串; 4 返回下标的数组
    // $errSlipChar 错误分隔符
    public static function judgeDBDataThrowErr($judgeType = 1, &$judgeData = [], $mustFields = [], $reType = 1, $errSlipChar = "<br/>", $configUbound = 'fields'){
        $isMulti = Tool::isMultiArr($judgeData, false);
        $result =static::judgeDBData($judgeType, $judgeData, $mustFields, $configUbound,  1);
        if($result === true) return true;
        if(!$isMulti) $result = [$result];
        $firstErr = [] ;// 第一条错误信息 的数组
        $ItemErrMsg = [] ;// 所有错误数组 一维数组
        $varErrMsg = [] ;  // 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
        foreach($result as $k => $v){
            $temItemErr = $v['errMsg'] ?? [];
            if(!empty($temItemErr)) $ItemErrMsg = array_merge($ItemErrMsg, $temItemErr);
            $temItemVarErr= $v['varErrMsg'] ?? [];
            if(!empty($temItemVarErr)) {
                if($isMulti){// 原数据是二维
                    // if(!isset( $varErrMsg[$k]))  $varErrMsg[$k] = [];
                    $varErrMsg[$k] = $temItemVarErr;
                }else{// 原数据是一维
                    $varErrMsg = $temItemVarErr;
                }

            }
        }
        $errMsg = implode($errSlipChar, $ItemErrMsg);
        if(($reType & 1) == 1) throws($errMsg);
        if(($reType & 4) == 4) return $varErrMsg;
        return $errMsg;
    }

    // ~~~~~~~~~~~插入/修改数据时，数据进行验证~~~~~~~~结束~~~~~~~~~~~~~~~~~~~

    // ~~~~~~~~特殊的验证~~~数据进行验证~~~~~~~~开始~~~~~~~~~~~~~~~~~~~
    /**
     * 特殊的验证
     *
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param string/array $judgeDataKey 验证规则的关键字 字符[多个用逗号分隔]或数组[一维]
     * @param array $extParams 其它扩展参数，
     * @return  array 错误：非空数组；正确：空数组
     * @author zouyan(305463219@qq.com)
     */
    public static function specialJudgeKey(&$mustFields = [], &$judgeData = [], $judgeDataKey = '', $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $errMsgs = [];// 错误信息的数组--一维数组，可以指定下标
        // 如果是字符，则转为数组
        if(is_string($judgeDataKey)) $judgeDataKey = explode(',', $judgeDataKey);
        if(empty($judgeDataKey)) return $errMsgs;

        $tableLangConfig = static::getLangTableConfig('', 'models',1);
        //  pr($tableLangConfig);
        foreach($judgeDataKey as $key){
            $result = static::singleJudgeDataByKey($mustFields, $judgeData, $key, $tableLangConfig, $extParams);
            if(!empty($result) && is_array($result)) $errMsgs = array_merge($errMsgs, $result);
        }
        return $errMsgs;
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
                break;
            default:
                break;
        }
        return $errMsgs;
    }

    /**
     * 验证数据，自定义[重写的singleJudgeDataByKey方法]
     *
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param string/array $judgeDataKey 验证规则的关键字 字符[多个用逗号分隔]或数组[一维]
     * @param array  $reType 有错返回类型 1 throws错误， 2返回字符串4 返回下标的数组
     * @param string  $errSlipChar 错误分隔符
     * @param array $extParams 其它扩展参数，
     * @return  mixed true:正确 错误：throws错误；string 错误的字符串,array(一或二维),其中每一维:['下标'] =>  ['错误内容1','错误内容2','错误内容3']
     * @author zouyan(305463219@qq.com)
     */
    public static function specialJudgeData(&$judgeData = [], &$mustFields = [], $judgeDataKey = [], $reType = 1, $errSlipChar = "<br/>", $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        // $isMulti = Tool::isMultiArr($judgeData, false);
        $errMsgs = [];
        // 先验证自定义
        if(!empty($judgeDataKey)){
            $errMsgs = static::specialJudgeKey($mustFields, $judgeData, $judgeDataKey, $extParams);
        }
        if(empty($errMsgs)) return true;
        $errOneStrArr = [];// 一维数组
        $errArr = [];
        foreach ($errMsgs as $k => $v) {
            if (is_array($v)) {// 二维
                foreach ($v as $t_k => $t_v) {
                    if (is_array($t_v)) {
                        $errArr[$k][$t_k] = $t_v;
                        $errOneStrArr = array_merge($errOneStrArr, $t_v);
                    } else {
                        $errArr[$k][$t_k] = [$t_v];
                        array_push($errOneStrArr,$t_v);
                    }

                }
            } else {// 一维
                $errArr[$k] = [$v];
                array_push($errOneStrArr,$v);
            }
        }
        // 二维数组，则优先返回
        // if($isMulti) return $errArr;

        // 有错 throws
        if(($reType & 1) == 1) throws(implode($errSlipChar, array_unique($errOneStrArr)));

        if(($reType & 2) == 2) return implode($errSlipChar, array_unique($errOneStrArr));
        if(($reType & 4) == 4) return $errArr;
        return true;

    }
    // ~~~~~~~~特殊的验证~~~数据进行验证~~~~~~~~结束~~~~~~~~~~~~~~~~~~~

    /**
     * 验证数据，先验证自定义[重写的singleJudgeDataByKey方法]，如果通过再验证多语言表的
     *
     * @param array $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param string/array $judgeDataKey 验证规则的关键字 字符[多个用逗号分隔]或数组[一维]
     * @param array  $reType 有错返回类型 1 throws错误[按顺序优先]， 2返回字符串[两种错误会拼接] 4 返回下标的数组[验证的数据是一维，则合并，二维则，按顺序优先]
     * @param string  $errSlipChar 错误分隔符
     * @param array $extParams 其它扩展参数，
     * @return  mixed true:正确 错误：throws错误；array(一或二维),其中每一维:['下标'] =>  ['错误内容1','错误内容2','错误内容3']
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeDataThrowsErr($judgeType = 1, &$judgeData = [], &$mustFields = [], $judgeDataKey = [], $reType = 1, $errSlipChar = "<br/>", $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $isMulti = Tool::isMultiArr($judgeData, false);
        $errMsgs = static::specialJudgeData($judgeData, $mustFields, $judgeDataKey, $reType, $errSlipChar, $extParams);
        // 二维数组，则优先返回
        if($isMulti && is_array($errMsgs)) return $errMsgs;

        // 返回 true:成功 ; string :失败的字符串；array(一或二维),其中每一维:['下标'] =>  ['错误内容1','错误内容2','错误内容3']
        $result = static::judgeDBDataThrowErr($judgeType,$judgeData, $mustFields, $reType);

        if($result === true){// 成功
            if($errMsgs === true) return true;
            if(($reType & 2) == 2) return $errMsgs;// implode($errSlipChar, $errMsgs);
            if(($reType & 4) == 4) return $errMsgs;
        }else{// 失败
            if($errMsgs === true) return $result;
            if(($reType & 2) == 2) return $errMsgs . $errSlipChar . $result;
            if(($reType & 4) == 4){
                if(!$isMulti) {// 原数据是一维数组
                    foreach($result as $k => $v){
                        if(!isset($errMsgs[$k])) continue;

                        $result[$k] = array_unique(array_merge($errMsgs[$k], $v));
                        unset($errMsgs[$k]);
                    }
                    if(!empty($errMsgs)) $result = array_merge($errMsgs, $result);
                }
                return $result;
            }
        }
        return true;
    }

    /**
     * 获得redis key前缀
     * @param int $keyNum 标识前紭编号 1 项目(整个资源集合)唯一标识；2 站点唯一标识,为空则为当前域及端口；
     *                                  4 环境标识 本地开发环境:local; 测试环境:testing;生产环境:production
     *                                  8  数据库ip ;  16 数据库端口  ; 32 数据库名
     *                                  64 数据库关键字;--如果为空，则默认为 数据库ip + 数据库端口 + 数据库名
     * @param string $itemSplit 数组转让字符串时的分隔符 默认 ':'
     * @param string $appendStr 字符不为空，则未尾加的字符 默认 ':'
     * @return  string 缓存前缀
     * @author zouyan(305463219@qq.com)
     */
    public static function getProjectKeyPre($keyNum = 0, $itemSplit = ':', $appendStr = ':'){
        return Tool::getProjectKey($keyNum, $itemSplit, $appendStr);
    }
}
