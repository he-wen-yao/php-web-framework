<?php


namespace he\extend;


class Session
{
    /**
     * Session配置参数
     */
    private static $_INI;
    /**
     * 是否有初始化过配置参数
     */
    private static $_MODEL = false;
    /**
     * Session过期时间分隔符
     */
    private static $_DS = '-+-';

    /**
     * 初始化配置参数
     */
    private static function construct()
    {
        if (self::$_MODEL === true) {
            return false;
        }
        # 只能初始化一次
        self::$_MODEL = true;
        # 开启Session
        isset($_SESSION) || session_start();
        self::$_INI = Config::get('session');
    }

    /**
     * 手动设置参数
     * @param array $ini 一维数组的配置参数，参考config
     */
    public static function init($ini)
    {
        self::construct();
        self::$_INI = array_merge(self::$_INI, $ini);
    }

    /**
     * 设置Session
     * @param string $key 键名
     * @param string $val 对应的值
     * @param bool
     */
    public static function set($key, $val)
    {
        self::construct();
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 二维Session
        if (!empty($data[1])) {
            $param_B = $data[1];
            $_SESSION[$param_A][$param_B] = $val . self::$_DS . (time() + self::$_INI['time']);
        } else {
            # 一维
            $_SESSION[$param_A] = $val . self::$_DS . (time() + self::$_INI['time']);
        }
        return true;
    }

    /**
     * 获取Session
     * @param string $key 键名
     * @return mixed
     */
    public static function get($key = null)
    {
        self::construct();
        if (empty($key)) {
            return $_SESSION;
        }
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 二维Session
        if (!empty($data[1])) {
            $param_B = $data[1];
            if (!isset($_SESSION[$param_A][$param_B])) {
                return false;
            }
            $session = $_SESSION[$param_A][$param_B];
        } else {
            # 一维
            if (!isset($_SESSION[$param_A])) {
                return false;
            }
            $session = $_SESSION[$param_A];
        }
        $data = explode(self::$_DS, $session);
        if (count($data) != 2) {
            return false;
        }
        if ($data[1] < time()) {
            return false;
        }
        return $data[0];
    }

    /**
     * 删除Session
     * @param string $key 键名
     * @return void
     */
    public static function delete($key)
    {
        self::construct();
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 二维Session
        if (!empty($data[1])) {
            $param_B = $data[1];
            unset($_SESSION[$param_A][$param_B]);
        } else {
            # 一维
            unset($_SESSION[$param_A]);
        }
    }

    /**
     * 清空Session
     * @param string $prefix 前缀名
     * @return void
     */
    public static function clear($prefix = null)
    {
        self::construct();
        if (empty($prefix)) {
            session_destroy();
            return true;
        }
        # 循环删除session
        foreach ($_SESSION as $key => $val) {
            if (strpos($key, $prefix) !== false) {
                unset($_SESSION[$key]);
            }
        }
        return true;
    }
}