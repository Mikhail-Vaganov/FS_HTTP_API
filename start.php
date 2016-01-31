<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 31-Jan-16
 * Time: 15:49
 */

include_once 'common.php';


spl_autoload_extensions(".php");
spl_autoload_register();
set_error_handler('ExceptionErrorHandler');

if (!file_exists(WORKING_FOLDER))
{
    mkdir(WORKING_FOLDER, true);
}

try
{
    $processorSelector = new FileSystemRequestSellector();
    $restProcessor = new RootRequestProcessor($processorSelector);
    $restProcessor->ProcessRequest();
}
catch(Exception $exception)
{
    SendError($exception);
}

function SendError (Exception $exception)
{
    header('Content-type: application/json');
    if(is_a($exception,"FsapiException"))
    {
        http_response_code($exception->getCode());
        echo json_encode($exception, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    else
    {
        $fsapiEx=new FsapiException($exception->getMessage(), 500, null, $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        http_response_code($fsapiEx->getCode());
        echo json_encode($fsapiEx,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}