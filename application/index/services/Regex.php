<?php
namespace app\index\services;

class Regex
{

    /**
     * getCookie() 函数用来获取相应头部中的 Cookie
     * @param  string $sArg 可能包含 Cookie 的字符串
     * @return string       返回所有匹配的 Cookie 字符串
     */
    public static function getCookie($sArg='')
    {
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
     * @return string       返回所有匹配的 Location: 字符串
     */
    public static function getLocation($sArg='')
    {
        preg_match('/Location: (.*)/', $sArg, $aLocation);
        return $aLocation[1];
    }

    /**
     * getFrom() 函数用来登录请求参数
     */
    public static function getFrom($sArg='')
    {
        preg_match('/type="text" id="(.*?)" tabindex="1"/', $sArg, $number);
        preg_match('/type="password" id="(.*?)" tabindex="2"/', $sArg, $password);
        preg_match('/type="text" id="(.*?)" tabindex="3"/', $sArg, $captcha);
        return [$number[1], $password[1], $captcha[1]];
    }

    /**
     * getLoginState() 函数用来获取登录状态
     */
    public static function getLoginState($sArg='')
    {
        preg_match('/name=\"__VIEWSTATE\" value=\"([\w\W]*?)\"/', $sArg, $match);  //唯一识别码
        return isset($match[1]) ? $match[1] : '';
    }

    /**
     * getName() 函数用来获取姓名
     */
    public static function getName($sArg='')
    {
        preg_match('/<span id=\"xhxm\">(.*)<\/span>/', $sArg, $result);
        return isset($result[1]) ? mb_substr($result[1], 0, -2, 'utf8') : '';
    }

    /**
     * getError() 函数用来获取错误信息
     */
    public static function getError($sArg='')
    {
        preg_match("/alert([\w\W]*?);/", $sArg, $error);
        return isset($error[1]) ? substr(($error[1]), 2, -2) : '';
    }

    /**
     * getUrl() 函数用来获取链接
     */
    public static function getUrl($sArg='', $name)
    {
        preg_match_all("/<a.*?>(.*?)<\/a>/", $sArg, $list);
        foreach($list[0] as $key => $val)
        {
            foreach ($name as $k => $v) {                
                if (strpos($val, "('".$v."')") !== false) {
                    preg_match("/<a href=\"(.*?)\"/", $val, $match);
                    return isset($match[1]) ? $match[1] : '';
                }
            }
        }
    }

    /**
     * getCjState() 函数用来获取成绩登录状态
     */
    public static function getCjState($sArg='')
    {
        preg_match('/name=\"__VIEWSTATE\" value=\"(.*)\"/', $sArg, $match);
        return isset($match[1]) ? $match[1] : '';
    }

    /**
     * getCjSemester() 函数用来成绩查询学期
     */
    public static function getCjSemester($sArg='')
    {
        preg_match('/<select name="ddlXN" id="ddlXN">([\w\W]*?)<\/select>/', $sArg, $match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/", $match[1], $data);
        return isset($data) ? $data : '';
    }

    /**
     * getCjData() 函数用来获取成绩信息
     */
    public static function getCjList($sArg='')
    {
        preg_match_all('/<tr.*?>\s+(<td.*?>([^<>]*)<\/td>){10}\s+<\/tr>/', $sArg, $list);
        return isset($list) ? $list : '';
    }

    /**
     * getCjList() 函数用来获取成绩信息列表
     */
    public static function getCjData($sArg='')
    {
        preg_match_all('/<td.*?>([^<>]*)<\/td>/', $sArg, $match);
        return isset($match[1]) ? $match[1] : '';
    }

    /**
     * getCjTable() 函数用来获取成绩表格头列表
     */
    public static function getCjTable($sArg='')
    {
        preg_match_all('/<td.*?>([^<>]*)<\/td>/', $sArg, $match);
        return isset($match[0]) ? $match[0] : '';
    }

    /**
     * getKbSemester() 函数用来课表查询学期
     */
    public static function getKbSemester($sArg='')
    {
        preg_match('/<select name="xnd"([\w\W]*?)>([\w\W]*?)<\/select>/', $sArg, $match);
        preg_match_all("/<option([\w\W]*?)>([\w\W]*?)<\/option>/", $match[2], $data);
        return isset($data) ? $data : '';
    }

    /**
     * getXqOption() 函数用来课表查询学期
     */
    public static function getXqOption($sArg='')
    {
        preg_match_all('/<option selected="selected" value="(.*)">/iU',$sArg,$data);
        return isset($data) ? $data : '';
    }

    /**
     * getKbState() 函数用来课表State
     */
    public static function getKbState($sArg='')
    {
        preg_match('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$sArg,$data);
        return isset($data) ? $data : '';
    }

    /**
     * getKbList() 函数用来课表列表
     */
    public static function getKbData($sArg='')
    {
        $table=preg_replace('/<td rowspan="\d" width="\d%">([\w\W]*?)<\/td>/','',$sArg);
        $table=preg_replace('/<td>第([\w\W]*?)节<\/td>/','',$table);
        $table=preg_replace('/<td width="1%">第([\w\W]*?)节<\/td>/','',$table);
        $table=preg_replace("/<font color='red'>([\w\W]*?)<\/font>/",'',$table);
        preg_match_all('/<tr>([\w\W]*?)<\/tr>/',$table,$data);
        return isset($data) ? $data : '';
    }

    /**
     * getKbState() 函数用来课表State
     */
    public static function getKbList($sArg='')
    {
        preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/',$sArg,$data);
        return isset($data) ? $data : '';
    }

    /**
     * getKbcol() 函数用来课表State
     */
    public static function getKbCol($sArg='')
    {
        preg_match('/rowspan="([\w\W]*?)"/',$sArg,$data);
        return isset($data[1]) ? $data[1] : '';
    }

    /**
     * getKbcol() 函数用来课表State
     */
    public static function getKbTime($sArg='')
    {
        preg_match('/第([\w\W]*?)-([\w\W]*?)周/',$sArg,$data);
        return isset($data) ? $data : '';
    }

    /**
     * handldKbData() 函数用来课表单条数据
     */
    public static function handldKbData($data='') {
        if (substr($data, -8) == '<br><br>') {
            $data = substr($data, 0, -8);
        }
        $data = preg_replace('/(<br>){3,}/','<br><br>',$data);
        return $data;
    }
}
