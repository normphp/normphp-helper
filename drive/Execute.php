<?php


class Execute
{
    /**
     * 需要的执行php版本
     */
    const NEED_PHP_VERSION = '8.1.0';
    /**
     * composer.phar 下载地址最新版本
     */
    const COMPOSER_PHAR_URL = 'https://getcomposer.org/download/latest-2.x/composer.phar';

    /**
     * composer.phar 下载地址 ^1 版本
     */
    const COMPOSER_1_PHAR_URL = 'https://getcomposer.org/download/latest-1.x/composer.phar';
    /**
     * 证书下载地址
     */
    const CACERT_PEM_URL = 'https://curl.haxx.se/ca/cacert.pem';
    /**
     * 运行库
     */
    const VC_URL = [
        'VC9-32'=>'https://www.php.cn/xiazai/download/1479',
        'VC10-64'=>'https://www.php.cn/xiazai/download/1480',
        'VC11-32'=>'https://www.php.cn/xiazai/download/1481',
        'VC12(32+64)'=>'https://www.php.cn/xiazai/download/1482',
        'VC13(32+64)'=>'https://www.php.cn/xiazai/download/1483',
        'VC14(32+64)'=>'https://www.php.cn/xiazai/download/1484',
    ];
    /**
     * 标准时间格式
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';
    /**
     * 标准等待提示
     */
    const MSG_AWAIT_ENTER = '如长时间界面不动请：按回车键  ..............';

    /**
     * 根目录
     * @var string
     */
    public  $dir = '';
    /**
     * 是否安装php环境变量
     * @var bool
     */
    public  $php = true;
    /**
     * 是否安装composer 环境变量
     * @var bool
     */
    public  $composer = true;
    /**
     * 是否安装normphp环境变量
     * @var bool
     */
    public  $normphp = true;
    /**
     * 换行
     * @var string
     */
    public  $eol= PHP_EOL.'*************************************'.PHP_EOL;
    /**
     * 被替换的原环境变量
     * @var array[]
     */
    public $quondam = ['php'=>[],'composer'=>[],'helper-tool'=>[]];
    /**
     * 需要注册的模块path
     * @var string[]
     */
    public  $addPath = [
        'php'=>'\\php\\base',//默认 php
        'composer'=>'\\composer',//composer 程序
        'helper-tool'=>'\\helper\\public',//脚手架->normphp 框架程序
    ];

    /**
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        $this->dir = $dir;
        foreach ($this->addPath as &$value){
            $value = $this->dir.$value;
        }
        # 检查当前PHP版本（执行）
        if (version_compare(PHP_VERSION, self::NEED_PHP_VERSION, '>=')){
            $this->msg('当前执行环境PHP_VERSION：'.PHP_VERSION);
        }else{
            $this->msg('执行环境PHP必须是'.self::NEED_PHP_VERSION.'及以以上，当前：'.PHP_VERSION,true);
        }
    }

    /**
     * 初始化base php的ini
     */
    public function initPhpIni(){
        #$this->msg('开始初始化:PHP8.1配置文件php.ini');
        #copy($this->dir.'\\php\\8.1\php.ini',$this->dir.'\\php\\8.1\x64\php.ini');
    }
    /**
     * 初始化开始
     * @throws Exception
     */
    public  function init()
    {
        $this->msg($this->eol);
        $this->msg('当前时间:'.date(self::DATE_FORMAT));
        $this->msg('开始初始化:注册安装PHP、Composer');
        #检查下载证书
        $this->getCaCert();
        #检查是否已经安装
        $this->detectionApp();
        # 检查环境变量  设置
        $this->setVariateMatch();
        # 写入日志
        $this->setLog();
        $this->msg('    初始化完成');
        $this->msg('    请新开命令行窗口执行：normphp 命令获取更多信息'.PHP_EOL.PHP_EOL);
    }

    /**
     * 公告提示
     */
    public function announcement()
    {
        echo "*====================================================================================*\n\r";
        echo "*当前为normphp 脚手架初始化脚本：                                                    *\n\r";
        echo "*  1、脚本需使用PHP".self::NEED_PHP_VERSION."+版本。                                                      *\n\r";
        echo "*  2、脚本判断是否已有PHP环境变量，如没有会使用PHP".self::NEED_PHP_VERSION."路径创建环境变量。              *\n\r";
        echo "*     有会使用PHP".self::NEED_PHP_VERSION."路径覆盖环境变量。                                               *\n\r";
        echo "*  3、脚本判断是否安装composer，如否就下载安装配置composer环境变量。                 *\n\r";
        echo "*  4、脚本判断是否已有normphp环境变量，如否就执行命令进行环境变量配置。              *\n\r";
        echo "*  5、如需切换PHP7.2、PHP7.3、PHP7.4，执行normphp set php [7.2|7.3|7.4]切换。        *\n\r";
        echo "*==========================  Perform initialization =================================*\n\r";
    }

