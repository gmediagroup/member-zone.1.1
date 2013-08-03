<?php

function getcache($name, $maxtime = 86400){
	$fpath = ROOT_DIR . "/_data/{$name}.tmp";
	if(!is_file($fpath) || (time() - filemtime($fpath)) > $maxtime || "" == $content = @file_get_contents($fpath)) return false;
	
	$content = unserialize($content);
	return $content;
}

function savecache($name, $content){
	$fpath = ROOT_DIR . "/_data/{$name}.tmp";
	$content = serialize($content);
	locker();
	$h = fopen($fpath, "w+");
	fwrite($h, $content);
	fclose($h);
	@chmod($fpath, 0666);
	locker(true);
}

### Получение данных о проекте с profit-bill ###
function getinfobyid($id, $secret){
	global $_CONFIG;
	
	$content = @file_get_contents("http://profit-bill.com/api/get_api_tariffs?smsapi_id={$id}&secret=". $secret . "&format=json");
	if($content == "") return false;	
	$content = json_decode_cyr($content);
	//echo nl2br(var_export($content, true));
	if(!is_array($content) || !isset($content['success']) || !$content['success'] || sizeof($content['data']) == 0) return false;
	
	eval('$content='.iconv('UTF-8','WINDOWS-1251',var_export($content,true)).';');
	return $content['data'];
}

function json_encode_cyr($str) {
	$arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
	'\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
	'\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
	'\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
	'\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
	'\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
	'\u0448','\u0429','\u0449','\u042a','\u044a','\u042d','\u044b','\u042c','\u044c',
	'\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
	$arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
	'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
	'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
	'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
	$str1 = json_encode($str);
	$str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
	return $str2;
}

function json_decode_cyr($str) {
	$arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
	'\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
	'\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
	'\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
	'\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
	'\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
	'\u0448','\u0429','\u0449','\u042a','\u044a','\u042d','\u044b','\u042c','\u044c',
	'\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
	$arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
	'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
	'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
	'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
	$str1 = json_decode($str, true);
	$str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
	return $str2;
}

### Сохранение sms-кодов ###
function savecodes($codes){
	$fpath = ROOT_DIR . "/_data/codes.cfg";
	locker();
	$codes = serialize($codes);
	$h = fopen($fpath, "w+");
	fwrite($h, $codes);
	fclose($h);
	@chmod($fpath, 0666);
	locker(true);
}

### Чтение sms-кодов ###
function getcodes(){
	$fpath = ROOT_DIR . "/_data/codes.cfg";
	if(!is_file($fpath)){
		return array();
	}
	$codes = file_get_contents($fpath);
	$codes = unserialize($codes);
	if(!is_array($codes) || count($codes) < 1) return array();
	return $codes;
}

### Проверка на наличие файла перед показом формы ввода sms-кода. Результат: имя файла шаблона для формы ###
function is_file_exist($file_path){
	global $_CONFIG, $_ERRORS;
	
	while(substr($file_path, 0, 1) == "/"){
		$file_path = substr($file_path, 1);
	}
	
	while(substr($file_path, -1) == "/"){
		$file_path = substr($file_path, 0, -1);
	}
	
	if(!is_file(ROOT_DIR . "/" . $file_path)) echodie("Данный файл не существует!");
	
	$file_info = pathinfo(ROOT_DIR . "/" . $file_path);
	$file_info['dirname'] = str_replace(ROOT_DIR . "/", "", $file_info['dirname']);
	
	if(!isset($file_info['extension']) || $file_info['extension'] == ""){
		echodie("Недопустимый формат файла.");
		return "";
	}
		
	foreach ($_CONFIG['projects'] as $pname => $project){
		# Проверяем наличие необходимых параметров в блоке
		if (!is_array($project) || !isset($project['pbid']) || $project['pbid'] == "") continue;
		
		# Проверяем принадлежность смс к данному блоку
		foreach($project['dirs'] as $dir){
			if((substr($dir, -1) == "/" && strpos($file_info['dirname'] . "/", $dir) === 0) || $dir == $file_info['dirname']){
				if(!isset($project['formtpl']) || $project['formtpl'] == ""){
					if(isset($_CONFIG['formtpl']) && $_CONFIG['formtpl'] != "") $project['formtpl'] = $_CONFIG['formtpl'];
					else echodie("Отсутствуют настройки шаблона формы авторизации.");
				}
				return $project;
			}
		}
	}
	
	return "";
}

