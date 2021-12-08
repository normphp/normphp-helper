@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\8.1\php.exe -v
echo *==========================install vc14 =================================*
pause
php\8.1\php.exe execute.php install-vc14
pause
