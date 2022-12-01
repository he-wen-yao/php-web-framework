<?php
return [
    // +------------------------------------------------------------------
    // | 应用设置
    // +------------------------------------------------------------------
    // 默认时区
    'default_timezone' => 'PRC',
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => '',
    // 默认语言
    'default_lang' => 'zh',
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    'template' => [
        // DEBUG开启时的错误页面解析模板
        'tpl_error_yes' => THINK_PATH . 'tpl' . DS . 'error_test.php',
        // DEBUG关闭时的错误页面解析模板
        'tpl_error_no' => THINK_PATH . 'tpl' . DS . 'error_formal.php',
        // 错误跳转模板
        'tpl_error' => THINK_PATH . 'tpl' . DS . 'error.php',
        // 警告跳转模板
        'tpl_notice' => THINK_PATH . 'tpl' . DS . 'notice.php',
        // 正确跳转模板
        'tpl_exec' => THINK_PATH . 'tpl' . DS . 'exec.php',
        // 小绿毛
        'app_debug' => THINK_PATH . 'tpl' . DS . 'error_debug.php',
    ],
];