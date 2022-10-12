<?php

namespace app;
require_once("core/Request.php");

use app\core\Request;

class App
{


    public static function run()
    {
        $request = Request::getInstance();


        echo $request->baseURI;
    }
}