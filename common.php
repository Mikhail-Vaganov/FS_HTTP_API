<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 22:33
 */

include_once 'configuration.php';

require_once 'Interfaces'.DIRECTORY_SEPARATOR.'iRestRequestProcessor.php';
require_once 'Interfaces'.DIRECTORY_SEPARATOR.'iRestRequestProcessorSelector.php';
require_once 'Classes'.DIRECTORY_SEPARATOR.'RequestProcessor.php';
require_once 'Classes'.DIRECTORY_SEPARATOR.'FileSystemRequestSellector.php';
require_once 'Classes'.DIRECTORY_SEPARATOR.'FileSystemRequestProcessor.php';
require_once 'Classes'.DIRECTORY_SEPARATOR.'RootRequestProcessor.php';
require_once 'Classes'.DIRECTORY_SEPARATOR.'FsapiException.php';


spl_autoload_extensions(".php");
spl_autoload_register();
set_error_handler('ExceptionErrorHandler');
register_shutdown_function( "fatal_handler" );

function fatal_handler()
{
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if( $error !== NULL)
    {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];
    }
    error_log($errstr);
    error_log($errno);
    error_log($errfile);
    error_log($errline);
}

function ExceptionErrorHandler($errNumber, $errStr, $errFile, $errLine )
{
    throw new ErrorException($errStr, 500, $errNumber, $errFile, $errLine);
}

function SendError (Exception $exception)
{
    header('Content-type: application/json');
    if(is_a($exception,"FsapiException"))
    {
        http_response_code($exception->getCode());
        echo json_encode($exception, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);
    }
    else
    {
        $fsapiEx=new FsapiException($exception->getMessage(), 500, null, $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        http_response_code($fsapiEx->getCode());
        echo json_encode($fsapiEx,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);
    }
}

function HumanFilesize($bytes, int $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = intval(floor((strlen($bytes) - 1) / 3));
    $unitName=($factor==0)?"B":($sz[$factor]."B");
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $unitName;
}

function NewFilePathIfFileExists($filePath)
{
    $filename = basename($filePath);
    if ($pos = strrpos($filename, '.'))
    {
        $name = substr($filename, 0, $pos);
        $ext = '.'.strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));
    }
    else
    {
        $name = $filename;
        $ext="";
    }

    $dirPath= dirname($filePath);
    $newpath = $dirPath.DIRECTORY_SEPARATOR.$filename;
    $newname = $filename;
    $counter = 1;
    while (file_exists($newpath))
    {
        $newname = $name .'('. $counter.')'.$ext;
        $newpath = $dirPath.DIRECTORY_SEPARATOR.$newname;
        $counter++;
    }
    return $newpath;
}

function GetFilePathInWorkingDir(string $file_name) : string
{
    $file_path = WORKING_FOLDER . DIRECTORY_SEPARATOR . $file_name;
    return $file_path;
}

function GetJsonErrorExplanation (int $error):string
{
    switch ($error) {
        case JSON_ERROR_NONE:
            return ' - No error has occurred';
        case JSON_ERROR_DEPTH:
            return ' - The maximum stack depth has been exceeded';
        case JSON_ERROR_STATE_MISMATCH:
            return ' - Invalid or malformed JSON';
        case JSON_ERROR_CTRL_CHAR:
            return ' - Control character error, possibly incorrectly encoded';
        case JSON_ERROR_SYNTAX:
            return ' - Syntax error';
        case JSON_ERROR_UTF8:
            return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        case JSON_ERROR_RECURSION:
            return ' - One or more recursive references in the value to be encoded';
        case JSON_ERROR_INF_OR_NAN:
            return ' - One or more NAN or INF values in the value to be encoded';
        case JSON_ERROR_UNSUPPORTED_TYPE:
            return ' - A value of a type that cannot be encoded was given';
        default:
            return ' - Unknown error';
    }
}

function GetFileUploadErrorExplanation (int $error):string
{
    switch ($error)
    {
        case UPLOAD_ERR_INI_SIZE:
            return " - The uploaded file exceeds the upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return " - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
        case UPLOAD_ERR_PARTIAL:
            return " - The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return " - No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return " - Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return " - Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return " - File upload stopped by extension";
        default:
            return " - Unknown upload error";
    }
}



