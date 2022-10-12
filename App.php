<?php

namespace app;
require_once("lib/core/Request.php");

use app\lib\core\Request;

final  class App
{
    public static function run()
    {
        self::_init_constant();
        self::_create_dir();
        self::_load_core_file();
    }


    /**
     * 初始化常量
     * @return void
     */
    private static function _init_constant()
    {
        $app_path = str_replace("\\", "/", __FILE__);
        define("FRAMEWORK_PATH", dirname($app_path));
        define("ROOT_PATH", dirname(FRAMEWORK_PATH));
        define("CONFIG_PATH", FRAMEWORK_PATH . "/config");
        define("DATA_PATH", FRAMEWORK_PATH . "/data");
        define("LIB_PATH", FRAMEWORK_PATH . "/lib");
        define("CORE_PATH", LIB_PATH . "/core");
        define("FUNCTION_PATH", LIB_PATH . "/function");

        // 需要创建的目录
        define("APP_CONFIG_PATH", ROOT_PATH . "/config");
    }


    /**
     * 创建应用目录
     * @return void
     */
    private static function _create_dir()
    {
        $file_arr = array(APP_CONFIG_PATH);
        foreach ($file_arr as $file_path) {
            is_dir($file_path) || mkdir($file_path);
        }

    }


    /**
     * 加载核心文件
     * @return void
     */
    private static function _load_core_file()
    {

    }
}


App::run();