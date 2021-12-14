@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\base\php.exe -v
echo *==========================update cli normphp =================================*
pause
php\base\php.exe execute.php update
pause
