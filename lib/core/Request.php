<?php

namespace app\lib\core;

class Request
{

    private static $request_methods = array("get", "put", "post", "delete");

    public $baseURI;

    public static function getInstance()
    {
        return new Request();
    }

    private function __construct()
    {
        $this->baseURI = $_SERVER["REQUEST_URI"];

    }


}