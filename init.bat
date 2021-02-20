@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\8.0\x86\php.exe -v
php\8.0\x86\php.exe execute.php announcement
pause
php\8.0\x86\php.exe execute.php
pause
