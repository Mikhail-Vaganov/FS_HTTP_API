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
    protected function SendResponse($answer)
    {
        header('Content-type: application/json');
        echo json_encode($answer, JSON_PRETTY_PRINT);
    }
}