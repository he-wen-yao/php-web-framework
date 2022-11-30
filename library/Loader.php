<?php

namespace he;


class Loader
{
    /**
     * 命名空间-自定义路径映射表
     */
    public static $vendorMap = [
        'app' => APP_PATH . APP_LICATION,
        'he' => THINK_PATH . 'library',
    ];

    /**
     * 注册自动加载机制
     * @return void
     */
    public static function register()
    {
        # 注册系统自动加载
        spl_autoload_register('\he\Loader::autoload', true, true);
        # 更新映射表
        self::mergeMap();
        # 下面可以继续做 Composer 自动加载支持
    }

    /**
     * 自动加载器
     * @param string $class 自动传入的命名空间路径
     */
    public static function autoload($class)
    {
        # 获得解析后的命名空间绝对路径
        $file = self::findFile($class);
        if (file_exists($file)) {
            # 调用引入
            self::includeFile($file);
        }
    }

    /**
     * 解析命名空间对应的文件路径
     * @param string $class 命名空间路径
     * @return string 标准路径
     */
    private static function findFile($class)
    {
        $vendor = substr($class, 0, strpos($class, '\\')); // 顶级命名空间
        # 取出文件基目录
        if (!empty(self::$vendorMap[$vendor])) {
            $vendorDir = self::$vendorMap[$vendor];
        } else {
            $vendorDir = __DIR__ . DS . $vendor;
        }
        $filePath = substr($class, strlen($vendor)) . EXT;   // 文件相对路径
        return strtr($vendorDir . $filePath, '\\', DS);       // 文件标准路径
    }

    /**
     * 框架映射表 与 扩展映射表合并
     */
    private static function mergeMap()
    {
        $file = APP_PATH . APP_LICATION . DS . 'autoload_class_map' . EXT;
        if (file_exists($file)) {
            $map = require_once $file;
            self::$vendorMap = array_merge(self::$vendorMap, $map);
        }
    }

    /**
     * 引入文件
     */
    private static function includeFile($file)
    {
        require $file;
    }
}