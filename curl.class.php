<?php
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

/**
 * get_cookie() 函数用来获取相应头部中的 Cookie
 * @param  string $sArg 可能包含 Cookie 的字符串
 * @return string 		返回所有匹配的 Cookie 字符串
 */
function get_cookie($sArg=''){
	preg_match_all('/Cookie: (.*);/iU', $sArg, $aCookie);
	for($i=0, $sCookie=''; $i<count($aCookie[1]); $i++){
		if(strpos($sCookie, $aCookie[1][$i])) continue;
		if($i != 0) $sCookie .= '; ';
		$sCookie .= $aCookie[1][$i];
	}
	return $sCookie;
}

/**
 * get_location() 函数用来获取相应头部中 Location: 的内容
 * @param  string $sArg 可能包含 Location: 的字符串
 * @return string 		返回所有匹配的 Location: 字符串
 */
function get_location($sArg=''){
	preg_match('/Location: (.*)/', $sArg, $aLocation);
	return $aLocation[1];
}

/**
 * geterror() 函数用来获取错误编码
 */
function geterror($sArg=''){
	preg_match('/alert(.*?);/', $sArg, $alert);
	return $alert[1];	
}

/**
 * getName() 函数用来获取姓名
 */
function getName($sArg=''){
	preg_match('/<span id=\"xhxm\">(.*)<\/span>/', $sArg, $name);
	return $name[1];
}

/**
 * gb2312() 函数用来获取转化字符编码gb2312
 */
function gb2312($sArg=''){
	$string=iconv('gb2312', 'utf-8', $sArg);
	return $string;
}

/**
 * utf8() 函数用来获取转化字符编码utf8
 */
function utf8($sArg=''){
	$string=iconv('utf-8', 'gb2312', $sArg);
	return $string;
}

/**
 * getSemester() 函数用来获取学年
 */
function getSemester($sArg=''){
	preg_match_all('/<option.*?>\d{4}-\d{4}<\/option>/', $sArg, $semester);
	return $semester[0];
}

/**
 * getxq() 函数用来获取学期
 */
function getxq($sArg=''){
	preg_match_all('/<option.*?>\d{4}-\d{4}<\/option>/', $sArg, $semester);
	return $semester[0];
}

/**
 * getCjList() 函数用来获取成绩列表
 */
function getCjList($sArg=''){
	preg_match_all('/<tr.*?>\s+(<td.*?>([^<>]*)<\/td>){10}\s+<\/tr>/', $sArg, $cjlist);
	return $cjlist[0];
}

/**
 * getCjInfo() 函数用来获取成绩信息
 */
function getCjInfo($sArg=''){
	preg_match_all('/<td.*?>([^<>]*)<\/td>/', $sArg, $cjinfo);
	return $cjinfo[1];		
}


function getUrl($ip){
	$curlArg = array(
	    'url'=>$ip,
		'method'=>'get',
		'responseHeaders'=>1
	);
	$result = curl_request($curlArg);
	return $result;
}

function code($ip,$session){
	$curlArg = array(
	    'url'=>$ip.'/CheckCode.aspx',
		'method'=>'get',
		'cookie'=>$session,
		'responseHeaders'=>0
	);
	$result = curl_request($curlArg);
	return $result;
}



function code2($ip,$session){
	$curlArg = array(
	    'url'=>'http://'.$ip.'/('.$session.')/CheckCode.aspx',
		'method'=>'get',
		'responseHeaders'=>0
	);
	$result = curl_request($curlArg);
	return $result;
}

/**
 * unicode_decode() 函数用来解码加密的 unicode 字符串
 * @param  string $sArg 加密的 unicode 字符串
 * @return string 		解码后的字符串
 */
function unicode_decode($sArg=''){
	return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
		create_function(
			'$matches',
			'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
		),
		$sArg
	);
}