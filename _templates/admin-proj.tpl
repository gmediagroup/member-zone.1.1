<h3>��������/�������������� �������</h3>
<form action="{PHP_SELF}" method="POST">
<input type="hidden" name="do" value="doedit" />
<p><label>��� ������� (������ ����� � �����). ���� ������ � ����� ������ ����������, �� ����� ������� (��������������):<br /><input type="text" name="name" value="{name}" /></label></p>
<p><label>����� (����� �������; '/' ����� �������� - � ������ ��������� �����):<br /><input type="text" name="dirs" value="{dirs}" /></label></p>
<p><label>ID ������� �� profit-bill: <br /><input type="text" name="pbid" value="{pbid}" /></label></p>
<p><label>��������� ����� (�� ������� ������� profit-bill): <br /><input type="text" name="seccode" value="{seccode}" /></label></p>
<p><label>����� ����� ���� ������� � �������� � ������� ������� ������������� (0 - �� ����������, 3600 - ���): <br /><input type="text" name="exptimeout" value="{exptimeout}" /></label></p>
<p><label>���� ��� - ���� ���� (1 - ��, 0 - ���): <br /><input type="text" name="once" value="{once}" /></label></p>
<p><label>���������� ������, ������� ����� ������������ � ��������, ��� ����� ��� ����������� (����� �������; ��������, html,jpeg): <br /><input type="text" name="show_ext" value="{show_ext}" /></label></p>
<p><label>���������� ������, ������� ����� ����������� (����� �������; ��������, php,php5): <br /><input type="text" name="exec_ext" value="{exec_ext}" /></label></p>
<p><label>���������� ������, ������� ������������ ������ ��������� (����� �������; ��������, zip,exe,mp3): <br /><input type="text" name="dl_ext" value="{dl_ext}" /></label></p>
<p><label>������ ����� ����� ���� ������� (��������: form.tpl):<br /><input type="text" name="formtpl" value="{formtpl}" /></label></p>
<p><input type="submit" value="���������" /> <a href="{PHP_SELF}?do=doremove&name={name}" onclick="return confirm('�������� ����������. ���������?');">������� ������</a></p>
</form>