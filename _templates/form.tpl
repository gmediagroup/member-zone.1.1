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
<p>��� ������� � �����, ���������� ������ ������.</p>
<p>�����e�� ������: <select id="country" onchange="selCountry(this.value);">{countries}</select></p>
<p>��� ��������� ������, ��������� sms � ������� <span id="smstext">{smstext}</span> �� ����� <span id="phonenum">{phonenum}</span></p>
<p class="red small">{errors}</p>
<form method="POST">
	<label>������� ������: <input type="text" name="code" value="" /></label> <input type="submit" value="����������" />
</form>
</body>
</html>