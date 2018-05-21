<?php
/**
 * Created by sublime text.
 * User: 邢益堂
 * Date: 2018/05/17
 * Time: 17:28
 */

//引入自动加载
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/function.php');

//引入验证码识别程序
use CAPTCHAReader\src\App\IndexController;

//开启session
session_start();


if($_POST){
	$a = new IndexController();
	$c=$a->entrance($_POST['codeurl'],'online');
	$a->image = str_replace('src\Config/../../','', $a->image);
	$a->image = substr($a->image,'24',strlen($a->image)-11);
	dump($c);
	echo "<img src=".$a->image.">";
	$end_time = microtime(true);//计时停止
	echo '执行时间为：' . ($end_time - $start_time) . ' s' . '<br/>';
}else{

}


/**
 * curl_request() 函数用来进行远程 http 请求
 * @param  array $aArg 设置请求的参数，可最多包含下面 (array)$default 中所有的键值对
 * @return string      返回请求结果，结果是字符串
 */
function curl_request($aArg=array()){
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

?>


<form method="POST">
	<input type="text" name="codeurl">
	<input type="submit" name="" value="提交"><br/>
	请填写正方教务系统验证码的地址（类似“http://210.36.247.23/CheckCode.aspx”），提交即可查询验证码。目前系统只有正方教务系统的训练库， 整体识别正确率 87%，单个字母识别正确率率到 96.5%，每次提交只能测试一次。
</form>