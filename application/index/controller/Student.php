<?php
namespace app\index\controller;

use \think\Log;
use \app\index\services\Regex;
use \app\index\services\Curl;

class Student extends Base
{
    //学期
    protected $xn = '';

    //学年
    protected $xq = '';

    //周数
    protected $week = '';

    //成绩信息
    protected $scoreinfo = '';

    //课表信息
    protected $scheduleinfo = '';

    // 成绩地址
    private $cj_url = '';

    // 课表地址
    private $kb_url = '';

    // 来源
    private $refer = '';

    // 可访问方法
    //private $action = ['getcjinfo', 'getkbinfo'];

    public function _initialize()
    {
        parent::_initialize();
         // 判断是否可访问
        // if (in_array(strtolower($this->request->action()), $this->action)) {
        //     if(empty(input('xueqi'))){
        //       $this->resultJson('', '学期参数有误！', -1);
        //     }
        //     $xueqi = input('xueqi');
        //     $arr = explode("*", $xueqi);
        //     $this->xn = $arr[0];
        //     $this->xq = $arr[1];
        // }
        if(!empty(input('xueqi'))){
            $xueqi = input('xueqi');
            $arr = explode("*", $xueqi);
            $this->xn = $arr[0];
            $this->xq = $arr[1];
            $this->week = input('week', 1);
        }
        $this->refer = $this->url .'xs_main.aspx?xh='.$this->number;
        $result = Curl::getMain($this->refer, '', $this->token);
        $this->cj_url = $this->url . Regex::getUrl($result, ['成绩查询', '学生成绩查询', '学习成绩查询']);
        $this->kb_url = $this->url . Regex::getUrl($result, ['学生个人课表','个人课表查询']);
        Log::record([$this->cj_url, $this->kb_url]);
    }

    // /**
    //  * getCjInfo() 函数用来获取成绩信息
    //  */
    // public function getCjInfo()
    // {
    //     //获取成绩查询viewstate
    //     $result = Curl::getBase($this->cj_url, '', $this->token, $this->refer);
    //     $cj_state = Regex::getCjState($result);
    //     // 注意变量顺序
    //     $aArg = array(
    //         '__EVENTTARGET' => '',
    //         '__EVENTARGUMENT' => '', 
    //         '__VIEWSTATE' => $cj_state,
    //         'ddl_kcxz' => '',
    //         'ddlXN' => $this->xn,
    //         'ddlXQ' => $this->xq,
    //         'hidLanguage' => ''
    //     );
    //     if (strpos($result, '按学期查询') !== false) {
    //         $aArg['Button1'] = utf8_encoding('按学期查询');
    //     } elseif (strpos($result, '学期成绩') !== false) {
    //         $aArg['btn_xq'] = utf8_encoding('学期成绩');
    //     } else {
    //         $this->resultJson('', '请先配置成绩查询参数', -1);
    //     }
    //     $result = Curl::getBase($this->cj_url, $aArg, $this->token, $this->cj_url);
    //     $list = Regex::getCjList($result);
    //     if (count($list[0]) > 0 && is_array($list[0])) {
    //         for ($i=1; $i < count($list[0]); $i++) {
    //             $data = Regex::getCjData($list[0][$i]);
    //             $score_info[$i] = $data;
    //         }
    //     }else{
    //         $this->resultJson('', '没有查询到相关成绩信息！', -1);
    //     }
    //     $this->resultJson($score_info, '成功');
    // }

