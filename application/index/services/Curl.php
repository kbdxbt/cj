<?php
namespace app\index\services;

class Curl
{
    /**
     * getHome() 函数用来获取首页
     */
    public static function getHome($url, $header = 1)
    {
        $curlArg = array(
            'url'=> $url,
            'method' => 'get',
            'responseHeaders' => $header,
        );
        $result = curl_request($curlArg);
        return encoding($result);
    }

    /**
     * getNewCaptcha() 函数用来获取新版教务系统验证码
     * @param  string $ip URL地址
     * @param  string $sessionId 对应的sessionID
     * @return string 返回相应URL返回的内容
     */
    public static function getNewCaptcha($ip, $sessionId)
    {
        $curlArg = array(
            'url' =>$ip.'CheckCode.aspx',
            'method' => 'get',
            'cookie' => $sessionId,
            'responseHeaders' => 0
        );
        $result = curl_request($curlArg);
        return $result;
    }

    /**
     * getCaptchaOld() 函数用来获取旧版教务系统验证码
     * @param  string $ip URL地址
     * @param  string $sessionId 对应的sessionID
     * @return string 返回相应URL返回的内容
     */
    public static function getOldCaptcha($ip, $sessionId)
    {
        $curlArg = array(
            'url' => $ip.'CheckCode.aspx',
            'method' => 'get',
            'responseHeaders' => 0
        );
        $result = curl_request($curlArg);
        return $result;
    }

    /**
     * getMain() 函数用来获取教务系统主页面
     */
    public static function getMain($url, $aArg, $token)
    {
        $curlArg = array(
            'url' => $url,
            'method' => 'post',
            'responseHeaders' => 0,
            'data' => $aArg,
            'cookie' => $token
        );
        $result = curl_request($curlArg);
        return encoding($result);
    }

    /**
     * getBase 获取基本请求页面
     */
    public static function getBase($url, $aArg, $token, $refer)
    {
        $curlArg = array(
            'url' => $url,
            'method' => $aArg ? 'post' : 'get',
            'responseHeaders' => 0,
            'data' => $aArg,
            'cookie' => $token,
            'referer'=> $refer
        );
        $result = curl_request($curlArg);
        return encoding($result);
    }

    /**
     * getContent() 函数用来主页公告内容
     */
    public static function getContent($url, $token)
    {
        $curlArg = array(
            'url'=> $url.'/content.aspx',
            'method' => 'get',
            'cookie'=> $token,
            'responseHeaders' => 0,
        );
        $result = curl_request($curlArg);
        return encoding($result);
    }
}
