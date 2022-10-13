<?php


namespace app\lib\core;


class Application
{
    /**
     * 启动应用
     */
    public static function Run()
    {
        self::_init();
        self::_set_url();
        echo __CLASS__;
        spl_autoload_register(array(__CLASS__, '_autoload'));

        self::_create_demo();
        self::_dispatch_router();
    }


    /**
     * 分发路由
     */
    public static function _dispatch_router()
    {
        $requestURI = $_SERVER["REQUEST_URI"];

        $pathArr = explode("/", $requestURI);


        $routerPath = PROJECT_CONTROLLER_PATH . "/" . ucfirst($pathArr[1]) . "Controller.php";
        // 判断是否存在此路由
        is_file($routerPath) || require_once $routerPath;
        $controller = ucfirst($pathArr[1]) . "Controller";

        new  $controller() . index();
    }


    /**
     * 创建一个案例
     */
    private static function _create_demo()
    {
        $path = PROJECT_CONTROLLER_PATH . "/IndexController.php";
        $userConfig = <<<str
<?php
class IndexController{
    
    public function index()
    {
        echo 'OK';
    }
}
str;
        is_file($path) || file_put_contents($path, $userConfig);
    }


    /**
     * 自动载入功能
     */
    private static function _autoload($className)
    {
    }

    /***
     * 设置外部路径
     */
    public static function _set_url()
    {
        $path = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $path = str_replace("\\", "/", $path);

        define("__APP__", $path);
        define("__ROOT__", dirname(__APP__));
    }


    /**
     * 初始化框架
     */
    private static function _init()
    {
        // 加载默认配置
        C(include CONFIG_PATH . "/config.php");

        $projectConfigPath = PROJECT_CONFIG_PATH . "/config.php";
        $projectConfig = <<<str
<?php
return array(
 // 用户自定义配置，配置信息可查看框架内 config/config.php
);
str;
        is_file($projectConfigPath) || file_put_contents($projectConfigPath, $projectConfig);
        // 加载用户配置，后置加载保证用户配置覆盖默认配置
        C(include $projectConfigPath);

        // 设置时区
        date_default_timezone_set(C('DEFAULT_TIMEZONE'));

        // 启用 SESSION
        C("SESSION_ENABLE") && session_start();
    }


}