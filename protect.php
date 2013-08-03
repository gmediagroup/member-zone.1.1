<?php

error_reporting(E_ALL ^ E_NOTICE);
define ("ROOT_DIR", dirname(__FILE__));

include(ROOT_DIR . "/_includes/functions.php");
$_CONFIG = getconfig();
$_CODES = getcodes();

$_TIME = time();
$_GTIME = strtotime(gmdate("Y-m-d H:i:s", $_TIME));
$_ERRORS = array();

if(!isset($_GET['prq'])) echodie("Обращение к данному файлу напрямую запрещено.");

if(isset($_POST['code'])){
	$smscode = $_POST['code'];
	if(!preg_match("#^([a-z0-9]{5,8})$#", $smscode)){
		$_ERRORS[] = "Код не введен или содержит недопустимые символы.";
	}elseif(!isset($_CODES[$smscode]) || (isset($_CODES[$smscode]['exptime']) && $_CODES[$smscode]['exptime'] < $_TIME)){
		unset($_COOKIE['_smsaccess']);
		$_ERRORS[] = "Введенный код не найден, либо срок его действия истек.";
	}elseif(!isset($_CONFIG['projects'][$_CODES[$smscode]['project']])){
		$_ERRORS[] = "Данный файл больше не доступен.";
	}else{
		setcookie("_smsaccess", $smscode);
		$_COOKIE['_smsaccess'] = $smscode;
		$fromform = true;
	}
}

if(isset($_COOKIE['_smsaccess']) && preg_match("#^([a-z0-9]{5,8})$#", $_COOKIE['_smsaccess']) && sizeof($_ERRORS) == 0){
	$smscode = $_COOKIE['_smsaccess'];
	if(!isset($fromform)){
		if(!isset($_CODES[$smscode]) || (isset($_CODES[$smscode]['exptime']) && $_CODES[$smscode]['exptime'] < $_TIME)){
			unset($_COOKIE['_smsaccess']);
			$_ERRORS[] = "Введенный код не найден, либо срок его действия истек.";
		}elseif(!isset($_CONFIG['projects'][$_CODES[$smscode]['project']])){
			$_ERRORS[] = "Данный файл больше не доступен.";
		}else{

			setcookie("_smsaccess", $smscode);
			$_COOKIE['_smsaccess'] = $smscode;
			$fromform = true;
		}
	}
	
	if(sizeof($_ERRORS) == 0)
		sendfile($_CONFIG['projects'][$_CODES[$smscode]['project']], $smscode);
}

echo_form();

?>