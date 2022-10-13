<?php

namespace app;

use app\lib\core\Application;

final  class App
{
    public static function run()
    {
        self::_init_constant();
        self::_create_dir();
        self::_load_core_file();

        Application::Run();
    }


    /**
     * 初始化常量
     * @return void
     */
    private static function _init_constant()
    {
        $app_path = str_replace("\\", "/", __FILE__);
        // 框架所在目录
        define("FRAMEWORK_PATH", dirname($app_path));
        // 框架配置目录
        define("CONFIG_PATH", FRAMEWORK_PATH . "/config");
        // 框架数据目录
        define("DATA_PATH", FRAMEWORK_PATH . "/data");
        // 框架库目录
        define("LIB_PATH", FRAMEWORK_PATH . "/lib");
        // 框架核心代码目录
        define("CORE_PATH", LIB_PATH . "/core");
        // 框架工具函数目录
        define("FUNCTION_PATH", LIB_PATH . "/function");

        // 项目所在目录，默认为框架外一层
        define("PROJECT_ROOT_PATH", dirname(FRAMEWORK_PATH));



        // 需要创建的目录
        define("PROJECT_CONFIG_PATH", PROJECT_ROOT_PATH . "/config");
        define("PROJECT_CONTROLLER_PATH", PROJECT_ROOT_PATH . "/controller");
    }


    /**
     * 创建应用目录
     * @return void
     */
    private static function _create_dir()
    {
        $file_arr = array(PROJECT_CONFIG_PATH, PROJECT_CONTROLLER_PATH);
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
        $file_path_arr = array(
            FUNCTION_PATH . "/fun.php",
            CORE_PATH . "/Application.php",
            CORE_PATH . "/Request.php",
        );
        foreach ($file_path_arr as $file_path) {
            require_once($file_path);
        }
    }


}


App::run();