    /**
     * 设置环境变量
     */
    public function setVariateMatch()
    {
        $VariateRes = $this->refreshVariateMatch();
        # 判断是否是第一次安装初始化是就写入第一次的环境变量备份
        if (!is_file($this->dir.'\\log\\original.log')){
            file_put_contents($this->dir.'\\log\\original.log',json_encode(['date'=>date(self::DATE_FORMAT),'data'=>$VariateRes]));
            file_put_contents($this->dir.'\\log\\'.date(self::DATE_FORMAT).'original.path',json_encode(implode(';',$VariateRes)));
        }

        $this->quondam['quondam'] = $VariateRes;
        foreach ($VariateRes as $key=>$value)
        {
            $unset=false;
            # 检查是否 有php 关键字
            preg_match('/\\\php[876\\\]/',$value,$phpRes);
            if (!empty($phpRes)){
                $this->quondam['php'][] = $value;
                $unset=true;
            }
            # 检查是否 有composer 关键字
            preg_match('/composer/',$value,$composerRes);
            if (!empty($composerRes)){
                $this->quondam['composer'][] = $value;
                $unset=true;
            }
            # 检查是否 有helper-tool 关键字
            preg_match('/\\\helper\\\public/',$value,$toolRes);
            if (!empty($toolRes)){
                $this->quondam['helper-tool'][] = $value;
                $unset=true;
            }
            # 删除 需要替换掉的
            if ($unset){unset($VariateRes[$key]);}
            # 删除重复的
            if (in_array($value,$arrayData??[])){unset($VariateRes[$key]);}
            # 删除空的
            if (empty($value)){unset($VariateRes[$key]);}
            $arrayData[] = $value;
        }
        # 拼接写入
        $this->msg($this->eol);
        $this->msg('操作环境变量');
        $pathRes = implode(';',array_merge($VariateRes,array_values($this->addPath)));
        $this->msg('替换helper-tool：'.PHP_EOL.implode("\r\n",$this->quondam['helper-tool']));
        $this->msg('替换composer：'.PHP_EOL.implode("\r\n",$this->quondam['composer']));
        $this->msg('替换php：'.PHP_EOL.implode("\r\n",$this->quondam['php']));
        $this->msg('新增：'.PHP_EOL.implode("\r\n",array_values($this->addPath)));
        exec('setx PATH "'.$pathRes.';"');
        $this->msg('当前环境变量：'.PHP_EOL.implode("\r\n",array_values($this->refreshVariateMatch())));
        echo $this->eol;
    }

    /**
     * 下载cacert.pem
     * @throws Exception
     */
    public  function getCaCert()
    {
        if (!file_exists($this->dir.'\\uploads\\cacert.pem')) {
            $this->msg('下载:cacert.pem');
            $this->msg(self::MSG_AWAIT_ENTER);
            if ((new RequestDownload())->downFile(self::CACERT_PEM_URL,'cacert.pem') ){
                $this->msg('cacert.pem下载成功');
            }else{
                $this->msg('cacert.pem下载失败',true);
            }
        }
    }

    /**
     * 下载安装vc14运行库
     */
    public function installVc14()
    {
        $path = $this->dir.'\\uploads\\vc14.zip';
        if (file_exists($path)){
            $this->msg('vc14.zip存在无需下载！');
        }else{
            $this->msg('vc14.zip不存在，正在下载！');
            $this->msg(self::MSG_AWAIT_ENTER);
            # 请求地址
            $header =[
                'Referer: https://www.php.cn/',
                'Accept-Encoding: gzip, deflate, br',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            ];
            if ((new RequestDownload())->downFile(url: self::VC_URL['VC14(32+64)'],fileName: 'vc14.zip',header: $header)){
                $this->msg('vc14.zip 下载成功！！');
            }else{
                $this->msg('vc14.zip 下载失败！！');
                $this->msg('url：'.self::VC_URL['VC14(32+64)'],true);
                exit();
            }
        }
        $this->msg('进行解压->'.$this->dir.'\\uploads\\VC 14 32位.exe');
        if ($this->unzip($path,$this->dir.'\\uploads\\')){
            $this->msg('进行解压成功');
            rename($this->dir.'\\uploads\\VC 14 32位.exe',$this->dir.'\\uploads\\VC14.exe');
            $this->msg('--->请注意弹出的安装界面<---');
            exec($this->dir.'\\uploads\\VC14.exe',$res);
        }else{
            $this->msg('进行解压失败：可尝试手动解压');
            $this->msg('下载地址：'. self::VC_URL['VC14(32+64)']);
            $this->msg('压缩包地址：'.$this->dir.'\\uploads\\vc14.zip');
        }
    }
    /**
     *检查是否已经安装
     * @throws Exception
     */
    public  function detectionApp()
    {
        # 执行命令判断是否已经有 php 环境变量
        echo $this->eol;
        exec('php -v',$res);
        if (!empty($res)){
            echo '已安装PHP:   '.$res[0].PHP_EOL;
        }else{
            $this->php =false;
            # 注册环境变量
        }
        usleep(1400000);
        $res = [];
        # 执行命令判断是否已经有 composer 环境变量
        echo $this->eol;
        exec('composer -V',$res);
        if (!empty($res)){
            $this->msg('已安装Composer:  '.$res[0]);
        }else{
            $this->msg('准备安装Composer');
            $this->composer =false;
        }
        exec('composer-1 -V',$res);
        if (!empty($res)){
            $this->msg('已安装Composer 1.X:  '.$res[0]);
        }else{
            $this->msg('准备安装Composer 1.X');
            $this->composer =false;
        }
        # 强制更新Composer
        $this->getComposerPhar();
        $this->getComposerPhar('-1');

        usleep(1400000);
        $res = [];
        # 执行命令判断是否已经有 normphp环境变量
        echo $this->eol;
        exec('normphp -v',$res);
        if (!empty($res)){
            $this->msg('已安装normphp脚手架: version->'.$res[0]);
        }else{
            $this->msg('准备安装normphp脚手架');
            $this->normphp =false;
        }
        $this->installNormPhp();
        echo $this->eol;
        usleep(5000000);
    }

