<?php

namespace he\route;

use he\Config;

class Sfy
{
    /**
     * 执行路由
     */
    public static function run()
    {
        self::go();
        # 引入私有函数库
        $file = APP_PATH . APP_LICATION . DS . MODULE_NAME . DS . 'common' . EXT;
        if (file_exists($file)) {
            require_once $file;
        }
        # 加载私有配置项
        $file = APP_PATH . APP_LICATION . DS . MODULE_NAME . DS . 'config' . EXT;
        if (file_exists($file)) {
            $config = require_once $file;
            Config::load($config);
        }
        # 加载控制器
        $path = 'app\\' . MODULE_NAME . '\\controller\\' . CONTROLLER_NAME;
        $aciton = ACTION_NAME;
        $obj = new $path();
        $obj->$aciton();
    }

    /**
     * 分解URL常量
     */
    private static function go()
    {
        # 获得路由地址
        $URL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', PATH_INFO());
        $URL = str_replace(ROOT_PATH, '/', $URL);
        # 为空设置默认值
        if (empty($URL)) {
            define('MODULE_NAME', Config::get('default_module'));         // 分组
            define('CONTROLLER_NAME', Config::get('default_controller')); // 控制器
            define('ACTION_NAME', Config::get('default_action'));         // 方法
        } else {
            # 分解参数
            $path = trim($URL, Config::get('url_html_suffix'));    // 删除后缀名
            $path = ltrim($path, '/');                             // 删除开头的/符号
            $paths = explode(Config::get('pathinfo_depr'), $path);  // 将URL分割成一维数组
            # 设置常量
            define('MODULE_NAME', array_shift($paths));
            !empty($paths[1]) ? define('CONTROLLER_NAME', ucfirst(array_shift($paths))) : define('CONTROLLER_NAME', Config::get('default_controller'));
            !empty($paths[2]) ? define('ACTION_NAME', array_shift($paths)) : define('ACTION_NAME', Config::get('default_action'));
            # 如果URL还有后续参数，则重新赋值到$_GET中
            if (count($paths) != 0) {
                # 根据隔行算法，将参数转化为GET参数
                foreach ($paths as $key => $value) {
                    if ($key % 2 == 0) {
                        $_GET[$value] = '';
                    } else {
                        $_GET[$paths[$key - 1]] = $value;
                    }
                }
            }
        }
    }
}