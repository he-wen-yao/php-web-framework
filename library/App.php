<?php


namespace he;


class App
{
    /**
     * 执行应用程序
     * @access public
     * @return Response
     * @throws Exception
     */
    public static function run()
    {
        # 设置时区
        date_default_timezone_set(\mimi\Config::get('default_timezone'));
        # 加载框架公共函数库
        require_once THINK_PATH . 'common' . EXT;
        # 加载应用公共函数库
        require_once APP_PATH . APP_LICATION . DS . 'common' . EXT;
        # 初始化路由表
        \he\Route::run();
        # 调试模式下，引入小绿毛
        if (APP_DEBUG) {
            include_once \mimi\Config::get('template.app_debug');
        }
    }
}