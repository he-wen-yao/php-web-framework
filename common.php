<?php
/**
 * 获取用户当前请求的URL地址
 * @return string|bool URL地址 或 系统禁止路由优化功能
 */
function PATH_INFO()
{
    # nginx环境下需要修改.conf文件才能支持
    if (isset($_SERVER['PATH_INFO'])) {
        return $_SERVER['PATH_INFO'];
    }
    if (isset($_SERVER['REDIRECT_PATH_INFO'])) {
        return $_SERVER['REDIRECT_PATH_INFO'];
    }
    if (isset($_SERVER['REDIRECT_URL'])) {
        return $_SERVER['REDIRECT_URL'];
    }
    return false;
}

/**
 * 美化 var_dump 函数
 * @param mixed $mixed 打印内容
 * @param bool $debug 是否需要断点
 */
function dump($mixed, bool $debug = false)
{
    echo '<pre>';
    var_dump($mixed);
    echo '</pre>';
    if ($debug) {
        exit;
    }
}