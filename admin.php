<?php

@session_start ();
error_reporting(E_ALL ^ E_NOTICE);
define ("ROOT_DIR", dirname(__FILE__));
$PHP_SELF = explode("/", $_SERVER['PHP_SELF']);
$PHP_SELF = $PHP_SELF[sizeof($PHP_SELF)-1];

### presets ###

$login = "admin";
$pass = "123123";
$salt = "59dht8n645tg983j45y3984j";
$userhash = md5(md5($login.$sessid.$salt.$pass));
$sessid = session_name();

###############

if((!isset($_COOKIE['userhashstat']) || $_COOKIE['userhashstat'] != $userhash) && $_POST['do'] != "login"){
	header('Content-type: text/html; charset=windows-1251');
	echo <<<HTML
<form action="$PHP_SELF" method="post">
	<input type="text" name="login" value="" /><br />
	<input type="password" name="pass" value="" /><br />
	<input type="hidden" name="do" value="login" />
	<input type="submit" value="Sign In" />
</form>	
HTML;
exit();
}elseif($_POST['do'] == "login"){
	if(trim($_POST['login']) != $login or trim($_POST['pass']) != $pass){
		@header("Location: $PHP_SELF");
		exit();
	}
	
	setcookie("userhashstat", $userhash);
	@header("Location: $PHP_SELF");
	exit();
}elseif($_GET['do'] == "logout"){
	
	setcookie("userhashstat");
	@header("Location: $PHP_SELF");
	exit();
}

include(ROOT_DIR . "/_includes/functions.php");
if(isset($_GET['reset'])) $reset = true;
else $reset = false;
$_CONFIG = getconfig($reset);

$_TIME = time();
$_GTIME = strtotime(gmdate("Y-m-d H:i:s", $_TIME));
$_ERRORS = array();

if(!is_file(ROOT_DIR . "/_templates/admin-main.tpl")) echodie("Отсутствует главный файл шаблона панели управления (admin-main.tpl)");

if($_GET['do'] == "editconf"){
	if(!is_file(ROOT_DIR . "/_templates/admin-conf.tpl")) echodie("Отсутствует файл шаблона редактирования настроек (admin-conf.tpl)");
	$content = file_get_contents(ROOT_DIR . "/_templates/admin-conf.tpl");
	foreach($_CONFIG as $key => $value){
		if($key == "projects") continue;
		if(is_array($value)) $value = implode(",", $value);
		$content = str_replace("{" . $key . "}", htmlspecialchars($value), $content);
	}
}elseif($_POST['do'] == "doeditconf"){
	foreach($_POST as $key => $value){
		if($key == 'do' || $key == 'projects') continue;
		if(in_array($key, array('dirs'))) $_POST[$key] = explode(",", $_POST[$key]);
		$_CONFIG[$key] = $_POST[$key];
	}
	saveconfig($_CONFIG);
	header("Location: $PHP_SELF?do=editconf&info=" . urlencode("Настройки сохранены."));
	exit();
}elseif($_GET['do'] == "resetconf"){
	$projects = $_CONFIG['projects'];
	@unlink(ROOT_DIR . "/_data/config.cfg");
	$_CONFIG = getconfig(true);
	$_CONFIG['projects'] = $projects;
	saveconfig($_CONFIG);
	header("Location: $PHP_SELF?do=editconf&info=" . urlencode("Настройки сброшены."));
	exit();
}elseif($_GET['do'] == "clearcodes"){
	savecodes(array());
	header("Location: $PHP_SELF?info=" . urlencode("Все выданные ранее коды доступа были аннулированы"));
	exit();
}elseif($_GET['do'] == "edit"){
	if(!is_file(ROOT_DIR . "/_templates/admin-proj.tpl")) echodie("Отсутствует файл шаблона создания/редактирования проекта (admin-proj.tpl)");
	$content = file_get_contents(ROOT_DIR . "/_templates/admin-proj.tpl");
	if(isset($_GET['name']) && $_GET['name'] != "" && isset($_CONFIG['projects'][$_GET['name']])){
		$project = $_CONFIG['projects'][$_GET['name']];
		$content = str_replace("{name}", $_GET['name'], $content);
	}else{
		$project = $_CONFIG;
		$content = str_replace("{name}", "", $content);
	}
	
	foreach($project as $key => $value){
		if($key == "projects") continue;
		if(is_array($value)) $value = implode(",", $value);
		$content = str_replace("{" . $key . "}", htmlspecialchars($value), $content);
	}
}elseif($_POST['do'] == "doedit"){
	if(trim($_POST['name']) == "" || preg_match("#[^a-zA-Z0-9]#", $_POST['name'])){
		header("Location: {$_SERVER['HTTP_REFERER']}&info=" . urlencode("Имя задано неверно."));
		exit();
	}
	
	if(intval($_POST['pbid']) == 0 || getinfobyid($_POST['pbid'], $_POST['seccode']) == false){
		header("Location: {$_SERVER['HTTP_REFERER']}&info=" . urlencode("ID проекта на profit-bill указано неверно либо не удалось получить данные."));
		exit();
	}
	
	$project = array();
	foreach($_POST as $key => $value){
		if($key == 'do') continue;
		if(in_array($key, array('dirs'))) $_POST[$key] = explode(",", $_POST[$key]);
		$project[$key] = $_POST[$key];
	}
	if(!isset($_CONFIG['projects']) || !is_array($_CONFIG['projects'])) $_CONFIG['projects'] = array($_POST['name'] => $project);
	elseif(!isset($_CONFIG['projects'][$_POST['name']])) $_CONFIG['projects'][$_POST['name']] = $project;
	else $_CONFIG['projects'][$_POST['name']] = $project;
	saveconfig($_CONFIG);
	header("Location: $PHP_SELF?do=edit&name={$_POST['name']}&info=" . urlencode("Проект сохранен."));
	exit();
}elseif($_GET['do'] == "doremove"){
	if(trim($_GET['name']) == "" || preg_match("#[^a-zA-Z0-9]#", $_GET['name'])){
		header("Location: {$_SERVER['HTTP_REFERER']}&info=" . urlencode("Не указано имя проекта для удаления либо имя содержит недопустимые символы."));
		exit();
	}
	if(isset($_CONFIG['projects'][$_GET['name']])) unset($_CONFIG['projects'][$_GET['name']]);
	saveconfig($_CONFIG);
	header("Location: {$_SERVER['HTTP_REFERER']}&info=" . urlencode("Если проект существовал, то он удален."));
	exit();
}

$tpl = file_get_contents(ROOT_DIR . "/_templates/admin-main.tpl");

if(isset($info) && is_array($info) && sizeof($info)) $tpl = str_replace("{info}", implode("<br />", $info), $tpl);
elseif(isset($_GET['info'])) $tpl = str_replace("{info}", $_GET['info'], $tpl);
else $tpl = str_replace("{info}", "", $tpl);

if(isset($content)) $tpl = str_replace("{content}", $content, $tpl);
else $tpl = str_replace("{content}", "", $tpl);

if(isset($_CONFIG['projects']) && sizeof($_CONFIG['projects'])){
	$projects = array();
	foreach($_CONFIG['projects'] as $pname => $project){
		$projects[] = "<a href=\"$PHP_SELF?do=edit&name=$pname\">$pname</a>";
	}
	$tpl = str_replace("{projects}", implode("<br />", $projects), $tpl);
}else $tpl = str_replace("{projects}", "У вас нет ни одного созданного проекта.", $tpl);

$tpl = str_replace("{PHP_SELF}", $PHP_SELF, $tpl);

header('Content-type: text/html; charset=windows-1251');
echo $tpl;

?>