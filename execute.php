<?php
date_default_timezone_set('Asia/Shanghai');
#exec('REG query HKCU\Environment /V PATH', $originPath);
#$path = explode('    ', $originPath[2])[3]??'';
session_write_close();
require('./drive/execute.php');
require('./drive/ProgressBar.php');
require('./drive/RequestDownload.php');

if(($argv[1]??'') ==='announcement'){
    (new execute(__DIR__))->announcement();
    (new execute(__DIR__))->initPhpIni();
}else if (($argv[1]??'') === 'update') {
    (new execute(__DIR__))->updateNormPhp();
}else if (($argv[1]??'') === 'install-vc14') {
    (new execute(__DIR__))->installVc14();
}else if (($argv[1]??'') === 'initPhpIni') {
    (new execute(__DIR__))->initPhpIni();
}else{
    (new execute(__DIR__))->init();
}


