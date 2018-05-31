<?php
session_start();
/**
* 教务系统查询成绩类
*/
class Score  extends Base
{
	//学期
    protected $xn = '';

 	//学年
    protected $xq = '';

	//学号
    protected $number = '';

	//密码
    protected $password = '';

	//验证码
    protected $captcha = '';

	//成绩信息
    protected $scoreinfo = '';


    //构造函数
	function __construct($xueqi='', $number='', $password='', $captcha='')
	{
		//参数设置
		$arr = explode("*",$xueqi);
		$this->xn = $arr[0];
		$this->xq = $arr[1];
		$this->number = $number;
		$this->password = $password;
		$this->captcha = $captcha;
		//获取登录viewstate
		$VIEWSTATE = $this->viewstate();
		//判断是否获取姓名
		if(empty($this->name($VIEWSTATE))){
			exit(json_encode(['code'=>'200','error'=>'教务系统请求出错！']));
		}
		//获取成绩查询viewstate
		$VIEWSTATE = $this->viewstate_cjcx();
		//查询成绩信息
		$result = $this->score($VIEWSTATE);
		//处理解析成绩信息
		$arr = $this->info($result);
		//构建成绩信息输出
		$this->display();
	}


	/**
	 * get_viewstate() 函数用来获取查询成绩viewstate
	 */
	protected function viewstate_cjcx()
	{
		$curlArg = array(
			'url'=>$_SESSION['version'] == 'new' ? $_SESSION['ip'].'/'."xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605" :
			$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605",
			'method'=>'get',
			'responseHeaders'=>0,
			'cookie'=>$_SESSION['sessionId'],
			'referer'=>$_SESSION['version'] == 'new' ? $_SESSION['ip'].'/'."xs_main.aspx?xh=".$this->number :
			$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xs_main.aspx?xh=".$this->number,
		);
		$result = curl_request($curlArg);
		preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$match);
		return $match[1][0];
	}

	/**
	 * score() 函数用来获取查询成绩信息
	 */
	protected function score($VIEWSTATE='')
	{
		$default = array('__EVENTTARGET'=>'','__EVENTARGUMENT'=>'', '__VIEWSTATE'=>$VIEWSTATE,'hidLanguage'=>'','ddlXN'=>$this->xn,'ddlXQ'=>$this->xq,'ddl_kcxz'=>'','btn_xq'=>urlencode(mb_convert_encoding('学期成绩','gb2312','utf-8'))
		);
		$aArg = array_merge($default);
		$curlArg = array(
			'url'=>$_SESSION['version'] == 'new' ? $_SESSION['ip'].'/'."xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605" :
			$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605",
			'method'=>'post',
			'responseHeaders'=>0,
			'cookie'=>$_SESSION['sessionId'],
			'data'=>$aArg,
			'referer'=>$_SESSION['version'] == 'new' ? $_SESSION['ip'].'/'."xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605" :
			$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N121605",
		);
		$result = curl_request($curlArg);
		return $result;
	}

	/**
	 * info() 函数用来处理解析成绩信息
	 * @return array      返回请求结果，数组
	 */
	protected function info($result='')
	{
		preg_match_all('/<tr.*?>\s+(<td.*?>([^<>]*)<\/td>){10}\s+<\/tr>/', $result, $list);
		if (count($list[0]) > 0 && is_array($list[0])) {
			for ($i=1; $i < count($list[0]); $i++) {
				preg_match_all('/<td.*?>([^<>]*)<\/td>/', $list[0][$i], $match);
				$score_info[$i]=$match[1];
			}
			$score_total = '';
			$score_credits = '';
			$score_point = '';
			for ($i=1; $i <= count($score_info); $i++){
				$score_total += $score_info[$i]['8'];
				$score_credits += $score_info[$i]['6'];
				$score_point += $score_info[$i]['7'];
			}
			$score_aver_total = $score_total / (count($score_info)-1);
			$score_aver_point = $score_point / (count($score_info)-1);
		}else{
			exit(json_encode(['code'=>'600','error'=>'没有查询到相关成绩信息！']));
		}
		$this->scoreinfo = ['score_info'       => $score_info, 
							'score_total'      => $score_total, 
							'score_credits'    => $score_credits, 
							'score_point'      => $score_point, 
							'score_aver_total' => $score_aver_total,
							'score_aver_point' => $score_aver_point];
	}

	/**
	 * display() 函数用来输出成绩信息
	 * @return json      返回请求结果，json格式
	 */
	protected function display()
	{
		$score_info = $this->scoreinfo['score_info'];
		if (is_array($score_info) && !empty($score_info)) {
			$str="";
			$str.='<div class="weui-header bg-blue"><div class="weui-header-left"> <a href="" class="icon icon-109 f-white">重新查询</a>  </div><h1 class="weui-header-title">成绩单</h1><div class="weui-header-right">简易版</div></div><div class="page-hd"><h1 class="page-hd-title">'.gb2312($this->name).'</h1><p class="page-hd-desc">'.$this->xn.'学年 第'.$this->xq.'学期'.'</p><p class="page-hd-desc">成绩总和：'.$this->scoreinfo['score_total'].'</p></div><p class="page-hd-desc">　 学期所获学分：'.$this->scoreinfo['score_point'].'</p></div><p class="page-hd-desc">　 平均成绩：'.round($this->scoreinfo['score_aver_total'], 1).'</p></div><p class="page-hd-desc">　 平均绩点：'.round($this->scoreinfo['score_aver_point'], 1).'</p></div>';
			for ($i=1; $i < count($score_info) ; $i++) {
				$str.='<div class="weui-form-preview"><div class="weui-form-preview-hd"><label class="weui-form-preview-label">课程名称</label><em style="font-size:15px;" class="weui-form-preview-value">'.gb2312($score_info[$i][3]).'</em></div><div class="weui-form-preview-bd"><p><label class="weui-form-preview-label">成绩</label><span class="weui-form-preview-value"><b style="color:';
				$str.= gb2312($score_info[$i][8])>=60 ? 'green' : 'red';
				$str.='">'.gb2312($score_info[$i][8]).'</b></span></p><p><label class="weui-form-preview-label">课程代码</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][2]).'</span></p><p><label class="weui-form-preview-label">课程性质</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][4]).'</span></p><p><label class="weui-form-preview-label">学分</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][6]).'</span></p><p><label class="weui-form-preview-label">绩点</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][7]).'</span></p>';
				if(gb2312($score_info[$i][10])!='&nbsp;'){
					$str.='<p><label class="weui-form-preview-label">补考成绩</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][10]).'</span></p>';
				}
				if(gb2312($score_info[$i][11])!='&nbsp;'){
					$str.='<p><label class="weui-form-preview-label">重修成绩</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][11]).'</span>';
				}
				$str.='</p><p><label class="weui-form-preview-label">开课学院</label><span class="weui-form-preview-value">'.gb2312($score_info[$i][12]).'</span></p></div></div>';
			}
			$str.='<table style="display:none;" class="weui-table weui-border-tb"><thead><tr><th style="width:80%;">课程名称</th><th>成绩</th></tr></thead><tbody>';
			for ($i=1; $i < count($score_info) ; $i++) {
				$str.='<tr><td>'.gb2312($score_info[$i][3]).'</td><td><b style="color:';
				$str.= gb2312($score_info[$i][8])>=60 ? 'green' : 'red';
				$str.='">'.gb2312($score_info[$i][8]).'</b></td></tr>';
			}
			$str.='</tbody></table>';
		}else{
			exit(json_encode(['code'=>'600','error'=>'没有查询到相关成绩信息！']));
		}
		exit(json_encode(['data'=>$str]));
	}
}








