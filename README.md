# 
normphp-helper normphp脚手架助手
## 特点需知
* normphp-helper 不包括nginx、MySQL等服务，只是单纯的多版本php环境支持
* 脚手架助手的开发初衷是尽可能的保证PHP开发人员本地开发电脑的干净整洁，php项目的运行统一使用中心开发服务器DNMP运行保证开发运行环境与生产运行环境无差异
* 简单说明一下本脚手架助手的使用场景：
  * 不考虑或者不常用本地环境执行php项目
  * 本地需要执行composer命令行操作
  * 需要快速切换php[7.1|7.2|7.3|7.4|8.1]执行php、composer相关操作
  * 适合normphp框架使用者、开发者
## 快速入门
### 注意事项：
    * 在安装normphp-helper时会自动重写当前系统的PHP环境变量为PHP8.1+
    * normphp-helper驱动的PHP程序是完全由PHP8.1语法编写无法向下兼容PHP7系列
    * 不影响您在此环境下编写PHP7系列的项目支持
### 初始化安装流程
  

    1、克隆normphp-helper项目
        git clone git@github.com:normphp/normphp-helper.git
    2、下载php8.1安装包
        通过百度云下载（百度云VIP推荐）链脚手架助手接：https://pan.baidu.com/s/1RxlNC5ZEHn4Hr4kzZtvCaQ 提取码：norm
        通过PHP官网下载（没有梯子下载缓慢，使用迅雷非会员可快速下载）链接：https://windows.php.net/downloads/releases/latest/php-8.1-nts-Win32-vs16-x64-latest.zip
    3、安装PHP8.1
        解压下载的php-8.1-nts-Win32-vs16-x64-latest.zip文件到normphp-helper\php\8.1\x86\目录
    4、双击normphp-helper\init.bat文件弹出CMD命令行窗口根据提示敲回车键确认即可

    至此normphp-helper脚手架助手初始化安装完成
        
### 简单使用攻略
* 如何启动命令行
    * 可使用CMD、windows PowerShell、Git Bash Here、PhpStorm Terminal（推荐使用git）打开命令行窗口
    * 在命令行窗口执行normphp即可查看助手工具的简单使用说明如下结果：


    $> normphp
        帮助信息 [x1|x2|x3] 代表参数可选择的范围
        -v   查看当前normphp-helper版本
        -i   查看当前可用信息
        -php   php相关操作如下载不同版本php、切换php版本
            run   运行对应版本php： -php run [7.3|7.4|8.1] [对应的php命令|composer+命令|phpunit+命令]
            install   安装对应版本php： -php install [7.3|7.4|8.1|all] [OFFICIAL|CLOUD]
            update    更新对应版本php： -php update [7.3|7.4|8.1|all] [OFFICIAL|CLOUD]
            switch    切换php环境变量到对应版本： -php switch [7.3|7.4|8.1]
* 关于PHP8.1之外的7.3、7.4版本安装问题
    * 精力有限只支持7.3和7.4
    * 由于使用命令行安装时是从PHP官网下载会出现缓慢或者下载错误，推荐使用迅雷下载（地址更新于2021-02-24）：
        * 7.3  https://windows.php.net/downloads/releases/php-debug-pack-7.3.27-nts-Win32-VC15-x86.zip
        * 7.4  https://windows.php.net/downloads/releases/php-debug-pack-7.4.15-nts-Win32-vc15-x86.zip
        * 把下载下来的压缩包重命名为php版本号如7.3.zip或者7.4.zip，然后复制到normphp-helper\uploads\x86\目录下
    * 可以直接执行nromphp -php install 7.3|7.4 安装对应的PHP7系列
        * 该安装命令会解压安装包到对应的运行目录normphp-helper\php\7.x\x86\
        * 该安装命令会自动化和安装常用的PHP扩展包括redis、ssh2、xlswriter等
    * 如果觉得过程麻烦：
        * 我会每过一段时间把已经配置好PHP全系列的安装包发布到百度云网盘，您可下载解压然后执行一次normphp-helper\init.bat初始化就可以使用（由于包括全系列PHP因此压缩包比较大）。
        * 百度云网盘下载地址（更新时间：2021-02-24）：
* 关于php命令|composer+命令|phpunit+命令
        

        PHP命令
            nromphp -php run 这里写对应的不包括[7.3|7.4|8.1]   然后是php命令行参数
            示例 查看对应php7.4 安装的扩展
            nromphp -php run 7.4 -m
        composer命令（如没有安装composer助手会自动安装并且注册环境变量）
            先进入到需要执行composer命令的目录
            nromphp -php run 这里写对应的不包括[7.3|7.4|8.1] composer 然后是composer命令行参数
            示例 使用php7.4 执行composer install 命令
            nromphp -php run 7.4 composer install
        phpunit命令（如没有安装phpunit助手会自动安装并且注册环境变量）
            nromphp -php run 这里写对应的不包括[7.3|7.4|8.1] phpunit 然后是phpunit命令行参数
            示例 使用php7.4 执行phpunit   --version 命令查看版本
            nromphp -php run 7.4 phpunit  --version
* 目录简介
 ~~~
normphp-helper  克隆或者下载解压得到的项目目录
├─composer                    composer安装目录、命令行转换脚本
│  ├─composer.phar            composer可执行文件通常会自动下载
│  ├─composer                 composer /bin/sh 命令行转换脚本
│  ├─composer.bat             composer cmd 命令行转换脚本
├─drive                     脚手架驱动文件由php编写实现
│  ├─CliProgressBar.php     CliProgressBar 进度条
│  ├─execute.php            实现执行命令行驱动类
│  ├─ProgressBar.php         ProgressBar 进度条
│  ├─RequestDownload.php    公共资源下载驱动类
├─helper                     normphp框架安装目录（复杂命令行由normphp框架composer生态包括normphp/normphp-helper-tool提供实现项目地址https://github.com/normphp/normphp-helper-tool）
│  ├─composer.json           normphp框架脚手架版composer.json文件
├─log                       脚手架日志目录
├─php                       各版本php安装目录
│  ├─7.x
│  │  ├─extension.ini         扩展配置模板文件
│  │  ├─php.ini               phpini配置模板文件（PHP8.1才有）
├─phpunit                     phpunit安装目录、命令行转换脚本
│  ├─phpunit.phar             phpunit 可执行文件通常会自动下载
│  ├─phpunit                  phpunit /bin/sh 命令行转换脚本
│  ├─phpunit.bat              phpunit cmd 命令行转换脚本
├─uploads                     命令行下载文件临时保存目录
├─execute.php         脚手架命令行人口文件所有简单命令都是通过这里进入再执行的
├─init.bat          脚手架初始化脚本，在第一次使用时执行（执行前请确保正确安装PHP8.1）
├─install-vc14.bat         vc14运行库安装脚本（通常不需要安装）
├─update-normphp.bat         复杂命令实现项目的更新（helper命令下项目）
注意：命令结构可能会更新变化
~~~
## 一些资源地址
        php下载地址
        https://windows.php.net/download/
        php扩展
        https://pecl.php.net/
        phpsdk
        https://github.com/microsoft/php-sdk-binary-tools/archive/php-sdk-2.2.0.zip
        https://github.com/microsoft/php-sdk-binary-tools/tags
