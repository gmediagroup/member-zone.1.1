<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
<title>Панель управления модулем</title>
<style>
p{
	margin:0 0 7px 0;
}
#main-wraper{
	width:99%;
	border: 1px #DDD solid;
}
#left-col{
	width:260px;
	float:left;
	padding-left:7px;
	padding-right:17px;
	border-right: 1px #DDD solid;
	height:100%
}
#right-col{
	float: left;
	padding-left: 14px;
}
.flc{
	clear:both;
	margin:0px;
	padding:0px;
}
td {border: 1px solid #ddd; text-align: center;}
div#info{
	font-family: Tahoma;
	font-size: 11px;
}
form p {
	border-bottom: 1px solid #CCC;
	padding-bottom: 4px;
}
</style>
</head>
<body>
<div id="main-wraper">
<div id="left-col">
<p>Привет, Админ! (<a href="{PHP_SELF}?do=logout">Выход</a>)</p>
<p><strong>Проекты</strong></p>
<p>{projects}</p>
<br />
<p><a href="{PHP_SELF}?do=edit">Создать новый проект</a></p>
<hr />
<p><strong>Быстрые ссылки</strong></p>
<p><a href="{PHP_SELF}?do=editconf">Общие настройки</a><br />
<a href="{PHP_SELF}?do=resetconf" onclick="return confirm('Операция необратима. Выполнить?');">Установить настройки по умолчанию</a><br />
<a href="{PHP_SELF}?do=clearcodes" onclick="return confirm('Операция необратима. Выполнить?');">Отменить все выданые пароли (!!!)</a>
</p>
<div id="info">О том, как сменить пароль от панели управления, вы можете прочитать в руководстве к модулю.</div>
</div>
<div id="right-col">
{info}
{content}
</div>
<br class="flc" />
</div>
</body>
</html>