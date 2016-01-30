<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 23:26
 */
include_once 'common.php';

class FileSystemRequestProcessor extends RequestProcessor implements iRestRequestProcessor
{

    public function  __construct()
    {

    }


    public function post()
    {
        //print_r($_FILES);
        $uploaddir = WORKING_FOLDER;

        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        if(isset($query['file_name']))
        {
            $fileName = $query['file_name'];
            foreach ($_FILES as $file => $fileData)
            {
                switch ($fileData['error'])
                {
                    case UPLOAD_ERR_OK: break;
                    case UPLOAD_ERR_NO_FILE: throw new RuntimeException('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE: throw new RuntimeException('Exceeded filesize limit.');
                    default: throw new RuntimeException('Unknown errors.');
                }



                $uploadfile = $uploaddir.DIRECTORY_SEPARATOR.$fileName;

                if(file_exists($uploadfile) && (!isset($query['autorename']) || isset($query['autorename']) && $query['autorename']==false))
                    throw new RuntimeException('File '.$fileName.' already exists');
                else
                    $uploadfile = NewFilePathIfFileExists($uploadfile);


                if (move_uploaded_file($fileData['tmp_name'], $uploadfile)) {
                    $this->SendResponse(new FileMetaData($uploadfile));
                } else {
                    $this->SendResponse("Something wrong has just happend");
                }
            }
        }
    }

    public function get()
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);

        if(!isset($parts['query']))
            $this->SendTheListOfFilesInDirectory();
        else
        {
            parse_str($parts['query'], $query);
            if(isset($query['file_name']))
            {
                $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$query['file_name'];
                $this->SendFile($file_path);
            }
            else
            {
                $this->SendTheListOfFilesInDirectory();
            }
        }
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    private function SendTheListOfFilesInDirectory()
    {
        $files = scandir(WORKING_FOLDER);
        $filesToAnswer = Array();

        foreach ($files as $file)
            if ($file == '.' || $file == '..')
                continue;
            else
                $filesToAnswer[] = $file;

        $filesToAnswer;
        $this->SendResponse($filesToAnswer);
    }

    private function SendFile($file_name)
    {
        $fileMetaData = new FileMetaData($file_name);
        $path_to_send = $fileMetaData->GetPathToFile();
        header('Content-Type: '.$fileMetaData->GetMime());
        header("Content-Length:".$fileMetaData->GetFileSize());
        header("Content-Disposition: attachment; filename=".$fileMetaData->GetFileName());
        readfile($path_to_send);
    }
}