<?php
namespace app\index;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
class Http extends Handle
{
    public function render(Exception $e)
    {
        return json(['data' => '', 'msg' => '数据出错', 'code' => -1]);
    }

}