### Вывод формы ввода sms-кода ###
function echo_form(){
	global $_CONFIG, $_ERRORS;
	
	$file_path = stripslashes(trim(urldecode($_GET['prq'])));
	if (strpos($file_path, "./") !== false || strpos($file_path, "../") !== false || strpos($file_path, ".htaccess") !== false || strpos($file_path, ".htpasswd") !== false) echodie("Файл не доступен!");

	$project = is_file_exist($file_path);
	if(!is_array($project)) echodie("Отсутствуют или повреждены настройки авторизации для данного файла.");
	
	if(!is_file(ROOT_DIR . "/_templates/" . $project['formtpl'])){
		echodie("Отсутствуют файл шаблона.");
	}
	
	$output = file_get_contents(ROOT_DIR . "/_templates/" . $project['formtpl']);
	
	$apidata = getcache("apidata", 3600*24);
	if(!$apidata){
		$apidata = getinfobyid($project['pbid'], $project['seccode']);
		savecache('apidata', $apidata);
	}
	
	if($apidata != false){
		$countries = "";
		$phonenums = "";
		$smstexts = "";
		foreach($apidata as $key => $value){
			$countries .= "<option value={$key}>{$value['country']}</option>";
			$phonenums .= "{$key}: '{$value['short_number']}',";
			$smstexts .= "{$key}: '{$value['message']}',";
		}
		$phonenums = substr($phonenums, 0, -1);
		$smstexts = substr($smstexts, 0, -1);
		$project['smstext'] = $apidata[0]['message'];
		$project['phonenum'] = $apidata[0]['short_number'];
	}else{
		$phonenums = "";
		$smstexts = "";
		$project['smstext'] = "";
		$project['phonenum'] = "";
		$countries = "<option value=false>Нет стран</option>";
	}
	
	$output = str_replace("{countries}", $countries, $output);
	$output = str_replace("{smstexts}", $smstexts, $output);
	$output = str_replace("{phonenums}", $phonenums, $output);
	$output = str_replace("{phonenum}", $project['phonenum'], $output);
	$output = str_replace("{smstext}", $project['smstext'], $output);
	if(is_array($_ERRORS) && sizeof($_ERRORS) > 0) $output = str_replace("{errors}", implode("<br />", $_ERRORS), $output);
	else  $output = str_replace("{errors}", "", $output);
	
	echo $output;
}

### Отправка файла ###
function sendfile($project, $smscode){
	global $_CONFIG, $_ERRORS, $_CODES, $_TIME;
	
	$file_path = stripslashes(trim(urldecode($_GET['prq'])));
	if (strpos($file_path, "./") !== false || strpos($file_path, "../") !== false || strpos($file_path, ".htaccess") !== false || strpos($file_path, ".htpasswd") !== false) echodie("Файл не доступен!");
	
	while(substr($file_path, 0, 1) == "/"){
		$file_path = substr($file_path, 1);
	}
	
	while(substr($file_path, -1) == "/"){
		$file_path = substr($file_path, 0, -1);
	}
	
	if(!is_file(ROOT_DIR . "/" . $file_path)) echodie("Данный файл не существует!");
	
	if(!isset($project['dirs']) || !is_array($project['dirs']) || sizeof($project['dirs']) == 0) {
		echodie("Отсутствут настройки доступа к файлу");
		exit;
	}
	
	$file_info = pathinfo(ROOT_DIR . "/" . $file_path);
	$file_info['dirname'] = str_replace(ROOT_DIR . "/", "", $file_info['dirname']);
	
	if(!isset($file_info['extension']) || $file_info['extension'] == ""){
		echodie("Недопустимый формат файла.");
		return false;
	}
	
	$isfound = false;
	foreach($project['dirs'] as $dir){
		if((substr($dir, -1) == "/" && strpos($file_info['dirname'] . "/", $dir) === 0) || $dir == $file_info['dirname']){
			$isfound = true;
			break;
		}
	}
	
	if(!$isfound){
		$_ERRORS[] = "Введенный вами sms-код не соответствует запрашиваемому разделу";
		return false;
	}
	
	if($project['once'] == 1){
		if(!isset($_CODES[$smscode]['filepath']) || $_CODES[$smscode]['filepath'] == "") $_CODES[$smscode]['filepath'] = $file_path;
		elseif($_CODES[$smscode]['filepath'] != $file_path) {
			$_ERRORS[] = "Введеный код уже привязан к другому файлу.";
			return false;
		}
	}
	
	if($project['exec_ext'] != "" && preg_match("#(?:^|\,)" . preg_quote($file_info['extension']) . "(?:\,|$)#i", $project['exec_ext'])){
		if(!isset($_CODES[$smscode]['exptime']) && $project['exptimeout'] > 0)
			$_CODES[$smscode]['exptime'] = $_TIME + $project['exptimeout'];
			
		savecodes($_CODES);
		include(ROOT_DIR . "/" . $file_path);
		
	}elseif($project['show_ext'] != "" && preg_match("#(?:^|\,)" . preg_quote($file_info['extension']) . "(?:\,|$)#i", $project['show_ext'])){
		if(!isset($_CODES[$smscode]['exptime']) && $project['exptimeout'] > 0)
			$_CODES[$smscode]['exptime'] = $_TIME + $project['exptimeout'];
			
		savecodes($_CODES);
		
		if ($file_info['extension'] == "jpg" || $file_info['extension'] == "jpeg") @header('Content-Type: image/jpeg');
		elseif ($file_info['extension'] == "png") @header('Content-Type: image/png');
		elseif ($file_info['extension'] == "gif") @header('Content-Type: image/gif');
		
		echo file_get_contents(ROOT_DIR . "/" . $file_path);
	}elseif($project['dl_ext'] != "" && preg_match("#(?:^|\,)" . preg_quote($file_info['extension']) . "(?:\,|$)#i", $project['dl_ext'])){
		if(!isset($_CODES[$smscode]['exptime']) && $project['exptimeout'] > 0)
			$_CODES[$smscode]['exptime'] = $_TIME + $project['exptimeout'];
			
		savecodes($_CODES);
		
		@header("Content-Type:application/octet-stream;");
		@header("Content-length: " . filesize(ROOT_DIR . "/" . $file_path));
		@header("Content-Disposition: attachment; filename=\"{$file_info['basename']}\"");
		if ($h = fopen(ROOT_DIR . "/" . $file_path, "rb")) {
			while(!feof($h)) {
				echo fread($h, 1024*32);
				@flush();
				if (connection_status() != 0) {
					break;
				}
			}
			fclose($h);
		}
	}else{
		echodie("Недопустимый формат файла.");
	}
	
	exit();
}

