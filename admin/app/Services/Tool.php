<?php

namespace App\Services;
use App\Services\Lock\RedisesLock;
use App\Services\Lock\RedisLock;
use App\Services\Redis\RedisString;
use App\Services\Request\CommonRequest;
use App\Services\SessionCustom\SessionCustom;
use Dingo\Api\Facade\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

/**
 * 通用工具服务类
 */
class Tool
{

    /**
     * HTTP Protocol defined status codes
     * HTTP协议状态码,调用函数时候只需要将$num赋予一个下表中的已知值就直接会返回状态了。
     * @param int $num
     *
     */
    public static function https($num) {
        $http = array (
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out"
        );
        header($http[$num]);
    }

    /**
     * 取得IP
     *
     *
     * @return string 字符串类型的返回结果
     */
    public static function getIp(){
        if (@$_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP']!='unknown') {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']!='unknown') {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/^\d[\d.]+\d$/', $ip) ? $ip : '';
    }


    /**
     * 获取文件列表(所有子目录文件)
     *
     * @param string $path 目录
     * @param array $file_list 存放所有子文件的数组
     * @param array $ignore_dir 需要忽略的目录或文件
     * @return boolean 数据格式的返回结果
     */
    public static function readFileList($path,&$file_list,$ignore_dir=array()){
        $path = rtrim($path,'/');
        if (is_dir($path)) {
            $handle = @opendir($path);
            if ($handle){
                while (false !== ($dir = readdir($handle))){
                    if ($dir != '.' && $dir != '..'){
                        if (!in_array($dir,$ignore_dir)){
                            if (is_file($path.DS.$dir)){
                                $file_list[] = $path.DS.$dir;
                            }elseif(is_dir($path.DS.$dir)){
                                self::readFileList($path.DS.$dir,$file_list,$ignore_dir);
                            }
                        }
                    }
                }
                @closedir($handle);
                //return $file_list;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    /**
     * 生成订单流水号（18位数字）
     * 最大可以支持1分钟1亿订单号不重复
     *
     * @return string $orderSn
     */
    public static function createSn($namespace = 'default', $prefix = '', $length = 8)
    {
        $insertId = Yii::$app->redis->incr('FlowSn:' . ucfirst($namespace));
        $suffix   = self::getSnSuffix();

        return $prefix . date('ymdHi') . str_pad(substr($insertId, -$length), $length, 0, STR_PAD_LEFT) . $suffix;
    }

    /**
     * 产生随机字符串
     *
     * @param int $length
     *
     * @return string
     */
    public static function createRandomStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $str;
    }

    /**
     * 生成随机令牌凭证
     *
     * @return string
     */
    public static function buildToken($uniqueId = null)
    {
        return sha1(uniqid($uniqueId) . mt_rand(1, 10000));
    }

    /**
     * 订单号生成器
     * @param int $uid 用户id
     * @return int
     */
    public static function order_sn($uid)
    {
        // return '619' . date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT) . $uid;
        //return date('YmdHis') . str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT)
        //    . str_pad($uid,4,"0",STR_PAD_LEFT);
        return date('ymdHis') . str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        // . str_pad($uid,4,"0",STR_PAD_LEFT);
    }

    /**
     * ShopNC 生成订单编号
     * @return string
     */
    public static function snOrder() {
        // $recharge_sn = date('Ymd').substr( implode(NULL,array_map('ord',str_split(substr(uniqid(),7,13),1))) , -8 , 8);
        $recharge_sn = date('ymd').substr( implode(NULL,array_map('ord',str_split(substr(uniqid(),7,13),1))) , -8 , 8);
        return $recharge_sn;
    }

    /**
     * 生成订单号
     *在网上找了一番，发现这位同学的想法挺不错的，redtamo，具体的请稳步过去看看，
     * 我作简要概述，该方法用上了英文字母、年月日、Unix 时间戳和微秒数、随机数，重复的可能性大大降低，还是很不错的。
     * 使用字母很有代表性，一个字母对应一个年份，总共16位，不多也不少.
     *
     * @return string
     */
    public static function createOrder(){
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', mt_rand(0, 99));
        return $orderSn;
    }


    /**
     * 生成订单流水号（18位数字）+ 前部会含 8位日期时间
     *
     * @param string $namespace redis记数器标识键 ；因为有redis锁，可以考虑把用户后两位做为用户分流，就分成100流了，注意在订单前缀/后缀加上分流的用户后两位。
     * @param array $fixParams
     *    $fixParams = [
     *       'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *        'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *        'backfix' => '',// 后缀[1-2位] 可填;备用
     *        'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
     *        'needNum' => 0,// 需要拼接的内容 1 年 2日期[一年中的第几个分钟[内-向上取整]] 4 自定义日期格式 8 自增的序号
     *        'dataFormat' => '', // needNum 值为 4时的日期格式  'YmdHis'
     *               // Y: 年，四位数字  y: 年，两位数字
     *               // n: 月份，两位数字，不补零；从"1"至"12"  m 数字表示的月份，有前导零 01 到 12
     *                //z 年份中的第几天 0 到 365
     *                // d: 几日，两位数字，若不足则补零；从"01"至"31"  j: 几日，不足不被零；从"1"至"31"
     *                   // h: 12小时制的小时，从"01"至"12"
     *                   //  g 小时，12 小时格式，没有前导零 1 到 12  ;
     *                   //  H: 24小时制的小时，从"00"至"23"；
     *                   //  G: 24小时制的小时，不补零；从"0"至"23"
     *                // i 有前导零的分钟数 00 到 59>
     *               // s 秒数，有前导零 00 到 59>
     *   ];
     * @param int $length 字符串长度- 使用以后，只能增，不建议减[这样可以按时间排序] 选择自己适合的体量/每分钟  订单[选4] ,  其它不重要的单号评估一下，一分钟生成的数量，给高/低
     *                                           共用          一个用户保一单/分
     * 1 最大可以支持1分钟10个订单号不重复       10/分          10 * 用户要用的位数(如2位100) = 1千用户
     * 2 最大可以支持1分钟1百个订单号不重复       1百/分        100 *   100  = 1 万+
     * 3 最大可以支持1分钟1千订单号不重复       1千/分          1000 *  100 =  10 万+
     * 4 最大可以支持1分钟1万订单号不重复       1万/分          1万 *  100  =  100 万+
     * 5 最大可以支持1分钟10万订单号不重复       10万/分       10万*  100  =   千万+
     * 6 最大可以支持1分钟百万订单号不重复       百万/分       百万 * 100 =  1亿+
     * 7 最大可以支持1分钟千万订单号不重复       千万/分
     * 8 最大可以支持1分钟1亿订单号不重复       1亿/分
     * 9 最大可以支持1分钟十亿订单号不重复       十亿/分
     * 10 最大可以支持1分钟百亿订单号不重复       百亿/分
     * 11 最大可以支持1分钟千亿订单号不重复       千亿/分
     * 12 最大可以支持1分钟万亿订单号不重复       万亿/分
     * @param string $backfix 后缀[1-2位] 可填
     * @return mixed
     */
    public static function makeOrder($namespace = 'default', $fixParams = [], $length = 6){
        $prefix = $fixParams['prefix'] ?? '';
        $midFix = $fixParams['midFix'] ?? '';
        $backfix = $fixParams['backfix'] ?? '';
        $expireNums = $fixParams['expireNums'] ?? [];// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
        $needNum = $fixParams['needNum'] ?? 0;// 需要拼接的内容 1 年 2日期 4 自增的序号
        $dataFormat = $fixParams['dataFormat'] ?? '';// needNum 值为 4时的日期格式
        // 业务编号(1位0-9); 年(2位 当前-99)  ; 月12--2位	日31--2位(12*31=372 --3位) ; 时24--2位	分60--2位	秒 60--2位 (=86400 --5位)
        if( (($needNum & (1 + 2)) > 0)  ) $year = date('y');// Y: 年，四位数字  y: 年，两位数字

        if(($needNum & 2) == 2){
            $month = date('n');//n: 月份，两位数字，不补零；从"1"至"12"  m 数字表示的月份，有前导零 01 到 12
            $yearDays =((int)  date('z'));// + 1;//z 年份中的第几天 0 到 365
            $day = date('j');// d: 几日，两位数字，若不足则补零；从"01"至"31"  j: 几日，不足不被零；从"1"至"31"
            // h: 12小时制的小时，从"01"至"12"
            //  g 小时，12 小时格式，没有前导零 1 到 12  ;
            //  H: 24小时制的小时，从"00"至"23"；
            //  G: 24小时制的小时，不补零；从"0"至"23"
            $hour = date('G') + 1;
            // i 有前导零的分钟数 00 到 59>
            $minute = ((int) date('i')) + 1;
            // s 秒数，有前导零 00 到 59>
            $second = ((int) date('s')) + 1;
            //一年中的第几个分钟[内] 月*日*时 12*31*24=8928--4位
            $mdh = $yearDays * 24 * 60 + $minute;
//        echo '$year = ' . $year . ';$month = ' . $month  . ';$yearDays = ' . $yearDays . ';$day = ' . $day . ';$hour = ' . $hour . ';$minute = ' . $minute . ';$second = ' . $second . '<br/>';
//        echo '$mdh = ' . $mdh . '<br/>';
        }

        $lockKey = Tool::getUniqueKey([Tool::getProjectKey(1, ':', ':'), Tool::getActionMethod(), __CLASS__, __FUNCTION__, $namespace, $fixParams]);
        $insertId = Tool::lockDoSomething('' . $lockKey,
            function()  use(&$namespace, &$expireNums){//
                $redisKey = 'FlowSn:' . ucfirst($namespace);
                $insertId = RedisString::incr($redisKey);// Redis::incr($redisKey);
                foreach($expireNums as $v){
                    if(count($v) < 3) continue;
                    $orderNums = [$v[0], $v[1]];
                    $orderNums = array_values($orderNums);
                    sort($orderNums);
                    if($insertId >= $orderNums[0] && $insertId <= $orderNums[1]) RedisString::expire($redisKey, $v[2]);// Redis::expire($redisKey, $v[2] );  #设置过期时间 单位秒数 一年  365 * 24 * 60 * 60
                }
                return $insertId;
            }, function($errDo){
                // TODO
                throws('生成单号有错，请稍后重试!');
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
            }, false, 1, 2000, 2000);
        /*
         *
        $lockObj = Tool::getLockRedisesLaravelObj();
        $lockState = $lockObj->lock('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1, ':', ':'), Tool::getActionMethod(), __CLASS__, __FUNCTION__, $namespace, $fixParams]), 2000, 2000);//加锁
        if($lockState)
        {
            try {
                $redisKey = 'FlowSn:' . ucfirst($namespace);
                $insertId = RedisString::incr($redisKey);// Redis::incr($redisKey);
                foreach($expireNums as $v){
                    if(count($v) < 3) continue;
                    $orderNums = [$v[0], $v[1]];
                    $orderNums = array_values($orderNums);
                    sort($orderNums);
                    if($insertId >= $orderNums[0] && $insertId <= $orderNums[1]) RedisString::expire($redisKey, $v[2]);// Redis::expire($redisKey, $v[2] );  #设置过期时间 单位秒数 一年  365 * 24 * 60 * 60
                }
            } catch ( \Exception $e) {
                throws($e->getMessage(), $e->getCode());
            }finally{
                $lockObj->unlock($lockState);//解锁
            }
        }else{
            throws('生成单号有错，请稍后重试!');
        }
         *
         */
        $orderNum = $prefix;// 前缀
        if(($needNum & 1) == 1) $orderNum .= $year;// 年2位

        // 到一年的第几分钟 6位
        if(($needNum & 2) == 2) $orderNum .= str_pad(substr($mdh, -6), 6, '0', STR_PAD_LEFT);

        // needNum 值为 4时的日期格式
        if(($needNum & 4) == 4 && (!empty($dataFormat))) $orderNum .= date($dataFormat);//

        $orderNum .= $midFix;// 中缀

        // 8 自增的序号
        if(($needNum & 8) == 8) $orderNum .= str_pad(substr($insertId, -$length), $length, 0, STR_PAD_LEFT);

        $orderNum .= $backfix;// 后缀

        //   return $prefix . $year . str_pad(substr($mdh, -6), 6, '0', STR_PAD_LEFT)
        //  . $midFix . str_pad(substr($insertId, -$length), $length, 0, STR_PAD_LEFT) . $backfix;// . $suffix
        return $orderNum;

    }

    /**
     * 获取唯一标识长度,最长37位,默认10位
     *
     * @param int $length 字符串长度
     *
     * @return string
     */
    public static function createUniqueNumber($length = 10)
    {
        return substr(date('YmdHis') . md5(uniqid()), 0, $length);
    }

    /**
     * 根据字符集生成随机字符串
     * Tool::generatePassword(4, (1 | 2 | 4));
     * @param int $length 字符串长度
     * @param int $type 1纯数字 2 小写字母 4大写字母
     *
     * @return string
     */
    public static function generatePassword($length = 6, $type = 0)
    {
        $chars = '';
        if(($type & 2) == 2) $chars .= 'abcdefghijklmnopqrstuvwxyz';
        if(($type & 4) == 4) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if(($type & 1) == 1) $chars .= '0123456789';
        if(strlen($chars) <= 0) $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 此方法用于生成 唯一用于id的的，  E27B37CA-C2C7-B2C5-9FED-6FE3773E77B6
     * 适用于大并发情况下，生成唯一id
     * @param int $length 字符串长度
     * @param int $type 0:纯数字, 1:数字与字母
     * @return string  E27B37CA-C2C7-B2C5-9FED-6FE3773E77B6
     */
    public static function getGUID() { //32
        if (function_exists('com_create_guid')) return trim(com_create_guid(), '{}');
        else {
            //mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), TRUE)));
            $result = substr($charid, 0, 8) . '-' . substr($charid, 8, 4) . '-' . substr($charid, 12, 4)
                . '-' . substr($charid, 16, 4) . '-' . substr($charid, 20, 12);
            return $result;
        }
    }

    //----------------- 单个redis锁-------------------
    /**
     * 获得锁对象
     *
     * @param array
     *   $config =[
     *       'host' => '',// 默认 localhost
     *      'port' => '',// 默认 6379
     *      'auth' => '',// 默认空
     *      'dbNum' => 0,// 默认 0
     *  ];
     * @return object
     */
    public static function getLockObjBase($config = []){
        return RedisLock::instance($config);
    }

    /**
     * 获得锁对象--laravel配置的
     *
     * @param array
     * @return object
     */
    public static function getLockLaravelObj(){
        $config =[
            'host' => config('public.redis.default.host', 'localhost'),// env('REDIS_HOST', 'localhost'),// 默认 localhost
            'port' => config('public.redis.default.port', 6379),// env('REDIS_PORT', 6379),// 默认 6379
            'auth' => config('public.redis.default.password', ''),// env('REDIS_PASSWORD', ''),// 默认空
            // 'dbNum' => config('public.redis.default.database', 0),// env('REDIS_DB', 0),// 默认 0
        ];
        return Tool::getLockObjBase($config);
    }

    //----------------- 分布式锁-多个redis锁---------------

    /**
     * 获得锁对象
     *
     * @param array 二维数组
     *   $servers = [
     *      //['127.0.0.1', 6379, 0.01, '', 0],
     *      ['192.168.56.114', 6379, 0.01, '', 0],
     *      //['172.29.8.165', 6379, 0.01, '', 0],
     *       //['127.0.0.1', 6399, 0.01, '', 0],
     *  ];
     * @return object
     */
    public static function getLockRedisesObjBase($config = []){
        return RedisesLock::instance($config);
    }

    /**
     *
     *
     * 获得锁对象--laravel配置的
     *
     * @param array
     * @return object
     */
    public static function getLockRedisesLaravelObj(){
        $config =[
            [
                'host' => config('public.redis.default.host', 'localhost'),// env('REDIS_HOST', 'localhost'),// 默认 localhost
                'port' => config('public.redis.default.port', 6379),// env('REDIS_PORT', 6379),// 默认 6379
                'timeout' => 0.01,// 以秒为单位）。默认值为 15 秒。值 0 指示无限制
                'auth' => config('public.redis.default.password', ''),// env('REDIS_PASSWORD', ''),// 默认空
                'dbNum' => config('public.redis.default.database', 0),// env('REDIS_DB', 0),// 默认 0
            ]
        ];
        return Tool::getLockRedisesObjBase($config);
    }


    /**
     * 使用闭包函数，实现缓存数据
     * @param string $key 缓存key --锁的key值，如果是缓存，也可以是缓存的key
     * @param mixed $fun 锁要处理的事的闭包函数 没有参数 ；有返回值[$errDo = 2时,最好不要返回文字]
     * @param mixed $notLockedFun $isNotLockedErr 值为false:不处理时，可以的闭包函数 参数会传入 $errDo   返回值会返回
     * @param boolean $isNotLockedErr 没有锁时是否报错 true:报错 ,false:不处理
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param int  $ttl  锁过时间，单位是毫秒
     * @param int $getwaittimeout       毫秒 循环获取锁的等待超时时间，在此时间内会一直尝试获取锁直到超时，为0表示失败后直接返回不等待
     * @return  mixed sting 具体错误 ； throws 错误 ;  null:没有获得锁会返回 ; 其它正确 ;
     * @author zouyan(305463219@qq.com)
     */
    public static function lockDoSomething($key, $fun, $notLockedFun, $isNotLockedErr = true, $errDo = 1, $ttl = 2000, $getwaittimeout= 2000){
        $result = null;
        $lockObj = static::getLockRedisesLaravelObj();
        $lockState = $lockObj->lock('lock:' . $key , $ttl, $getwaittimeout);//加锁
        // 获得锁
        if($lockState)
        {
            try {
                if(is_callable($fun)){
                    $result = $fun();
                }
            } catch ( \Exception $e) {
                $errMsg = $e->getMessage();
                if($errDo == 1) throws($e->getMessage(), $e->getCode());
                // throws($e->getMessage(), $e->getCode());
                return $errMsg;
                // throws($e->getMessage(), $e->getCode());
            }finally{
                $lockObj->unlock($lockState);//解锁
            }
        }else{// 未获得锁
            if($isNotLockedErr){
                $errMsg = '获得锁失败，请稍后重试!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
            if(is_callable($notLockedFun)){
                $result = $notLockedFun($errDo);
            }
        }
        return $result;
    }


    /**
     * Xml To array
     *
     * @param $data
     *
     * @return array
     */
    public static function xmlToArray($data)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        $data = str_ireplace(['encoding="GB2312"', 'encoding="GBK"'], 'encoding="GB18030"', $data);

        // 先把xml转换为simplexml对象，再把simplexml对象转换成 json，再将 json 转换成数组
        try {
            $result = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

            return $result ? json_decode(json_encode($result), true) : [];
        } catch (\Throwable $e) {
            throws('xml格式不正确：' . $data);
        }
    }

    /**
     * 数组转换成xml
     *
     * @param $arr
     * @param string $root
     * @param string $endroot
     *
     * @return string
     */
    public static function arrayToXml($arr, $root = '<msgdata>', $endroot = '</msgdata>')
    {
        $xml = $root;

        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                if (is_numeric($key)) {
                    $xml .= self::arrayToXml($val, '', '');
                } else {
                    $xml .= '<' . $key . '>' . self::arrayToXml($val, '', '') . '</' . $key . '>';
                }
            }
            else {
                if (is_numeric($val) || $val === '') {
                    $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
                }
                else {
                    $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
                }
            }
        }

        $xml .= $endroot;

        return $xml;
    }

    /**
     * 获取xml post请求数据
     *
     * @return bool|mixed
     */
    public static function getXmlPost()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            if (! empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $xmlInput = $GLOBALS['HTTP_RAW_POST_DATA'];
            }
            else {
                $xmlInput = file_get_contents('php://input');
            }
        }
        else {
            $xmlInput = file_get_contents('php://input');
        }

