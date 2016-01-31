<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 31-Jan-16
 * Time: 15:49
 */

include_once 'common.php';

if (!file_exists(WORKING_FOLDER))
    mkdir(WORKING_FOLDER, true);

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