/**
* 教务系统查询基础类
*/
class Base
{
	//姓名
    protected $name = '';

	/**
	 * viewstate() 函数用来登录viewstate
	 */
	protected function viewstate()
	{
		$curlArg = array(
			'url'=> $_SESSION['version'] == 'new' ? $_SESSION['ip'] : $_SESSION['ip'].'/('.$_SESSION['sessionId'].")/default2.aspx",
			'method' => 'get',
			'responseHeaders' => 0,
		);
		$result = curl_request($curlArg);
		preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/', $result, $match);  //唯一识别码
		return $match[1][0];
	}

	/**
	 * name() 函数用来获取并设置姓名
	 */
	protected function name($VIEWSTATE='')
	{
		$default = array('__VIEWSTATE' => $VIEWSTATE, 'txtUserName' => $this->number, 'TextBox2' => $this->password, 'txtSecretCode' => $this->captcha, 'RadioButtonList1' => '%D1%A7%C9%FA', 'Button1' => '', 'lbLanguage' => '');
		$aArg = array_merge($default);
		$curlArg = array(
			'url'=>$_SESSION['version'] == 'new' ? $_SESSION['ip'].'/'."default2.aspx" : $_SESSION['ip'].'/('.$_SESSION['sessionId'].")/default2.aspx",
			'method'=>'post',
			'responseHeaders'=>0,
			'data'=>$aArg,
			'cookie'=>$_SESSION['sessionId'],
			'referer'=>$_SESSION['ip'],
		);
		$result = curl_request($curlArg);
		$this->error(strip_tags($result));
		preg_match('/<span id=\"xhxm\">(.*)<\/span>/', $result, $name);
		$this->name = mb_substr($name[1],0,-2,'utf8');
		return $this->name;
	}
	/**
     * 处理错误信息
     */
    protected function error($result) {
		preg_match('/alert(.*?);/', $result, $error);
		if (empty($error[1])) {
			return true;
		}
        switch (gb2312($error[1])) {
            case "('用户名不存在或未按照要求参加教学活动！！')":
                exit(json_encode(['code'=>'300','error'=>'用户名不存在或未按照要求参加教学活动！']));
            case "('验证码不正确！！')":
                exit(json_encode(['code'=>'400','error'=>'验证码不正确！']));
            case "('密码错误！！')":
                exit(json_encode(['code'=>'500','error'=>'密码错误！']));
            default:
                return true;
        }
    }
}



//判断请求参数
if(empty($_POST['xueqi'])||empty($_POST['number'])||empty($_POST['password'])||empty($_POST['yzm'])){
	exit(json_encode(['code'=>'100','error'=>'请求出错！']));
}
//判断服务器加载
if(empty($_SESSION['sessionId'])||empty($_SESSION['ip'])){
	exit(json_encode(['code'=>'200','error'=>'教务系统请求出错！']));
}

//cookie记录用户名  有效期30天
setCookie("number", $_POST['number'], time()+3600*24*30);
//实例化
$score = new score($_POST['xueqi'], $_POST['number'], $_POST['password'], $_POST['yzm']);







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