        if (empty($xmlInput)) {
            return [];
        }

        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        // 先把xml转换为simplexml对象，再把simplexml对象转换成 json，再将 json 转换成数组
        return json_decode(json_encode(simplexml_load_string($xmlInput, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $value 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化 3 不转换
     * @return $key
     * @author zouyan(305463219@qq.com)
     */
    public static function setRedis($pre = '', $key = null, $value = '', $expire = 0, $operate = 1)
    {
        return RedisString::setRedis($pre, $key, $value, $expire, $operate);
//        if(empty($key)){
//            $key = self::createUniqueNumber(25);
//        }
//        $key = $pre . $key;
//        // 序列化保存
//        try{
//            switch($operate){
//                case 1:
//                    if(is_array($value)){
//                        $value = json_encode($value);
//                    }
//                    break;
//                case 2:
//                    $value = serialize($value);
//                    break;
//                default:
//                    break;
//            }
//            if(is_numeric($expire) && $expire > 0){
//                Redis::setex($key, $expire, $value);
//            }else{
//                Redis::set($key, $value);
//            }
//        } catch ( \Exception $e) {
//            throws('redis[' . $key . ']保存失败；信息[' . $e->getMessage() . ']');
//        }
//        return $key;
    }

    /**
     * 获得key的redis值
     * @param string $key 键
     * @param int 选填 $operate 操作 1 转为json 2 序列化 3 不转换
     * @return $value ; false失败
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedis($key, $operate = 1)
    {
        return RedisString::getRedis($key, $operate);
//        $value = Redis::get($key);
//        if(is_bool($value) || is_null($value)){//string或BOOL 如果键不存在，则返回 FALSE。否则，返回指定键对应的value值。
//            return false;
//        }
//        switch($operate){
//            case 1:
//                if (!self::isNotJson($value)) {
//                    $value = json_decode($value, true);
//                }
//                break;
//            case 2:
//                $value = unserialize($value);
//                break;
//            default:
//                break;
//        }
//        return $value;

    }

    /**
     * 获得key的redis值
     * @param string $key 键
     * @return $value
     * @author zouyan(305463219@qq.com)
     */
    public static function delRedis($key)
    {
        return RedisString::del($key);
//        return Redis::del($key);
    }

    /**
     * 获得并判断缓存是否有效 有效返回缓存数据，失效：false ;  没有缓存 :null
     * @param string $redisKeyPre 缓存数据的键前缀 cacheDB:RunBuy:U:
     * @param string $redisKey 缓存数据的键 getList:md5key
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param mixed $cacheDataDofun 开启缓存且缓存有数据 对读取到的缓存数据进行处理-判断缓存是否失效 function(&$cacheData, &$isReadOrCache, &$isOpenCache){}；主要根据情况改动$isReadOrCache
     *                                              // $cacheData 引用传参 是从缓存中读取到的数据
     *                                              // $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param int $operateRedis 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
     * @return mixed 有效返回缓存数据，失效：false ;  没有缓存 :null
     * @author zouyan(305463219@qq.com)
     */
    public static function getAndJudgeCacheData($redisKeyPre , $redisKey, &$isOpenCache, &$isReadOrCache, $cacheDataDofun, $operateRedis = 2){
        $requestData = null;
        // 已开启缓存
        if($isOpenCache) $requestData = static::getRedis($redisKeyPre . $redisKey, $operateRedis);

        // 开启缓存且缓存有数据
        if($isOpenCache && $requestData !== false ){
            $isReadOrCache = false;
            // 对缓存中的数据处理下---判断缓存是否失效
            if(is_callable($cacheDataDofun)){
                $cacheDataDofun($requestData, $isReadOrCache, $isOpenCache);
            }
            // 缓存失效
            if($isReadOrCache) $requestData = false;
        }
        return $requestData;
    }

    /**
     * redis自动指定时间延长有效期
     * @param string $redisKeyPre 缓存数据的键前缀 cacheDB:RunBuy:U:
     * @param string $redisKey 缓存数据的键 getList:md5key
     * @param   array  $extendExpire 为空则不延期// 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
     *                  // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置     *
     * [
     *        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *       'requestNum' => 8,// 访问次数
     *        'maxExendNum' => 3,// 可延长3次
     *    ]
     * @param int $expire 有效期 秒  长期有效 必须 >0
     * @return array
     *   return [
     *      'everyRequesNumKey' => $extendRequestRedisKey,// 单位时间内，访问缓存次数的key
     *      'extendNumKey' => $extendNumRedisKey,// 延长有效期次数的key
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function extendCacheExpire($redisKeyPre, $redisKey, $extendExpire = [], $cacheExpire = 60){
        if(!is_numeric($cacheExpire) || $cacheExpire <= 0) $cacheExpire = 60;
        $extendRequestRedisKey = $redisKeyPre . $redisKey . ':reqNumExtend';
        $extendNumRedisKey = $redisKeyPre . $redisKey . ':reqNumExtendChNum';
        $result = [
            'everyRequesNumKey' => $extendRequestRedisKey,// 单位时间内，访问缓存次数的key
            'extendNumKey' => $extendNumRedisKey,// 延长有效期次数的key

        ];
        if(!is_array($extendExpire) || empty($extendExpire)) return $result;
        // 为空则不延期// 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
        $extendCacheExpire = $extendExpire['expire'] ?? 0;// 单位时长，单位秒
        $extendRequestNum = $extendExpire['requestNum'] ?? 0;// 访问次数
        $extendMaxNum = $extendExpire['maxExendNum'] ?? 0;// 可延长3次
        if( $extendCacheExpire > 0 && $extendRequestNum > 0){
            // 获得延长有效期次数
            $chNums = static::getRedis($extendNumRedisKey, 3);
            if(!is_numeric($chNums) || $chNums <= 0) $chNums = 0;
            if($chNums < $extendMaxNum){

                // 单位时间内，访问缓存次数自增
                $extendCacheRequestNum = static::limitIncr($extendRequestRedisKey ,$extendCacheExpire, -1, 1,'次数超限!', 1);
                // 更新缓存有效期
                if( $extendCacheRequestNum >= $extendRequestNum ){
                    // 记录延长次数
                    $chNums++;
                    static::setRedis('', $extendNumRedisKey, $chNums, $extendCacheExpire, 3);
                    // 更改有效期
                    RedisString::existSetnxExpire($redisKeyPre . $redisKey, $cacheExpire);
                    // 删除访问次数
                    RedisString::existDel($extendRequestRedisKey);
                }
            }
        }
        return $result;
    }

    /**
     * 判断是否达到请求次数  >次数,返回true
     * @param string $redisKeyPre 缓存数据的键前缀 cacheDB:RunBuy:U:
     * @param string $redisKey 缓存数据的键 getList:md5key
     * @param   array $openCacheRequest // 为空则[默认-不限] ；未缓存时，单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     *                  // 值[] 空时，会使用 public.DBDataCache.openCache 配置
     *  [
     *         'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *        'requestNum' => 3,// 访问次数 ，>此数,返回true
     *    ];
     * @return boolean true:达到请求限止次数; false:未达到请求限止次数[默认-不限]
     * @author zouyan(305463219@qq.com)
     */
    public static function hasRequestLimit($redisKeyPre, $redisKey, $openCacheRequest = []){
        $cacheRequestExpire = $openCacheRequest['expire'] ?? 0;//
        $cacheRequestNum = $openCacheRequest['requestNum'] ?? 0;
        // 单位时间请求次数
        $requestRedisKey = $redisKeyPre . $redisKey . ':reqNum';
        // 没有缓存时，使用判断
        if($cacheRequestExpire > 0){
            $requestNum = static::limitIncr($requestRedisKey ,$cacheRequestExpire, -1, 1,'次数超限!', 1);
            // 没有缓存时,也没有达到缓存访问数时，不缓存
            if($requestNum <= $cacheRequestNum){
                return false;
            }else{
                return true;
            }
        }
        return false;
    }

    /**
     * 读取数据并缓存[有加锁]---[防击穿，以及雪崩，穿透]在读取前，可以重新从缓存读取并判断是否有效[有效：返回缓存，无效：重新读取并缓存]
     * @param mixed $fun 没有缓存时，要读取数据 的闭包函数  function(&$isOpenCache){}
     * @param mixed $cacheDataDofun 开启缓存且缓存有数据 对读取到的缓存数据进行处理-判断缓存是否失效 function(&$cacheData, &$isReadOrCache, &$isOpenCache){}；主要根据情况改动$isReadOrCache
     *                                              // $cacheData 引用传参 是从缓存中读取到的数据
     *                                              // $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param mixed $readDataDofun 开启了缓存时 对读取到的原数据进行处理 function(&$readData, &$isOpenCache){} 主要根据情况改动$isOpenCache 也可以改动 $readData的值[最终缓存的就是这个数据]
     *                                              // $readData 引用传参 读取到的需要缓存的数据
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param string $redisKeyPre 缓存数据的键前缀 cacheDB:RunBuy:U:
     * @param string $redisKey 缓存数据的键 getList:md5key
     * @param int $cacheExpire 缓存时间 单位秒
     * @param int $operateRedis 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param boolean $isReJudgeCache  在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
     * @param mixed $setCacheBackFun 缓存保存后执行的操作--一些操作 的闭包函数  function(){}
     * @return  mixed  false:获得数据失败[未获得锁]; null ：获得数据失败  ; 其它：缓存的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function readAndCacheData($fun, $cacheDataDofun, $readDataDofun, $redisKeyPre, $redisKey, $cacheExpire = 60, $operateRedis = 2, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true, $setCacheBackFun = null){
        $requestData = null;
        // 重新读取数据
        if(is_callable($fun)){
            if($isOpenCache){
                $recordFun = __FUNCTION__;
                $requestData = Tool::lockDoSomething('Redis:lock:' . $redisKeyPre . $redisKey,
                    function() use(&$fun, &$cacheDataDofun, &$isOpenCache, &$isReadOrCache, &$isReJudgeCache, &$readDataDofun, &$redisKeyPre, &$redisKey, &$cacheExpire, &$operateRedis, &$recordFun, &$setCacheBackFun){//  &$extendRequestRedisKey, &$extendNumRedisKey
                        // ##### 重新获得缓存--防击穿，以及雪崩，穿透####开始#####################
                        if($isReJudgeCache){
                            // 获得并判断缓存是否有效 有效返回缓存数据，失效：false ;  没有缓存 :null
                            $requestData = static::getAndJudgeCacheData($redisKeyPre , $redisKey, $isOpenCache, $isReadOrCache, $cacheDataDofun, $operateRedis);
                            // 缓存有数据
                            if($requestData !== false && $requestData !== null){
                                // 缓存未失效，直接返回
                                if(!$isReadOrCache){
                                    Log::info('数据缓存日志 --从缓存中获取到---获得锁，重新判断缓存--防击穿->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [  $redisKeyPre, $redisKey]);
                                    return $requestData;
                                }
                            }
                        }

                        // ##### 重新获得缓存--防击穿，以及雪崩，穿透####结束#####################

                        // 缓存失效，或没有缓存，重读再缓存
                        $requestData = $fun($isOpenCache);
                        //开启了缓存 缓存前对数据处理
                        $cacheData = $requestData;// 用来缓存的数据,复制的原因是：下面的方法可能会对数据进行一些处理，已经不是要返回的数据了
                        if($isOpenCache && is_callable($readDataDofun)){
                            $readDataDofun($cacheData, $isOpenCache);
                        }
                        if($isOpenCache){
                            static::setRedis($redisKeyPre, $redisKey, $cacheData, $cacheExpire, $operateRedis);
                            Log::info('数据缓存日志 --无缓存，重新缓存-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . $recordFun, [ $redisKeyPre, $redisKey]);
                            // 缓存保存后执行的操作--一些操作
                            if(is_callable($setCacheBackFun)){
                                $setCacheBackFun();
                            }
                            // 删除访问次数
                            // if(!empty($extendRequestRedisKey)) RedisString::existDel($extendRequestRedisKey);
                            // 删除延长次数
                            // if(!empty($extendNumRedisKey)) RedisString::existDel($extendNumRedisKey);
                        }else{
                            Log::info('数据缓存日志 --无缓存，重新读取但不缓存-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . $recordFun, [ $redisKeyPre, $redisKey]);
                        }
                        return  $requestData;
                    }, function($errDo) use(&$redisKeyPre, &$redisKey, &$isOpenCache, &$isReadOrCache, &$cacheDataDofun, &$operateRedis){// 未获得锁，重试一下获得缓存
                        $requestData = static::getAndJudgeCacheData($redisKeyPre , $redisKey, $isOpenCache, $isReadOrCache, $cacheDataDofun, $operateRedis);
                        // 缓存有数据
                        if($requestData !== false && $requestData !== null){
                            // 缓存未失效，直接返回
                            if(!$isReadOrCache){
                                Log::info('数据缓存日志 --从缓存中获取到---获得锁失败，重新判断缓存->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [  $redisKeyPre, $redisKey]);
                                return $requestData;
                            }
                        }
                        // 缓存还是无效
                        $errMsg = '获得数据失败，请稍后重试!';
                        Log::info('数据缓存日志 --' . $errMsg . '->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [  $redisKeyPre, $redisKey]);
                        if($errDo == 1) throws($errMsg);
                        // return $errMsg;
                        return false;
                    }, false, 2, 2000, 2000);


            }else{// 直接读取--不缓存
                $requestData = $fun($isOpenCache);
                Log::info('数据缓存日志 --直接读取--不缓存-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [ $redisKeyPre, $redisKey]);
            }
        }else{
            $requestData = null;
            Log::info('数据缓存日志 --无缓存，也无法执行回调函数获得数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [  $redisKeyPre, $redisKey]);
        }
        return $requestData;
    }

    /**
     * 最全的对数据块缓存的方法
     * 可 -- 对数据进行处理
     * 可  -- 未缓存时，单位时间内，访问多少次，开启缓存
     * 可  -- 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期
     * 使用闭包函数，实现缓存数据
     * @param mixed $fun 没有缓存时，要读取数据 的闭包函数  function(&$isOpenCache){}
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param mixed $cacheDataDofun 开启缓存且缓存有数据 对读取到的缓存数据进行处理-判断缓存是否失效 function(&$cacheData, &$isReadOrCache, &$isOpenCache){}；主要根据情况改动$isReadOrCache
     *                                              // $cacheData 引用传参 是从缓存中读取到的数据
     *                                              // $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param mixed $readDataDofun 开启了缓存时 对读取到的原数据进行处理 function(&$readData, &$isOpenCache){}主要根据情况改动$isOpenCache 也可以改动 $readData的值[最终缓存的就是这个数据]
     *                                              // $readData 引用传参 读取到的需要缓存的数据
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param string $redisKeyPre 缓存数据的键前缀 cacheDB:RunBuy:U:
     * @param string $redisKey 缓存数据的键 getList:md5key
     * @param int $cacheExpire 缓存时间 单位秒
     * @param int $operateRedis 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param int $operateType  操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
     * @param   array $openCacheRequest // 为空则直接缓存 ；未缓存时，单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     *                  // 值[] 空时，会使用 public.DBDataCache.openCache 配置
     *  [
     *         'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *        'requestNum' => 3,// 访问次数
     *    ];
     * @param   array  $extendExpire 为空则不延期// 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
     *                  // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置     *
     * [
     *        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *       'requestNum' => 8,// 访问次数
     *        'maxExendNum' => 3,// 可延长3次
     *    ]
     * @return  mixed  false:获得数据失败[未获得锁]; null ：获得数据失败  ; 其它：缓存的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisCacheData($fun, $cacheDataDofun, $readDataDofun, $redisKeyPre, $redisKey, $cacheExpire = 60, $operateRedis = 2, $isOpenCache = true, $operateType = 1 | 2, $openCacheRequest = [], $extendExpire = []){
        // 查询条件
        // $cacheExpire = 60 * 2;
        // $operateRedis = 2;// 操作 1 转为json 2 序列化 ; 3 不转换
        $requestData = null;
        // $isOpenCache = true;// 是否开启缓存 true:开启/使用缓存；false：不使用缓存
        $isReadOrCache = true;// 是否需要重新读取并缓存 true:重新读取并缓存；false:不用重新读取[有缓存数据]

        // 已开启缓存
        // 获得并判断缓存是否有效 有效返回缓存数据，失效：false ;  没有缓存 :null
        if($isOpenCache && ($operateType & 1) == 1)  $requestData = static::getAndJudgeCacheData($redisKeyPre , $redisKey, $isOpenCache, $isReadOrCache, $cacheDataDofun, $operateRedis);

        // 为空则直接缓存 ；单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
        // 没有缓存时，使用判断
        if($isOpenCache &&  is_array($openCacheRequest) && !empty($openCacheRequest) && ($requestData === false ||  ($operateType & 1) != 1)) {
            // 没有缓存时,也没有达到缓存访问数时，不缓存
            if(!static::hasRequestLimit($redisKeyPre, $redisKey, $openCacheRequest)) $isOpenCache = false;
        }

        // 没有开启缓存--则肯定会重新读取--强制修改为重新读取
        if(!$isOpenCache) $isReadOrCache = true;
        // 没有读取缓存，肯定是要重新读原数据的
        if(($operateType & 1) != 1) $isReadOrCache = true;

        $extendRequestRedisKey = '';// $redisKeyPre . $redisKey . ':reqNumExtend'; // 单位时间内，访问缓存次数的key
        $extendNumRedisKey = '';// $redisKeyPre . $redisKey . ':reqNumExtendChNum';// 延长有效期次数的key
        // 为空则不延期// 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
        if(!$isReadOrCache && is_array($extendExpire) && !empty($extendExpire)){
            $extendResult = static::extendCacheExpire($redisKeyPre, $redisKey, $extendExpire, $cacheExpire);
            list($extendRequestRedisKey, $extendNumRedisKey) = array_values($extendResult);
        }

        if( $isReadOrCache){
            // self::resolveSqlParams($modelObj, $queryParams);
            // $requestData = $modelObj->get();
            // 重新读取数据
            // 重新读取并缓存数据
            // 在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
            $isReJudgeCache = false;
            if(($operateType & 2) == 2) $isReJudgeCache = true;
            $requestData = static::readAndCacheData($fun, $cacheDataDofun, $readDataDofun, $redisKeyPre, $redisKey,
                $cacheExpire, $operateRedis, $isOpenCache, $isReadOrCache,
                $isReJudgeCache, function() use(&$extendRequestRedisKey, &$extendNumRedisKey){
                    // 删除访问次数
                    if(!empty($extendRequestRedisKey)) RedisString::existDel($extendRequestRedisKey);
                    // 删除延长次数
                    if(!empty($extendNumRedisKey)) RedisString::existDel($extendNumRedisKey);
                });
        }else{// 从缓存获取的数据
            Log::info('数据缓存日志 --从缓存中获取到-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [  $redisKeyPre, $redisKey]);
            // vd($requestData);
        }
        return $requestData;
    }

    /**
     * 需要对缓存数据块的数据进行一些处理，可有此方法
     * 缓存或获得数据，可对数据进行处理，具体参数说明请看上面方法 getRedisCacheData
     * 不可  -- 未缓存时，单位时间内，访问多少次，开启缓存
     * 不可  -- 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期
     */
    public static function getRedisCacheDataDoSomething($fun, $cacheDataDofun, $readDataDofun, $redisKeyPre, $redisKey, $cacheExpire = 60, $operateRedis = 2, $isOpenCache = true, $operateType = 1 | 2){

        return static::getRedisCacheData($fun, $cacheDataDofun, $readDataDofun, $redisKeyPre, $redisKey, $cacheExpire,$operateRedis, $isOpenCache, $operateType, [], []);
    }

    /**
     * 仅对数据块缓存，可直接有这个方法
     * 缓存或获得数据，仅是直接获取缓存数据或重新获取数据，具体参数说明请看上面方法 getRedisCacheData
     *  不可 -- 对数据进行处理
     * 不可  -- 未缓存时，单位时间内，访问多少次，开启缓存
     * 不可  -- 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期
     */
    public static function getCacheDataOnly($fun, $redisKeyPre, $redisKey, $cacheExpire = 60, $operateRedis = 2, $isOpenCache = true, $operateType = 1 | 2){

        return static::getRedisCacheDataDoSomething($fun,
            function(&$cacheData, &$isReadOrCache, &$isOpenCache){

            },
            function(&$readData, &$isOpenCache){

            }, $redisKeyPre, $redisKey, $cacheExpire,$operateRedis, $isOpenCache, $operateType);
    }

    //判断数据不是JSON格式:
    public static function isNotJson($str){
        return is_null(json_decode($str));
    }

    // 保存session
    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $value 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return $key
     * @author zouyan(305463219@qq.com)
     */
    /**
     * 保存session值-json/序列化保存 注意如果是session，一定要确保前面有 session_start(); // 初始化session
     * @param string $key_pre 前缀
     * @param string 选填 $value 需要保存的值，如果是对象或数组，则序列化
     * @param boolean 选填 $save_session 是否保存session true:键保存到session.false，只返回key，给小程序用
     * @param string 选填 $session_key  如果保存的session，session的键名
     * @param int 选填 $expire 有效期 秒 <=0 长期有效 60*60*24*1
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return $redisKey  数据在redis中的键值
     * @author zouyan(305463219@qq.com)
     */
    public static function setLoginSession($key_pre= 'login', $value = '', $save_session = true, $session_key = 'loginKey', $expire = 0, $operate = 1)
    {
        $key = null;// 键名
        $pre = '';// 前缀
        $need_save_key = false; // 是否需要重新获得key
        // 如果有用的浏览器，则获取保存在session中的redis键值
        if($save_session){// 使用seesion
//            if (!session_id()) session_start();
//            $key = $_SESSION[$session_key] ?? '';// session 中的key
            $key = SessionCustom::get($session_key, true);
            // 没有redis 中的键值
            if(empty($key)){
                $key = null;
                $need_save_key = true;
            }
        }
        // 没有key则加前缀
        if(empty($key)){
            $pre = $key_pre;
        }

        $redisKey = self::setRedis($pre, $key, $value, $expire , $operate); // 1天

        // 重新保存session , 用session 且 key有变化
        if($save_session && $need_save_key){
//            if (!session_id()) session_start();
//            $_SESSION[$session_key] = $redisKey;
            SessionCustom::set($session_key, $redisKey, 0);
        }
        return $redisKey;
    }

    // 获得session

    /**
     * 获得key的值 注意如果是session，一定要确保前面有 session_start(); // 初始化session
     * @param string $redisKey 全键[含前缀],小程序传入的 $save_session 为 true时，可以传null
     * @param boolean 选填 $save_session 是否保存session true:键保存到session.false，只返回key，给小程序用
     * @param string 选填 $session_key  如果保存的session，session的键名
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return $value redis中保存的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getSession($redisKey = null, $save_session = true, $session_key = 'loginKey', $operate = 1)
    {
        if($save_session){
//            if (!session_id()) session_start();
//            $redisKey = $_SESSION[$session_key] ?? '';
            $redisKey = SessionCustom::get($session_key, true);

        }
        $val = '';
        if(!empty($redisKey)){
            $val = self::getRedis($redisKey, $operate);
        }else{
            // throws('参数redisKey不能为空!');
        }
        return $val;
    }

    /**
     * 获得key的值 注意如果是session，一定要确保前面有 session_start(); // 初始化session
     * @param string $redisKey 全键[含前缀],小程序传入的 $save_session 为 true时，可以传null
     * @param boolean 选填 $save_session 是否保存session true:键保存到session.false，只返回key，给小程序用
     * @param string 选填 $session_key  如果保存的session，session的键名
     * @return boolean true:成功 ;false:失败
     * @author zouyan(305463219@qq.com)
     */
    public static function delSession($redisKey = null, $save_session = true, $session_key = 'loginKey')
    {
        if($save_session){
//            if (!session_id()) session_start();
//            $redisKey = $_SESSION[$session_key] ?? '';
            $redisKey = SessionCustom::get($session_key, false);
        }

        // if($save_session && isset($_SESSION[$session_key])){
        if($save_session){
//            if(isset($_SESSION[$session_key])){
//                unset($_SESSION[$session_key]); //保存某个session信息
//            }
              SessionCustom::clear($session_key);
        }
        return self::delRedis($redisKey); // 删除redis中的值
    }

    // 数组操作
    /**
     * 将数组中的标签替换为数组｛下标为标签｝对应的值
     *
     * @param array $dataArr 需要替换的数组 任一维数组
     * @param array $replaceKV 数组 一/二/多维数组 --下标为标签 --自动遍历
     * @param string $keyPre 字符中的标签前字符
     * @param string $keyBack 字符中的标签后字符
     * @return string 替换后的字符
     */
    public static function arrReplaceKV(&$dataArr = '', $replaceKV = [], $keyPre = '{', $keyBack = '}'){
        if(!is_array($dataArr)) {// 非数组
            static::strReplaceKV($dataArr, $replaceKV, $keyPre, $keyBack);
            return ;
        }
        foreach($dataArr as $k => $v){
            if(is_array($v)){
                static::arrReplaceKV($dataArr[$k], $replaceKV, $keyPre, $keyBack);
            }else{
                static::strReplaceKV($dataArr[$k], $replaceKV, $keyPre, $keyBack);
            }
        }
    }

    /**
     * 将字符中的标签替换为数组｛下标为标签｝对应的值
     *
     * @param string $repStr 需要替换的字符
     * @param array $replaceKV 数组 一/二/多维数组 --下标为标签 --自动遍历
     * @param string $keyPre 字符中的标签前字符
     * @param string $keyBack 字符中的标签后字符
     * @return string 替换后的字符
     */
    public static function strReplaceKV(&$repStr = '', $replaceKV = [], $keyPre = '{', $keyBack = '}'){
        if(strlen($repStr) <= 0) return $repStr;
        if(!is_array($replaceKV) || empty($replaceKV)) return $repStr;
        foreach($replaceKV as $k => $v){
            if(is_array($v)){
                static::strReplaceKV($repStr, $v, $keyPre, $keyBack);
                continue;
            }
            $repKey = $keyPre . $k . $keyBack;
            // 单条
            if (strpos($repStr, $repKey) !== false)  $repStr = str_replace($repKey, $v, $repStr);
        }
        return $repStr;
    }

    /**
     * 判断数组是否是二维数组
     *
     * @param array $dataList 源数据 一/二维数组
     * @param boolean $convertMultiArr 如果是一维数组，是否转为二维数组 false:不转;true:转(注意空一维数组不会处理)
     * @return boolean true:原数组是二维数组；false:原数组是一维数组或空数组
     */
    public static function isMultiArr(&$dataList, $convertMultiArr = false){
        $isMultiArr = false; // true:二维;false:一维
        $dataArr = is_object($dataList) ? $dataList->toArray() : $dataList;
        foreach($dataArr as $k => $v){
            if(is_array($v) || is_object($v)){
                $isMultiArr = true;
            }
            break;
        }
        // 一维
        if(!$isMultiArr && $convertMultiArr) $dataList = [$dataList];
        return $isMultiArr;
    }

    /**
     * 对象或数组值中有对象转换为数组
     *
     * @param mixed $object 对象或数组值中有对象
     * @return array 转换后的数组
     */
    public static function objectToArray($object) {
        if(!is_array($object) && !is_object($object)) return $object;
        //先编码成json字符串，再解码成数组
        return json_decode(json_encode($object), true);
    }

    /**
     * 一/二维数组中指定某个字段的值 为 一维数组
     *
     * @param array $array 源数据 一/二维数组
     * @param string $uboundField 字段名称下标
     * @return array 一维数组
     */
    public static function getArrFields($array, $uboundField){
        // 如果是一维数组,则转为二维数组
        static::isMultiArr($array, true);
        $fieldArr = array_values(array_unique(array_column($array, $uboundField)));
        return $fieldArr;
    }

    /**
     * 通过指定下标，汇总下标字段的值
     * 一/二维数组转变为指定下标的值汇总到一个下标下的数据。
     *  如：转变为 ['字段1' => ['字段1值1'，'字段1值2',...], '字段2' => '字段2值1',....]
     * @param array $array 源数据 一/二维数组
     * @param array $uboundFieldArr 字段名称下标数组---一维数组
     * @param boolean $moveEmptyVals 是否移除空值的下标  true:移除； false：不移除[默认]
     * @param boolean $oneValToField 汇总值，如果只有一个时，是否不用一维数组为下标值； true:值直接给下标[默认]，false:还是有一维数组
     * @return array 数组
     */
    public static function collectArrByFields($array, $uboundFieldArr, $moveEmptyVals = false, $oneValToField = true){
        $reArr = [];
        foreach($uboundFieldArr as $field){
            $fieldVals = static::getArrFields($array, $field);
            if($oneValToField && count($fieldVals) === 1) $fieldVals = $fieldVals[0] ?? '';
            if($moveEmptyVals && empty($fieldVals) ) continue;// 移除空值
            $reArr[$field] = $fieldVals;
        }
        return $reArr;
    }

    /**
     * 二维数组中每个一维数组追加指定的一维数组值
     *
     * @param array $dataList 源数据 一维/二维数组
     * @param array $appendArr 需要追加的一维数据 一维数组   ['is_multi' => 0, 'is_must' => 1]
     * @return array
     */
    public static function arrAppendKeys(&$dataList, $appendArr){
        $isMulti = Tool::isMultiArr($dataList, true);
        foreach($dataList as $k => $v){
            // $v = array_merge($v, $appendArr);
            // $dataList[$k] = $v;
            foreach($appendArr as $tk => $tv){
                $dataList[$k][$tk] = $tv;
            }
        }
        if(!$isMulti) $dataList = $dataList[0] ?? [];
        return $dataList;
    }

    /**
     * 一维数组清除空值
     *
     * @param array $array
     * @return array
     */
    public static function arrClsEmpty(&$array){
        foreach($array as $k => $v){
            if(is_null($v) || trim($v) === '') unset($array[$k]);
        }
        return $array;
    }

    /**
     * 对一维数数值进行格式化处理，如果是字符，会自动转为数组
     *
     * @param string/array $paramVals 参数的值 数组-一维或字符 不是有效的字符或数组【源变量$paramVals设置为空数组】
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param int $operate_type 操作类型 1 每个元素去前后空；2值去重；4取差；8重置数组下标，0，1，2...
     * @return  boolean true:有数据项  false:无数据或不是有效的字符或数组【源变量$paramVals设置为空数组】
     * @author zouyan(305463219@qq.com)
     */
    public static function formatOneArrVals(&$paramVals = '', $excludeVals = [0, '0', ''], $valsSeparator = ',', $operate_type = (1 | 2 | 4 | 8)){
        if(!is_array($paramVals) && !is_string($paramVals)) {
            $paramVals = [];
            return false;
        }
        // 字符串，则转为数组 ；
        if(!is_array($paramVals))  $paramVals = explode($valsSeparator, $paramVals);

        // 1每个元素去前后空
        if(($operate_type & 1) == 1){
            $paramVals = array_map("trim",$paramVals);
        }

        // 2并去重
        if(($operate_type & 2) == 2){
            $paramVals = array_unique($paramVals);
        }

        // 4取差
        if(($operate_type & 4) == 4 && !empty($excludeVals)){
            $paramVals = array_diff($paramVals, $excludeVals);
        }

        // 8重置数组下标，0，1，2...
        if(($operate_type & 8) == 8){
            $paramVals = array_values($paramVals);
        }

        // 空数组不处理
        $valNums = count($paramVals);
        if ($valNums <= 0 ) return false;

        return true;
    }

    /**
     * 返回以原数组某个值为下标的新数组
     *
     * @param array $array
     * @param string $key
     * @param int $type 1一维数组2二维数组
     * @return array
     */
    public static function arrUnderReset($array, $key, $type = 1){
        if (is_array($array)){
            $tmp = [];
            foreach ($array as $v) {
                if ($type === 1){
                    $tmp[$v[$key]] = $v;
                }elseif($type === 2){
                    $tmp[$v[$key]][] = $v;
                }
            }
            return $tmp;
        }else{
            return $array;
        }
    }


    /**
     * 一/二维数组指定下标的值为下标,指定下标的值为值，的一维数组
     *
     * @param array $array 一/二维数组
     * @param string $uboundkey 值做为新数组的键的下标
     * @param string $uboundValKey 值做为新数组的键的下标
     * @return array 一维数组
     */
    public static function formatArrKeyVal($array, $keyUbound, $valUbound){
        $reArr = [];
        if (! is_array($array)) return $reArr;
        // 如果是一维数组,则转为二维数组
        static::isMultiArr($array, true);
        foreach ($array as $v) {
            if( !isset($v[$keyUbound]) || !isset($v[$valUbound])) continue;
            $reArr[$v[$keyUbound]] = $v[$valUbound];
        };
        return $reArr;
    }

    /**
     * 获得数据指定下标的新数组
     * @param array $dataArr 字段值数组 --一维或二维数组 格式为 ['字段名' => '字段值'] ；会包含所有的$fields 字段值
     * @param array $fields 字段数组 --一维数组; 为空则返回原数组 ['字段名1', '字段名2',....]
     * @param boolean $needNotIn  $fields在数组中不存在的，false:不要，true：空值
     * @return array 新的数组 一维/二维--原数组是几维就几维
     * @author zouyan(305463219@qq.com)
     */
    public static function getArrFormatFields($dataArr = [], $fields = [], $needNotIn = false){
        $newArr = [];
        if(empty($dataArr) ) return $newArr;
        if(empty($fields)) return $dataArr;
        $isMulti = static::isMultiArr($dataArr, true);
        foreach($dataArr as $k => $v){
            $temArr = [];
            foreach($fields as $t_f){
                if(!isset($v[$t_f])){
                    if($needNotIn) $temArr[$t_f] = '';

                }else{
                    $temArr[$t_f] = $v[$t_f];
                }
            }
            $newArr[$k] = $temArr;
        }

        if(!$isMulti) $newArr = $newArr[0] ?? [];
        return $newArr;
    }

    /**
     * 获得数据排除指定下标的新数组
     * @param array $dataArr 字段值数组 --一维或二维数组 格式为 ['字段名' => '字段值'] ；会包含所有的$fields 字段值
     * @param array $excludeFields 需要排除的字段数组 --一维数组; 为空则返回原数组 ['字段名1', '字段名2',....]
     * @return array 新的数组 一维/二维--原数组是几维就几维
     * @author zouyan(305463219@qq.com)
     */
    public static function getArrFormatExcludeFields($dataArr = [], $excludeFields = []){
        // pr($dataArr->toArray());
        // pr($excludeFields);
        if(empty($dataArr) ) return $dataArr;
        if(empty($excludeFields)) return $dataArr;
        $isMulti = static::isMultiArr($dataArr, true);
        foreach($dataArr as $k => $v){
            $info = is_object($v) ? $v->toArray() : $v;
            foreach($info as $t_f => $t_v){
                if(in_array($t_f, $excludeFields)) unset($dataArr[$k][$t_f]);
            }
        }
        if(!$isMulti) $dataArr = $dataArr[0] ?? [];
        return $dataArr;
    }

    /**
     * 判断两个数据是否相等 --一维数组
     * @param array $firstArr 字段值数组 --一维数组
     * @param array $secondArr 字段值数组 --一维数组
     * @param int $judgeType 判断类型 1值相等 2 下标相等 4 值顺序相等
     * @return boolean true:相等  false:不相等
     * @author zouyan(305463219@qq.com)
     */
    public static function isEqualArr($firstArr, $secondArr, $judgeType = 1){
        if(empty($firstArr) && empty($secondArr)) return true;

        if( ($judgeType & 4) == 4){
            if(implode('!#@', $firstArr) != implode('!#@', $secondArr)) return false;
        }

        if( ($judgeType & 1) == 1) {
            if(!empty(array_diff($firstArr, $secondArr)) || !empty(array_diff($secondArr, $firstArr))) return false;
        }

        if( ($judgeType & 2) == 2) {
            foreach ($firstArr as $k => $v) {
                if(!isset($secondArr[$k])) return false;
                unset($secondArr[$k]);
            }
            if(!empty($secondArr)) return false;
        }

        return true;
    }

    /**
     * 一维数组返回指定下标数组的一维数组,-以原数组下标不准，
     *
     * @param array $array 一维数组
     * @param array $keys 要获取的下标数组 -维 [ '新下标名' => '原下标名' ]
     * @param boolean $needNotIn  keys在数组中不存在的，false:不要，true：空值
     * @return array 一维数组
     */
    public static function formatArrKeys(&$array, $keys, $needNotIn = false){
        $newArr = [];
        foreach($keys as $new_k => $old_k){
            if(!isset($array[$old_k])){// 不存在
                if($needNotIn){// true：空值
                    $newArr[$new_k] = '';
                }
            }else{// 存在
                $newArr[$new_k] = $array[$old_k];
            }
        }
        $array = $newArr;
        return $newArr;
    }

    /**
     * 一/二维数组返回指定下标数组的新的二维维数组,-以原数组下标为准，
     *
     * @param array $array 一/二维数组
     * @param array $keys 要获取的下标数组 -维[ '新下标名' => '原下标名' ]
     * @param boolean $needNotIn  keys在数组中不存在的，false:不要，true：空值
     * @return array 一/二维数组
     */
    public static function formatTwoArrKeys(&$array, $keys, $needNotIn = false){

        // 如果是一维数组
        if (!static::isMultiArr($array)){
            self::formatArrKeys($array, $keys, $needNotIn );
            return $array;
        }
        foreach($array as $k => $v){
            self::formatArrKeys($array[$k], $keys, $needNotIn );
        }
        return $array;
    }

    /**
     * [取指下下标、排除指定下标、修改下标名称]
     * 一/二维数组返回指定下标数组的新的二维维数组或修改数组的下标名,-以原数组下标为准，
     *  参数的意思请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     * @param array $data_list 一/二维数组
     * @param array $temFormatData // 格式化，为空，则不操作。
     *       [    // 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     * @return array 一/二维数组
     */
    public static function formatArrUboundDo(&$data_list, $temFormatData){
        if(!empty($temFormatData) && is_array($temFormatData)){
            $temNeedNotIn = $temFormatData['needNotIn'] ?? true;
            $temIncludeUboundArr = $temFormatData['includeUboundArr'] ?? [];
            $temExceptUboundArr = $temFormatData['exceptUboundArr'] ?? [];
            static::formatArrUbound($data_list, $temNeedNotIn, $temIncludeUboundArr , $temExceptUboundArr);
        }
        return $data_list;
    }

    /**
     * 一/二维数组返回指定下标数组的新的二维维数组或修改数组的下标名,-以原数组下标为准，
     *  两个下标数组都不为空时，把排除数组中在包含数据中的去掉作为排除下标
     *  只有包含，则只返回包含的:
     * 只有排除的，则去除排除的；如果此时有包含的，则把包含的也加到,再去排除下标
     *   1、仅包含--仅包含下标有值；
     *   Tool::formatArrUbound($data_list, true, Tool::arrEqualKeyVal(['id', 'table_person_id', 'table_name', 'person_name']), []);
     *   2仅排除--仅排除下标有值；
     *   Tool::formatArrUbound($data_list, true, [], ['id', 'table_person_id', 'table_name', 'person_name']);
     *   4先加入包含的，再排除去掉-加入些,排除些，修改一些下标名，并排除一些字段是很有用-- 两个下标都有值；   8包含中的，再去除排除的---没必要处理--可直接用仅包含
     *    修改一些下标名，并排除一些字段是很有用-
     *  Tool::formatArrUbound($data_list, true, ['data_id' => 'id', 'person_id' => 'table_person_id', 'tablename' => 'table_name', 'personname' => 'person_name'], ['id', 'table_person_id', 'table_name', 'person_name']);
     *   或
     *    $changeUbound = ['data_id' => 'id', 'person_id' => 'table_person_id', 'tablename' => 'table_name', 'personname' => 'person_name'];
     *    Tool::formatArrUbound($data_list, true, $changeUbound , array_values($changeUbound));
     * @param array $array 一/二维数组
     * @param boolean $needNotIn  keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     * @param array $includeUboundArr 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     * @param array $exceptUboundArr 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     * @return array 一/二维数组
     */
    public static function formatArrUbound(&$array, $needNotIn = false, $includeUboundArr = [], $exceptUboundArr = []){
        if(empty($array) || !is_array($array)) return $array;

        // 下标数组，不是数组，则转为空数组
        if(!is_array($includeUboundArr))  $includeUboundArr = [];
        if(!is_array($exceptUboundArr))  $exceptUboundArr = [];

        // 下标数组都为空，则直接返回
        if(empty($includeUboundArr) && empty($exceptUboundArr))  return $array;

        $operateType = 0;
        if(!empty($includeUboundArr) ){
            if(!empty($exceptUboundArr)){
                $operateType = 4;
            }else{
                $operateType = 1;
            }
        }else{
            $operateType = 2;
        }

        // 原数据中的下标
        $dataUbounds = [];
        if( !static::isMultiArr($array) ){// 一维数组
            $dataUbounds = array_keys($array);
        }else{
            foreach($array as $info){
                if(is_array($info)) $dataUbounds = array_keys($info);
                break;
            }
        }

        // 原下标为空，则直接返回
        // if(empty($dataUbounds)) return $array;

        switch($operateType) {
            case 1:// 1、仅包含；
                static::formatTwoArrKeys($array, $includeUboundArr, $needNotIn);
                break;
            case 2://  2仅排除；
                $realUbound = array_values(array_diff($dataUbounds, $exceptUboundArr));
                if(empty($realUbound)){// 下标都移除完了，自然为空数组了
                    $array = [];
                    return $array;
                }
                $realUbound = static::arrEqualKeyVal($realUbound);// 一维数组转换为键值相同的一维数组
                static::formatTwoArrKeys($array, $realUbound, $needNotIn);
                break;
            case 4:// 4先加入包含的，再排除去掉-加入些,排除些，修改一些下标名，并排除一些字段是很有用-
                $realUbound = $dataUbounds;
                $realUbound = static::arrEqualKeyVal($realUbound);// 一维数组转换为键值相同的一维数组
                $realUbound = array_merge($realUbound, $includeUboundArr);
                $exceptUboundArrTem = static::arrEqualKeyVal($exceptUboundArr);
                $realUbound = array_diff_key($realUbound, $exceptUboundArrTem);
                static::formatTwoArrKeys($array, $realUbound, $needNotIn);
                break;
            default:
                break;
        }
        return $array;
    }

    /**
     * 一维数组转换为键值相同的一维数组
     *
     * @param array $array 一维数组
     * @param boolean $equalType  统计的类型，false:以键为标准，true：以值为标准
     * @return array 一维数组
     */
    public static function arrEqualKeyVal($array,  $equalType = true){
        $reArr = [];
        foreach($array as $k => $v){
            if($equalType){
                $reArr[$v] = $v;
            }else{
                $reArr[$k] = $k;
            }
        }
        return $reArr;
    }

    /**
     * 获得当前的路由和方法
     *
     * @return string 当前的路由和方法  App\Http\Controllers\CompanyWorkController@addInit
     */
    public static function getActionMethod(){
        // \Request::route()->getActionName()
        // web : App\Http\Controllers\Web\DogTools\ClassesController@add
        // api : Web\DogTools\ClassesController@ajax_add

        //  \Route::current()->getActionName(); web是可以的，但是api \Route::current()会是null
        return \Request::route()->getActionName() ?? '';
    }

    /**
     * 获得缓存数据
     * @param string $pre 键前缀 __FUNCTION__
     * @param string $cacheKey 键
     * @param array $paramKeyValArr 会作为键的关键参数值数组 --一维数组
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ;
     * @param keyPush 键加入无素 1 $pre 键前缀 2 当前控制器方法名;
     * @return mixed ;; false失败
     */
    public static function getCacheData($pre, &$cacheKey, $paramKeyValArr, $operate, $keyPush = 0){
        $dir = __DIR__;// 加入当前文件路径，防止一个服务器布置多个站点时，缓存键相同，被复盖。
        array_push($paramKeyValArr, $dir);

        if( ($keyPush & 1) == 1)  array_push($paramKeyValArr, $pre);

        if( ($keyPush & 2) == 2){
            $actionMethod = self::getActionMethod();// 当前控制器方法名  App\Http\Controllers\weixiu\IndexController@index
            array_push($paramKeyValArr, $actionMethod);
        }
        $temArr = [];
        foreach ( $paramKeyValArr as $k => $v) {
            if(! is_string($v) && ! is_numeric($v)){
                $v = serialize($v);
            }
            array_push($temArr, $k . '$@' . $v);
        }
        $cacheKey = md5(implode("#!%", $temArr));
        return self::getRedis($pre .$cacheKey, $operate);
    }

    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $value 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化
     * @return string $key [含前缀]
     * @author zouyan(305463219@qq.com)
     */
    public static function cacheData($pre = '', $key = null, $value = '', $expire = 0, $operate = 1)
    {
        // 缓存数据
        return self::setRedis($pre, $key, $value, $expire , $operate); // 1天
    }

    /**
     * 列出日期區間的 所有日期清單
     * @param string $first 开始日期 YYYY-MM-DD
     * @param string $last 结束日期 YYYY-MM-DD
     * @param string $step 步长 '+1 day'
     * @param string $format 日期格式化 'Y-m-d'
     * @return array $dates  区间内的日期[含]
     * @author zouyan(305463219@qq.com)
     */
    public static function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates   = [];
        $current = strtotime($first);
        $last    = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    /**
     * 列出日期區間的 所有月清單
     * @param string $start 开始日期 YYYY-MM-DD
     * @param string $end 结束日期 YYYY-MM-DD
     * @return array $dates  区间内的月[含] [201809,201810]
     * @author zouyan(305463219@qq.com)
     */
    public static function showMonthRange($start, $end)
    {
        $end = date('Ym', strtotime($end)); // 转换为月
        $range = [];
        $i = 0;
        do {
            $month = date('Ym', strtotime($start . ' + ' . $i . ' month'));
            //echo $i . ':' . $month . '<br>';
            $range[] = $month;
            $i++;
        } while ($month < $end);

        return $range;
    }

    /**
     * 列出日期區間的 所有年清單
     * @param string $start 开始日期 YYYY-MM-DD
     * @param string $end 结束日期 YYYY-MM-DD
     * @return array $dates  区间内的年[含] [2015,2016,2017,2018]
     * @author zouyan(305463219@qq.com)
     */
    public static function showYearRange($start, $end)
    {
        $end = date('Y', strtotime($end)); // 转换为月
        $range = [];
        $i = 0;
        do {
            $year = date('Y', strtotime($start . ' + ' . $i . ' year'));
            //echo $i . ':' . $year . '<br>';
            $range[] = $year;
            $i++;
        } while ($year < $end);

        return $range;
    }

    /**
     * 你上面的方法我觉得不怎么好，介绍一下我写的一个方法。方法函数如下，这样当你要的结果001的话，方法：dispRepair('1',3,'0')
     * 功能：补位函数
     * @param string str 原字符串
     * @param string len 新字符串长度
     * @param string $msg 填补字符
     * @param string $type 类型，0为后补，1为前补
     * @return array $dates  区间内的年[含] [2015,2016,2017,2018]
     * @author zouyan(305463219@qq.com)
     */
    public static function dispRepair($str, $len, $msg, $type = '1') {
        $length = $len - strlen($str);
        if ($length<1) return $str;
        if ($type == 1) {
            $str = str_repeat($msg, $length) . $str;
        } else {
            $str .= str_repeat($msg, $length);
        }
        return $str;
    }

    /**
     * 功能：获得日期
     * @param int $dateType 日期类型 1本周一;2 本周日;3 上周一;4 上周日;5 本月一日;6 本月最后一日;7 上月一日;8 上月最后一日;9 本年一日;10 本年最后一日;11 上年一日;12 上年最后一日
     * @return mixed $date 日期
     * @author zouyan(305463219@qq.com)
     */
    public static function getDateByType($dateType){
        switch($dateType){
            case 1://1本周一;
                return date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)); //w为星期几的数字形式,这里0为周日
                break;
            case 2://2 本周日;
                return date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)); //同样使用w,以现在与周日相关天数算
                break;
            case 3://3 上周一;
                // return date('Y-m-d', strtotime('-1 wednesday', time())); //无论今天几号,-1 monday为上一个有效周未
                return date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600) - 7*24*60*60); //本周一 减七天;
                break;
            case 4:// 4 上周日;
                // return date('Y-m-d', strtotime('-1 sunday', time())); //上一个有效周日,同样适用于其它星期;
                return date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600) - 1*24*60*60); //本周一 减一天;
                break;
            case 5:// 5 本月一日;
                return date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00')); //直接以strtotime生成;
                break;
            case 6:// 6 本月最后一日;
                return date('Y-m-d', strtotime(date('Y-m', time()) . '-' . date('t', time()) . ' 00:00:00')); //t为当月天数,28至31天
                break;
            case 7:// 7 上月一日;
                return date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00'))); //本月一日直接strtotime上减一个月;
                break;
            case 8:// 8 上月最后一日
                return date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400); //本月一日减一天即是上月最后一日;
                break;
            case 9:// 9 本年一日
                return date("Y-01-01");
                break;
            case 10:// 10 本年最后一日
                return date("Y-12-31");
                break;
            case 11:// 11 上年一日
                return date('Y-01-01', strtotime(date('Y-m-d') . ' -1 year'));
                break;
            case 12:// 12 上年最后一日
                return date('Y-12-31', strtotime(date('Y-m-d') . ' -1 year'));
                break;
            default:
                break;
        }
        return '';
    }

    /**
     * 功能：开始、结束日期 判断
     * @param string $begin_date 开始日期
     * @param string $end_date 结束日期
     * @param int $judge_type 1 判断开始日期不能为空 ; 2 判断结束日期不能为空；
     *                        4 开始日期 不能大于 >  当前日；8 开始日期 不能等于 =  当前日；16 开始日期 不能小于 <  当前日
     *                        32 结束日期 不能大于 >  当前日；64 结束日期 不能等于 =  当前日；128 结束日期 不能小于 <  当前日
     *                        256 开始日期 不能大于 >  结束日期；512 开始日期 不能等于 =  结束日期；1024 开始日期 不能小于 <  结束日期
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $nowTime 比较日期 格式 Y-m-d,默认为当前日期 Y-m-d; 需要时分秒时，可以传 date('Y-m-d H:i:s')
     * @param string $dateName 日期(默认); 时间
     * @return boolean 结果 true通过判断; sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeBeginEndDate($begin_date, $end_date, $judge_type = 0, $errDo = 1, $nowTime = '', $dateName = '日期' ){
//        $begin_date = CommonRequest::get($request, 'begin_date');// 开始日期
//        $end_date = CommonRequest::get($request, 'end_date');// 结束日期
        if(empty($nowTime)) $nowTime = date('Y-m-d');
        $nowTimeUnix = judgeDate($nowTime);

        if( ($judge_type & 1) == 1 && empty($begin_date)){// 1 判断开始日期不能为空
            $errMsg = '开始' . $dateName . '不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        if (!empty($begin_date)) {
            $begin_date_unix = judgeDate($begin_date);
            if($begin_date_unix === false){
                $errMsg = '开始' . $dateName . '不是有效' . $dateName . '!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
            // 4 开始日期 不能大于 >  当前日
            if(($judge_type & 4) == 4 && $begin_date_unix > $nowTimeUnix ){
                $errMsg = '开始' . $dateName . '不能大于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 8 开始日期 不能等于 =  当前日
            if(($judge_type & 8) == 8 && $begin_date_unix == $nowTimeUnix ){
                $errMsg = '开始' . $dateName . '不能等于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 16 开始日期 不能小于 <  当前日
            if(($judge_type & 16) == 16 && $begin_date_unix < $nowTimeUnix ){
                $errMsg = '开始' . $dateName . '不能小于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
        }

        if( ($judge_type & 2) == 2 && empty($end_date)){//2 判断结束日期不能为空；
            $errMsg = '结束' . $dateName . '不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        if (!empty($end_date)) {
            $end_date_unix = judgeDate($end_date);
            if($end_date_unix === false){
                $errMsg = '结束' . $dateName . '不是有效' . $dateName . '!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 32 结束日期 不能大于 >  当前日
            if(($judge_type & 32) == 32 && $end_date_unix > $nowTimeUnix ){
                $errMsg = '结束' . $dateName . '不能大于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 64 结束日期 不能等于 =  当前日
            if(($judge_type & 64) == 64 && $end_date_unix == $nowTimeUnix ){
                $errMsg = '结束' . $dateName . '不能等于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 128 结束日期 不能小于 <  当前日
            if(($judge_type & 128) == 128 && $end_date_unix < $nowTimeUnix ){
                $errMsg = '结束' . $dateName . '不能小于' . $dateName . '[' . $nowTime . ']!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
        }

        if(!empty($begin_date) && !empty($end_date) ){

            // 256 开始日期 不能大于 >  结束日期；
            if(($judge_type & 256) == 256 && $begin_date_unix > $end_date_unix ){
                $errMsg = '开始' . $dateName . '不能大于结束' . $dateName . '!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 512 开始日期 不能等于 =  结束日期；
            if(($judge_type & 512) == 512 && $begin_date_unix == $end_date_unix ){
                $errMsg = '开始' . $dateName . '不能等于结束' . $dateName . '!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            // 1024 开始日期 不能小于 <  结束日期
            if(($judge_type & 1024) == 1024 && $begin_date_unix < $end_date_unix ){
                $errMsg = '开始' . $dateName . '不能小于结束' . $dateName . '!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
        }
        return true;
    }

    /**
     * 功能：日期 加/减操作
     * @param string $operateDate 操作日期/时间;// 为空，则操作当前时间
     * @param array $oprates 操作类型 一维数组, 下面空格拼接执行
     *   // +1 day +1 hour +1 minute  可以随便自由组合，以达到任意输出时间的目的
     *   // -1 day  ---昨天  // 可以修改参数1为任何想需要的数  day也可以改成year（年），month（月），hour（小时），minute（分），second（秒）
     *   // +1 day  ---明天
     *   // +1 week  ---一周后
     *   // +1 week 2 days 4 hours 2 seconds  ---一周零两天四小时两秒后
     *  // next Thursday   ---下个星期四
     *   // last Monday  --- 上个周一
     *   // last month  ---一个月前
     *   // +1 month  ---一个月后
     *   // +10 year  ---十年后
     * @param string $format 返回数据格式化 "Y-m-d H:i:s","Y-m-d","H:i:s"
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $dateName 日期(默认); 时间
     * @return mixed  sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function addMinusDate($operateDate, $oprates = [], $format = 'Y-m-d H:i:s', $errDo = 1, $dateName = '时间')
    {
        // date_default_timezone_set('PRC'); //默认时区
        if(empty($operateDate)) $operateDate = date('Y-m-d H:i:s');
        // 开始时间
        $date_unix = judgeDate($operateDate);
        if($date_unix === false){
            $errMsg = '开始' . $dateName . '不是有效' . $dateName . '!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        // date('Y-m-d', strtotime ("+1 day", strtotime('2011-11-01')))
        if(!empty($oprates)){
            return date($format, strtotime (implode(' ', $oprates), strtotime(judgeDate($date_unix, "Y-m-d H:i:s"))));
        }
        return judgeDate($date_unix, $format);
    }

    /**
     * 功能：开始、结束日期 差--单位秒
     * @param string $begin_date 开始日期
     * @param string $end_date 结束日期,默认当前时间
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $dateName 日期(默认); 时间
     * @param int $reType  类型 1[默认]只返回正值 ; 2 $end_date - $begin_date ;3 $begin_date - $end_date
     * @return mixed  sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function diffDate($begin_date, $end_date = '', $errDo = 1, $dateName = '时间', $reType = 1){

        if(empty($end_date)) $end_date = date('Y-m-d H:i:s');

        // 开始时间
        $begin_date_unix = judgeDate($begin_date);
        if($begin_date_unix === false){
            $errMsg = '开始' . $dateName . '不是有效' . $dateName . '!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 结束时间
        $end_date_unix = judgeDate($end_date);
        if($end_date_unix === false){
            $errMsg = '结束' . $dateName . '不是有效' . $dateName . '!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        $starttime = $begin_date_unix;
        $endtime = $end_date_unix;
        switch($reType) {
            case 2://  2 $end_date - $begin_date ;
                break;
            case 3:// 3 $begin_date - $end_date
                $starttime = $end_date_unix;
                $endtime = $begin_date_unix;
                break;
            case 1:// 1[默认]只返回正值 ;
            default:
                if($begin_date_unix <= $end_date_unix){
                    $starttime = $begin_date_unix;
                    $endtime = $end_date_unix;
                }else{
                    $starttime = $end_date_unix;
                    $endtime = $begin_date_unix;
                }
                break;
        }
        //计算天数
        $timediff = $endtime - $starttime;

        return $timediff;
    }

    /**
     * 功能：格式化时间差--以年为基准
     * @param int $timediff 时间差秒数
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $dateName 日期(默认); 时间
     * @return mixed  sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function formatTimeDiff($timediff){
        $timediff = abs($timediff);
        // 1: 总年 ; 2 总年[向上取整] ;
        // 8 总天数 ; 16 总天数[向上取整] ;32 天数[去除年] 64 天数[去除年][向上取整]
        // 128 总时数 ; 256 总时数[向上取整] ;512 时数[去除年天] 1024 时数[去除年天][向上取整]
        // 2048 总分数 ; 4096 总分数[向上取整] ;8192 分数[去除年天时] 16384 分数[去除年天时][向上取整]
        // 总秒数  秒数[去除年天时分]
        // 返回类型

        // 多少年
        // 总年 ;
        $yearInt = intval($timediff / (365 * 24 * 60 * 60) );
        //  总年[向上取整] ;
        $yearCeil = ceil($timediff / (365 * 24 * 60 * 60) );

        // 8 总天数 ; 16 总天数[向上取整] ;32 天数[去除年] 64 天数[去除年][向上取整]
        $daysInt = intval($timediff / (24 * 60 * 60) ); // 86400  多少天
        $daysCeil = ceil($timediff / (24 * 60 * 60) ); // 86400  多少天

        // 去除年的天数
        $yearRemain = $timediff % (365 * 24 * 60 * 60);
        $daysRemainInt = intval($yearRemain / (24 * 60 * 60) ); // 86400  多少天
        $daysRemainCeil = ceil($yearRemain / (24 * 60 * 60) ); // 86400  多少天

        // 128 总时数 ; 256 总时数[向上取整] ;512 时数[去除年天] 1024 时数[去除年天][向上取整]
        //计算小时数
        $hoursInt = intval($timediff / (60 * 60));// 多少小时 3600
        $hoursCeil = ceil($timediff / (60 * 60));// 多少小时 3600

        // 去除年天
        $remain = $timediff % (24 * 60 * 60);// 86400
        $hoursRemain = intval($remain / (60 * 60));// 多少小时 3600
        $hoursRemainCeil = ceil($remain / (60 * 60));// 多少小时 3600

        // 2048 总分数 ; 4096 总分数[向上取整] ;8192 分数[去除年天时] 16384 分数[去除年天时][向上取整]
        //计算分钟数
        $minsInt = intval($timediff / 60); // 多少分钟
        $minsCeil = ceil($timediff / 60); // 多少分钟

        $remain = $remain % (60 * 60);// 3600
        $minsRemain = intval($remain / 60); // 多少分钟
        $minsRemainCeil = ceil($remain / 60); // 多少分钟

        //计算秒数
        $secsRemain = $remain % 60; // 多少秒
        $secsRemainCeil = ceil($remain % 60); // 多少秒
        return [
            "yearInt" => $yearInt // 总年
            ,"yearCeil" => $yearCeil // 总年[向上取整]

            ,"daysInt" => $daysInt // 总天数
            ,"daysCeil" => $daysCeil // 总天数[向上取整]
            ,"daysRemainInt" => $daysRemainInt // 天数[去除年]
            ,"daysRemainCeil" => $daysRemainCeil // 天数[去除年][向上取整]

            ,"hoursInt" => $hoursInt // 总时数
            ,"hoursCeil" => $hoursCeil // 总时数[向上取整]
            ,"hoursRemain" => $hoursRemain // 时数[去除年天]
            ,"hoursRemainCeil" => $hoursRemainCeil // 时数[去除年天][向上取整]

            ,"minsInt" => $minsInt // 总分数
            ,"minsCeil" => $minsCeil // 总分数[向上取整]
            ,"minsRemain" => $minsRemain // 分数[去除年天时]
            ,"minsRemainCeil" => $minsRemainCeil // 分数[去除年天时][向上取整]

            ,"timediff" => $timediff // 总秒数
            ,"secsRemain" => $secsRemain // 秒数[去除年天时分]
            ,"secsRemainCeil" => $secsRemainCeil // 秒数[去除年天时分][向上取整]
            // ,"aaaa" => $aaaaa // aaaaa
        ];
    }

    /**
     * 具体秒数转为年天时分秒格式的文字
     *
     * @param int $secondNum 时间秒数
     * @return  string sting 年天时分秒格式的文字
     * @author zouyan(305463219@qq.com)
     */
    public static function formatSecondNum($secondNum = 0){
        if(!is_numeric($secondNum) || $secondNum <= 0) $secondNum = 0;
        $reArr = [];
        $formatArr = static::formatTimeDiff($secondNum);
        $yearInt = $formatArr['yearInt'];// 年
        if(is_numeric($yearInt) && $yearInt > 0)  array_push($reArr, $yearInt . '年');
        $daysRemainInt = $formatArr['daysRemainInt'];// 天
        if(is_numeric($daysRemainInt) && $daysRemainInt > 0)  array_push($reArr, $daysRemainInt . '天');
        $hoursRemain = $formatArr['hoursRemain'];// 时
        if(is_numeric($hoursRemain) && $hoursRemain > 0)  array_push($reArr, $hoursRemain . '小时');
        $minsRemain = $formatArr['minsRemain'];// 分
        if(is_numeric($minsRemain) && $minsRemain > 0)  array_push($reArr, $minsRemain . '分钟');
        $secsRemain = $formatArr['secsRemain'];// 秒
        if(is_numeric($secsRemain) && $secsRemain > 0)  array_push($reArr, $secsRemain . '秒');
        return implode('', $reArr);
    }

    /**
     * 功能：计算两个时间内相差多少个月
     *
     * @param string $start_m 开始日期 --小
     * @param string $end_m 结束日期,默认当前时间  --大
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $dateName 日期(默认); 时间
     * @return mixed  sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     *
    // 总月数
    $monthInt = Tool::diffMonth($starttime, $endtime, $errDo, $dateName);
    // 月数[去除年月]
    $monthRemainInt = $monthInt % 12;
     */
    public static function diffMonth($begin_date, $end_date, $errDo = 1, $dateName = '时间'){

        // 开始时间
        $begin_date_unix = judgeDate($begin_date);
        if($begin_date_unix === false){
            $errMsg = '开始' . $dateName . '不是有效' . $dateName . '!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 结束时间
        $end_date_unix = judgeDate($end_date);
        if($end_date_unix === false){
            $errMsg = '结束' . $dateName . '不是有效' . $dateName . '!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        if($begin_date_unix <= $end_date_unix){
            $starttime = $begin_date_unix;
            $endtime = $end_date_unix;
        }else{
            $starttime = $end_date_unix;
            $endtime = $begin_date_unix;
        }

        $starttime = judgeDate($starttime, "Y-m-d H:i:s");
        $endtime = judgeDate($endtime, "Y-m-d H:i:s");

        $date1 = explode('-',$starttime);
        $date2 = explode('-',$endtime);

        if($date1[1] < $date2[1]){ //判断月份大小，进行相应加或减
            $month_number = abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
        }else{
            $month_number = abs($date1[0] - $date2[0]) * 12 - abs($date1[1] - $date2[1]);
        }
        return $month_number;
    }

    /**
     * 功能：获得区分多数据库的数据目录下的表对象
     *
     * @param string $dbModelDir 区分多数据库的数据目录
     * @param string $tableName 数据表名称
     * @param string $tableObj 区分多数据库的数据目录
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  $tableObj 多数据库的数据目录下的表对象； throws 错误  string字符: 错误字符串
     * @author zouyan(305463219@qq.com)
     *
     */
    // $tableName App\ModelsVerify\$dbModelDir 目录下 的表对象
    public static function getAPIObjByModelName($dbModelDir = '', $tableName = '', &$tableObj = null, $errDo = 1){
        // App\Services\Request\API\Sites
        $className = "App\\ModelsVerify" ;// . 'Business';
        if(strlen($dbModelDir) > 0) $className .= '\\' . $dbModelDir;
        $className .= '\\' . $tableName;
        if (! class_exists($className )) {
            // throws('参数[Model_name]不正确！');
            $error = "类[" . $className . "]不存在！";
            if($errDo == 1) throws($error);
            return $error;
        }
        $tableObj = new $className();
        return $tableObj;
    }

    /**
     * 功能：获得数据表字段说明内容--主要用来做数据验证
     *
     * @param string $table_name 数据表名称
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $dbDir 区分多数据库的数据目录
     * @param string $dbFileTag 数据表配置文件的标识
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getDBFieldsConfig($table_name = '', $configUbound = '', $dbDir = 'RunBuy', $dbFileTag = 'models', $errDo = 1){
//        $key = $dbFileTag . $dbDir . $table_name; // 'modelsRunBuyfailed_jobs.fields'
//        if(is_string($configUbound) && strlen($configUbound) > 0) $key .=  '.' . $configUbound;
//        $fieldsConfig = __($key);// __('modelsRunBuyfailed_jobs.fields');
//        if($fieldsConfig == $key){
//            $fieldsConfig = [];// 没有读取到
//            // $error = "没有[" . $key . "]配置信息！";
//            $error = "没有[" . $key . "]配置信息！";
//            if($errDo == 1) throws($error);
//            return false;
//        }
        $result = static::getAPIObjByModelName($dbDir, $table_name, $tableObj, $errDo);
        if(is_string($result)) {
            $error = $result;
            if($errDo == 1) throws($error);
            return $error;
        }
        $fieldsConfig = $tableObj::getVerifyRule($configUbound, $dbFileTag);
        return $fieldsConfig;

    }

    /**
     * 功能：获得多语言的model 或 对应数据库的 说明内容
     *
     * @param int $langType 数据库多语言文件的类型 1 总标识model的 8 当前数据库的 -- 只能一个一个的用
     * @param string $table_name 数据表名称
     * @param string $dbDir 区分多数据库的数据目录
     * @param string $dbFileTag 数据表配置文件的标识
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  string 有错：  throws 错误 数组: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getDBConfig($langType = 8, $table_name = '', $dbDir = 'RunBuy', $dbFileTag = 'models', $errDo = 1){
        $result = static::getAPIObjByModelName($dbDir, $table_name, $tableObj, $errDo);
        if(is_string($result)) {
            $error = $result;
            if($errDo == 1) throws($error);
            return $error;
        }
        $langConfig = $tableObj::getModelsOrDbLang($dbFileTag, $langType);
        return $langConfig;

    }

    /**
     * 功能：获得多语言的缓存文件指定下标的内容
     *
     * @param string $langFileName 文件名称
     * @param string $configUbound 读取配置数组下的指定下标，可为空：读整个配置
     * @return array 文件指定下标内容
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangUboundContent($langFileName, $configUbound = ""){
        $langContent = static::getLangContent($langFileName);
        if(strlen($configUbound) > 0 ) return $langContent[$configUbound] ?? '';
        return $langContent;
    }

    /**
     * 功能：获得多语言的缓存文件内容
     *
     * @param string $langFileName 文件名称
     * @return array 文件内容
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getLangContent($langFileName){
        $needCache = config('public.langDBFieldCacheOpen');
        $cacheExpire = config('public.langDBFieldCacheExpire');
        $keyRedisPre = Tool::getProjectKey(1 | 2 | 4, ':', ':') . 'lang'  ;
        $keyRedis = ':' . $langFileName ;
        $operateRedis = 1;

        // 获得缓存
        if($needCache){
            $langContent = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
            Log::info('多语言文件日志 --从缓存中获取到-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langContent]);
            if(is_array($langContent) && !empty($langContent)) return $langContent;
        }

        $langContent = __($langFileName);
        if(!is_array($langContent)) $langContent = [];
        // 缓存
        if($needCache){
            Log::info('多语言文件日志 --缓存数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langContent]);
            Tool::setRedis($keyRedisPre, $keyRedis, $langContent, $cacheExpire , $operateRedis);
        }
        return $langContent;
    }

    /**
     * 功能：获得数据表字段说明内容--主要用来做数据验证
     *
     * @param array $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
     * @param array $judgeData 待验证数据 一维/二维
     * @param array $mustFields 必填字段
     * @param string $table_name 数据表名称
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $dbDir 区分多数据库的数据目录
     * @param string $dbFileTag 数据表配置文件的标识
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  throws 错误 boolean: true 成功 ; sting 具体错误 ；array 具体错误 一维/二维的[根数据的维数一样]
     *  [
     *      'firstErr' => $firstErr,// 第一条错误信息
     *       'errMsg' => $ItemErrMsg,// 所有错误数组 一维数组
     *       'varErrMsg' => $varErrMsg,// 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
     *  ]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function judgeInDBData($judgeType = 1, &$judgeData = [], $mustFields = [], $table_name = '', $configUbound = '', $dbDir = 'RunBuy', $dbFileTag = 'models', $errDo = 1){
        $fieldsConfig = static::getDBFieldsConfig($table_name, $configUbound, $dbDir, $dbFileTag, $errDo);
        if(is_string($fieldsConfig)){
            $error = $fieldsConfig;// "没有配置信息！";
            if($errDo == 1) throws($error);
            return $error;
        }
        return static::judgeInDBDataByConfig($judgeType, $fieldsConfig, $judgeData, $mustFields, $errDo);
    }

    /**
     * 功能：验证数据是否合规则 === true 代表通过验证
     *
     * @param array $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
     * @param array $fieldsConfig 数据表配置
     * @param array $judgeData 待验证数据 一维/二维
     * @param array $mustFields 必填字段
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  throws 错误 ; boolean: true成功 array 具体错误 一维/二维的[根数据的维数一样]
     *  [
     *      'firstErr' => $firstErr,// 第一条错误信息
     *       'errMsg' => $ItemErrMsg,// 所有错误数组 一维数组
     *       'varErrMsg' => $varErrMsg,// 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
     *  ]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function judgeInDBDataByConfig($judgeType = 1, $fieldsConfig = [], &$judgeData = [], $mustFields = [], $errDo = 1){
        if(!is_array($mustFields)) $mustFields = [];
        $fieldsConfig = isset($fieldsConfig['fields']) ? $fieldsConfig['fields']: $fieldsConfig;

        // 如果是一维数组,则转为二维数组
        $isMulti = static::isMultiArr($judgeData, true);
        $hasErr = false;// 是否有过错误
        $errMsgAll = [];//错误信息数组
        // $dataInfo = $judgeData;
        foreach($judgeData as $dataInfo){
            $firstErr = '';// 第一条错误信息
            $ItemErrMsg = [];// 所有错误数组 一维数组
            $varErrMsg = [];//错误信息数组

            // 新加/修改时字段判断
            if(($judgeType & 2) == 2 || ($judgeType & 4) == 4){
                $dataKey = array_keys($dataInfo);
                foreach($fieldsConfig as $itemField => $itemFieldConfig){
                    // 特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                    $itemPowerNum = $itemFieldConfig['powerNum'] ?? 0;
                    // 新加时必填
                    if(($itemPowerNum & 2) == 2 && ($judgeType & 2) == 2){
                        // 加入必填
                        if(!in_array($itemField,$mustFields)) array_push($mustFields, $itemField);
                    }

                    // 修改时如果有下标则判断必填
                    //if(($judgeType & 4) == 4 && in_array($itemField, $dataKey)){
                    // 8修改时如果有下标则判断必填
                    if(($itemPowerNum & 8) == 8 && ($judgeType & 4) == 4 && in_array($itemField, $dataKey)){
                        // 加入必填
                        if(!in_array($itemField,$mustFields)) array_push($mustFields, $itemField);
                    }

                    // 4不可修改
                    if(($itemPowerNum & 4) == 4 && ($judgeType & 4) == 4){
                        // 存在此下标
                        if(in_array($itemField, $dataKey)) {
                            $error = "[" . $itemField . "]值不可修改！";
                            // if($errDo == 1) throws($error);
                            $hasErr = true;// 是否有过错误
                            if(strlen($firstErr) <= 0) $firstErr = $error;
                            array_push($ItemErrMsg, $error);
                            if(!isset($varErrMsg[$itemField])) $varErrMsg[$itemField] = [];
                            array_push($varErrMsg[$itemField], $error);
                            // return $error;
                        }

                    }
                }

            }

            $valiDateParam = [];
            // 必填字段验证规则
            foreach($mustFields as $field){
                // 字段不存在
                if(!isset($fieldsConfig[$field])){
                    $error = "没有[" . $field . "]配置信息！";
                    // if($errDo == 1) throws($error);
                    $hasErr = true;// 是否有过错误
                    if(strlen($firstErr) <= 0) $firstErr = $error;
                    array_push($ItemErrMsg, $error);
                    if(!isset($varErrMsg[$field])) $varErrMsg[$field] = [];
                    array_push($varErrMsg[$field], $error);
                    // return $error;
                }
                $configInfo = $fieldsConfig[$field] ?? [];
                if(empty($configInfo)){

                    continue;
                }
                $field_value = $dataInfo[$field] ?? '';

                $result = static::appendFieldValidate($valiDateParam, $configInfo, $field, $field_value, (1 | 2), $errDo);
                if(is_string($result)) {
                    $errMsg = $result;
                    // if($errDo == 1) throws($errMsg);
                    $hasErr = true;// 是否有过错误
                    if(strlen($firstErr) <= 0) $firstErr = $errMsg;
                    array_push($ItemErrMsg, $errMsg);
                    if(!isset($varErrMsg[$field])) $varErrMsg[$field] = [];
                    array_push($varErrMsg[$field], $errMsg);
                    // return $errMsg;
                }
            }

            // 需要验证的数据中的其它字段,有值才验证
            foreach($dataInfo as $field => $field_value){
                if(in_array($field, $mustFields)) continue;// 在必填项
                // 字段不存在
                if(!isset($fieldsConfig[$field])){
//                    $error = "没有[" . $field . "]配置信息!！";
//                    if($errDo == 1) throws($error);
//                    if(strlen($firstErr) <= 0) $firstErr = $error;
//                    array_push($ItemErrMsg, $error);
//                    if(!isset($varErrMsg[$field])) $varErrMsg[$field] = [];
//                    array_push($varErrMsg[$field], $error);
//                    return $error;
                    continue;
                }
                $configInfo = $fieldsConfig[$field] ?? [];
                if(empty($configInfo)) continue;

                $result = static::appendFieldValidate($valiDateParam, $configInfo, $field, $field_value, 2, $errDo);
                if(is_string($result)) {
                    $errMsg = $result;
                    // if($errDo == 1) throws($errMsg);
                    $hasErr = true;// 是否有过错误
                    if(strlen($firstErr) <= 0) $firstErr = $errMsg;
                    array_push($ItemErrMsg, $errMsg);
                    if(!isset($varErrMsg[$field])) $varErrMsg[$field] = [];
                    array_push($varErrMsg[$field], $errMsg);
                    // return $errMsg;
                }
            }

            $validRes = "";
            if(!empty($valiDateParam)){
                $validRes = static::dataValid($valiDateParam, $errDo);
            }
            // 验证通过 且 上面没有错
            if(is_string($validRes) && strlen($validRes) <= 0 && count($varErrMsg) <= 0){
                array_push($errMsgAll, [
                    'firstErr' => $firstErr,
                    'errMsg' => $ItemErrMsg,
                    'varErrMsg' => $varErrMsg,
                ]);
                continue;
            }
            // 验证通过 但 上面有错
            if(is_string($validRes) && strlen($validRes) <= 0 && count($varErrMsg) > 0){
                $hasErr = true;// 是否有过错误
                array_push($errMsgAll, [
                    'firstErr' => $firstErr,
                    'errMsg' => $ItemErrMsg,
                    'varErrMsg' => $varErrMsg,
                ]);
                continue;
            }
            // 验证不通过
            $hasErr = true;// 是否有过错误
            $vaildFirstErr = $validRes['firstErr'] ?? '';
            $vaildErrMsg = $validRes['errMsg'] ?? '';
            $vaildVarErrMsg = $validRes['varErrMsg'] ?? '';
            if(strlen($firstErr) <= 0 && strlen($vaildFirstErr) > 0) $firstErr = $vaildFirstErr;
            foreach($vaildVarErrMsg as $vaild_field => $vald_errs){
                if(! isset($varErrMsg[$vaild_field])) {
                    $varErrMsg[$vaild_field] = $vald_errs;
                }else{
                    $varErrMsg[$vaild_field] = array_values(array_merge($varErrMsg[$vaild_field], $vald_errs));
                }
            }
            $ItemErrMsg = array_values(array_merge($ItemErrMsg, $vaildErrMsg));
            array_push($errMsgAll, [
                'firstErr' => $firstErr,
                'errMsg' => $ItemErrMsg,
                'varErrMsg' => $varErrMsg,
            ]);
//            if(is_string($validRes) && strlen($validRes) > 0) {
//                $errMsg = $validRes;
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
//            }
        }

        if(!$isMulti){
            $judgeData = $judgeData[0] ?? [];
            $errMsgAll = $errMsgAll[0] ?? [];
        }
        if($hasErr) return $errMsgAll;
        return true;
    }

    /**
     * 功能：获得单个字段的数据验证规则
     *
     * @param array $valiDateParam 数据验证规则 -二维数组
     * @param array $configInfo 单个字段的配置信息
     * @param string $field 字段
     * @param string $field_value 字段值
     * @param int $appendType 生成的规则类型 1:必填验证 ；2：有值才验证
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  sting 具体错误 ； throws 错误 ; array: 成功
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function appendFieldValidate(&$valiDateParam = [], $configInfo = [], $field = '', $field_value = '', $appendType = 2, $errDo = 1){
        $field_name = $configInfo['field_name'] ?? '';

        // 必填验证规则
        if(($appendType & 1) == 1){
            $judgeItem = ["var_name" => $field, "input"=> $field_value, "require"=>"true", "message"=>'数据校验不通过，[' . $field . '(' . $field_name . ')]必须有值！'];
            array_push($valiDateParam, $judgeItem);
        }

        // 如果有值验证规则
        if(($appendType & 2) == 2){
            $tem_valiDateParam_twoarr = $configInfo['valiDateParam'] ?? [];
            // 如果是一维数组,则转为二维数组
            $isMulti = static::isMultiArr($tem_valiDateParam_twoarr, true);
            foreach($tem_valiDateParam_twoarr as $tem_valiDateParam){
                // 没有验证规则-- 不验
                if(empty($tem_valiDateParam)){

                    //                $error = "[' . $field . '(' . $field_name . ')]配置信息有误！";
                    //                if($errDo == 1) throws($error);
                    //                return $error;
                }else{
                    $message = $tem_valiDateParam['message'] ?? '';
                    $message = str_replace('{fieldName}', $field_name, $message);

                    $tem_valiDateParam = array_merge($tem_valiDateParam, ["var_name" => $field, "input"=> $field_value,"require"=>"false", "message"=> $message]);
                    array_push($valiDateParam, $tem_valiDateParam);
                }

            }
        }
        return $valiDateParam;
    }

    /**
     * 功能：验证数据
     * @param array $valiDateParam 需要验证的条件
     *  $valiDateParam= [
     *      //["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["title"],"require"=>"true","message"=>'闪购名称不能为空'],  -- 必填  -- require是否必填，可以与下面的一方一起参与验证
     *       ["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["state"],"require"=>"false","validator"=>"custom","regexp"=>"/^([01]|10)$/","message"=>'闪购状态值有误'],--正则
     *       ["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["title"],"require"=>"false","validator"=>"length","min"=>"1","max"=>"160","message"=>'闪购名称长度为1~ 160个字符'],--判断长度
     *      ["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["title"],"require"=>"false","validator"=>"compare","operator"=>"比较符>=<=","to"=>"被比较值","message"=>'闪购名称不能大于10'],--比较
     *       ["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["title"],"require"=>"false","validator"=>"range","min"=>"最小值1","max"=>"最大值10","message"=>'闪购值必须大于等于1且小于等于10'],--范围
     *      ["var_name" => "验证变量名[可为空]" ,"input"=>$_POST["market_id"],"require"=>"false","validator"=>"integer","message"=>'闪购地编号必须为数值'], --配置好的
     *   ];
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed array 或不为空的字符[没有可能性]  错误信息 ，没有错，则为空
     *   只可能返回这样的数组 ：下标 firstErr ：为 '' ;或 errMsg为空数组，就代表没有错误
     *   可能是 false 或 null ：代表没有错
     *   $errs = [
     *       'firstErr' => '',// // 第一条错误信息
     *       'errMsg' => [],// 所有错误数组 一维数组
     *       'varErrMsg' => [],// 如果有 var_name 验证变量名[可为空]的，按此下标分组错误信息
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function dataValid($valiDateParam = [], $errDo = 1) {
        if(empty($valiDateParam) || (!is_array($valiDateParam))){
            return false;
        }
        $validateObj = new Validate();
        $validateObj->validateparam = $valiDateParam;
        // return $validateObj->validate();
        $error = $validateObj->validate();
        if ($error != ''){
            // if($errDo == 1) throws($error);
            // return $error;
            // output_error($error);
            // 如果是json格式
            if (!isNotJson($error)) {
                $errArr = json_decode($error , true);
                $firstErr = $errArr['firstErr'] ?? '';
                $errMsg = $errArr['errMsg'] ?? [];
                $varErrMsg = $errArr['varErrMsg'] ?? [];
                if($errDo == 1 && is_array($errMsg) && count($errMsg) <= 0) throws(implode('<br/>', $errMsg));
                return $errArr;
            }
            // 可能是 false 或 null ：代表没有错
            //  if($errDo == 1) throws($error);
            return $error;
        }
        return '';
    }

//    public static function dataValid($valiDateParam = [], $errDo = 1) {
//        if(empty($valiDateParam) || (!is_array($valiDateParam))){
//            return false;
//        }
//        $validateObj = new Validate();
//        $validateObj->validateparam = $valiDateParam;
//        // return $validateObj->validate();
//        $error = $validateObj->validate();
//        if ($error != ''){
//            if($errDo == 1) throws($error);
//            return $error;
//            // output_error($error);
//        }
//        return '';
//    }

    /**
     * 功能：获得框文件夹绝对路径
     * @param string $pathKey 路径关键字
     *       app   app目录的绝对路径 srv/www/work/work.0101jz.com/app
     *       base  项目根目录的绝对路径 /srv/www/work/work.0101jz.com
     *       base   'public'  相对于应用目录的给定文件生成绝对路径 /srv/www/work/work.0101jz.com/public
     *       config 应用配置目录的绝对路径  /srv/www/work/work.0101jz.com/config
     *       database 应用数据库目录的绝对路径 /srv/www/work/work.0101jz.com/database
     *       public public目录的绝对路径 /srv/www/work/work.0101jz.com/public
     *       storage   storage目录的绝对路径 /srv/www/work/work.0101jz.com/storage
     *       storage    'app/file.txt'   还可以使用storage_path函数生成相对于storage目录的给定文件的绝对路径 /srv/www/work/work.0101jz.com/storage/app/file.txt
     * @param string $dir 目录或文件
     * @return string  绝对路径
     * @author zouyan(305463219@qq.com)
     */
    public static function getPath($pathKey = '', $dir = ''){
        $returnPath = '';
        switch (strtolower($pathKey)) {
            case 'app':
                // app_path();//app目录的绝对路径 srv/www/work/work.0101jz.com/app
                $returnPath = app_path();
                break;
            case 'base':
                // base_path();// 项目根目录的绝对路径 /srv/www/work/work.0101jz.com
                // $path = base_path('vendor/bin'); // 相对于应用目录的给定文件生成绝对路径
                //    base_path('public') ;// /srv/www/work/work.0101jz.com/public
                if(empty($dir)){
                    $returnPath = base_path();
                }else{
                    $returnPath = base_path($dir);
                }
                break;
            case 'config':
                // config_path();  // 应用配置目录的绝对路径  /srv/www/work/work.0101jz.com/config
                $returnPath = config_path();
                break;
            case 'database':
                // database_path();// 应用数据库目录的绝对路径 /srv/www/work/work.0101jz.com/database
                $returnPath = database_path();
                break;
            case 'public':
                // public_path(); // public目录的绝对路径 /srv/www/work/work.0101jz.com/public
                $returnPath = public_path();
                break;
            case 'storage':
                // storage_path(); // storage目录的绝对路径 /srv/www/work/work.0101jz.com/storage
                // storage_path('app/file.txt')还可以使用storage_path函数生成相对于storage目录的给定文件的绝对路径 /srv/www/work/work.0101jz.com/storage/app/file.txt
                if(empty($dir)){
                    $returnPath = storage_path();
                }else{
                    $returnPath = storage_path($dir);
                }
                break;
            default:
                break;
        }
        return $returnPath;
    }

    /**
     * 功能：对二维数组,按指定多个下标进行排序
     * @param array $data 需要排序的二维数组[如数据表数据-二维数据]
     * @param array  $keys ,用来排序的字段
     *      key:字段下标 ;
     *      sort:排序顺序标志;asc[按照上升顺序排序]-默认,desc[按照下降顺序排序]；
     *      type: 排序类型标志;regular[将项目按照通常方法比较]-默认,numeric[将项目按照数值比较],string[将项目按照字符串比较]
     *      array(
     *          array(key=>col1, sort=>desc),
     *          array(key=>col2, type=>numeric)
     *      )
     * @return array 排序后的二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function php_multisort($data, $keys){
        if(empty($data) || (!is_array($data)))  return $data;
        // List As Columns
        foreach ($data as $key => $row) {
            foreach ($keys as $k){
                $cols[$k['key']][$key] = $row[$k['key']];
            }
        }
        // List original keys
        $idkeys=array_keys($data);
        // Sort Expression
        $i=0;
        $sort = '';
        foreach ($keys as $k){
            if($i>0){$sort.=',';}
            $sort.='$cols["'.$k['key'].'"]';
            if($k['sort']){$sort.=',SORT_'.strtoupper($k['sort']);}
            if($k['type']){$sort.=',SORT_'.strtoupper($k['type']);}
            $i++;
        }
        $sort.=',$idkeys';
        // Sort Funct
        $sort='array_multisort('.$sort.');';
        eval($sort);
        // Rebuild Full Array
        foreach($idkeys as $idkey){
            $result[$idkey]=$data[$idkey];
        }
        return $result;
    }


    // 判断参数
    public static function judgeInitParams($paramName, $pramVal)
    {
        if (((int )$pramVal) <=0){
            throws('参数[' . $paramName . ']必须为整数！');
        }
    }

    // 判断是否为空
    public static function judgeEmptyParams($paramName, $pramVal)
    {
        if (empty($pramVal)){
            throws('参数[' . $paramName . ']不能为空！');
        }
    }


    // 后缀可区分环境
    public static function getSnSuffix()
    {
        static $suffixes = [
            'dev'  => 1,
            'test' => 2,
            'prod' => 0,
        ];

        $suffix = $suffixes[YII_ENV] ?? 9;

        return $suffix;
    }

    /**
     * 获得属性
     *
     * @param object $modelObj 对象
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @return string 属性值
     * @author zouyan(305463219@qq.com)
     */
    public static function getAttr(&$modelObj, $attrName, $isStatic = 0){
        if ( !property_exists($modelObj, $attrName)) {
            throws("未定义[" . $attrName  . "] 属性");
        }
        // 静态
        if($isStatic == 1) return $modelObj::${$attrName};
        return $modelObj->{$attrName};
    }

    /**
     * 调用模型方法
     *  模型中方法定义:注意参数尽可能给默认值
        public function aaa($aa = [], $bb = []){
            echo $this->getTable() . '<BR/>';
            print_r($aa);
            echo  '<BR/>';
            print_r($bb);
            echo  '<BR/>';
            echo 'aaaaafunction';
        }
     * @param object $modelObj 对象
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @return mixed 返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function exeMethod(&$modelObj, $methodName, $params = []){
        if(!method_exists($modelObj,$methodName)){
            throws("未定义[" . $methodName  . "] 方法");
        }
        $params = array_values($params);
        return $modelObj->{$methodName}(...$params);
    }

    /**
     * 根据路由名称及参数获得生成的url
     *  $urlType 为2和4的控制器方法，必须要使用中间件  signed
     *                                    ->name('event.rsvp')->middleware('signed');
     *                                    或 控制器中指定
     *                                    *
     *                                   * 实例化一个新的控制器实例.
     *                                   * @ return void
     *                                  public function __construct()
     *                                  {
     *                                       // $this->middleware('auth');
     *                                      $this->middleware('signed')->only('indexSigned', 'indexSignedExpires');
     *                                      // $this->middleware('subscribed')->except('store');
     *                                  }
     * @param int $urlType 获得的url类型 1  普通url; 2 URL 签名; 4 URL 签名+ 临时 URLs
     * @param string  $routeName url 路由名称  'event.rsvp'  web.php中的 ->name('event.rsvp');
     * @param array $params 参数
     * @param mixed $expires  增加过期时间  now()->addHour() -- $urlType 为4的参数 如果为空，则默认为 1小时
     * @return string 生成的url
     * @author zouyan(305463219@qq.com)
     */
    public static function getUrlByRouteName($urlType, $routeName = '', $params = [], $expires = ''){
        $url = '';
        switch ($urlType) {
            case 1:// 普通url
                // 如果有不怀好意的用户会很容易地修改 URL 中的任何变量，这并非我们想要的。
                // http://runbuy.admin.cunwo.net/admin/event/25/rsvp/100/yes
                // $url = Url::route('event.rsvp', ['id' => 25, 'user' => 100, 'response' => 'yes']);
                $url = Url::route($routeName, $params);
                // echo '普通的:<a href="' . $url . '" target="_black">' . $url . '</a><br/>';
                break;
            case 2:// 给 URL 签名
                // 给 URL 签名
                // http://runbuy.admin.cunwo.net/admin/event/signed/25/rsvp/100/yes?signature=5778df73bb443617df6634e89078544cf8ab7b53afdfe0390f35358beaaa2374
                // 在使用了签名过的 URL 情况下，如果一个 “好奇” 的用户试图篡改用户 ID，比方把 100 改成 101，或者把签名尾部的 4 改成 5，
                // Laravel 将会抛出 Illuminate\Routing\Exceptions\InvalidSignatureException 异常。
                // $url = Url::signedRoute('event.rsvp.signed', ['id' => 25, 'user' => 100, 'response' => 'yes']);
                $url = Url::signedRoute($routeName, $params);
                // echo 'URL 签名:<a href="' . $url . '" target="_black">' . $url . '</a><br/>';
                break;
            case 4:// URL 签名+ 临时 URLs
                // 临时 URLs
                // http://runbuy.admin.cunwo.net/admin/event/signedExpires/25/rsvp/100/yes?expires=1574480473&signature=2ce0aae155ac7411dff9d088488950172b70cbf604bbdbeb0c5dae6cddcb9706
                // 非常好的对签名增加过期时间的方法。如果我们想要让生成的链接在 1 小时后过期，可以如下更新我们的代码。
                // $url = Url::temporarySignedRoute('event.rsvp.signedExpires', now()->addHour(), ['id' => 25, 'user' => 100, 'response' => 'yes']);
                if(empty($expires)) $expires = now()->addHour();
                $url = Url::temporarySignedRoute($routeName, $expires, $params);
                // echo 'URL 签名+ 临时 URLs:<a href="' . $url . '" target="_black">' . $url . '</a><br/>';
                break;
        }
        return $url;

    }

    /**
     * 根据数据表记录[二维]，转换资源url为可以访问的地址
     *
     * @param array $reportsList 栏目记录数组 - 二维
     * @param int $type 多少维  1:一维[默认]；2 二维 --注意是资源的维度
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function resoursceUrl(&$reportsList, $type = 2){
        foreach($reportsList as $k=>$item){
            $reportsList[$k] = static::resourceUrl($item,$type);
        }
        return $reportsList;
    }

    /**
     * 根据数据表记录，转换资源url为可以访问的地址
     *
     * @param array $dataList 资源记录数组 - 二维 / 一维
     * @param int $type 多少维  1:一维[默认]；2 二维 --注意是资源的维度
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function resourceUrl(&$dataList,$type = 2){
        if($type == 2){
            if(isset($dataList['site_resources'])){
                $site_resources = $dataList['site_resources'] ?? [];
                foreach($site_resources as $k=>$site_resource){
                    $resource_url = url($site_resource['resource_url']);
                    $site_resources[$k]['resource_url'] = $resource_url;
                    $site_resources[$k] = array_merge($site_resources[$k], static::formatUrlByExtension($resource_url));
                }
                $dataList['site_resources'] = $site_resources;
            }
        }else{
            if(isset($dataList['resource_url'])){
                $resource_url = url($dataList['resource_url']);
                $dataList['resource_url'] = $resource_url;
                $dataList = array_merge($dataList, static::formatUrlByExtension($resource_url));
            }
        }
        return $dataList;
    }

    /**
     * 格式化资源数据
     *
     * @param array $dataList 资源记录数组 - 二维 / 一维
     * @param int $type 多少维  1:一维[默认]；2 二维 --注意是资源的维度
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function formatResource($data_list, $type = 2){
        $reList = [];
        if($type == 1) $data_list = [$data_list];
        foreach($data_list as $k => $v){
            $resource_url = url($v['resource_url']);
            $temArr = [
                'id' => $v['id'],
                'resource_name' => $v['resource_name'],
                'resource_url' => $resource_url,
                'created_at' => $v['created_at'],
            ];
            $temArr = array_merge($temArr, static::formatUrlByExtension($resource_url));
            array_push($reList, $temArr);
        }
        if($type == 1) $reList = $reList[0] ?? [];
        return $reList;
    }

    /**
     * 通过扩展名，返回文件前端访问的特殊格式
     *
     * @param string $resource_url 方件地址 如  http://qualitycontrol.admin.cunwo.net/resource/company/10/pdfword/2020/07/19/20200719225926e22f3d6997188bf6.docx
     * @return array  一维数组 ['resource_file_name' => '文件名', 'resource_file_extension' => '扩展名', 'resource_url_format' => '格式化后的地址']
     * @author zouyan(305463219@qq.com)
     */
    public static function formatUrlByExtension($resource_url){
        // 获得扩展名
        $url_file_name = basename($resource_url);// basename() 函数返回路径中的文件名部分。
        $url_file_extension = pathinfo($url_file_name,PATHINFO_EXTENSION);
        $resource_url_format = $resource_url;
        switch(strtolower($url_file_extension)){
            // PPT、Excel、Word 文件类型
            // 不需要使用任何第三家扩展，使用 Office 官方提供的 Office Web Viewer 即可.
            // https://view.officeapps.live.com/op/view.aspx?src={yourFileOnlinePath}
            case 'doc':// word
            case 'docx'://
            case 'xls':// excel
            case 'xlsx'://
            case 'ppt':// ppt
            case 'pptx'://
                $resource_url_format = 'https://view.officeapps.live.com/op/view.aspx?src=' . $resource_url;
                break;
//                    case 'aa'://
//                        break;
            default:
                break;
        }
        return [
            'resource_file_name' => $url_file_name,// 文件名
            'resource_file_extension' => strtolower($url_file_extension),// 扩展名
            'resource_url_format' => $resource_url_format// 格式化后的地址
        ];
    }

    /**
     * 根据数据表记录，删除本地文件
     *
     * @param object $modelObj 当前模型对象
     * @param array $resources 资源记录数组 - 二维 或一维 下标必须有 resource_url
     * @author zouyan(305463219@qq.com)
     */
    public static function resourceDelFile($resources = []){
        // 如果是一维数组，则转为二维数组
        $isMulti = static::isMultiArr($resources, true);
        foreach($resources as $resource){
            $resource_url = $resource['resource_url'] ?? '';
            if(empty($resource_url)){
                continue;
            }
            @unlink(public_path($resource_url));// 删除文件
        }
    }

    /**
     * 根据site_resources记录，转换小程序的图片列数组-二维
     *
     * @param array $site_resources 资源记录数组 - 二维
     * @return  array $upload_picture_list 小程序的图片列数组-二维
     * @author zouyan(305463219@qq.com)
     */
    public static function getFormatResource($site_resources){
        $upload_picture_list = [];
        // $site_resources = $infoData['site_resources'] ?? [];
        foreach($site_resources as $v){
            $upload_picture_list[] = [
                'upload_percent' => 100,
                'path' => $v['resource_url'] ?? '',
                'path_server' => $v['resource_url'] ?? '',
                'resource_id' => $v['id'] ?? 0,
            ];
        }
        //$infoData['upload_picture_list'] = $upload_picture_list;
        return $upload_picture_list;
    }

    /**
     * 格式化字符串--字符串每隔多少个字符加指定字符
     *
     * @param string $str 字符串
     * @param string $splitStr 指定字符--默认空隔
     * @param int  $len 每隔多少长度--默认4
     * @return  string 格式化后字符串
     * @author zouyan(305463219@qq.com)
     */
    public static function formatStrMiddle($str, $splitStr = ' ', $len = 4){
        return implode($splitStr, str_split($str, $len));
    }

    /**
     * 格式化后手机/电话号码
     *
     * @param string $str 需要格式化的字符
     *  $formatArr = [
     *       [
     *           'len' => 3,// 长度
     *           'splitStr' => '',// 分隔符
     *       ],
     *       ....
     *  ];
     * @return  string 格式化后手机/电话号码
     * @author zouyan(305463219@qq.com)
     */
    public static function formatStr($str, $formatArr = []) {
        $reStr = '';
        $strLen = strlen($str);
        foreach($formatArr as $v){
            $len = $v['len'] ?? 1;
            if($len < 1) $len = 1;
            $splitStr = $v['splitStr'] ?? ' ';
            if($splitStr == '') $splitStr = ' ';
            $reStr .= substr($str,0, $len);

            // 剩下的字符
            $str = substr($str,$len);
            $strLen = strlen($str);
            if($strLen > 0) $reStr .= $splitStr;
        }
        // 加上剩下的
        if($strLen > 0) $reStr .= $str;

        return $reStr;
        // $phone = preg_replace("/[^0-9]/", "", $phone);
        // $replacement = [];// 用于替换的字符串或字符串数组。
        // return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/","$1 $2 $3",$phone);
        /*
        if(strlen($phone) == 7)// 029-88214602  0831-6746036
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif(strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","($1) $2-$3",$phone);
        elseif(strlen($phone) == 11)
            return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/","$1 $2 $3",$phone);
        else
            return $phone;
        */
    }

    /**
     *  关键数据生成键 ,
     *
     * @param array $keyData 其内容项可以是字符、数字、数组
     * @param string $reType 返回的值类型 md5
     * @return  string md5值
     * @author zouyan(305463219@qq.com)
     */
    public static function getUniqueKey($keyData, $reType = 'md5') {
        $keyArr = [];
        foreach($keyData as $k => $v){
            array_push($keyArr, $k);
            if(is_numeric($v) || is_string($v)) array_push($keyArr, $v);
            if(is_array($v)) array_push($keyArr, json_encode($v));
        }
        $keyStr = implode('>!@#', $keyArr);
        if($reType == 'md5' ) return md5($keyStr);
        return $keyStr;
    }

    /**
     * 格式化数字保留多少位小数 如:1234.15  3向下取[正负:往小的数取];4 向上取[正负:往大的数取];
     *
     * @param int/float $num 整数或小数
     * @param int $decimalDigits 保留小数位数
     * @param int $type 类型 1 四舍五入;2不四舍五入;3向下取[正负:往小的数取];4 向上取[正负:往大的数取];
     * @param float $sign
     * @return string
     */
    public static function formatFloat($num, $decimalDigits = 2, $type = 2){
        // 判断是否有小数点
        $decNum = 0;// 小数点位数
        if(strpos($num, '.') !== false){ // 没有小数点
            $decNum = strlen($num) - (strpos($num, '.') + 1);// 小数点位数
        }
        switch ($type)
        {
            case 1:// // 保留两位小数并且四舍五入
                // $num = 123213.666666;
                // sprintf("%.2f", $num);
                return sprintf("%." . $decimalDigits . "f", $num);
                break;
            case 2:// 保留两位小数并且不四舍五入
            case 3:// 向下取
                // $num = 123213.666666;
                // echo sprintf("%.2f",substr(sprintf("%.3f", $num), 0, -1));
                if( $decimalDigits <  $decNum){
                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num *= 10;
                    }
                    $numArr = explode('.', $num);
                    if(count($numArr) == 2){
                        $intNum = $numArr[0] ?? 0;
                        $digitNum = $numArr[1] ?? 0;

                        // 向下取整
                        // $num = floor($num);
                        $num = $intNum;
                    }

                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num /= 10;
                    }
                }
                return sprintf("%." . $decimalDigits . "f", $num);
                // return sprintf("%." . $decimalDigits . "f",substr(sprintf("%." . ($decimalDigits + 1) . "f", $num), 0, -1));
                break;
            case 4:// 4 向上取
                if( $decimalDigits <  $decNum){
                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num *= 10;
                    }
                    $numArr = explode('.', $num);
                    if(count($numArr) == 2){
                        $intNum = $numArr[0] ?? 0;
                        $digitNum = $numArr[1] ?? 0;

                        // 向上取整
                        // $num = ceil($num);
                        $num = $intNum;
                        if( $digitNum > 0 ) $num += 1;
                    }

                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num /= 10;
                    }
                }
                return sprintf("%." . $decimalDigits . "f", $num);
            default:
        }
        return $num;
    }


    /**
     * 格式化数字保留多少位小数 如:1234.15  3向下取[正:往小的数取;负:往大的负数取];4 向上取[正:往大的数取;负:往小的数负取];
     *
     * @param int/float $num 整数或小数
     * @param int $decimalDigits 保留小数位数
     * @param int $type 类型 1 四舍五入;2不四舍五入;3向下取[正:往小的数取;负:往大的负数取];4 向上取[正:往大的数取;负:往小的数负取];
     * @param float $sign
     * @return string
     */
    public static function formatFloatVal($num, $decimalDigits = 2, $type = 2){
        // 判断是否有小数点
        $decNum = 0;// 小数点位数
        if(strpos($num, '.') !== false){ // 没有小数点
            $decNum = strlen($num) - (strpos($num, '.') + 1);// 小数点位数
        }
        switch ($type)
        {
            case 1:// // 保留两位小数并且四舍五入
                return static::formatFloat($num, $decimalDigits, 1);
                break;
            case 2:// 保留两位小数并且不四舍五入
                return static::formatFloat($num, $decimalDigits, 2);
                break;
            case 3:// 向下取
                // $num = 123213.666666;
                // echo sprintf("%.2f",substr(sprintf("%.3f", $num), 0, -1));
                if( $decimalDigits <  $decNum){
                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num *= 10;
                    }

                    // 向下取整
                    $num = floor($num);

                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num /= 10;
                    }
                }
                return sprintf("%." . $decimalDigits . "f", $num);
                break;
            case 4:// 4 向上取
                if( $decimalDigits <  $decNum){
                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num *= 10;
                    }
                    // 向上取整
                    $num = ceil($num);
                    for($i = 1; $i <= $decimalDigits; $i++){
                        $num /= 10;
                    }
                }
                return sprintf("%." . $decimalDigits . "f", $num);
            default:
        }
        return $num;
    }

    /**
     * 格式化金额-仅显示用 如:￥1,234.15
     *
     * @param int $money
     * @param int $len
     * @param string $sign
     * @return string
     */
    public static function formatMoney($money, $len=2, $sign='￥'){
        $negative = $money >= 0 ? '' : '-';
        $int_money = intval(abs($money));
        $len = intval(abs($len));
        $decimal = '';//小数
        if ($len > 0) {
            $decimal = '.'.substr(sprintf('%01.'.$len.'f', $money),-$len);
        }
        $tmp_money = strrev($int_money);
        $strlen = strlen($tmp_money);
        $format_money = '';
        for ($i = 3; $i < $strlen; $i += 3) {
            $format_money .= substr($tmp_money,0,3).',';
            $tmp_money = substr($tmp_money,3);
        }
        $format_money .= $tmp_money;
        $format_money = strrev($format_money);
        return $sign.$negative.$format_money.$decimal;
    }

    /**
     *
     * 时间比较
     * @details
     * @param $beginTime 开始时间 05:00:00
     * @param $endTime 结束时间 15:00:00
     * @return boolean  true:结束时间 >= 开始时间 或 false:结束时间 < 开始时间
     *
     */
    public static function timeDomparison($beginTime, $endTime){
        $beginDate = date('Y-m-d') . ' ' . $beginTime;
        $endDate = date('Y-m-d') . ' ' . $endTime;
        $diffNum = Tool::diffDate($beginDate, $endDate, 1, '时间', 2);
        return $diffNum >= 0 ? true : false;
    }

    // 参数如下方法 timesJudge
    public static function timesJudgeDo($timeList, $judgeRangeTime = '', $judgeType = 0, $errDo = 1, $beginTimeKey = 'begin_time', $endTimeKey = 'end_time', $beginTimeName = '开始时间', $endTimeName = '结束时间', $judgeRangeTimeName = '', $level = 1){
        // 先执行条件数据验证
        $temJudgeType = $judgeType & (1 + 2 + 4);
        if($temJudgeType > 0){
            $result = Tool::timesJudge($timeList, $judgeRangeTime, $temJudgeType, $errDo, $beginTimeKey, $endTimeKey, $beginTimeName, $endTimeName, $judgeRangeTimeName, $level);
            if (is_string($result)) {
                return $result;
            }
        }
        // 再执行时间段数据验证
        $temJudgeType = $judgeType & (8 + 16 + 32 + 64 + 128 + 256);
        if($temJudgeType > 0){
            $result = Tool::timesJudge($timeList, $judgeRangeTime, $temJudgeType, $errDo, $beginTimeKey, $endTimeKey, $beginTimeName, $endTimeName, $judgeRangeTimeName, $level);
            if (is_string($result)) {
                return $result;
            }
        }
        return true;
    }

    /**
     *
     * 多时间段验证 ;具体使用，请用方法 timesJudgeDo
     * @details
     * @param array $timeList 需要验证的时间列表 一维或二维数组 ['begin_time' => '05:00:00', 'end_time'=> '15:00:00']
     * @param string $judgeRangeTime 需要验证范围的时间, 为空：则不做范围验证
     * @param int $judgeType 判断类型  [满足就是错误]
     *                           1 开始时间 < 结束时间 ; 2 开始时间 = 结束时间 ; 4开始时间 > 结束时间
     *
     *                           8 开始时间不能在其它的范围内[不可含任一端] -----需要验证范围的时间
     *                           16 开始时间不能在其它的范围内[不可含左端]  -----需要验证范围的时间
     *                           32 开始时间不能在其它的范围内[不可含右端] -----需要验证范围的时间
     *
     *                          64 结束时间不能在其它的范围内[不可含任一端]  -----需要验证范围的时间
     *                          128 结束时间不能在其它的范围内[不可含左端]  -----需要验证范围的时间
     *                          256 结束时间不能在其它的范围内[不可含右端]   -----需要验证范围的时间
     * @param int $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $beginTimeKey 开始时间下标
     * @param string $endTimeKey 结束时间下标
     * @param string $beginTimeName 开始时间名称
     * @param string $endTimeName 结束时间名称
     * @param string $judgeRangeTimeName 需要验证范围的时间名称
     * @param string $level 层数 1 :初始调用 2 :第二次调用;最多2层 ；主要作用是不要递卡尔集递归
     * @return mixed  true:成功; string:具体错误
     *
     */
    public static function timesJudge($timeList, $judgeRangeTime = '', $judgeType = 0, $errDo = 1, $beginTimeKey = 'begin_time', $endTimeKey = 'end_time', $beginTimeName = '开始时间', $endTimeName = '结束时间', $judgeRangeTimeName = '', $level = 1){
        if($level > 2) return true;
        // 如果是一维，则变为二维
        if(isset($timeList[$beginTimeKey]) && isset($timeList[$endTimeKey]))  $timeList = [$timeList];
        $timeList = array_values($timeList);
        foreach($timeList as $k => $v){
            $beginTime = $v[$beginTimeKey] ?? '';
            $endTime = $v[$endTimeKey] ?? '';
            // 判断
            if(($judgeType & (1 + 2 + 4 ) ) > 0) {
                $result = compare_time($beginTime, $endTime, $beginTimeName, $endTimeName, $errDo);
                if (is_string($result) && !is_numeric($result)) return $result;// 有错误
                // 1 开始时间 < 结束时间
                if (($judgeType & 1) == 1 && $result > 0) {
                    $errMsg = $beginTimeName . "[" . $beginTime . "]不能小于" . $endTimeName . "[" . $endTime . "]";
                    if ($errDo == 1) throws($errMsg);
                    return $errMsg;
                }
                // 2 开始时间 = 结束时间
                if (($judgeType & 2) == 2 && $result == 0) {
                    $errMsg = $beginTimeName . "[" . $beginTime . "]不能等于" . $endTimeName . "[" . $endTime . "]";
                    if ($errDo == 1) throws($errMsg);
                    return $errMsg;
                }
                // 4开始时间 > 结束时间
                if (($judgeType & 4) == 4 && $result < 0) {
                    $errMsg = $beginTimeName . "[" . $beginTime . "]不能大于" . $endTimeName . "[" . $endTime . "]";
                    if ($errDo == 1) throws($errMsg);
                    return $errMsg;
                }
            }

            // 8 开始时间不能在其它的范围内 -----需要验证范围的时间
            // 16 结束时间不能在其它的范围内 -----需要验证范围的时间
            if( ($judgeType & (8 + 16 + 32 + 64 + 128 + 256) ) > 0  && !empty($judgeRangeTime)  ){
                // 开始时间-判断时间
                $beginRangeDiff = compare_time($judgeRangeTime, $beginTime, $judgeRangeTimeName, $beginTimeName, $errDo);
                if(is_string($beginRangeDiff) && !is_numeric($beginRangeDiff)) return $beginRangeDiff;// 有错误
                // 结束时间-判断时间
                $endRangeDiff = compare_time($judgeRangeTime, $endTime, $judgeRangeTimeName, $endTimeName, $errDo);
                if(is_string($endRangeDiff) && !is_numeric($endRangeDiff)) return $endRangeDiff;// 有错误
                if( ( ($judgeType & (8 + 64) ) > 0 &&  $beginRangeDiff <= 0 && $endRangeDiff >= 0 )
                    ||  ( ($judgeType & (16 + 128) ) > 0 &&  $beginRangeDiff <= 0 && $endRangeDiff > 0 )
                    ||  ( ($judgeType & (32 + 256) ) > 0 &&  $beginRangeDiff < 0 && $endRangeDiff >= 0 )
                ){
                    $errMsg = $judgeRangeTimeName . "[" . $judgeRangeTime . "]不能在时间范围[" . $beginTime . " - " . $endTime . "]";
                    if($errDo == 1) throws($errMsg);
                    return $errMsg;
                }
            }
            // if(empty($judgeRangeTime)) continue;
            if(($judgeType & (8 + 16 + 32 + 64 + 128 + 256) ) <= 0 ) continue;
            if($level >= 2) continue;
            $temOpenTimeList = $timeList;
            for($n = 0; $n <= $k; $n++ ){
                unset($temOpenTimeList[$n]);
            }
            if(empty($temOpenTimeList)) continue;
            // 比较开始时间是否在时间范围
            if(($judgeType & (8 + 16 + 32 ) ) > 0){
                $rangeBegin = Tool::timesJudge($temOpenTimeList, $beginTime, ($judgeType & (8 + 16 + 32 ) ) , $errDo
                    , $beginTimeKey, $endTimeKey, $beginTimeName, $endTimeName, $beginTimeName, 2);
                if(is_string($rangeBegin)){
                    return $rangeBegin;
                }
            }
            // 比较结束时间是否在时间范围
            if(($judgeType & (64 + 128 + 256 ) ) > 0) {
                $rangeEnd = Tool::timesJudge($temOpenTimeList, $endTime, ($judgeType & (64 + 128 + 256)), $errDo
                    , $beginTimeKey, $endTimeKey, $beginTimeName, $endTimeName, $endTimeName, 2);
                if (is_string($rangeEnd)) {
                    return $rangeEnd;
                }
            }
        }
        return true;
    }

    /**
     * 将字符串转换成二进制
     * @param type $str
     * @return type
     */
    public static function StrToBin($str){
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        foreach($arr as &$v){
            $temp = unpack('H*', $v);
            $v = base_convert($temp[1], 16, 2);
            unset($temp);
        }

        return join(' ',$arr);
    }

    /**
     * 将二进制转换成字符串
     * @param type $str
     * @return type
     */
    public static function BinToStr($str){
        $arr = explode(' ', $str);
        foreach($arr as &$v){
            $v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
        }
        return join('', $arr);
    }

    /**
     * 生成随机数--都小于0，则不生成随机数 [] 想要一个介于 10 和 100 之间（包括 10 和 100）的随机整数，请使用 mt_rand (10,100)。
     * @param int $mix  生成随机数的最小数 , 都小于0，则不生成随机数
     * @param int $max  生成随机数的最大数
     * @return string
     */
    public static function getRandNum($mix = 0, $max = 10000){
        $randNum = '';
        if(is_numeric($mix) &&  is_numeric($max) && $mix >= 0 && $max >=0 && $max >= $mix){
            $randNum = mt_rand($mix, $max);
        }
        return $randNum;
    }

    /**
     * 生成md5随机数（防重放）
     * @param string $nonce 重放字符，为空则为 time()
     * @param int $mix  生成随机数的最小数 , 都小于0，则不生成随机数
     * @param int $max  生成随机数的最大数
     * @param array 需要执行的加密操作,下标顺序代表执行顺序
     *   $secureTypeArr = [
     *        'md5' => [],// md5加密
     *        'sha1' => [],// sha1加密
     *       'hmac-md5' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha1' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha256' => ['key' => '', 'raw_output' => false],// sha1加密
     *   ];
     *   // 都可能会有的参数,下标顺序代表执行顺序
     *   [
     *        'operates' => ['base64','strtoupper','strtolower','urlencode', 'urldecode'],
     *   ];
     * @return string
     */
    public static function createNonce($nonce = '', $mix = 0, $max = 10000, $secureTypeArr = false)
    {
        if(!is_string($nonce) || strlen($nonce) <= 0)  $nonce = time();

        // 后面加随机数
        $nonce .= static::getRandNum($mix, $max);

        $nonce = static::secureOperate($nonce, $secureTypeArr);
        return $nonce;
    }

    /**
     * 数据加密
     * @param string $str 需要加密的数据
     * @param array 需要执行的加密操作,下标顺序代表执行顺序
     *   $secureTypeArr = [
     *        'md5' => [],// md5加密
     *        'sha1' => [],// sha1加密
     *       'hmac-md5' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha1' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha256' => ['key' => '', 'raw_output' => false],// sha1加密
     *   ];
     *   // 都可能会有的参数,下标顺序代表执行顺序
     *   [
     *        'operates' => ['base64','strtoupper','strtolower','urlencode', 'urldecode'],
     *   ];
     * @return string
     */
    public static function secureOperate($str, $secureTypeArr = []){
        // 签名类型1 md5 ; 2 sha1 ; 3 hash_hmac
        foreach($secureTypeArr as $k => $info){
            $operates = $info['operates'] ?? [];
            if(!is_array($operates)) $operates = [];

            $key = $info['key'] ?? '';
            $raw_output = $info['raw_output'] ?? false;
            if(!is_bool($raw_output)) $raw_output = false;
            switch ($k)
            {
                case 'md5':// 1 md5 ;
                    $str = md5($str);
                    break;
                case 'sha1'://  2 sha1 ;
                    $str = sha1($str);
                    break;
                case 'hmac-md5':// 3 hash_hmac-- md5
//                    $str = hash_hmac("md5", $str, $appsecret, true);// 原始二进制数据
                    $str = hash_hmac("md5", $str, $key, $raw_output);// 原始二进制数据
                    break;
                case 'hmac-sha1':// 4 hash_hmac-- sha1
//                    $str = hash_hmac("sha1", $str, $appsecret, true);// 原始二进制数据
                    $str = hash_hmac("sha1", $str, $key, $raw_output);// 原始二进制数据
                    break;
                case 'hmac-sha256':// 5 hash_hmac-- sha256
//                    $str = hash_hmac("sha256", $str, $appsecret, true);// 原始二进制数据
                    $str = hash_hmac("sha256", $str, $key, $raw_output);// 原始二进制数据
                    break;
                default:
//                    $str = md5($str);
                    break;
            }
            // 数据格式处理
            $str = static::secureStrOperate($str, $operates);
        }
        return $str;
    }

    /**
     * 数据加密
     * @param string $str 需要加密的数据
     * @param array 都可能会有的参数,下标顺序代表执行顺序
     *   $operates =
     *   [
     *        'operates' => ['base64','strtoupper','strtolower','urlencode', 'urldecode'],
     *   ];
     * @return string
     */
    public static function secureStrOperate($str, $operates = []){

        // ['base64','strtoupper','strtolower','urlencode']
        foreach($operates as $op_v){
            switch ($op_v)
            {
                case 'base64':
                    $str = base64_encode($str);
                    break;
                case 'strtoupper':
                    $str = strtoupper($str);
                    break;
                case 'strtolower':
                    $str = strtolower($str);
                    break;
                case 'urlencode':
                    $str = urlencode($str);
                    break;
                case 'urldecode':
                    $str = urldecode($str);
                    break;
                default:
//                    $str = md5($str);
                    break;
            }
        }
        return $str;
    }

    /**
     * @desc redis保存数据时，对数据进行序列化或格式化操作
     * @param array $array 一/二维数组
     * @param int $doType 操作类型 1 序列化 ;2返序列化或返解析
     * @param int  $operate  操作类型 操作 1 转为json 2 序列化 3 不转换
     * @return  mixed 处理好的的数据 一/二维数组
     */
    public static function dataFormatBath(&$array, $doType = 1, $operate = 3){
        if (! is_array($array) || empty($array)) return $array;
        // 如果是一维数组,则转为二维数组
        $isMulti = static::isMultiArr($array, true);
        foreach ($array as $key => $v) {
            switch($doType){
                case 1:
                    $array[$key] = static::dataFormat($v, $operate);
                    break;
                case 2:
                    $array[$key] = static::dataResolv($v, $operate);
                    break;
                default:
                    break;
            }
        };
        if(!$isMulti) $array = $array[0] ?? [];
        return $array;
    }

    /**
     * @desc redis保存数据时，对数据进行序列化或格式化操作
     * @param mixed $value  需要处理的数据
     * @param int  $operate  操作类型 操作 1 转为json 2 序列化 3 不转换
     * @return  string 处理好的的数据
     */
    public static function dataFormat(&$value, $operate){
        switch($operate){
            case 1:
                if(is_array($value)){
                    $value = json_encode($value);
                }
                break;
            case 2:
                $value = serialize($value);
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * @desc 解析数据 返回redis保存数据，对数据进行反序列化或解析格式化操作
     * @param mixed $value  需要处理的数据
     * @param int  $operate  操作类型 操作 1 转为json 2 序列化 3 不转换
     * @return  string 处理好的的数据
     */
    public static function dataResolv(&$value, $operate){
        switch($operate){
            case 1:
                if (!static::isNotJson($value)) {
                    $value = json_decode($value, true);
                }
                break;
            case 2:
                $value = unserialize($value);
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * 一分钟限制请求100次 或 其它需要限次数的键 --注意[键的唯一性问题]只能针对单个用户限,（根据ip等限不现实,只能对具体某一用户来限）
     * @param string $key 键---单个用户的标识 token 或 其它需要限次数的键
     * @param int $limitSecond 多少秒请求的限制 -单位：秒
     * @param int $maxLimit 最多请求次数 -1:不判断是否次数超限[只自增]
     * @param int $defaultNum 缓存不存在时，第一次赋值的默认值
     * @param string $errLimitStr 超过限止次数的错误提示文字
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed 具体数字:自增后的结果;;  sting 具体错误 ； throws 错误
     */
    public static function limitIncr($key = '', $limitSecond = 60, $maxLimit = 100, $defaultNum = 1, $errLimitStr = '次数超限!', $errDo = 1){

        $maxNum = RedisString::numIncr($key, $limitSecond, $defaultNum, $errDo);
        if(!is_numeric($maxNum) && strlen($maxNum) > 0){
            $errMsg = $maxNum;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        //  -1:不判断是否次数超限[只自增]
        if($maxLimit === -1) return $maxNum;
        if($maxNum > $maxLimit){
            $errMsg = $errLimitStr;// '次数超限!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
            // return false;
        }
        return $maxNum;
    }

    /**
     * //方法二，序列化反序列化实现对象深拷贝
     * 对象复制
     * https://www.cnblogs.com/zuikeol/p/10491074.html
     * @param object $obj 要复制的对象
     * @return object 新的对象
     * @author zouyan(305463219@qq.com)
     */
    public static function copyObject($obj){
        // $newObj = serialize($obj);
        return  unserialize(serialize($obj));
    }

    /**
     *
     * 笛卡尔积
     * @param array $dataArr 二维数组--每一维都不能为空，为空，则返回的也是空。
     * @return array 笛卡尔积数组
     * @author zouyan(305463219@qq.com)
     */
    public static function descartes($dataArr){
        if(!is_array($dataArr) || empty($dataArr)) return [];
        if(count($dataArr) == 1){
            $reArr = [];
            foreach($dataArr as $v){
                if(!is_array($v)) continue;
                foreach($v as $t_v){
                    $reArr[] = [$t_v];
                }
            }
            return $reArr;
        }

        // 笛卡尔积
        return Descartes($dataArr);// [ [字段值]或['字段1值','字段2值'],....]
    }

    /**
     * 获得秒、毫秒、微秒 注意：32位系统不可用，只能在64位系统用
     * 1秒(second) = 1000毫秒(millisecond) = 1000,000微秒(microsecond)
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function msecTime() {
        $microtime = microtime();
        list($msec, $sec) = explode(' ', $microtime);
        // $msectime = (float)sprintf('%.0f', ((float)($msec) + (float)($sec)) * 1000);
        $msectime = (int)(((float)$msec + (float)$sec) * 1000);//
        // $microsecond = (float)sprintf('%.0f', ((float)($msec) + (float)($sec)) * 1000000);
        $microsecond = (int)(((float)$msec + (float)$sec) * 1000000);//

        $msecint = $msec;
        $pointNum = strpos($msecint, '.');
        if ( $pointNum !== false){
            $len = strlen($msec) - ($pointNum + 1);
            for($i = 1; $i <=  $len; $i++){
                $msecint *= 10;
            }
        }

        return [
            'microtime' => $microtime,// 0.34090600 1577676341
            'sec' => $sec,// 秒 1577676341
            'msec' => $msec,// 微秒 0.34090600
            'msecint' => $msecint,// 微秒的整数形式 34090600
            'msectime' => $msectime,// 毫秒 1577676341340
            'microsecond' => $microsecond,// 微秒 1577676341340906
        ];
    }

    /**
     *
     * 代码执行时间--微秒数
     * 注意：32位系统不可用，只能在64位系统用
     * 1秒(second) = 1000毫秒(millisecond) = 1000,000微秒(microsecond)
     * @param mixed $doFun 需要统计执行时间 的闭包函数  function(){}
     * @return float 代码执行的时间--微秒数 0.030647993087769
     * @author zouyan(305463219@qq.com)
     */
    public static function getDoTime($doFun){
        $startTime = microtime(true);// 当前 Unix 时间戳的微秒数

        if(is_callable($doFun)){
            $doFun();
        }
        $endTime = microtime(true);// 当前 Unix 时间戳的微秒数
        $doTime = $endTime - $startTime ;
        return $doTime;
    }

    /**
     *
     * 时间后面加上 微秒的整数形式 的字符串
     * @param string $dateTime 时间字符串 Y-m-d H:i:s  2019-12-30 10:29:57
     * @param int $msecint 微秒的整数
     * @param string $splitText 分隔符---不能是'-' ' '
     * @return string 时间后面加上 微秒的整数 16855800
     * @author zouyan(305463219@qq.com)
     */
    public static function timeJoinMsec($dateTime, $msecint = 0, $splitText = '!!!'){
        if(!is_numeric($msecint) || $msecint <= 0) $msecint = (Tool::msecTime())['msecint'];
        return $dateTime . $splitText . $msecint;
    }

    /**
     *
     * 获得时间和微秒的整数形式
     * @param string $dateTimeStr 时间 微秒的整数 字符串 Y-m-d H:i:s  2019-12-30 10:29:57!!!16855800
     * @param string $splitText 分隔符---不能是'-' ' '
     * @return array 时间后面加上 微秒的整数
     *  [
     *      'dateTime' => $dataArr[0] ?? '',// 时间 字符串 Y-m-d H:i:s  2019-12-30 10:29:57
     *      'msecint' => $dataArr[1] ?? 0,// 微秒的整数 16855800
     *  ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getTimeMsec($dateTimeStr = '', $splitText = '!!!'){
        $dataArr = explode($splitText, $dateTimeStr);
        return [
            'dateTime' => $dataArr[0] ?? '',// 时间 字符串 Y-m-d H:i:s  2019-12-30 10:29:57
            'msecint' => $dataArr[1] ?? 0,// 微秒的整数 16855800
        ];
    }

    /**
     *
     * 获得项目标识前紭
     * @param int $keyNum 标识前紭编号 1 项目(整个资源集合)唯一标识；2 站点唯一标识,为空则为当前域及端口；
     *                                  4 环境标识 本地开发环境:local; 测试环境:testing;生产环境:production
     *                                  8  数据库ip ;  16 数据库端口  ; 32 数据库名
     *                                  64 数据库关键字;--如果为空，则默认为 数据库ip + 数据库端口 + 数据库名
     * @param string $itemSplit 数组转让字符串时的分隔符 默认 ':'
     * @param string $appendStr 字符不为空，则未尾加的字符 默认 ':'
     * @return string 项目标识前紭 字符
     * @author zouyan(305463219@qq.com)
     */
    public static function getProjectKey($keyNum = 0, $itemSplit = ':', $appendStr = ':'){
        $keyStr = implode($itemSplit, static::getProjectKeyArr($keyNum));
        if(strlen($keyStr) > 0 && strlen($appendStr) > 0) $keyStr .= $appendStr;
        return $keyStr;
    }

    /**
     *
     * 获得项目标识前紭
     * @param int $keyNum 标识前紭编号 1 项目(整个资源集合)唯一标识；2 站点唯一标识,为空则为当前域及端口；
     *                                  4 环境标识 本地开发环境:local; 测试环境:testing;生产环境:production
     *                                  8  数据库ip ;  16 数据库端口  ; 32 数据库名
     *                                  64 数据库关键字;--如果为空，则默认为 数据库ip + 数据库端口 + 数据库名
     * @return array 项目标识前紭数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getProjectKeyArr($keyNum = 0){
        $keyArr = [];
        // 1 项目(整个资源集合)唯一标识；
        $appProject = '';
        if(($keyNum & 1) == 1){
            $appProject = config('public.appProject', '');
            if(strlen($appProject) > 0) $keyArr[] = $appProject;
        }
        // 2 站点唯一标识,为空则为当前域及端口；
        $appWebSite = '';
        if(($keyNum & 2) == 2){
            $appWebSite = config('public.appWebSite', '');
            if(strlen($appWebSite) <= 0) {
                // $server = \Illuminate\Support\Facades\Request::server();
                $serviceNmae = \Illuminate\Support\Facades\Request::server('SERVER_NAME');// runbuy.admin.cunwo.net
                $servicePort = \Illuminate\Support\Facades\Request::server('SERVER_PORT');// 80
                // dd($servicePort);
                $appWebSite = str_replace(".","-",$serviceNmae . $servicePort);// runbuy-admin-cunwo-net80
            }
            if(strlen($appWebSite) > 0) $keyArr[] = $appWebSite;
        }
        // 4 环境标识 本地开发环境:local; 测试环境:testing;生产环境:production
        $appEnv = '';
        if(($keyNum & 4) == 4){
            $appEnv = config('public.appEnv', '');
            if(strlen($appEnv) > 0) $keyArr[] = $appEnv;
        }
        // 8  数据库ip
        $dbHost = '';
        if(($keyNum & 8) == 8 || ($keyNum & 64) == 64 ){
            $dbHost = config('public.dbHost', '');
            $dbHost = str_replace(".","-",$dbHost);
            if(($keyNum & 8) == 8 && strlen($dbHost) > 0) $keyArr[] = $dbHost;
        }
        // 16 数据库端口
        $dbPort = '';
        if(($keyNum & 16) == 16 || ($keyNum & 64) == 64 ){
            $dbPort = config('public.dbPort', '');
            if(($keyNum & 16) == 16 && strlen($dbPort) > 0) $keyArr[] = $dbPort;
        }
        // 32 数据库名
        $dbDatabase = '';
        if(($keyNum & 32) == 32 || ($keyNum & 64) == 64 ){
            $dbDatabase = config('public.dbDatabase', '');
            $dbDatabase = str_replace("_", "-", $dbDatabase);
            if(($keyNum & 32) == 32 && strlen($dbDatabase) > 0) $keyArr[] = $dbDatabase;
        }
        // 64 数据库关键字;--如果为空，则默认为 数据库ip + 数据库端口 + 数据库名
        $dbKey = '';
        if(($keyNum & 64) == 64){
            $dbKey = config('public.dbKey', '');
            if(strlen($dbKey) <= 0) $dbKey = $dbHost . $dbPort . $dbDatabase;
            if(strlen($dbKey) > 0) $keyArr[] = $dbKey;
        }
        return $keyArr;
    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param array $queryParams 已有的查询条件数组
     * @param string/array $paramVals 参数的值 数组-一维或字符
     * @param string $fieldName 查询的字段名--表中的
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param boolean $hasInIsMerge 如果In条件有值时  true:合并；false:用新值--覆盖 --默认
     * @return  boolean true:有拼查询  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function appendParamQuery(&$queryParams, &$paramVals = '', $fieldName = '', $excludeVals = [0, '0', ''], $valsSeparator = ',', $hasInIsMerge = false){
        // 空字符不处理
        if(is_string($paramVals) && strlen($paramVals) <= 0) return false;
        // 不是字符也不是数组不处理
        if(!is_string($paramVals) && !is_numeric($paramVals) && !is_array($paramVals)) return false;
        // 字符串，则转为数组 ；并去重
        if(!is_array($paramVals))  $paramVals = array_unique(explode($valsSeparator, $paramVals));
        // 每个元素去前后空
        $paramVals = array_map("trim",$paramVals);
        // 取差
        $paramVals = array_values(array_diff($paramVals, $excludeVals));
        // 空数组不处理
        $valNums = count($paramVals);
        if ($valNums <= 0 ) return false;

        if($valNums == 1){// if (strpos($paramVals, ',') === false) { // 单条
            // array_push($queryParams['where'], ['id', $paramVals]);
            // 不存在where条件，则先创建一个空的where条件，好push内容到数组
            if(!isset($queryParams['where'])) $queryParams['where'] = [];
            array_push($queryParams['where'], [$fieldName, $paramVals[0]]);
        } else {
            // $queryParams['whereIn']['id'] = explode(',', $paramVals);
            // 合并
            if($hasInIsMerge && isset($queryParams['whereIn'][$fieldName]) && !empty($queryParams['whereIn'][$fieldName])){
                $paramVals = array_values(array_merge($queryParams['whereIn'][$fieldName], $paramVals));
            }
            $queryParams['whereIn'][$fieldName] = $paramVals;
        }
        return true;
    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param array $queryParams 已有的查询条件数组
     * @param array $paramConfigs 可能的参数配置  -- 二维数组
     * @return  boolean true:  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function appendParamQueryByArr(&$queryParams, &$paramConfigs = []){

//        $paramConfigs = [
//            [
//                // 必有下标
//            'paramVals' => '1', // 参数的值 数组-一维或字符 -- 必填
//           'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                // 可有下标
//           'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//           'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//           'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//           ]
//       ];

        if(empty($paramConfigs)) return false;
        foreach($paramConfigs as $k => $paramConfig){
            $paramVals = $paramConfig['paramVals'] ?? '';// 可能为空值或0
            $fieldName = $paramConfig['fieldName'] ?? '';
            if(strlen($paramVals) <= 0 || empty($fieldName)) continue;

            $excludeVals = $paramConfig['excludeVals'] ?? [0, '0', ''];
            $valsSeparator = $paramConfig['valsSeparator'] ?? ',';
            $hasInIsMerge = $paramConfig['hasInIsMerge'] ?? false;
            static::appendParamQuery($queryParams, $paramVals, $fieldName, $excludeVals, $valsSeparator, $hasInIsMerge);
            $paramConfigs[$k]['paramVals'] = $paramVals;
        }
        return true;
    }

    /**
     * 判断当前要操作的数据，是当前登录用户可以操作的
     * @param int $own_user_type 当前用户类型1平台2企业4个人
     * @param int $own_company_id 当前操作者的  所属平台 id -- -1: 忽略【不判断】
     * @param int $company_id 要判断的 记录 所属平台 id -- 0: 【不判断】
     * @param int $own_organize_id  当前操作者的   组织id--所属企业 -- -1: 忽略【不判断】
     * @param int $organize_id  要判断的 记录  组织id--所属企业 -- 0: 【不判断】
     * @param int $own_personal_id 当前操作者的 个人id--最底层登录人员id -- -1: 忽略【不判断】
     * @param int $personal_id 要判断的 个人id--最底层登录人员id -- 0: 【不判断】
     * @return boolean 是否有权限  true:有权限  ； false:无权限
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeOwnOperateAuth($own_user_type = 0, $own_company_id = 0, $company_id = 0, $own_organize_id = 0, $organize_id = 0, $own_personal_id = 0, $personal_id = 0){
        // 传入值就判断
        if($own_company_id != -1 && strlen($company_id) > 0 && $company_id != 0  && $own_company_id != $company_id) return false;
        if($own_organize_id != -1 && strlen($organize_id) > 0 && $organize_id != 0  && $own_organize_id != $organize_id) return false;
        if($own_personal_id != -1 && strlen($personal_id) > 0 && $personal_id != 0  && $own_personal_id != $personal_id) return false;
        return true;
    }

    /**
     * 批量判断当前要操作的数据，是当前登录用户可以操作的
     * @param array $data 需要判断的数组  一维或二维数组
     * @param array $powerFields 在数组值中，用来判断权限的字段名称指定
     *   $powerFields = [
     *       'company_id' => '',// 所属平台 id 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *      'organize_id' => '',// 组织id--所属企业 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *      'personal_id' => '',// 个人id--最底层登录人员id 在 数组中的下标，--为空，则不用数组中的字段来判断;--没有字段，则不用此下标或为空值
     *  ];
     * @param int $own_user_type 当前用户类型1平台2企业4个人
     * @param int $own_company_id 当前操作者的  所属平台 id -- -1: 忽略【不判断】
     * @param int $company_id 要判断的 记录 所属平台 id； -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @param int $own_organize_id  当前操作者的   组织id--所属企业 -- -1: 忽略【不判断】
     * @param int $organize_id  要判断的 记录  组织id--所属企业； -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @param int $own_personal_id 当前操作者的 个人id--最底层登录人员id -- -1: 忽略【不判断】
     * @param int $personal_id 要判断的 个人id--最底层登录人员id； -- 0: 【不判断】 --- 如果有 > 0 的值，则 使用这里的，不然使用 $powerFields
     * @return boolean 是否有权限  true:有权限  ； false:无权限
     * @author zouyan(305463219@qq.com)
     */
    public static function batchJudgeOwnOperateAuth($data = [], $powerFields = [], $own_user_type = 0, $own_company_id = 0, $company_id = 0, $own_organize_id = 0, $organize_id = 0, $own_personal_id = 0, $personal_id = 0){
        // 转为二维数组
        $isMulti = static::isMultiArr($data, true);
        // 循环判断
        foreach($data as $k => $v){
            $tem_company_id = $company_id;
            if( !(strlen($tem_company_id) > 0 && $tem_company_id != 0 ) ){
                $tem_field_name = (isset($powerFields['company_id']) && strlen($powerFields['company_id']) > 0 ) ? $powerFields['company_id'] : '';
                if(strlen($tem_field_name) > 0 && isset($v[$tem_field_name]) && strlen($v[$tem_field_name]) > 0 && $v[$tem_field_name] != 0){
                    $tem_company_id = $v[$tem_field_name];
                }
            }
            $tem_organize_id = $organize_id;
            if( !(strlen($tem_organize_id) > 0 && $tem_organize_id != 0 ) ){
                $tem_field_name = (isset($powerFields['organize_id']) && strlen($powerFields['organize_id']) > 0 ) ? $powerFields['organize_id'] : '';
                if(strlen($tem_field_name) > 0 && isset($v[$tem_field_name]) && strlen($v[$tem_field_name]) > 0 && $v[$tem_field_name] != 0){
                    $tem_organize_id = $v[$tem_field_name];
                }
            }
            $tem_personal_id = $personal_id;
            if(! (strlen($tem_personal_id) > 0 && $tem_personal_id != 0 ) ){
                $tem_field_name = (isset($powerFields['personal_id']) && strlen($powerFields['personal_id']) > 0 ) ? $powerFields['personal_id'] : '';
                if(strlen($tem_field_name) > 0 && isset($v[$tem_field_name]) && strlen($v[$tem_field_name]) > 0 && $v[$tem_field_name] != 0){
                    $tem_personal_id = $v[$tem_field_name];
                }
            }
            $infoPower = static::judgeOwnOperateAuth($own_user_type, $own_company_id, $tem_company_id, $own_organize_id, $tem_organize_id, $own_personal_id, $tem_personal_id);
            if(!$infoPower) return false;
        }
        return true;
    }

    // ********************视图**错误*****执行方法*******************开始***************************************************

    /**
     *
     * 所有视图方法要调用的函数，以便后捕抓错误，--显示错误信息；或跳转到登录面【没有登录】
     * @param object $obj 当前的控制器对象
     * @param object $request 当前的控制器对象的request对象
     * @param mixed $doFun 需要统计执行视图方法 的闭包函数  function(&$reDataArr){} ，
     *   参数只有一个  $reDataArr ：代码执行中可以指定值，也会传入到错误视图中【使用】；
     *   其它参数都通过 use传入函数内 如：use(&$namespace, &$expireNums)
     * @param string  $errMethod 根据错误码执行自己独特的操作方法 参数 $request, $reDataArr, $errCode, $errStr
     * @param array $reDataArr 在数组值中，用来判断权限的字段名称指定,以及其它想传到错误视图的变量
     *   $reDataArr = [
     *       'errorMsg' => '',// 错误文字 ，  您没有操作权限---此值是throws抛出的错误内容
     *      'isShowBtn' => '',// // 1:显示“回到首页”；2：显示“返回上页”
     *       ...
     *  ];
     * @param string $errorView 错误显示的视图
     * @return mixed 返回视图或 继续 throws 错误到最外层
     * @author zouyan(305463219@qq.com)
     */
    public static function doViewPages(&$obj, $request, $doFun, $errMethod = 'errorViewDo', &$reDataArr = [], $errorView = 'error'){
        // 判断是否已经过了报名时间
        try{
            if(is_callable($doFun)){
                return $doFun($reDataArr);
            }
            throws('没有指定执行函数！');
        } catch ( \Exception $e) {
            $errStr = $e->getMessage();
            $errCode = $e->getCode();
//            switch($errCode){
//                case 700:// 登录状态过期
//                    break;
//                case 1001:// 待补充企业资料
//                    return redirect('web/perfect_company');
//                    break;
//                case 1002:// 待补充用户资料
//                    return redirect('web/perfect_user');
//                    break;
//                default:
//                    break;
//            }
            // 自已有定义错误处理方法，则执行
            // 返回 bool类型，说明还要继续执行
            if(method_exists($obj, $errMethod)){
                $errRetun = $obj->{$errMethod}($request, $reDataArr, $errCode, $errStr);
                // if(is_object($errRetun)) return $errRetun;// 是对象直接返回
                if(!is_bool($errRetun))  return $errRetun;// 不是布尔类型，直接返回
            }
            // 没有登录或登录状态过期 -- 会跳转到配置文件配置的登录页
            if($errCode == 999)  throws($e->getMessage(), $e->getCode());
            $reDataArr['errCode'] = $errCode;
            $reDataArr['errorMsg'] = $errStr;
            // $reDataArr['isShowBtn'] = 0;// 1:显示“回到首页”；2：显示“返回上页”
            return static::errorView($reDataArr, $errorView);
        }
    }


    /**
     * 错误页面视图
     * @param array $reDataArr 在数组值中，用来判断权限的字段名称指定
     *   $reDataArr = [
     *       'errorMsg' => '',// 错误文字 ，  您没有操作权限
     *      'isShowBtn' => '',// // 1:显示“回到首页”；2：显示“返回上页”
     *  ];
     * @param string $errorView 错误显示的视图
     * @return mixed 返回视图
     * @author zouyan(305463219@qq.com)
     */
    public static function errorView($reDataArr = [], $errorView = 'error'){
//        $reDataArr['errorMsg'] = '您没有操作权限！';
//        $reDataArr['isShowBtn'] = 1;// 1:显示“回到首页”；2：显示“返回上页”
        if(!isset($reDataArr['errorMsg'])) $reDataArr['errorMsg'] = '您没有操作权限！';
        if(!isset($reDataArr['isShowBtn'])) $reDataArr['isShowBtn'] = 0;
        return view($errorView, $reDataArr);
    }

    // ********************视图**错误*****执行方法*******************结束***************************************************

}
