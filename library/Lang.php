<?php


namespace he;


class Lang
{
    /**
     * 语言包
     */
    private static array $_LANG = [];

    /**
     * 初始化语言包
     */
    public static function run()
    {
        $file = THINK_PATH . 'lang' . DS . \mimi\Config::get('default_lang') . EXT;
        if (file_exists($file)) {
            self::$_LANG = require_once $file;
        }
    }

    /**
     * 导入语言包合并，只支持最多二维数组
     * @param array 需要导入的语言包
     * @param string|null $k 二维数组的键名
     */
    public static function load($data, string $k = null)
    {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                self::load($val, $key);
            } else {
                if (!empty($k)) {
                    self::$_LANG[$k][$key] = $val;
                } else {
                    self::$_LANG[$key] = $val;
                }
            }
        }
    }

    /**
     * 获取语言包参数
     * @param string $key 语言包键名，为空读取所有
     * @param void
     */
    public static function get($key = null)
    {
        if ($key === null) {
            return self::$_LANG;
        }
        $data = explode('.', $key);
        $param_A = $data[0];
        # 二维参数读取
        if (!empty($data[1])) {
            $param_B = $data[1];
            if (isset(self::$_LANG[$param_A][$param_B])) {
                return self::$_LANG[$param_A][$param_B];
            }
        }
        # 一维参数读取
        if (isset(self::$_LANG[$param_A])) {
            return self::$_LANG[$param_A];
        }
        return false;
    }

    /**
     * 设置语言包对应参数
     * @param string $key 键名
     * @param string $val 对应的值
     * @param bool
     */
    public static function set(string $key, string $val): bool
    {
        $data = explode('.', $key);
        $param_A = $data[0];
        # 二维参数修改
        if (!empty($data[1])) {
            $param_B = $data[1];
            self::$_LANG[$param_A][$param_B] = $val;
            return true;
        }
        # 一维参数修改
        if (isset(self::$_LANG[$param_A])) {
            self::$_LANG[$param_A] = $val;
            return true;
        }
        return false;
    }

    /**
     * 判断语言包项是否存在
     * @param string $key 配置键名
     * @return bool
     */
    public static function has(string $key): bool
    {
        $data = explode('.', $key);
        $param_A = $data[0];
        # 二维参数读取
        if (!empty($data[1])) {
            $param_B = $data[1];
            if (!isset(self::$_LANG[$param_A][$param_B])) {
                return false;
            }
        }
        # 一维参数读取
        if (!isset(self::$_LANG[$param_A])) {
            return false;
        }
        return true;
    }
}