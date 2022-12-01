<?php

namespace he;

use he\Config;

class Controller
{
    /**
     * 视图实例
     */
    private $_VIEW;

    /**
     * 页面处理错误跳转
     * @param string $msg 错误提示内容
     * @param string $url 跳转链接地址，如何该参数等于false，则不进行自动跳转
     * @param int $time 自动跳转时间(秒)
     */
    protected function error($msg, $url = '', $time = 3)
    {
        require_once Config::get('template.tpl_error');
        exit;
    }

    /**
     * 页面处理警告跳转
     * @param string $msg 错误提示内容
     * @param string $url 跳转链接地址
     * @param int $time 自动跳转时间(秒)
     */
    protected function notice($msg, $url = '', $time = 3)
    {
        require_once Config::get('template.tpl_notice');
        exit;
    }

    /**
     * 页面处理正确跳转
     * @param string $msg 错误提示内容
     * @param string $url 跳转链接地址
     * @param int $time 自动跳转时间(秒)
     */
    protected function exec($msg, $url = '', $time = 3)
    {
        require_once Config::get('template.tpl_exec');
        exit;
    }

    /**
     * 注入模板变量
     * @param string $key 变量名
     * @param void $val 变量内容
     */
    protected function assign($key, $val = null)
    {
        $this->isView();
        $this->_VIEW->assign($key, $val);
    }

    /**
     * 输出固定格式的JSON
     * @param string $code 状态码
     * @param string $msg 返回说明
     * @param mixed $data 返回内容
     */
    protected function json(string $code = '00', string $msg = '', $data = [])
    {
        echo json_encode([
            'code' => "$code",
            'msg' => $msg,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 调用模板
     * @param string $file 模板文件地址
     */
    protected function view(string $file)
    {
        $this->isView();
        $this->_VIEW->display($file);
    }

    /**
     * 获得模板实例
     */
    private function isView()
    {
        if (empty($this->_VIEW)) {
            $this->_VIEW = new \he\View();
        }
    }
}