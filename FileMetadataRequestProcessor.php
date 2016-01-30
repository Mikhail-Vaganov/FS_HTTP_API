<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 01:21
 */

include_once 'common.php';

class FileMetadataRequestProcessor extends RequestProcessor implements iRestRequestProcessor
{

    /**
     * FileMetadataRequestProcessor constructor.
     */
    public function __construct()
    {
    }

    public function post()
    {
        // TODO: Implement post() method.
    }

    public function get()
    {
        // TODO: Implement get() method.
        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);

        //EchoArray($parts);

        parse_str($parts['query'], $query);
        //echo "<p>".$query['file_name']."</p>";

        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$query['file_name'];
        if (!$fp = fopen($file_path, 'r')) {
            trigger_error("Unable to open URL ($file_path)", E_USER_ERROR);
        }

        //$meta = stream_get_meta_data($fp);
        $meta = new FileMetaData($file_path);
        $this->SendResponse($meta);
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }
}