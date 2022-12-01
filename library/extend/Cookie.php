<?php


namespace he\extend;


class Cookie
{
    /**
     * Cookie配置参数
     */
    private static $_INI;
    /**
     * 是否有初始化过配置参数
     */
    private static $_MODEL = false;

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
        self::$_INI = Config::get('cookie');
        self::outTime();
    }

    /**
     * 手动设置参数
     * @param array $ini 一维数组的配置参数，参考config
     */
    public static function init($ini)
    {
        self::construct();
        self::$_INI = array_merge(self::$_INI, $ini);
        self::outTime();
    }

    /**
     * 设置Cookie过期时间
     */
    private static function outTime()
    {
        # 设置下过期时间
        if (self::$_INI['time'] != 0) {
            self::$_INI['time'] = time() + self::$_INI['time'];
        }
    }

    /**
     * 设置Cookie的httponly属性开关
     */
    private static function httpOnly()
    {
        if (self::$_INI['httponly'] == true) {
            ini_set('session.cookie_httponly', true);
        }
    }

    /**
     * 设置Cookie
     * @param string $key 键名
     * @param string $val 对应的值
     * @param bool
     */
    public static function set($key, $val)
    {
        self::construct();
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 检测是否需要加密
        if (self::$_INI['secure'] == true) {
            # 案例原因，只用了base64简单转换下
            $val = base64_encode($val);
        }
        # 二维Cookie
        if (!empty($data[1])) {
            $param_B = $data[1];
            $param = str_replace(' ', '', "$param_A [$param_B]");
            setcookie($param, $val, self::$_INI['time'], self::$_INI['path'], self::$_INI['domain']);
        } else {
            # 一维
            setcookie($param_A, $val, self::$_INI['time'], self::$_INI['path'], self::$_INI['domain']);
        }
        return true;
    }

    /**
     * 获取Cookie
     * @param string $key 键名
     * @return mixed
     */
    public static function get($key)
    {
        self::construct();
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 二维Cookie
        if (!empty($data[1])) {
            $param_B = $data[1];
            if (!isset($_COOKIE[$param_A][$param_B])) {
                return false;
            }
            $cookie = $_COOKIE[$param_A][$param_B];
        } else {
            # 一维
            if (!isset($_COOKIE[$param_A])) {
                return false;
            }
            $cookie = $_COOKIE[$param_A];
        }
        # 检测是否需要解密
        if (self::$_INI['secure'] == true) {
            # 案例原因，只用了base64简单转换下
            return base64_decode($cookie);
        } else {
            return $cookie;
        }
        return false;
    }

    /**
     * 删除Cookie
     * @param string $key 键名
     * @return void
     */
    public static function delete($key)
    {
        self::construct();
        $data = explode('.', $key);
        $param_A = Config::get('prefix') . $data[0];
        # 二维Cookie
        if (!empty($data[1])) {
            $param_B = $data[1];
            $param = str_replace(' ', '', "$param_A [$param_B]");
            setcookie($param, 1, time() - 1);
        } else {
            # 一维
            setcookie($param_A, 1, time() - 1);
        }
    }

    /**
     * 清空Cookie
     * @param string $prefix 前缀名
     * @return voidW
     */
    public static function clear($prefix = null)
    {
        self::construct();
        if (empty($prefix)) {
            foreach ($_COOKIE as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $param = str_replace(' ', '', "$key [$k]");
                        setcookie($param, 1, time() - 1, self::$_INI['path'], self::$_INI['domain']);
                    }
                } else {
                    setcookie($key, 1, time() - 1, self::$_INI['path'], self::$_INI['domain']);
                }
            }
            return true;
        }
        # 循环删除Cookie
        foreach ($_COOKIE as $key => $value) {
            if (strpos($key, $prefix) !== false) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $param = str_replace(' ', '', "$key [$k]");
                        echo $param;
                        setcookie($param, 1, time() - 1, self::$_INI['path'], self::$_INI['domain']);
                    }
                } else {
                    setcookie($key, 1, time() - 1, self::$_INI['path'], self::$_INI['domain']);
                }
            }
        }
        return true;
    }
}