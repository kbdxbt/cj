<?php 
	session_start();
	error_reporting(0);
	include 'curl.class.php';

	if(!empty($_POST['number'])){
		if(empty($_POST['xueqi'])||empty($_POST['number'])||empty($_POST['password'])||empty($_POST['yzm'])){
			echo "4";  //参数有误
			return false;
		}
		$_SESSION['txtUserName'] = $_POST['number'];
		$_SESSION['TextBox2'] = $_POST['password'];
		$_SESSION['yzm'] = $_POST['yzm'];
		$arr = explode("*",$_POST['xueqi']);
		$_SESSION['xn'] = $arr[0];
		$_SESSION['xq'] = $arr[1];

	if($_SESSION['version']=='new'){
		$curlArg = array(
			'url'=>$_SESSION['ip'],
			'method'=>'get',
			'responseHeaders'=>0,
		);
		$result = curl_request($curlArg);
		preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);   //唯一识别码
		$_SESSION['VIEWSTATE1']=$VIEWSTATE[1][0];
		$default = array('__VIEWSTATE'=>$_SESSION['VIEWSTATE1'],'txtUserName'=>$_SESSION['txtUserName'], 'TextBox2'=>$_SESSION['TextBox2'],'txtSecretCode'=>$_SESSION['yzm'],'RadioButtonList1'=>'%D1%A7%C9%FA','Button1'=>'','lbLanguage'=>'');
		$_SESSION['host'] = "'Host:".$_SESSION['ip1']."'";
		$aArg = array_merge($default);
		$curlArg = array(
			'url'=>$_SESSION['ip'].'/'."default2.aspx",
			'method'=>'post',
			'responseHeaders'=>0,
			'data'=>$aArg,
			'cookie'=>$_SESSION['sessionId'],
			'referer'=>$_SESSION['ip1'],
			'requestHeaders'=>array(
				$_SESSION['host'],
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; W…) Gecko/20100101 Firefox/55.0',
				'Accept: text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
				'Accept-Encoding: gzip, deflate',
				'Connection: keep-alive',
				'Upgrade-Insecure-Requests: 1'
			)
		);	
		$result = curl_request($curlArg);
		$_SESSION['name']=mb_substr(getName($result),0,-2,'utf8');
		
		@$error=geterror(strip_tags($result));
		if(gb2312($error)=="('用户名不存在或未按照要求参加教学活动！！')"){
			echo "5";  //用户名不存在或未按照要求参加教学活动！！
			return false;
		}
		if(gb2312($error)=="('验证码不正确！！')"){
			echo "6";  //验证码不正确！！
			return false;
		}
		if(gb2312($error)=="('密码错误！！')"){
			echo "7";  //密码错误！！
			return false;
		}

		$curlArg = array(
			'url'=>$_SESSION['ip'].'/'."xs_main.aspx?xh=".$_SESSION['txtUserName'],
			'method'=>'get',
			'cookie'=>$_SESSION['sessionId'],
			'responseHeaders'=>0,
			'referer'=>$_SESSION['ip1'],
			'requestHeaders'=>array(
				$_SESSION['host'],
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; W…) Gecko/20100101 Firefox/55.0',
				'Accept: text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
				'Accept-Encoding: gzip, deflate',
				'Connection: keep-alive',
				'Upgrade-Insecure-Requests: 1'
			)
		);
		$result = curl_request($curlArg);
		//$_SESSION['name']=mb_substr(getName($result),0,-2,'utf8');
		$curlArg = array(
			'url'=>$_SESSION['ip'].'/'."xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
			'method'=>'get',
			'responseHeaders'=>0,
			'cookie'=>$_SESSION['sessionId'],
			'referer'=>$_SESSION['ip'].'/'."xs_main.aspx?xh=".$_SESSION['txtUserName'],
		);
		$result = curl_request($curlArg);
		preg_match_all('/name=\"__EVENTTARGET\" value=\"(.*)\"/',$result,$EVENTTARGET);
		preg_match_all('/name=\"__EVENTARGUMENT\" value=\"(.*)\"/',$result,$EVENTARGUMENT);
		preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
		$_SESSION['EVENTTARGET']=$EVENTTARGET[1][0];
		$_SESSION['EVENTARGUMENT']=$EVENTARGUMENT[1][0];
		$_SESSION['VIEWSTATE']=$VIEWSTATE[1][0];
		
		$xuenian=getSemester($result);
		@$default = array('__EVENTTARGET'=>'','__EVENTARGUMENT'=>'', '__VIEWSTATE'=>$_SESSION['VIEWSTATE'],'hidLanguage'=>'','ddlXN'=>$_SESSION['xn'],'ddlXQ'=>$_SESSION['xq'],'ddl_kcxz'=>'','btn_xq'=>urlencode(mb_convert_encoding(学期成绩,'gb2312','utf-8'))
		);

		$aArg = array_merge($default);
			$curlArg = array(
			'url'=>$_SESSION['ip'].'/'."xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
			'method'=>'post',
			'responseHeaders'=>0,
			'cookie'=>$_SESSION['sessionId'],
			'data'=>$aArg,
			'referer'=>$_SESSION['ip'].'/'."xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
		);

		$result = curl_request($curlArg);
		$cj=getCjList($result);
		for($i=1;$i<=count($cj);$i++){
			@$CjInfo[$i]=getCjInfo($cj[$i]);
		}
		for($i=1;$i<=count($CjInfo);$i++){
			@$cjNum+=$CjInfo[$i]['8'];
		}
		for($i=1;$i<=count($CjInfo);$i++){
			@$cjXf+=$CjInfo[$i]['6'];
		}
		for($i=1;$i<=count($CjInfo);$i++){
			@$cjJd+=$CjInfo[$i]['7'];
		}
		$cjNum=$cjNum;
		$cjXf=$cjXf;
		$cjAver=$cjNum/(count($CjInfo)-1);
		$cjAverJd=$cjJd/(count($CjInfo)-1);
		$chengji=$CjInfo;
		if(empty($CjInfo)){
			echo "8";  //没有查询到相关成绩信息
			return false;
		}
		$str="";
		if($CjInfo){
			$str="";
			$str.='<div class="weui-header bg-blue"><div class="weui-header-left"> <a href="" class="icon icon-109 f-white">重新查询</a>  </div><h1 class="weui-header-title">成绩单</h1><div class="weui-header-right">简易版</div></div><div class="page-hd"><h1 class="page-hd-title">'.gb2312($_SESSION['name']).'</h1><p class="page-hd-desc">'.$_SESSION['xn'].'学年 第'.$_SESSION['xq'].'学期'.'</p><p class="page-hd-desc">成绩总和：'.$cjNum.'</p></div><p class="page-hd-desc">　 学期所获学分：'.$cjXf.'</p></div><p class="page-hd-desc">　 平均成绩：'.round($cjAver,1).'</p></div><p class="page-hd-desc">　 平均绩点：'.round($cjAverJd,1).'</p></div>';

			for ($i=1; $i < count($CjInfo) ; $i++) {
				$str.='<div class="weui-form-preview"><div class="weui-form-preview-hd"><label class="weui-form-preview-label">课程名称</label><em style="font-size:15px;" class="weui-form-preview-value">'.gb2312($CjInfo[$i][3]).'</em></div><div class="weui-form-preview-bd"><p><label class="weui-form-preview-label">成绩</label><span class="weui-form-preview-value">';
				if(gb2312($CjInfo[$i][8])>=60){
					$str.='<b style="color:green">'.gb2312($CjInfo[$i][8]).'</b>';
				}else{
					$str.='<b style="color:red">'.gb2312($CjInfo[$i][8]).'</b>';
				}
					$str.='</span></p><p><label class="weui-form-preview-label">课程代码</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][2]).'</span></p><p><label class="weui-form-preview-label">课程性质</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][4]).'</span></p><p><label class="weui-form-preview-label">学分</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][6]).'</span></p><p><label class="weui-form-preview-label">绩点</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][7]).'</span></p>';
				if(gb2312($CjInfo[$i][10])!='&nbsp;'){
					$str.='<p><label class="weui-form-preview-label">补考成绩</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][10]).'</span></p>';
				}
				if(gb2312($CjInfo[$i][11])!='&nbsp;'){
					$str.='<p><label class="weui-form-preview-label">重修成绩</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][11]).'</span>';
				}
				$str.='</p><p><label class="weui-form-preview-label">开课学院</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][12]).'</span></p></div></div>';
			}
			$str.='<table style="display:none;" class="weui-table weui-border-tb"><thead><tr><th style="width:80%;">课程名称</th><th>成绩</th></tr></thead><tbody>';
			for ($i=1; $i < count($CjInfo) ; $i++) {
				$str.='<tr><td>'.gb2312($CjInfo[$i][3]).'</td><td>';
				if(gb2312($CjInfo[$i][8])>=60){
					$str.='<b style="color:green">'.gb2312($CjInfo[$i][8]).'</b>';
				}else{
					$str.='<b style="color:red">'.gb2312($CjInfo[$i][8]).'</b>';
				}
					$str.='</td></tr>';
			}
			$str.='</tbody></table>';
		}else{
			$str.='<div class="weui_msg hide" id="msg3"><div class="weui_msg_box"><p><i class="icon icon-40 f20 f-green"></i>现在还没有数据</p></div></div>'; 
		}
		print_r($str);
		}elseif($_SESSION['version']=='old'){
			$curlArg = array(
				'url'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/default2.aspx",
				'method'=>'get',
				'responseHeaders'=>0
			);
			$result = curl_request($curlArg);
			preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
			$_SESSION['VIEWSTATE1']=$VIEWSTATE[1][0];

			$default = array('__VIEWSTATE'=>$_SESSION['VIEWSTATE1'],'txtUserName'=>$_SESSION['txtUserName'], 'TextBox2'=>$_SESSION['TextBox2'],'txtSecretCode'=>$_SESSION['yzm'],'RadioButtonList1'=>'%D1%A7%C9%FA','Button1'=>'','lbLanguage'=>'');

			$aArg = array_merge($default);
			$curlArg = array(
				'url'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/default2.aspx",
				'method'=>'post',
				'responseHeaders'=>0,
				'data'=>$aArg,
			);
			$result = curl_request($curlArg);
			$error=geterror(strip_tags($result));
			if(gb2312($error)=="('用户名不存在或未按照要求参加教学活动！！')"){
				echo "5";  //用户名不存在或未按照要求参加教学活动！！
				return false;
			}
			if(gb2312($error)=="('验证码不正确！！')"){
				echo "6";  //验证码不正确！！
				return false;
			}
			if(gb2312($error)=="('密码错误！！')"){
				echo "7";  //密码错误！！
				return false;
			}
			if($error){
				echo "<script type='text/javascript'>alert".$error.";location.href='index.php';</script>";
				exit();
			}
			$curlArg = array(
				'url'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xs_main.aspx?xh=".$_SESSION['txtUserName'],
				'method'=>'get',
				'cookie'=>$_SESSION['sessionId'],
				'responseHeaders'=>0
			);
			$result = curl_request($curlArg);
			$_SESSION['name']=mb_substr(getName($result),0,-2,'utf8');
			$curlArg = array(
				'url'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
				'method'=>'get',
				'responseHeaders'=>0,
				'referer'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xs_main.aspx?xh=".$_SESSION['txtUserName'],
			);
			$result = curl_request($curlArg);
			preg_match_all('/name=\"__EVENTTARGET\" value=\"(.*)\"/',$result,$EVENTTARGET);
			preg_match_all('/name=\"__EVENTARGUMENT\" value=\"(.*)\"/',$result,$EVENTARGUMENT);
			preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
			$_SESSION['EVENTTARGET']=$EVENTTARGET[1][0];
			$_SESSION['EVENTARGUMENT']=$EVENTARGUMENT[1][0];
			$_SESSION['VIEWSTATE']=$VIEWSTATE[1][0];
			$xuenian=getSemester($result);

			@$default = array('__EVENTTARGET'=>'','__EVENTARGUMENT'=>'', '__VIEWSTATE'=>$_SESSION['VIEWSTATE'],'hidLanguage'=>'','ddlXN'=>$_SESSION['xn'],'ddlXQ'=>$_SESSION['xq'],'ddl_kcxz'=>'','btn_xq'=>urlencode(mb_convert_encoding(学期成绩,'gb2312','utf-8')));

			$aArg = array_merge($default);
			$curlArg = array(
				'url'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
				'method'=>'post',
				'responseHeaders'=>0,
				'cookie'=>$_SESSION['sessionId'],
				'data'=>$aArg,
				'referer'=>$_SESSION['ip'].'/('.$_SESSION['sessionId'].")/xscjcx.aspx?xh=".$_SESSION['txtUserName']."&xm=".urlencode(mb_convert_encoding($_SESSION['name'],'gb2312','utf-8'))."&gnmkdm=N121605",
			);
			$result = curl_request($curlArg);
			$cj=getCjList($result);
			//echo $result;
			for($i=1;$i<=count($cj);$i++){
				@$CjInfo[$i]=getCjInfo($cj[$i]);
			}
			for($i=1;$i<=count($CjInfo);$i++){
				@$cjNum+=$CjInfo[$i]['8'];
			}
			for($i=1;$i<=count($CjInfo);$i++){
				@$cjXf+=$CjInfo[$i]['6'];
			}
			for($i=1;$i<=count($CjInfo);$i++){
				@$cjJd+=$CjInfo[$i]['7'];
			}
			$cjNum=$cjNum;
			$cjXf=$cjXf;
			$cjAver=$cjNum/(count($CjInfo)-1);
			$cjAverJd=$cjJd/(count($CjInfo)-1);
			$chengji=$CjInfo;
			if(empty($CjInfo)){
				echo "8";  //没有查询到相关成绩信息
				return false;
			}
			$str="";
			if($CjInfo){
				$str="";
				$str.='<div class="weui-header bg-blue"><div class="weui-header-left"> <a href="" class="icon icon-109 f-white">重新查询</a>  </div><h1 class="weui-header-title">成绩单</h1><div class="weui-header-right">简易版</div></div><div class="page-hd"><h1 class="page-hd-title">'.gb2312($_SESSION['name']).'</h1><p class="page-hd-desc">'.$_SESSION['xn'].'学年 第'.$_SESSION['xq'].'学期'.'</p><p class="page-hd-desc">成绩总和：'.$cjNum.'</p></div><p class="page-hd-desc">　 学期所获学分：'.$cjXf.'</p></div><p class="page-hd-desc">　 平均成绩：'.round($cjAver,1).'</p></div><p class="page-hd-desc">　 平均绩点：'.round($cjAverJd,1).'</p></div>';

				for ($i=1; $i < count($CjInfo) ; $i++) {
					$str.='<div class="weui-form-preview"><div class="weui-form-preview-hd"><label class="weui-form-preview-label">课程名称</label><em style="font-size:15px;" class="weui-form-preview-value">'.gb2312($CjInfo[$i][3]).'</em></div><div class="weui-form-preview-bd"><p><label class="weui-form-preview-label">成绩</label><span class="weui-form-preview-value">';
					if(gb2312($CjInfo[$i][8])>=60){
						$str.='<b style="color:green">'.gb2312($CjInfo[$i][8]).'</b>';
					}else{
						$str.='<b style="color:red">'.gb2312($CjInfo[$i][8]).'</b>';
					}
					$str.='</span></p><p><label class="weui-form-preview-label">课程代码</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][2]).'</span></p><p><label class="weui-form-preview-label">课程性质</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][4]).'</span></p><p><label class="weui-form-preview-label">学分</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][6]).'</span></p><p><label class="weui-form-preview-label">绩点</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][7]).'</span></p>';
					if(gb2312($CjInfo[$i][10])!='&nbsp;'){
						$str.='<p><label class="weui-form-preview-label">补考成绩</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][10]).'</span></p>';
					}
					if(gb2312($CjInfo[$i][11])!='&nbsp;'){
						$str.='<p><label class="weui-form-preview-label">重修成绩</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][11]).'</span>';
					}
					$str.='</p><p><label class="weui-form-preview-label">开课学院</label><span class="weui-form-preview-value">'.gb2312($CjInfo[$i][12]).'</span></p></div></div>';
				}
				$str.='<table style="display:none;" class="weui-table weui-border-tb"><thead><tr><th style="width:80%;">课程名称</th><th>成绩</th></tr></thead><tbody>';
				for ($i=1; $i < count($CjInfo) ; $i++) {
					$str.='<tr><td>'.gb2312($CjInfo[$i][3]).'</td><td>';
					if(gb2312($CjInfo[$i][8])>=60){
						$str.='<b style="color:green">'.gb2312($CjInfo[$i][8]).'</b>';
					}else{
						$str.='<b style="color:red">'.gb2312($CjInfo[$i][8]).'</b>';
					}
					$str.='</td></tr>';
				}
				$str.='</tbody></table>';
			}else{
				$str.='<div class="weui_msg hide" id="msg3"><div class="weui_msg_box"><p><i class="icon icon-40 f20 f-green"></i>现在还没有数据</p></div></div>'; 
			}
			print_r($str);
		}else{
			echo "3";   //服务器出错
			return false;
		}
	}else{
		echo "2";  //请求出错
		return false;
	}

 ?>