    /**
     * getCjInfo() 函数用来获取成绩信息
     */
    public function getCjInfo()
    {
        //获取成绩查询viewstate
        $result = Curl::getBase($this->cj_url, '', $this->token, $this->refer);
        $cj_state = Regex::getCjState($result);
        // 注意变量顺序
        $aArg = array(
            '__EVENTTARGET' => '',
            '__EVENTARGUMENT' => '', 
            '__VIEWSTATE' => $cj_state,
            'ddl_kcxz' => '',
            'ddlXN' => $this->xn,
            'ddlXQ' => $this->xq,
            'hidLanguage' => ''
        );
        if ($this->system == 'old') {
            if (!$this->xn || !$this->xq) {
                $this->getCjOption();
            }
            $aArg['Button1'] = utf8_encoding('按学期查询');
        } elseif (strpos($result, '在校学习成绩查询') !== false) {
            $aArg['Button2'] = utf8_encoding('在校学习成绩查询');
        } elseif (strpos($result, '学期成绩') !== false) {
            $aArg['btn_zcj'] = utf8_encoding('历年成绩');
        } else {
            $this->resultJson('', '请先配置成绩查询参数', -1);
        }
        $result = Curl::getBase($this->cj_url, $aArg, $this->token, $this->cj_url);
        $list = Regex::getCjList($result);
        $header = Regex::getCjTable($list[0][0]);
        $count = 0;
        if ($this->system == 'old') {
            if (count($list[0]) > 0 && is_array($list[0]) && $header) {
                for ($i=1; $i < count($list[0]); $i++) {
                    $count++;
                    $data = Regex::getCjData($list[0][$i]);
                    foreach ($data as $k => $v) {
                        $score_info[$this->xn.'学年第'.$this->xq.'学期'][$count][strip_tags($header[$k])] = $v;
                    }
                }
            }else{
                $this->resultJson('', '没有查询到相关成绩信息！', -1);
            }
            if (!isset($score_info)) {
                $this->resultJson('', '没有查询到相关成绩信息！', -1); 
            }
            $this->resultJson([array_reverse($score_info),'name'=>$this->name]); 
        }
        if (count($list[0]) > 0 && is_array($list[0])) {
            for ($i=1; $i < count($list[0]); $i++) {
                $data = Regex::getCjData($list[0][$i]);
                if (isset($data[0]) && isset($data[1])) {
                    if (isset($score_info[$data[0].'学年第'.$data[1].'学期'])) {
                        $count++;
                    } else {
                        $count = 0;
                    }
                    foreach ($data as $k => $v) {
                        $score_info[$data[0].'学年第'.$data[1].'学期'][$count][strip_tags($header[$k])] = $v;
                    }
                } else {
                    $this->resultJson('', '没有查询到相关成绩信息！', -1);
                }
            }
        }else{
            $this->resultJson('', '没有查询到相关成绩信息！', -1);
        }
        if (!isset($score_info)) {
            $this->resultJson('', '没有查询到相关成绩信息！', -1); 
        }
        $this->resultJson([array_reverse($score_info),'name'=>$this->name]); 
    }
  
    /**
     * getCjOption() 函数用来获取成绩请求Option
     */
    public function getCjOption()
    {
        $result = Curl::getBase($this->cj_url, '', $this->token, $this->refer);
        $xninfo = Regex::getCjSemester($result);
        // 学期列表
        $xnlist = array_filter($xninfo[1]);
        foreach ($xnlist as $key => $value) {
            for($i=1; $i <= 2; $i++){
              $list[$value . '*' . $i] = $value.'学年第'.$i.'学期';
            }
        }
        $data = new \stdClass();
        $data->list = array_reverse($list);
        $this->resultJson($data);
    }

     /*
    获取课表信息
     */
    public function getKbInfo()
    {
        $result = Curl::getBase($this->kb_url, '', $this->token, $this->refer);
        $xq = Regex::getXqOption($result);
        // 如果匹配当前学年学期
        if((!$this->xn&&!$this->xq)||($this->xn==$xq[1][0])&&($this->xq==$xq[1][1])){
        } else {
            $result = Curl::getBase($this->kb_url, '', $this->token, $this->refer);
            $data = Regex::getKbState($result);
            $aArg = array(
                '__EVENTTARGET'=>'xnd',
                '__EVENTARGUMENT'=>'', 
                '__VIEWSTATE'=>$data[1],
                '__VIEWSTATEGENERATOR'=>'55530A43',
                'xnd'=>$this->xn,
                'xqd'=>$this->xq
            );
            $result = Curl::getBase($this->kb_url, $aArg, $this->token, $this->refer);
            $xq = Regex::getXqOption($result);
        }
        $list = Regex::getKbData($result);
        foreach ($list[0] as $key => $value) {
            if($key>1){
                $td[$key] = Regex::getKbList($value);
            }
        }
        $schedulelist = $this->handleKbInfo($td);
        $this->resultJson([$schedulelist,'xueqi'=>$xq[1][0].'*'.$xq[1][1]]);
    }

