<?php
return array(
   'exception_handle' => '\\app\\index\\1http',

   'log'   => [
        'type'  => 'File',
        // 日志记录级别，使用数组表示
        'path' => LOG_PATH,
        //单个日志文件的大小限制，超过后会自动记录到第二个文件
        'file_size'     =>2097152,
        //日志的时间格式，默认是` c `
        'time_format'   =>'c',
        'level' => ['log'],
    ],

    'session' => [
        'prefix'     => 'module',
        'type'       => 'redis',
        'auto_start' => true,
         // redis主机
        'host'       => '127.0.0.1',
         // redis端口
        'port'       => 6379,
         // 密码
        'password'   => '',
    ],

    'token_salt' => 'kbapp',


);