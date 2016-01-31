<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 00:30
 */

include_once 'common.php';

class FileSystemRequestSellector implements iRestRequestProcessorSelector
{
    public function __construct()
    {
    }

    public function GetAppropriateRequestProcessor() : iRestRequestProcessor
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);

        $request_parts = explode("/",  $parts['path']);
        switch (strtolower($request_parts[2]))
        {
            case ('files') : return new FileSystemRequestProcessor();
            case ('metadata') : return new FileMetadataRequestProcessor();
            default: throw new FsapiException('Wrong resource name',400,null,null,$url);
        }
    }
}