<h3>Создание/редактирование проекта</h3>
<form action="{PHP_SELF}" method="POST">
<input type="hidden" name="do" value="doedit" />
<p><label>Имя проекта (только буквы и цифры). Если проект с таким именем существует, он будет заменен (отредактирован):<br /><input type="text" name="name" value="{name}" /></label></p>
<p><label>Папки (через запятую; '/' после названия - с учетом вложенных папок):<br /><input type="text" name="dirs" value="{dirs}" /></label></p>
<p><label>ID проекта на profit-bill: <br /><input type="text" name="pbid" value="{pbid}" /></label></p>
<p><label>Секретное слово (из личного кабинет profit-bill): <br /><input type="text" name="seccode" value="{seccode}" /></label></p>
<p><label>Время жизни кода доступа в секундах с момента первого использования (0 - не ограничено, 3600 - час): <br /><input type="text" name="exptimeout" value="{exptimeout}" /></label></p>
<p><label>Один код - один файл (1 - да, 0 - нет): <br /><input type="text" name="once" value="{once}" /></label></p>
<p><label>Расширения файлов, которые будут отображаться в браузере, как текст или изображения (через запятую; например, html,jpeg): <br /><input type="text" name="show_ext" value="{show_ext}" /></label></p>
<p><label>Расширения файлов, которые будут выполняться (через запятую; например, php,php5): <br /><input type="text" name="exec_ext" value="{exec_ext}" /></label></p>
<p><label>Расширения файлов, которые пользователь начнет скачивать (через запятую; например, zip,exe,mp3): <br /><input type="text" name="dl_ext" value="{dl_ext}" /></label></p>
<p><label>Шаблон формы ввода кода доступа (например: form.tpl):<br /><input type="text" name="formtpl" value="{formtpl}" /></label></p>
<p><input type="submit" value="Сохранить" /> <a href="{PHP_SELF}?do=doremove&name={name}" onclick="return confirm('Операция необратима. Выполнить?');">Удалить проект</a></p>
</form>