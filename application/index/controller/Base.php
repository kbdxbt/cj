<?php
namespace app\index\controller;

use \think\Controller;
use \think\Log;
use \app\index\services\Regex;
use \app\index\services\Curl;
use \think\Cache;
use \think\Session;

/**
* 教务系统查询基础类
* Author: 狂奔的小白兔
*/
class Base extends Token
{
    const URL = "http://hljw.hjrxkj.com:8080/";

    // 教务系统地址
    protected $url;

    // 教务系统基础地址
    protected $base_url;

    //学号
    protected $number;

    //姓名
    protected $name;
  
    // Token
    protected $token;

    // 版本
    protected $system;

    // 可访问方法
    private $action = ['gettoken', 'getcaptcha', 'login', 'getsemeter'];

    // 可访问方法
    private $login_type = ['学生', '教师'];
  
    // 构造函数
    public function _initialize() 
    {
        parent::_initialize();
        $this->token = $this->getSession('token');
        $this->number = $this->getSession('number');
        $this->name = $this->getSession('name');
        $this->system = $this->getSession('system');
        if (substr(self::URL, -1) != '/') {
            $this->url = self::URL . '/';
        } else {
            $this->url = self::URL;
        }
        if ($this->system == 'old' && $this->token) {
            $this->url = $this->url . '(' . $this->token .')/'; // 旧版地址
        }
        // 判断是否可访问
        if (!in_array(strtolower($this->request->action()), $this->action)) {
            if (!$this->token || !$this->number || !$this->name) {
                $this->resultJson('', '请求出错！', -1);
            } else {
                // 参数设置
                if (strlen(Curl::getContent($this->url, $this->token)) < 1000){
                  $this->resultJson('', '登录失效了！', -1);
                }
            }                 
        }
    }
  
    public function login() 
    {
        $number = input('number');
        $password = input('password');
        $captcha = input('captcha');
        $user_type = input('user_type') ? : '学生';
        $system = input('system') ? : 'new';
        $this->setSession(input(''));
        $token = $this->getSession('token');
        if(!$number || !$password || !$captcha || !$token){
            $this->resultJson('', '参数出错！', -1);
        }
        if((!in_array($user_type, $this->login_type)) || !$user_type){
            $this->resultJson('', '用户类型出错！', -1);
        }
        //获取登录viewstate
        $result = Curl::getHome($this->url, 0);
        $login_state = Regex::getLoginState($result);
        $login_form = array_combine(Regex::getFrom($result), [$number, $password, $captcha]);
        $default = [
            '__VIEWSTATE' => $login_state, 
            'Button1' => '',
            'RadioButtonList1' => iconv('utf-8', 'gb2312', $user_type)
        ];
        $aArg = array_merge($default, $login_form);
        $result = Curl::getMain($this->url, $aArg, $token);
        $error = Regex::getError($result);
        $this->name = Regex::getName($result);
        if ($error && empty($this->name)) {
            $this->resultJson('', $error, -1);
        }
        //判断是否获取姓名
        if(!$this->name){
            $this->resultJson('', '教务系统请求出错！', -1);
        }else{
            $data['name'] = $this->name;
            $this->setSession($data);
            $this->resultJson('', '成功');
        }
    }

    /*
    检测登录
     */
    public function checkLogin() {
        $this->resultJson('', '成功');
    }

    /*
    获取教务系统登录标识
     */
    public function getToken() 
    {
        Session::clear();
        $type = input('type');
        $result = Curl::getHome(self::URL, 1);
        $session['token'] = Regex::getCookie($result);
        if (empty($session['token'])) { // 判断旧版
            $session['token'] = substr(Regex::getLocation($result), 2, 24);
            $session['system'] = 'old';
        }
        $token = $type ? $this->generateToken() : '';
        if ($type) {
            Cache::set($token, $session, 3600);
        } else {
            $this->setSession($session);
        }
        $this->resultJson($token, '成功');
    }

    /*
    获取教务系统验证码,验证码使用过一次即无效
     */
    public function getCaptcha() 
    {
        $token = $this->getSession('token');
        header("content-Type:image/Gif; charset=gb2312");
        if (empty($this->system)) {
            echo Curl::getNewCaptcha($this->url, $token);
        } else {
            echo Curl::getOldCaptcha($this->url, $token);
        }
        exit;
    }

    /**
    * 学期选项填充
    */
    public function getSemeter() 
    {
        date_default_timezone_set("PRC");
        $year = (int)date("Y");
        $month = (int)date("m");
        $list = [];
        if ($month >= '9') {
            $list['now'][$year .'-'.($year+1).'*1'] = $year.'-'.($year+1).'学年第1学期';
        } elseif ($month>'3' && $month<'9') {
            $list['now'][$year-1 .'-'.($year).'*2'] = ($year-1).'-'.$year.'学年第2学期';
        } else {
        }
      
        for ($i=1; $i <= 3; $i++) {
             $list['list'][$year-$i .'-'.($year-$i+1).'*1'] = $year-$i.'-'.($year-$i+1).'学年第1学期';
             $list['list'][$year-$i .'-'.($year-$i+1).'*2'] = $year-$i.'-'.($year-$i+1).'学年第2学期';
             $list['list'][$year-$i .'-'.($year-$i+1).'*3'] = $year-$i.'-'.($year-$i+1).'学年第3学期';
        }
        $list['list'] = array_reverse($list['list']);
        ajaxReturn($list);
    }

    /*
    返回json数据
     */
    protected function resultJson($data = null, $msg = '', $code = 0)
    {
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $res = [
            'data' => is_null($data) ? new \stdClass() : $data,
            'msg' => $msg,
            'code' => $code
        ];
        echo json_encode($res);
        exit;
    }
}

