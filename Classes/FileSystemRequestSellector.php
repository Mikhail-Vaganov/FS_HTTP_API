<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 00:30
 */

//include_once 'common.php';

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
        if(count($request_parts)<3)
            throw new FsapiException('Wrong url',400,null,null,$url);

        if(strtolower($request_parts[1])!="fsapi")
            throw new FsapiException('Wrong url',400,null,null,$url);

        switch (strtolower($request_parts[2]))
        {
            case ('files') : return new FileSystemRequestProcessor();
            case ('metadata') : return new FileMetadataRequestProcessor();
            default: throw new FsapiException('Wrong resource name',400,null,null,$url);
        }
    }
}