<?php

/**
 * 路由组
 */
class RouterGroup extends Router
{

    // 路由名称 （路径）
    private $path;

    // 存放路由组内的路由信息
    private $routers;

    /**
     * 构造一个路由组
     * @param $path 路由组的公共路径
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->routers = [];
    }




}