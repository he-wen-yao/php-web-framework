<?php


/**
 * 代替 var_dump
 * @param $val
 */
function P($val)
{
    var_dump($val);
}

/**
 * 操作配置文件
 * @param null $key
 * @param null $val
 * @return void|array
 */
function C($key = null, $val = null)
{
    // static 保证函数结束变量不会被销毁
    static $config = array();
    if (is_array($key)) {
        // 合并配置
        $config = array_merge($config, array_change_key_case($key, CASE_UPPER));
        return;
    }
    if (is_string($key)) {
        $key = strtoupper($key);
        if (is_null($val)) {
            return isset($config[$key]) ? $config[$key] : null;
        }
        $config[$key] = $val;
        return;
    }
    return $config;
}