@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
echo *==========================The execution environment=================================*
php\base\php.exe -v
php\base\php.exe execute.php announcement
pause
php\base\php.exe execute.php
pause
