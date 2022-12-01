<?php


namespace he;

class Config
{
    /**
     * 配置项
     */
    private static array $_CONFIG = [];

    /**
     * 初始化框架应用配置参数
     * @param string $file 框架核心配置路径
     */
    public static function run(string $file)
    {
        if (file_exists($file)) {
            self::$_CONFIG = require_once $file;
        }
    }

    /**
     * 导入配置项合并，只支持最多二维数组
     * @param array 需要导入的配置项
     * @param string|null $k 二维数组的键名
     */
    public static function load($data, string $k = null)
    {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                self::load($val, $key);
            } else {
                if (!empty($k)) {
                    self::$_CONFIG[$k][$key] = $val;
                } else {
                    self::$_CONFIG[$key] = $val;
                }
            }
        }
    }

    /**
     * 获取配置参数
     * @param string|null $key 配置键名，为空读取所有
     * @return array|false|mixed
     */
    public static function get(string $key = null)
    {
        if ($key === null) {
            return self::$_CONFIG;
        }
        $data = explode('.', $key);
        $param_A = $data[0];
        # 二维参数读取
        if (!empty($data[1])) {
            $param_B = $data[1];
            if (isset(self::$_CONFIG[$param_A][$param_B])) {
                return self::$_CONFIG[$param_A][$param_B];
            }
        }
        # 一维参数读取
        if (isset(self::$_CONFIG[$param_A])) {
            return self::$_CONFIG[$param_A];
        }
        return false;
    }

    /**
     * 设置配置参数
     * @param string $key 配置键名
     * @param object $val 配置对应的值
     * @return bool
     */
    public static function set(string $key, object $val): bool
    {
        $data = explode('.', $key);
        $param_A = $data[0];
        # 二维参数修改
        if (!empty($data[1])) {
            $param_B = $data[1];
            self::$_CONFIG[$param_A][$param_B] = $val;
            return true;
        }
        # 一维参数修改
        if (isset(self::$_CONFIG[$param_A])) {
            self::$_CONFIG[$param_A] = $val;
            return true;
        }
        return false;
    }

    /**
     * 判断配置项是否存在
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
            if (!isset(self::$_CONFIG[$param_A][$param_B])) {
                return false;
            }
        }
        # 一维参数读取
        if (!isset(self::$_CONFIG[$param_A])) {
            return false;
        }
        return true;
    }
}