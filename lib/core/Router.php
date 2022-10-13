<?php


/**
 * 路由类
 */
class Router
{
    // 路由路径
    private $path;
    // 路由的处理函数
    private $handel;

    /**
     * 生成路由
     * @param $path
     * @param $handel
     */
    public function __construct($path, $handel)
    {
        $this->path;
        $this->handel = $handel;
    }


}