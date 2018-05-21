<?php
/**
* 学期选项填充
*/
date_default_timezone_set("PRC");
$year = (int)date("Y");
$month = (int)date("m");
$str = '';
if ($month >= '10') {
	$str .= '<option value="'.($year).'-'.($year+1).'*1">'.($year).'-'.($year+1).' 第1学期</option>';
} elseif ($month>'3' && $month<'10') {
	$str .= '<option value="'.($year-1).'-'.($year).'*2">'.($year-1).'-'.($year).' 第2学期</option>';
} else {

}
for ($i=1; $i <= 3; $i++) {
	$str .= '<option value="'.($year-$i).'-'.($year-$i+1).'*1">'.($year-$i).'-'.($year-$i+1).' 第1学期</option>
			 <option value="'.($year-$i).'-'.($year-$i+1).'*2">'.($year-$i).'-'.($year-$i+1).' 第2学期</option>
			 <option value="'.($year-$i).'-'.($year-$i+1).'*3">'.($year-$i).'-'.($year-$i+1).' 第3学期</option>';
}
!empty($_COOKIE['number']) ? ($number = $_COOKIE['number']) : ($number = '');
exit(json_encode(['data'=>$str,'number'=>$number]));


