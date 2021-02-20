@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\8.0\x86\php.exe -v
echo *==========================update cli normphp =================================*
pause
php\8.0\x86\php.exe execute.php update
pause
