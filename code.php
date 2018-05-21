<?php
    session_start();
    include 'curl.class.php';
    $_SESSION['ip1']="210.36.247.23";
    $_SESSION['ip']="http://".$_SESSION['ip1']."";   
    $_SESSION['sessionId']=get_cookie(getUrl($_SESSION['ip']));   //默认使用新版
    if(empty($_SESSION['sessionId'])){   //判断新旧版
    	$_SESSION['version'] = 'old';
    	$_SESSION['sessionId']=substr(get_location(getUrl($_SESSION['ip1'])),2,24);
		header("content-Type:image/Gif; charset=gb2312");
		echo code2($_SESSION['ip1'],$_SESSION['sessionId']);
    }else{
    	$_SESSION['version'] = 'new';
    	header("content-Type:image/Gif; charset=gb2312");
    	echo code($_SESSION['ip'],$_SESSION['sessionId']);
    }
    
