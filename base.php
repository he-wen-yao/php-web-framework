<?php
# 框架当前版本
const THINK_VERSION = '1.0.1';
# 程序开始的执行时间戳
define('THINK_START_TIME', microtime(true));
# 程序开始时的内存量
define('THINK_START_MEM', memory_get_usage());
# 文件后缀名
const EXT = '.php';
# 服务器文件分隔符号
const DS = DIRECTORY_SEPARATOR;
# 调试模式
defined('APP_DEBUG')  or define('APP_DEBUG', true);
# 框架应用文件夹名
defined('APP_LOCATION') or define('APP_LOCATION', 'application');
# 框架核心文件的根目录地址
defined('THINK_PATH') or define('THINK_PATH', __DIR__ . DS);
# 框架核心文件的目录地址
const LIB_PATH = THINK_PATH . 'library' . DS;
# 相对于项目根目录地址-用于引入文件
defined('APP_PATH')  or define('APP_PATH', dirname(THINK_PATH).DS);
# 相对于浏览器的项目根目录地址-用于加载静态文件
defined('ROOT_PATH') or define('ROOT_PATH', str_replace(basename($_SERVER['SCRIPT_NAME']), '',$_SERVER['SCRIPT_NAME']));
# 框架缓存与日志文件根目录地址
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH . 'runtime' . DS);
# 日志文件存放地址
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
# 视图解析文件存放地址
defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);

# 载入命名空间
require LIB_PATH . 'Loader.php';
# 注册自动加载
\he\Loader::register();