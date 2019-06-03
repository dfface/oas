<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => '',  // 修改无前缀
    // 驱动方式 支持redis memcache memcached
    'type'           => 'redis',  // 更改
    // 是否自动开启 SESSION
    'auto_start'     => true,
    'host'           => 'docker.for.mac.localhost',  // 增加
    'port'           => 6379,  // 增加
    'password'       => 'Passw0rd!',  // 增加 redis 密码 需要设置 CONFIG set requirepass ""
    'expire'         => 3600,  // session过期时间 一小时，但是token手动存在redis中可以永不过期
    'use_cookies'    => true,  // 设置使用 cookies
    "name"           => 'PHPSESSID',  // （不）更改 session id 默认的 PHPSESSID，为了配合 cookie 的作用域

];
