<?php
namespace app\index\controller;

use \think\Controller;
use \think\Request;
use \think\Cache;
use \think\Session;

/**
* Token类
* Author: 狂奔的小白兔
*/
class Token extends Controller
{
    /*
    初始函数
     */
    public function _initialize() {

    }

    public static function generateToken(){
        $randChar = randomStr(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('token_salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }

    /**
     * 设置session
     */
    public static function setSession($aArg) {
        $token = Request::instance()->header('token');
        if ($data = Cache::get($token)) {
            Cache::set($token, array_merge($data, $aArg), 3600);
        } else {
            if (is_array($aArg)) {
                foreach ($aArg as $k => $v) {
                    Session::set($k, $v);
                }
            }
        }
    }

    /**
     * 获取session
     */
    public static function getSession($aArg) {
        $token = request()->header('token');
        if ($token) {
            $session = Cache::get($token);
            return isset($session[$aArg]) ? $session[$aArg] : '';
        } else {
            return Session::get($aArg);
        }
    }
}

