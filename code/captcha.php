<?php
/**
 * Created by sublime text.
 * User: 邢益堂
 * Date: 2018/05/17
 * Time: 17:28
 */

//引入自动加载
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/function.php');

//引入验证码识别程序
use CAPTCHAReader\src\App\IndexController;

//开启session
session_start();

//教务系统网址，修改成自己学校的正方教务系统网址
$_SESSION['ip'] = "http://210.36.247.18/";

//默认使用新版
$_SESSION['sessionId'] = get_cookie(get_url($_SESSION['ip']));

//判断目录并创建目录
if (!is_dir('cache'))
{
	@mkdir('cache', 777);
}

//设置验证码图片缓存名称
$imgurl = 'cache/'.md5(time()).'.gif';

//判断教务新旧版
if (empty($_SESSION['sessionId']))
{
    $_SESSION['version'] = 'old';
    //获取旧版sessionId
    $_SESSION['sessionId'] = substr(get_location(get_url($_SESSION['ip'])), 2, 24);

    //保存图片
	file_put_contents($imgurl, captcha_old($_SESSION['ip'], $_SESSION['sessionId']));
}else{
    $_SESSION['version'] = 'new';
    //保存图片
    file_put_contents($imgurl, captcha_new($_SESSION['ip'], $_SESSION['sessionId']));
}

// //启动验证码识别程序
$app = new IndexController();

//识别的验证码
$captcha = $app->entrance($imgurl, 'online');

//当前网站地址目录
$url = dirname("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);

//图片地址
$image = rtrim($url.'/sample/TempSamples/'.pathinfo($app->image, PATHINFO_BASENAME), '/');

//删除缓存的验证码图片
if (is_file($imgurl))
{
	@unlink ($imgurl);
}

//删除识别的验证码  1/50的概率触发
(!(rand(1, 50) == '6')) ? : del_dir('sample/TempSamples/');

//返回json数据
exit(json_encode(['captcha'=>$captcha, 'image'=>$image]));
