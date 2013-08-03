<?php

error_reporting(E_ALL ^ E_NOTICE);

define ("ROOT_DIR", dirname(__FILE__));

if(!isset($_GET['message']) || "" == $message = preg_replace("#[^\w\d]#i", "", $_GET['message'])) die("Code: 101");
if(!isset($_GET['short_number']) || 0 == $short_number = intval($_GET['short_number'])) die("Code: 102");
if(!isset($_GET['id']) || "" == $id = trim($_GET['id'])) die("Code: 103");

include(ROOT_DIR . "/_includes/functions.php");
$_CONFIG = getconfig();

// Ищем проект по короткому номеру и префиксу
foreach($_CONFIG['projects'] as $pname => $tproject){
	if(!isset($tproject['pbid']) || $tproject['pbid'] == "") continue;
	$apidata = getcache("apidata_".$tproject['pbid'], 3600*24);
	if(!$apidata){
		$apidata = getinfobyid($tproject['pbid'], $tproject['seccode']);
		savecache('apidata_'.$tproject['pbid'], $apidata);
		
		if(!$apidata) continue;
	}
	foreach($apidata as $data){
		if($data['short_number'] != $short_number || strpos($message, $data['message']) !== 0) continue;
		
		//проект найден
		$project = $tproject;
		break(2);
	}
}

if(!isset($project)) die("Code: 110"); //проект не найден

if(!isset($_GET['key']) || md5($project['seccode'].$id) != $_GET['key']) die('Code: 001');

$_CODES = getcodes();
$_TIME = time();
$_GTIME = strtotime(gmdate("Y-m-d H:i:s", $_TIME));

$template = "reply\nYour password for $pname: {usercode}\nhttp://".$_SERVER['HTTP_HOST'];

do{
	$usercode = get_rand_str(7);
}while(isset($_CODES[$usercode]));


$from = $_GET['from'];
$date = $_GET['date'];
$cost_rur = $_GET['cost_rur'];
$country = $_GET['country'];
$operator = $_GET['operator'];

$_CODES[$usercode] = array(
	'project'		=>	$project['name'],
	'create'		=>	$_TIME,
	'id'			=>	$id,
	'from'			=>	$from,
	'date'			=>	$date,
	'message'		=>	$message,
	'cost_rur'		=>	$cost_rur,
	'country'		=>	$country,
	'operator'		=>	$operator,
	'short_number'	=>	$short_number,
);

savecodes($_CODES);

@header('Content-type: text/html; charset=utf-8');
echo str_replace("{usercode}", $usercode, $template);

?>