<?php

namespace app\lib\core;

class Controller
{

    /**
     * 返回 json 数据
     * @param $code
     * @param $message
     * @param $data
     * @return false|string
     */
    public static function json($code, $message, $data)
    {
        return json_encode(["code" => $code, "message" => $message, "data" => $data]);
    }

}