### 

### Вывод критической ошибки ###
function echodie($msg){
	setcookie("_smsaccess", "");
	die($msg);
}

### Создание настроек по умолчанию ###
function buildconfig($fpath){
	$config = array(
		'formtpl' => 'form.tpl',
		'dirs' => array (
			0 => 'privatezone/',
		),
		'pbid' => '1111',
		'seccode' => '111222',
		'exptimeout' => '3600',
		'once' => '0',
		'show_ext' => array (
			0 => 'html',
			1 => 'htm',
			2 => 'txt',
			3 => 'gif',
			4 => 'jpg',
			5 => 'jpeg',
			6 => 'png',
		),
		'exec_ext' => array (
			0 => 'php',
		),
		'dl_ext' => array (
			0 => 'zip',
			1 => 'exe',
			2 => 'mp3',
		)
	);
	saveconfig($config);
}

### Чтение настроек ###
function getconfig($rebuild = false){
	$fpath = ROOT_DIR . "/_data/config.cfg";
	if(!is_file($fpath)){
		if(!$rebuild) echodie("Ошибка чтения файла настроек. Отсутствует файл.");
		else buildconfig($fpath);
	}
	$config = file_get_contents($fpath);
	$config = unserialize($config);
	if(!is_array($config) || count($config) < 2) echodie("Ошибка чтения файла настроек. Файл поврежден или имеет неправильный формат.");
	return $config;
}

### Чтение настроек ###
function saveconfig($config){
	locker();
	$fpath = ROOT_DIR . "/_data/config.cfg";
	$config = serialize($config);
	$h = fopen($fpath, "w+");
	fwrite($h, $config);
	fclose($h);
	@chmod($fpath, 0666);
	locker(true);
}

### Генерация кода без двусмысленных символов ###
function get_rand_str($num){
	$arr = array(	
		'a','b','c','d','e','f','g','h','k','m','n','p','r','s','t','u','v','x','y','z',					  
		'2','3','4','5','6','7','8','9'
	);
	
	$str = "";
	for($i = 0; $i < $num; $i++){
		$str .= $arr[array_rand($arr, 1)];
	}
	return $str;
}

### Блокировка системных файлов на запись ###
function locker($unlock = false){
	global $lock;
	if($unlock){
		flock($lock, LOCK_UN);
        fclose($lock);
		return true;
	}else{
		$done = false;
		while(!$done){
			$lock = @fopen(ROOT_DIR . "/_data/_LOCK.ER","a");
			$done = @flock($lock, LOCK_EX);
		}
		return true;
	}
	return false;
}

?>