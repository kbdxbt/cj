<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * curl_request() 函数用来进行远程 http 请求
 * @param  array $aArg 设置请求的参数，可最多包含下面 (array)$default 中所有的键值对
 * @return string      返回请求结果，结果是字符串
 */
function curl_request($aArg=array())
{
    /* 定义默认的参数 */
    $default = array(
        'url'=>'', //远程请求的页面, 等同于 html 表单中的 action;
        'method'=>'get', //数据传输方式: post 和 get(默认);
        'data'=>'', //HTTP请求中的数据, 支持数组和 name=value 方式的 url 查询字符串, 要发送文件，在文件名前面加上@前缀并使用完整路径。;
        'cookie'=>'', //HTTP请求中"Cookie: "部分的内容。多个cookie用分号分隔，分号后带一个空格(例如， "fruit=apple; colour=red");
        'referer'=>'', //HTTP请求头中"Referer: "的内容
        'userAgent'=>'', //HTTP请求中包含一个"User-Agent: "头的字符串
        'requestHeaders'=>array(), //用来设置 HTTP 请求头部字段的数组，形式： array('Content-type: text/plain', 'Content-length: 100')
        'sessionCookie'=>false, //传输 cookie 时仅传输 Session Cookie
        'autoReferer'=>true, //当根据 Location: 重定向时自动填写头部 Referer: 信息
        'responseHeaders'=>false, //也将响应头部返回在文件流中
        'sslVerify'=>false, //SSL 安全证书验证
        'timeout'=>30, //设置超时;
        'username'=>'', //http 登录方式的用户名;
        'password'=>'' //http 登录方式的密码;
        );
    $aArg = array_merge($default, $aArg);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $aArg['url']);
    curl_setopt($ch, CURLOPT_COOKIESESSION, $aArg['sessionCookie']);
    curl_setopt($ch, CURLOPT_AUTOREFERER, $aArg['autoReferer']);
    curl_setopt($ch, CURLOPT_HEADER, $aArg['responseHeaders']);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/4");
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $aArg['timeout']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(strtolower($aArg['method']) == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aArg['data']);
    }else{
        if($aArg['data'] && (is_string($aArg['data']) || is_array($aArg['data']))) {
            $aArg['url'] .= (preg_match('/\?/', $aArg['url']) ? '&' : '?') . (is_string($aArg['data']) ? $aArg['data'] : http_build_query($aArg['data']));
        }
    }
    if($aArg['sslVerify']){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
    }else{
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    if($aArg['cookie']) curl_setopt($ch, CURLOPT_COOKIE, $aArg['cookie']);
    if($aArg['referer']) curl_setopt($ch, CURLOPT_REFERER, $aArg['referer']);
    if($aArg['userAgent']) curl_setopt($ch, CURLOPT_USERAGENT, $aArg['userAgent']);
    if(!empty($aArg['requestHeaders'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $aArg['requestHeaders']);
    if($aArg['username'] && $aArg['password']) curl_setopt($ch, CURLOPT_USERPWD, '['.$aArg['username'].']:['.$aArg['password'].']');
    $data = curl_exec($ch);
    if (curl_errno($ch)) return curl_error($ch);
    curl_close($ch);
    return $data;
}

/**
*
* @param mixed $data
* @param string $msg
* @param int $code
*/
function ajaxReturn($data = null, $msg = '', $code = 0)
{
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $res = [
        'data' => is_null($data) ? new \stdClass() : $data,
        'msg' => $msg,
        'code' => $code
    ];
    echo json_encode($res);
    exit;
}


/**
 * encoding() 函数用来获取转化字符编码
 */
function encoding($sArg=''){
    $string=iconv("gbk","utf-8//IGNORE",$sArg);
    return $string;
}


/**
 * transcoding() 函数用来获取转化字符编码utf8
 */
function utf8_encoding($sArg=''){
    $string = urlencode(mb_convert_encoding($sArg, 'gb2312', 'utf-8'));
    return $string;
}

/**
 *随机字符
 * @param  [type]  $len     生成长度
 * @param  integer $type    生成类型 1数字,2大写字母,3小写字母,4混合
 * @param  boolean $special 是否加入特殊字符
 * @return [type]           string
 */
function randomStr($len, $type=4, $special=false)
{
    $str1 = 'ABCDEFGHIJKRMNOPQLSTUVWXYZ';
    $str2 = 'abcdefghijkrmnopqlstuvwxyz';
    $str3 = '0123456789';
    $str4 = '~!@#$%^&*()_+=-?.,<>|{}[]';
    $randstr = '';
    switch ($type) {
        case 1:
            $str = $str3;
            break;
        case 2;
            $str = $str1;
            break;
        case 3:
            $str = $str2;
            break;
        default:
            $str = $str1.$str2.$str3;
            break;
    }
    if($special){
        $str .= $str4;
    }
    for($i = 0;$i < $len;$i++){
        $rand = mt_rand(0,strlen($str)-1);
        $randstr .= $str[$rand];
    }
    return $randstr;
}



