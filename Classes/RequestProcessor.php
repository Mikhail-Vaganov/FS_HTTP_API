<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 22:18
 */
include_once 'common.php';

class RequestProcessor
{
    protected $http_method;
    protected $url;

    protected function __construct()
    {
        $this->http_method=$_SERVER['REQUEST_METHOD'];
        $this->url= $_SERVER['REQUEST_URI'];
    }

    protected function SendResponse($answer)
    {
        header('Content-type: application/json');
        $json_string=json_encode($answer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $last_er= json_last_error();
        if($last_er!=JSON_ERROR_NONE)
           SendError(new Exception("Error has occurred during serialization in JSON. ". GetJsonErrorExplanation($last_er)));

        echo $json_string;
    }
}