    /*
    处理课表查询信息
     */
    public function handleKbInfo ($td) {
        $week = $this->week;
        $count = 0;
        $sn = 0;
        $color = ["#00FF00","#EEC900","#EE30A7","#4B0082","#66CD00","#CDCD00","#EE3B3B","#7FFF00","#FFA500","#00BFFF","#FFD700"];
        for ($i=2; $i <= 12; $i++) { 
            for ($j=0; $j < 7; $j++) {
                if (isset($td[$i][0][$j])  && $td[$i][1][$j] != '&nbsp;') {
                    $td[$i][1][$j] = Regex::handldKbData($td[$i][1][$j]);
                    $data = explode('<br><br>', $td[$i][1][$j]);
                    if (count($data)>1) {
                        foreach ($data as $k => $v) {
                            $kb = explode('<br>', $v);
                            $kb1 = explode('{', $kb[1]);
                            $section = Regex::getKbTime(substr($kb1[1], 0, -1));
                            if (($section[1] <= $week && $section[2] >= $week)) {
                                if (strpos($kb[1], '单') && $week%2==1) {
                                    $list[$i-1][$j][$k] = $this->setkbinfo("1", "1", '', '', '');
                                    if (!$sn) $sn = $k;
                                    $count++;
                                } elseif (strpos($kb[1], '双')  && $week%2==0) {
                                    $list[$i-1][$j][$k] = $this->setkbinfo("0", "1", '', '', '');
                                    if (!$sn) $sn = $k;
                                    $count++;
                                } elseif (!strpos($kb[1], '双') && !strpos($kb[1], '单')) {
                                    $list[$i-1][$j][$k] = $this->setkbinfo('', "1", '', '', '');
                                    if (!$sn) $sn = $k;
                                    $count++;
                                } else {
                                    $list[$i-1][$j][$k] = $this->setkbinfo('', '', '', '', '');
                                }
                            } else {
                                $list[$i-1][$j][$k] = $this->setkbinfo('', "1", '', '', '');
                            }
                            $list[$i-1][$j][$k]['class'] = '';
                            $list[$i-1][$j][$k]['name'] = $kb[0];
                            $list[$i-1][$j][$k]['timeinfo'] = $kb1[0];
                            $list[$i-1][$j][$k]['weekinfo'] = substr($kb1[1], 0, -1);
                            $list[$i-1][$j][$k]['start'] = $section[1];
                            $list[$i-1][$j][$k]['end'] = $section[2];
                            $list[$i-1][$j][$k]['teacher'] = $kb[2];
                            $list[$i-1][$j][$k]['classroom'] = $kb[3];
                            if (!isset($list[$i-1][$j][0]['col'])) {
                                $list[$i-1][$j][$k]['col'] = Regex::getKbCol($td[$i][$k][$j]);
                            } else {
                                $list[$i-1][$j][$k]['col'] = $list[$i-1][$j][0]['col'];
                            }
                        }
                        if ($count == 0) {
                            $list[$i-1][$j][0]['class'] = '1';
                            $list[$i-1][$j][0]['take'] = '1';
                            $list[$i-1][$j][0]['color'] = $color[rand(0,10)];
                            $list[$i-1][$j][0]['type'] = '1';
                        } elseif ($count >= 1) {
                            $list[$i-1][$j][$sn]['class'] = '1';
                            $list[$i-1][$j][$sn]['take'] = '1';
                            $list[$i-1][$j][$sn]['color'] = $color[rand(0,10)];
                            $list[$i-1][$j][$sn]['type'] = '1';
                        }
                        $count = 0;$sn = 0;
                    } else {
                        $kb = explode('<br>', $data[0]);
                        $kb1 = explode('{', $kb[1]);
                        $section = Regex::getKbTime(substr($kb1[1], 0, -1));
                        if (($section[1] <= $week && $section[2] >= $week)) {
                            if (strpos($kb[1], '单') && $week%2==1) {
                                $list[$i-1][$j][0]['time'] = $this->setkbinfo("1", "1", "1", "1", $color[rand(0,10)]);
                            } elseif (strpos($kb[1], '双')  && $week%2==0) {
                                $list[$i-1][$j][0] = $this->setkbinfo("0", "1", "1", "1", $color[rand(0,10)]);
                            } elseif (!strpos($kb[1], '双') && !strpos($kb[1], '单')) {
                                $list[$i-1][$j][0] = $this->setkbinfo('', "1", "1", "1", $color[rand(0,10)]);
                            } else {
                                $list[$i-1][$j][0] = $this->setkbinfo('', "1", "1", '', $color[rand(0,10)]);
                            }
                        } else {
                            $list[$i-1][$j][0] = $this->setkbinfo('', 1, 1, '', $color[rand(0,10)]);
                        }
                        $list[$i-1][$j][0]['show'] = '1';
                        $list[$i-1][$j][0]['name'] = $kb[0];
                        $list[$i-1][$j][0]['timeinfo'] = $kb1[0];
                        $list[$i-1][$j][0]['weekinfo'] = substr($kb1[1], 0, -1);
                        $list[$i-1][$j][0]['start'] = $section[1];
                        $list[$i-1][$j][0]['end'] = $section[2];
                        $list[$i-1][$j][0]['teacher'] = $kb[2];
                        $list[$i-1][$j][0]['classroom'] = $kb[3];
                        $list[$i-1][$j][0]['col'] = Regex::getKbCol($td[$i][0][$j]);
                    }
                } elseif (isset($td[$i][0][$j]) && $td[$i][1][$j] == '&nbsp;') {
                    $list[$i-1][$j][0] = $this->setkbinfo('', "1", "1", "1", '');
                } else {
                    $list[$i-1][$j][0] = $this->setkbinfo('', '', '', '', '');
                }
            }
        }
        return $list;
    }