    /**
     * 安装normphp helper
     */
    public function installNormPhp()
    {
        $cli = $this->dir.'/php/base/php.exe '.$this->dir.'/composer/composer.phar  install -d '.$this->dir.'/helper/';
        $this->msg('安装normphp命令行框架:'.PHP_EOL);
        $this->msg($cli);
        exec($cli,$res);
        echo implode(PHP_EOL,$res);
    }
    /**
     * 安装normphp helper
     */
    public function updateNormPhp()
    {
        $cli = $this->dir.'/php/base/php.exe '.$this->dir.'/composer/composer.phar  update -d '.$this->dir.'/helper/';

        $this->msg('更新 normphp 命令行框架 '.'exec->['.$cli.']');
        $this->msg('命令行框架root目录：'.$this->dir.'/helper/');
        $this->msg('如长时间无反应：可尝试按回车键');
        exec($cli,$res);
    }
    /**
     * 判断是否已经有composer.phar文件,没有就下载
     * @throws Exception
     */
    public  function  getComposerPhar($v='')
    {
        if (file_exists($this->addPath['composer'].'\\composer'.$v.'.phar')){
            $this->msg('composer'.$v.'.phar存在无需下载！');
        }else{
            $this->msg('composer'.$v.'.phar不存在，正在下载！');
            $this->msg(self::MSG_AWAIT_ENTER);
            # 请求地址
            if ((new RequestDownload())->downFile($v===''?self::COMPOSER_PHAR_URL:self::COMPOSER_1_PHAR_URL,'composer'.$v.'.phar',$this->addPath['composer'].'\\')){
                $this->msg('composer'.$v.'.phar 下载成功！！');
            }else{
                $this->msg('composer'.$v.'.phar 下载失败！！');
                $this->msg('url：'.($v===''?self::COMPOSER_PHAR_URL:self::COMPOSER_1_PHAR_URL),true);
            }
            # 写入本地
        }
        # 写入环境变量
    }

    /**
     * 刷新环境变量 并返回变量信息 字符串
     * @param string $variate
     * @return mixed
     */
    public  function refreshVariate($variate ='PATH'): mixed
    {
        exec('REG query HKCU\Environment /V '.$variate, $originPath);
        $originPath = explode('    ', $originPath[2]??'');
        $originPath = $originPath[3]??'';
        return $originPath;
    }

    /**
     * 获取格式化 环境变量 array
     * @param string $variate
     * @return array|bool
     */
    public  function refreshVariateMatch($variate ='PATH'): array|bool
    {
        $str = $this->refreshVariate($variate);
        if (isset($str) && !empty($str)){
            $data = explode(';',$str);
            unset($data[count($data)-1]);
            return $data;
        }
        return [];
    }
    /**
     * 标准信息输出
     * @param string $msg 输出信息
     * @param bool $exit 是否直接结束
     */
    public  function msg(string $msg,bool $exit=false)
    {
        echo PHP_EOL.$msg.PHP_EOL;
        if ($exit){exit();}
    }
    /**
     * 写入日志
     */
    public function setLog()
    {
        $log = "\r\n[setVariateMatch][quondam][helper-tool]\r\n".implode("\r\n",$this->quondam['helper-tool']);
        $log .= "\r\n[setVariateMatch][quondam][composer]\r\n".implode("\r\n",$this->quondam['composer']);
        $log .= "\r\n[setVariateMatch][quondam][php]\r\n".implode("\r\n",$this->quondam['php']);
        $log .= "\r\n[setVariateMatch][quondam][quondam]\r\n".implode(";",$this->quondam['quondam']);
        file_put_contents($this->dir.'\\log\\install'.date('Y-m-d').'.log', "[INSTALL_TIME]\r\n".date('Y-m-d H:i:s')."\r\n".$log."\r\n", FILE_APPEND);
    }
    /**
     * zip解压方法
     * @param string $filePath 压缩包所在地址 【绝对文件地址】d:/test/123.zip
     * @param string $path 解压路径 【绝对文件目录路径】/test
     * @return bool
     */
    function unzip(string $filePath, string $path):bool
    {
        # 判断文件是否存在
        if (!is_file($filePath)){
            return false;
        }
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            $zip->extractTo($path);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}