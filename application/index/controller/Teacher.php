<?php
namespace app\index\controller;

class Teacher extends Base
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

    //学生信息
    protected $studentinfo = '';

    //课表信息
    protected $scheduleinfo = '';

    //TOKEN信息
    protected $token = '';

    /**
     * getstuoptions() 函数用来获取学生option
     */
    public function getstuoptions()
    {
        $curlArg = array(
            'url'=>$this->url.'/'."xzbxsmdcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'get',
            'responseHeaders'=>0,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
        session('viewstate',$VIEWSTATE[1][0]);

        // 年级列表
        $gradelist = [];
        preg_match('/<select name="DropDownlist1".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$gradeinfo);
        foreach ($gradeinfo[1] as $key => $value) {
            if ($value!=' ') {
                $gradelist[$value] = $gradeinfo[2][$key];
            }  
        }
 
        // 学院列表
        $academylist = [];
        preg_match('/<select name="DropDownList2".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$academyinfo);
        foreach ($academyinfo[1] as $key => $value) {
            if ($value!=' ') {
                $academylist[$value] = gb2312($academyinfo[2][$key]);
            }
        }
        
        $data['gradelist'] = $gradelist;
        $data['academylist'] = $academylist;
        ajaxReturn($data,'成功',0);
    }
  
    /**
     * getstuoptions1() 函数用来获取学生option
     */
    public function getstuoptions1()
    {
        $grade = input('post.grade','');
        $academy = input('post.academy','');
        $professional = input('post.professiona','+');
        $class = input('post.class','+');
        $default = array('__EVENTARGUMENT'=>'','__EVENTTARGET'=>'DropDownList2', '__VIEWSTATE'=>session('viewstate'),'__VIEWSTATEGENERATOR'=>'81970EE3','DropDownlist1'=>$grade,'DropDownList2'=>$academy,'DropDownList3'=>$professional,'DropDownList4'=>'+');
        $aArg = array_merge($default);
        $curlArg = array(
            'url'=>$this->url.'/'."xzbxsmdcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'post',
            'responseHeaders'=>0,
            'data'=>$aArg,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
        session('viewstate',$VIEWSTATE[1][0]);
        // 年级列表
        $gradelist = [];
        preg_match('/<select name="DropDownlist1".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$gradeinfo);
        foreach ($gradeinfo[1] as $key => $value) {
            if ($value!=' ') {
                $gradelist[$value] = $gradeinfo[2][$key];
            }  
        }
 
        // 学院列表
        $academylist = [];
        preg_match('/<select name="DropDownList2".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$academyinfo);
        foreach ($academyinfo[1] as $key => $value) {
            if ($value!=' ') {
                $academylist[$value] = gb2312($academyinfo[2][$key]);
            }
        }
      
        // 专业列表
        $professionallist = [];
        preg_match('/<select name="DropDownList3".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$professionalinfo);
        foreach ($professionalinfo[1] as $key => $value) {
            if ($value!=' ') {
                $professionallist[$value] = gb2312($professionalinfo[2][$key]);
            }
        }
      
        // 行政班列表
        $classlist = [];
        preg_match('/<select name="DropDownList4".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option value=\"([\w\W]*?)\">([\w\W]*?)<\/option>/",$match[1],$classinfo);
        foreach ($classinfo[1] as $key => $value) {
            if ($value!=' ') {
                $classlist[$value] = gb2312($classinfo[2][$key]);
            }
        }
        
        $data['gradelist'] = $gradelist;
        $data['academylist'] = $academylist;
        $data['professionallist'] = $professionallist;
        $data['classlist'] = $classlist;
        ajaxReturn($data,'成功',0);
    }
  
    /**
     * getstudent() 函数用来获取学生信息
     */
    public function getstudent()
    {
        $curlArg = array(
            'url'=>$this->url.'/'."xzbxsmdcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'get',
            'responseHeaders'=>0,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);

        $grade = input('post.grade','');
        $academy = input('post.academy','');
        $professional = input('post.professional','+');
        $class = input('post.class','+');
        $default = array('__EVENTARGUMENT'=>'','__EVENTTARGET'=>'', '__VIEWSTATE'=>$VIEWSTATE[1][0],'__VIEWSTATEGENERATOR'=>'81970EE3','Button1'=>'%B2%E9+%D1%AF','DropDownlist1'=>$grade,'DropDownList2'=>$academy,'DropDownList3'=>$professional,'DropDownList4'=>$class);
        $aArg = array_merge($default);
        $curlArg = array(
            'url'=>$this->url.'/'."xzbxsmdcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'post',
            'responseHeaders'=>0,
            'data'=>$aArg,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        $result = iconv('GB2312', 'UTF-8//IGNORE', $result);
        preg_match('/<table class="datelist" cellspacing="0" cellpadding="3" border="0" id="DataGrid1" width="100%">([\w\W]*?)<\/table>/',$result,$match);
        if (empty($match[1])) {
            ajaxReturn('', '没有查询学生信息！', -1);
        }
        preg_match_all('/<tr.*?>([\w\W]*?)<\/tr>/',$match[1],$student);
        $studentlist = [];
        $studentinfo = [];
        foreach($student[1] as $key => $value){
          if($key>0){
            preg_match_all('/<td>([\w\W]*?)<\/td>/',$student[1][$key],$studentinfo[$key]);
          }
        }          
        foreach($studentinfo as $key => $value){
          $studentlist[$key] = $value[1];
        }
        ajaxReturn($studentlist,'成功',0);
    }
  
    /**
     * getteacheroptions() 函数用来获取教师option
     */
    public function getteacheroptions()
    {
        $curlArg = array(
            'url'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'get',
            'responseHeaders'=>0,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        $result = iconv('GB2312', 'UTF-8//IGNORE', $result);

        // 学年列表
        $xnlist = [];
        preg_match('/<select name="xn".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$xninfo);
        foreach ($xninfo[1] as $key => $value) {
            if ($value!='') {
                $xnlist[$value] = $xninfo[1][$key];
            }  
        }
 
        // 学期列表
        $xqlist = [];
        preg_match('/<select name="xq".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$xqinfo);
        foreach ($xqinfo[1] as $key => $value) {
            if ($value!='') {
                $xqlist[$value] = $xqinfo[1][$key];
            }
        }
        
        // 部门列表
        $bmlist = [];
        preg_match('/<select name="bm".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$bminfo);
        foreach ($bminfo[1] as $key => $value) {
            if ($value!='') {
                $bmlist[$value] = $bminfo[1][$key];
            }
        }
      
        // 教师列表
        $jslist = [];
        preg_match('/<select name="js".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$jsinfo);
        foreach ($jsinfo[1] as $key => $value) {
            if ($value!='') {
                $jslist[$value] = $jsinfo[1][$key];
            }
        }
        
        $data['xnlist'] = $xnlist;
        $data['xqlist'] = $xqlist;
        //$data['bmlist'] = $bmlist;
        //$data['jslist'] = $jslist;
        ajaxReturn($data,'成功',0);
    }
  
    /**
     * getteacheroptions1() 函数用来获取教师option
     */
    protected function getteacheroptions1()
    {
        $curlArg = array(
            'url'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'get',
            'responseHeaders'=>0,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        $result = gb2312($result);
        preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
      
        $xn = input('post.xn','');
        $xq = input('post.xq','');
        $bm = input('post.bm','');
        $js = input('post.js','');
        $teacher = input('post.teacher','');
      
        $default = array('__EVENTARGUMENT'=>'','__EVENTTARGET'=>'bm', '__VIEWSTATE'=>$VIEWSTATE[1][0],'__VIEWSTATEGENERATOR'=>'E6032F7F','js'=>$js,'bm'=>urlencode(mb_convert_encoding($bm,'gb2312','utf-8')),'xq'=>$xq,'xn'=>$xn,'TextBox1'=>$teacher);
        $aArg = array_merge($default);
        $curlArg = array(
            'url'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122303",
            'method'=>'post',
            'responseHeaders'=>0,
            'data'=>$aArg,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        $result = gb2312($result);

        // 学年列表
        $xnlist = [];
        preg_match('/<select name="xn".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$xninfo);
        foreach ($xninfo[1] as $key => $value) {
            if ($value!='') {
                $xnlist[$value] = $xninfo[1][$key];
            }  
        }
 
        // 学期列表
        $xqlist = [];
        preg_match('/<select name="xq".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$xqinfo);
        foreach ($xqinfo[1] as $key => $value) {
            if ($value!='') {
                $xqlist[$value] = $xqinfo[1][$key];
            }
        }
        
        // 部门列表
        $bmlist = [];
        preg_match('/<select name="bm".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$bminfo);
        foreach ($bminfo[1] as $key => $value) {
            if ($value!='') {
                $bmlist[$value] = $bminfo[1][$key];
            }
        }
      
        // 教师列表
        $jslist = [];
        preg_match('/<select name="js".*?>([\w\W]*?)<\/select>/',$result,$match);
        preg_match_all("/<option.*?>([\w\W]*?)<\/option>/",$match[1],$jsinfo);
        foreach ($jsinfo[1] as $key => $value) {
            if ($value!='') {
                $jslist[$value] = $jsinfo[1][$key];
            }
        }
        
        $data['xnlist'] = $xnlist;
        $data['xqlist'] = $xqlist;
        $data['bmlist'] = $bmlist;
        $data['jslist'] = $jslist;
        ajaxReturn($data,'成功',0);
    }
  
    /**
     * getteacherkb() 函数用来获取教师课表
     */
    public function getteacherkb()
    {
        $xn = input('post.xn','');
        $xq = input('post.xq','');
        $js = input('post.js',$this->number);
      
        $curlArg = array(
            'url'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122307",
            'method'=>'get',
            'responseHeaders'=>0,
            'cookie'=>$this->token
        );
        $result = curl_request($curlArg);
        preg_match_all('/<option selected="selected" value="(.*)">/iU',$result,$xnq);
        // 如果匹配当前学年学期
        if(($xn==$xnq[1][0])&&($xq==$xnq[1][1])){
        }else{
        preg_match_all('/name=\"__VIEWSTATE\" value=\"(.*)\"/',$result,$VIEWSTATE);
     
        $default = array('__EVENTARGUMENT'=>'','__EVENTTARGET'=>'xq', '__VIEWSTATE'=>$VIEWSTATE[1][0],'__VIEWSTATEGENERATOR'=>'E6032F7F','js'=>$js,'bm'=>'','xq'=>$xq,'xn'=>$xn,'TextBox1'=>'');
        $aArg = array_merge($default);
        $curlArg = array(
            'url'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122303",
            'method'=>'post',
            'responseHeaders'=>0,
            'data'=>$aArg,
            'cookie'=>$this->token,
            'referer'=>$this->url.'/'."jstjkbcx.aspx?zgh=".$this->number."&xm=".urlencode(mb_convert_encoding($this->name,'gb2312','utf-8'))."&gnmkdm=N122303"
        );
        
        $result = curl_request($curlArg);
        }
        $result = iconv('GB2312', 'UTF-8//IGNORE', $result);
        //$result = gb2312($result);
        $this->schedule($result);
    }
    
    protected function schedule($table) {
        $table=preg_replace('/<td rowspan="\d" width="\d%">上午<\/td>/','',$table);
        $table=preg_replace('/<td rowspan="\d" width="\d%">下午<\/td>/','',$table);
        $table=preg_replace('/<td rowspan="\d" width="\d%">晚上<\/td>/','',$table);
        $table=preg_replace("/<font color='red'>([\w\W]*?)<\/font>/",'',$table);
        $table=str_replace('<td width="1%">第一节</td>','',$table);
        $table=str_replace('<td>第二节</td>','',$table);
        $table=str_replace('<td>第三节</td>','',$table);
        $table=str_replace('<td>第四节</td>','',$table);
        $table=str_replace('<td>第五节</td>','',$table);
        $table=str_replace('<td>第六节</td>','',$table);
        $table=str_replace('<td>第七节</td>','',$table);
        $table=str_replace('<td>第八节</td>','',$table);
        $table=str_replace('<td>第九节</td>','',$table);
        $table=str_replace('<td>第10节</td>','',$table);
        $table=str_replace('<td>第11节</td>','',$table);
        $table=str_replace('<td>第12节</td>','',$table);
        preg_match_all('/<tr>([\w\W]*?)<\/tr>/',$table,$match);

        foreach ($match[0] as $key => $value) {
            if($key>1){
                preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/',$value,$td[$key]);
            }
        }
        $schedulelist = [];
        for ($i=2; $i <= 13; $i++) { 
            for ($j=1; $j <= 7; $j++) { 
                $schedulelist[$j][$i-1] = !empty($td[$i][1][$j-1]) ? $td[$i][1][$j-1] : '&nbsp;';
                if(strpos($schedulelist[$j][$i-1],'<br>') == false) {
                    unset($schedulelist[$j][$i-1]);          
                }else{
                    if(strpos($schedulelist[$j][$i-1],'<br><br><br><br>') !== false) {
                       $list = explode('<br><br><br><br>',$schedulelist[$j][$i-1]);
                       $schedulelist[$j][$i-1] = [];
                        for ($m=1; $m <= count($list); $m++) {
                          $list1 = explode('<br>',$list[$m-1]);
                          for ($n=0; $n <= count($list1)+10; $n++) {
                            if(empty($list1[$n])){
                              unset($list1[$n]);
                            } 
                          }
                          $list1 = array_values($list1);
                          $schedulelist[$j][$i-1][$m] = $list1;
                        }
                    }else{
                        $list = explode('<br>',$schedulelist[$j][$i-1]);
                        for ($m=0; $m <= count($list)+1; $m++) {
                          if(empty($list[$m])){
                            unset($list[$m]);
                          } 
                        }
                        $schedulelist[$j][$i-1] = $list;
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
      ajaxReturn($schedulelist);
    }
}
