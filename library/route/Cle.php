<?php


namespace he\route;


class Cle
{
    /**
     * 路由表
     */
    private static $_TABLE;

    /**
     * 执行路由
     */
    public static function run()
    {
        self::$_TABLE = \he\Route::get();
        self::go();
    }

    /**
     * 分解URL常量
     */
    private static function go()
    {
        # 获得路由地址
        $URL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', PATH_INFO());
        $URL = str_replace(ROOT_PATH, '/', $URL);
        $URL = explode('?', $URL);
        $URL = $URL[0];
        # 获得当前URL
        $URL = ltrim($URL, '/');
        # 为空则设置为默认路由表
        $URL = !empty($URL) ? $URL : '/';
        $URL = str_replace(Config::get('url_html_suffix'), '', $URL);
        # 再按分隔符生成数组
        $route = explode(Config::get('pathinfo_depr'), $URL);
        if (empty($route[0])) {
            $route[0] = '/';
        }
        if (empty(self::$_TABLE[$route[0]])) {
            # 路由表找不到，则调用美化模式
            \he\route\Sfy::run();
            exit;
        }
        $table = self::$_TABLE[$route[0]];
        # 设置常量
        !empty($table['module']) ? define('MODULE_NAME', $table['module']) : define('MODULE_NAME', Config::get('default_module'));       // 分组
        # 加载私有配置项
        $file = APP_PATH . APP_LICATION . DS . MODULE_NAME . DS . 'config' . EXT;
        if (file_exists($file)) {
            $config = require_once $file;
            Config::load($config);
        }
        # 引入私有函数库
        $file = APP_PATH . APP_LICATION . DS . MODULE_NAME . DS . 'common' . EXT;
        if (file_exists($file)) {
            require_once $file;
        }
        $path = explode('/', $table['path']);
        !empty($path[0]) ? define('CONTROLLER_NAME', ucfirst($path[0])) : define('CONTROLLER_NAME', Config::get('default_controller')); // 控制器
        !empty($path[1]) ? define('ACTION_NAME', $path[1]) : define('ACTION_NAME', Config::get('default_action'));         // 方法
        # 删除路由关键字
        array_shift($route);
        # 不为空设置参数
        if (!empty($table['param']) && count($route) > 0) {
            # 获得$_GET键名
            $LEFT = explode('-', $table['param']);
            # 如果URL还有后续参数，则重新赋值到$_GET中
            foreach ($LEFT as $key => $value) {
                $_GET[$value] = $route[$key];
            }
        }
        # 判断并过滤请求类型
        if (!empty($table['request'])) {
            $Http_Type = explode('|', $table['request']);
            $status = false;
            foreach ($Http_Type as $value) {
                # 类型小写化
                $str = strtolower($value);
                # 循环判断三种请求
                if (strtolower($_SERVER['REQUEST_METHOD']) == $str) {
                    $status = true;
                    break;
                }
                if (strtolower($_SERVER['REQUEST_METHOD']) == $str) {
                    $status = true;
                    break;
                }
                if ($str == 'ajax' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) == true && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    $status = true;
                    break;
                }
            }
            if ($status == false) {
                die('请求类型错误');
            }
        }
        # 检测前后置操作是否开启
        if (isset($table['prepose']) && !empty($table['prepose'])) {
            $prepose = ucfirst($table['prepose']);
            # 加载前后置控制器
            $prepose_url = 'app\\' . MODULE_NAME . '\\prepose\\' . $prepose;
            $prepose_obj = new $prepose_url();
            $prepose_obj->front();
        }
        # 加载控制器
        if (isset($table['view']) && $table['view'] == false) {
            # 加载控制器
            $path = 'app\\' . MODULE_NAME . '\\controller\\' . CONTROLLER_NAME;
            $aciton = ACTION_NAME;
            $obj = new $path();
            $obj->$aciton();
        } else {
            # 直接加载视图
            $obj = new \he\View();
            $obj->display($table['path']);
        }
        # 执行后置操作
        if (isset($table['prepose']) && !empty($table['prepose'])) {
            $prepose_obj->after();
        }
    }
}