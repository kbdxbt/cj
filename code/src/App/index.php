<?php
/**
 * Created by sublime text.
 * User: 邢益堂
 * Date: 2018/05/17
 * Time: 17:28
 */

//引入自动加载
require_once(__DIR__ . '/../../vendor/autoload.php');

use CAPTCHAReader\src\App\IndexController;
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


?>
<form method="POST">
	<input type="text" name="codeurl">
	<input type="submit" name="" value="提交"><br/>
	请填写正方教务系统验证码的地址（类似“http://210.36.247.23/CheckCode.aspx”），提交即可查询验证码。目前系统只有正方教务系统的训练库， 整体识别正确率 87%，单个字母识别正确率率到 96.5%，每次提交只能测试一次。
</form>