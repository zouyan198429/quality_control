<?php

require_once 'SessionCustom.php';

$redisKeyPre = '_database_';// Laravel 指定的 redis前缀 REDIS_PREFIX  默认为 _database_
$loginKeyArr = ['loginKeyadmin', 'loginKeywebfront'];
$redisKey = '';
foreach($loginKeyArr as $key){
    $redisKey = SessionCustom::get($key,true);// $_SESSION['loginKey'] ?? '';
    if(!empty($redisKey)) break;
}
print_r($redisKey);
echo '<br/>-->';
$redis = new Redis();
$redis->connect('localhost', 6379);
$redis->auth('ABCabc123456!@!');
$redis->select(0);//选择数据库0
// $redis->set( "test" , "Hello World");
//echo $redis->get( "test");

// $userInfo = $_SESSION['userInfo']?? [];
$userInfo = $redis->get($redisKeyPre . $redisKey);//$_SESSION['userInfo']?? [];
print_r($userInfo);

die;
$key = 'sessionid666';
$bbb = SessionCustom::set($key, '11111111', 0);
// $_SESSION['sessionid'] = 'this is session content!';
$aa = SessionCustom::get($key,true);// $_SESSION['sessionid'];

print_r($aa);
die;
