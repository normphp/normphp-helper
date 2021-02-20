<?php

/**
 * Class Request
 */
class RequestDownload
{

    /**
     * CURL下载文件 成功返回文件名，失败返回false
     * @param string $url 下载地址
     * @param string $fileName 保存的文件名
     * @param string $savePath
     * @param array $header
     * @param bool $restart
     * @return bool|string
     * @throws Exception
     * @author Zou Yiliang
     */
    public  function downFile(string $url, string $fileName,$savePath = './uploads/',$header=[],bool$restart=false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 跳过证书验证（https）的网站无法跳过，会报错
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        # 开启进度条
        curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        # 进度条的触发函数
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'downloadProgress'));
        curl_setopt($ch, CURLOPT_HEADER, TRUE);  //需要response header
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);  //需要response body
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);  //当根据Location:重定向时，自动设置header中的Referer:信息。
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  //跟随重定向

        $output = curl_exec($ch);
        $getinfo = curl_getinfo($ch); //获取请求信息
        $error = curl_error($ch);
        if (!empty($error) && !$restart ){
            $this->downloadFile(url: $url,fileName: $fileName,savePath:$savePath ,header:$header,restart:true);
        }else if (!empty($error)&& $restart) {
            throw new \Exception($error);
        }
        $output = str_replace('HTTP/1.1 100 Continue'.PHP_EOL,'',$output);
        /**
         * 分类$output 获取获取头部信息和主体信息
         */
        if (empty($output)){
            $header = '';
            $body = '';
        }else{
            $outputArr = explode("\r\n\r\n", $output);
            $header = $outputArr[0]??'';
            $body = isset($outputArr[2])?$outputArr[2]:$outputArr[1];
        }
        $header = explode("\n", $header);
        curl_close($ch);
        if (empty($body??'')){return false;}
        //处理目录
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
            @chmod($savePath, 0777);
        }
        if (file_put_contents($savePath.$fileName, $body)) {
            return $savePath.$fileName;
        }
        return false;

    }
    /**
     * 模式
     * @var string
     */
    public string $progressBarPattern = 'cli';
    /**
     * 是否在显示下载进度条
     * @var bool
     */
    public bool $startTheDownload = false;
    /**
     * @var bool
     */
    public bool $startTheDownloadEOL = false;

    /**
     * 进度条下载.
     * @param $ch
     * @param int $countDownloadSize 总下载量
     * @param int $currentDownloadSize 当前下载量
     * @param $countUploadSize
     * @param $currentUploadSize
     * @return false
     * @throws Exception
     */
    public function  downloadProgress($ch, int $countDownloadSize, int $currentDownloadSize, $countUploadSize, $currentUploadSize)
    {
        try {
            if ($countDownloadSize !==0){
                if ($this->progressBarPattern ==='cli'){
                    (new ProgressBar())->init(40,'B')->output($countDownloadSize,$currentDownloadSize);
                }else{
                    (new ProgressBar())->init(40,'B')->percentageOutput($countDownloadSize,$currentDownloadSize);
                }
                $this->startTheDownload = TRUE;
                $startTheDownloadEOL= true;
            }else{
                if ($this->startTheDownload && $this->startTheDownloadEOL){
                    $this->startTheDownloadEOL=false;
                    if ($this->progressBarPattern ==='cli'){
                        //echo PHP_EOL;
                        return true;
                    }
                }else{
                    if ($this->progressBarPattern ==='cli'){
                        echo "\033[300D 正在 请求\重定向 下载地址";
                    }
                }
            }
        }catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
            return false;
        }
        return false;
    }
}
