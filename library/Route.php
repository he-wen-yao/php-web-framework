<?php


namespace he;


class Route
{
    /**
     * 路由表
     */
    private static array $_TABLE = [];

    /**
     * 初始化路由表
     */
    public static function run()
    {
        # 加载路由表
        $file = APP_PATH . APP_LICATION . DS . 'route' . EXT;
        if (file_exists($file)) {
            $table = require_once $file;
            self::$_TABLE = array_merge(self::$_TABLE, $table);
        }
        # 加载应用目录下的配置项
        $config = require_once APP_PATH . APP_LICATION . DS . 'config' . EXT;
        \he\Config::load($config);
        # 按路由模式开启
        if (\he\Config::get('url_route_on') == true) {
            \he\route\Cle::run();
        } else {
            \he\route\Sfy::run();
        }
        # 做一些性能监听的逻辑
    }

    /**
     * 获取路由
     * @param string|null $key 路由关键字名，为空读取所有
     * @param void
     */
    public static function get(string $key = null)
    {
        if ($key === null) {
            return self::$_TABLE;
        }
        # 读取
        if (self::has($key)) {
            return self::$_TABLE[$key];
        }
        return false;
    }

    /**
     * 检测路由是否存在
     * @param string $key 路由关键字名
     * @param bool
     */
    public static function has($key)
    {
        if (isset(self::$_TABLE[$key])) {
            return true;
        }
        return false;
    }

    /**
     * 设置路由参数
     * @param string $key 路由关键字名
     * @param string $val 配置对应的值
     * @param bool
     */
    public static function set($key, $val)
    {
        if (!self::has($key)) {
            self::$_TABLE[$key] = $val;
            return true;
        }
        return false;
    }
}