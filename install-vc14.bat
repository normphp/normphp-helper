@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\base\php.exe -v
echo *==========================install vc14 =================================*
pause
php\base\php.exe execute.php install-vc14
pause
