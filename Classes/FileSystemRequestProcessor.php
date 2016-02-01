<?php

class FileSystemRequestProcessor extends RequestProcessor implements iRestRequestProcessor
{

    public function  __construct()
    {
        parent::__construct();
    }


    public function post()
    {
        $parts = parse_url($this->url);
        $request_parts = explode("/",  $parts['path']);
        parse_str($parts['query']??"", $query);

        if (!isset($request_parts[3]) || $request_parts[3]=="")
            throw new FsapiException("A name of a new file missed", 400, null, $this->http_method,$this->url);

        $file_name = $request_parts[3];
        $file_path = GetFilePathInWorkingDir($file_name);
        $autorename= $query['autorename'] ?? false;

        if(count($_FILES)==0)
        {
            $file_path = $this->ValidateFilePath($file_path, $autorename);
            $this->CreateSingleFileFromInputStream($file_path);
            $this->SendMessageAboutFileCreation($file_path, $parts['path']);
            return;
        }

        if(count($_FILES)==1)
        {
            $fileData = array_values($_FILES)[0];
            $file_path = $this->ValidateFilePath($file_path, $autorename);
            $this->CreateSingleFile($fileData, $file_path);
            $this->SendMessageAboutFileCreation($file_path, $parts['path']);
            return;
        }

        if(count($_FILES)>1 && !$autorename)
            throw new FsapiException("More than one file are tried to be created under the same name. Consider autorename flag triggering or submit just one file to be created.", 400, null, $this->http_method,$this->url);
        elseif (count($_FILES)>1 && $autorename)
        {
            $metadata=Array();
            foreach ($_FILES as $file => $fileData)
            {
                $file_path = $this->ValidateFilePath($file_path, $autorename);
                $this->CreateSingleFile($fileData, $file_path);
                $metadata = new FileMetaData($file_path);
            }
            $this->SendResponse($metadata);
        }

        return;
    }

    public function get()
    {
        $parts = parse_url($this->url);
        $request_parts = explode("/",  $parts['path']);

        if (!isset($request_parts[3]) || $request_parts[3]=="")
            $this->SendTheListOfFilesInDirectory();
        else
        {
            $file_name=$request_parts[3];
            $this->SendFile($file_name);
        }
    }

    public function put()
    {
        $parts = parse_url($this->url);
        $request_parts = explode("/",  $parts['path']);
        parse_str($parts['query']??"", $query);

        if (!isset($request_parts[3]) || $request_parts[3]=="")
            throw new FsapiException("A name of a new file missed", 400, null, $this->http_method,$this->url);

        $file_name = $request_parts[3];
        $file_path=GetFilePathInWorkingDir($file_name);

        $this->CreateSingleFileFromInputStream($file_path);
        $this->SendMessageAboutFileCreation($file_path, $parts['path']);
    }

    public function delete()
    {
        $parts = parse_url($this->url);
        $request_parts = explode("/",  $parts['path']);

        if (!isset($request_parts[3]) || $request_parts[3]=="")
            throw new FsapiException("File to delete hasn't been pointed", 400, null, $this->http_method,$this->url);

        $file_name = $request_parts[3];
        $file_path = GetFilePathInWorkingDir($file_name);

        if(!file_exists($file_path))
            throw new FsapiException("File not found", 404, $file_name, $this->http_method,$this->url);
        else
        {
            $metadata = new FileMetaData($file_path);
            unlink($file_path);
            $this->SendResponse($metadata);
        }

    }

    private function SendTheListOfFilesInDirectory()
    {
        $files = scandir(WORKING_FOLDER);
        $filesToAnswer = Array();

        foreach ($files as $file)
            if ($file == '.' || $file == '..')
                continue;
            else
                $filesToAnswer[] = rawurldecode($file);

        http_response_code(200);
        $this->SendResponse($filesToAnswer);
    }

    private function SendFile($file_name)
    {
        $file_path = GetFilePathInWorkingDir($file_name);

        if(!file_exists($file_path))
            throw new FsapiException("File not found", 404, $file_name, $this->http_method,$this->url);

        http_response_code(200);
        $fileMetaData = new FileMetaData($file_path);;
        header('Content-Type: '.$fileMetaData->GetMime());
        header("Content-Length:".$fileMetaData->GetFileSize());
        header("Content-Disposition: attachment; filename=".$fileMetaData->GetFileName());
        readfile($file_path);
    }

    private function CreateSingleFile($fileData, $file_path)
    {
        $file_name = basename($file_path);

        if($fileData['error']!=UPLOAD_ERR_OK)
            throw new FsapiException("Incorrect upload file data: ".GetFileUploadErrorExplanation($fileData['error']) , 404, $file_name, $this->http_method,$this->url);

        $file_path = GetFilePathInWorkingDir($file_name);

        if (!move_uploaded_file($fileData['tmp_name'], $file_path))
            throw new FsapiException("Failed to move uploaded file.", 500, $file_name, $this->http_method,$this->url);
    }

    private function CreateSingleFileFromInputStream($file_path)
    {
        $file_name = basename($file_path);
        $fp = fopen($file_path, "w");
        if (flock($fp, LOCK_EX))
        {
            $putdata = fopen("php://input", "r");

            while ($data = fread($putdata, 1024))
                fwrite($fp, $data);

            flock($fp, LOCK_UN);
            fclose($fp);
            fclose($putdata);
            touch($file_path);
        } else
            throw new FsapiException("Couldn't lock the file before update", 500, $file_name, $this->http_method, $this->url);
    }

    private function ValidateFilePath($file_path, $autorename)
    {
        $file_name=basename($file_path);
        if (file_exists($file_path) && !$autorename)
            throw new FsapiException("A file with the same name already exists.  Consider autorename flag triggering.", 409, $file_name, $this->http_method, $this->url);
        else
            $file_path = NewFilePathIfFileExists($file_path);

        return $file_path;
    }

    private function SendMessageAboutFileCreation($file_path, $external_path)
    {
        header("Location: ".$external_path);
        http_response_code(201);
        $this->SendResponse(new FileMetaData($file_path));
    }
}