    /*
    设置课表信息
     */
    public function setkbinfo ($time = '', $show = '', $take = '', $class = '', $color = '') {
        $data['time'] = $time;
        $data['show'] = $show;
        $data['take'] = $take;
        $data['class'] = $class;
        $data['color'] = $color;
        return $data;
    }

    /**
     * kboption() 函数用来获取课程请求Option
     */
    public function getKbOption()
    {
        $result = Curl::getBase($this->kb_url, '', $this->token, $this->refer);
        $xninfo = Regex::getKbSemester($result);
        // 学院列表
        $xnlist = array_filter($xninfo[2]);
        foreach ($xnlist as $key => $value) {
            for($i=1; $i <= 2; $i++){
                $list[$value.'*'.$i] = $value.'学年第'.$i.'学期';
            }
        }
        $this->resultJson($list);
    }

    public function schedule() {
        $table = $this->scheduleinfo;
        $table=preg_replace('/<td rowspan="\d" width="\d%">([\w\W]*?)<\/td>/','',$table);
        $table=preg_replace('/<td>第([\w\W]*?)节<\/td>/','',$table);
        $table=preg_replace('/<td width="1%">第一节<\/td>/','',$table);
        preg_match_all('/<tr>([\w\W]*?)<\/tr>/',$table,$match);

        foreach ($match[0] as $key => $value) {
            if($key>1){
                preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/',$value,$td[$key]);
            }
        }

        for ($i=2; $i <= 13; $i++) { 
            for ($j=1; $j <= 7; $j++) { 
                $list[$j] = !empty($td[$i][1][$j-1]) ? $td[$i][1][$j-1] : '&nbsp;';
            }
        }

        var_dump($schedulelist);die;
        for ($i=2; $i <= 13; $i++) { 
            for ($j=1; $j <= 7; $j++) { 
                $list[$j] = !empty($td[$i][1][$j-1]) ? $td[$i][1][$j-1] : '&nbsp;';
                if(strpos($list[$j],'<br>') == false) {
                    unset($list[$j]);          
                }else{
                    if(strpos($list[$j],'<br><br><br><br>') !== false) {
                       $list = explode('<br><br><br><br>',$list[$j]);
                       $list[$j] = [];
                        for ($m=1; $m <= count($list); $m++) {
                          $list1 = explode('<br>',$list[$m-1]);
                          for ($n=0; $n <= count($list1)+10; $n++) {
                            if(empty($list1[$n])){
                              unset($list1[$n]);
                            } 
                          }
                          $list1 = array_values($list1);
                          $list[$j][$m] = $list1;
                        }
                    }else{
                        $list = explode('<br>',$list[$j]);
                        for ($m=0; $m <= count($list)+1; $m++) {
                          if(empty($list[$m])){
                            unset($list[$m]);
                          } 
                        }
                        $list[$j] = $list;
                    }
                }
            }
        }
        // 调课信息判断
        preg_match('/<table class="datelist" cellspacing="0" cellpadding="3" border="0" id="DBGrid" width="100%">([\w\W]*?)<\/table>/',$table,$match);
        if(!empty($match)){
          preg_match_all('/<tr[\w\W]*?>([\w\W]*?)<\/tr>/',$match[1],$list);
            foreach ($list[1] as $key => $value) {
                if($key>0){
                    preg_match_all('/<td>([\w\W]*?)<\/td>/',$value,$other[$key]);
                }
            }
        }
        foreach ($schedulelist as $key => $value) {
          foreach ($value as $key1 => $value1) {
              if (count($value1)==count($value1, 1)) {
              // 一维数组
                if(!empty($other)){
                    foreach ($other as $key3 => $value3) {
                    if($value1[0] == $value3[1][1]){
                      $join = explode('/',$value3[1][3]);
                      $schedulelist[$key][$key1][0] = $schedulelist[$key][$key1][0] . '--调课--' .$join[0];
                      $schedulelist[$key][$key1][3] = $schedulelist[$key][$key1][3] . '--调课--' .$join[1];
                    }
                  }
                } 
              } else {
                  // 多维数组
                    foreach ($value1 as $key2 => $value2) {
                      if(!empty($other)){
                        foreach ($other as $key3 => $value3) {
                          if($value2[0] == $value3[1][1]){
                            $join = explode('/',$value3[1][3]);
                            $schedulelist[$key][$key1][$key2][0] = $schedulelist[$key][$key1][$key2][0] . '--调课--' .$join[0];
                            $schedulelist[$key][$key1][$key2][3] = $schedulelist[$key][$key1][$key2][3] . '--调课--' .$join[1];
                          }
                        }
                    }
                  }
              }
          }    
        }
        $this->resultJson($schedulelist, '成功');
    }

}
