<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 01:21
 */

//include_once 'common.php';

class FileMetadataRequestProcessor extends RequestProcessor implements iRestRequestProcessor
{

    /**
     * FileMetadataRequestProcessor constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function post()
    {
        throw new FsapiException("POST processor is not implemented",501,null,$this->http_method,$this->url);
    }

    public function get()
    {

        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);
        $request_parts = explode("/",  $parts['path']);

        if (!isset($request_parts[3]) || $request_parts[3]=="")
            $this->SendMetadataOfAllFiles();
        else
            $this->SendMetadataOfSingleFIle($request_parts[3]);
    }

    public function put()
    {
        throw new FsapiException("PUT processor is not implemented",501,null,$this->http_method,$this->url);
    }

    public function delete()
    {
        throw new FsapiException("DELETE processor is not implemented",501,null,$this->http_method,$this->url);
    }

    /**
     * @param $request_parts
     */
    private function SendMetadataOfSingleFIle($file_name)
    {
        $file_path = GetFilePathInWorkingDir($file_name);
        if(!file_exists($file_path))
            throw new FsapiException("File doesn't exist",404,$file_name,$this->http_method,$this->url);


        if (!$fp = fopen($file_path, 'r')) {
            trigger_error("Unable to open URL ($file_path)", E_USER_ERROR);
        }

        $meta = new FileMetaData($file_path);

        http_response_code(200);
        $this->SendResponse($meta);
    }

    private function SendMetadataOfAllFiles()
    {
        $files = scandir(WORKING_FOLDER);
        $metadataToAnswer = Array();

        foreach ($files as $file)
            if ($file == '.' || $file == '..')
                continue;
            else
                $metadataToAnswer[] = new FileMetaData(GetFilePathInWorkingDir($file));

        http_response_code(200);
        $this->SendResponse($metadataToAnswer);
    }
}