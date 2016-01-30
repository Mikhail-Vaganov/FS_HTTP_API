<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 20:15
 */


include_once 'common.php';

//echo "<html><title>HTTP API of File System</title><body>";
//echo "The working local store is ".WORKING_FOLDER."<br/>";
spl_autoload_extensions(".php");
spl_autoload_register();

if (!file_exists(WORKING_FOLDER))
{
    mkdir(WORKING_FOLDER, true);
}

$response = GetStandardResponse();
try
{
    $processorSelector = new FileSystemRequestSellector();
    $restProcessor = new RootRequestProcessor($processorSelector);

    $restProcessor->ProcessRequest();
}
catch(Exception $exception)
{
    $response=$exception->getMessage();
    header('Content-type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}


function GetStandardResponse()
{
    return "Nothing has happened";
}

//header('Content-Type: application/json');
//echo json_encode($response);

//echo "</body></html>";