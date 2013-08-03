<html>
<head>
<title></title>
<style>
*{font-family:Tahoma; font-size: 13px;}
.red {color: red;}
.small {font-size: 85%;}
</style>
</head>
<body>
<script>
var phonenums = {{phonenums}};
var smstexts = {{smstexts}};
function selCountry(v){
	document.getElementById('smstext').innerHTML = smstexts[v];
	document.getElementById('phonenum').innerHTML = phonenums[v];
}
</script>
<p>Для доступа к файлу, необходимо ввести пароль.</p>
<p>Выберeте страну: <select id="country" onchange="selCountry(this.value);">{countries}</select></p>
<p>Для получения пароля, отправьте sms с текстом <span id="smstext">{smstext}</span> на номер <span id="phonenum">{phonenum}</span></p>
<p class="red small">{errors}</p>
<form method="POST">
	<label>Введите пароль: <input type="text" name="code" value="" /></label> <input type="submit" value="Продолжить" />
</form>
</body>
</html>