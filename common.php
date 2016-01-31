<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 22:33
 */

require_once 'iRestRequestProcessor.php';
require_once 'iRestRequestProcessorSelector.php';
require_once 'RequestProcessor.php';
require_once 'FileSystemRequestSellector.php';
require_once 'FileSystemRequestProcessor.php';
require_once 'RootRequestProcessor.php';

define('WORKING_FOLDER', 'c:'.DIRECTORY_SEPARATOR.'local_store');

function exceptionErrorHandler($errNumber, $errStr, $errFile, $errLine )
{
    throw new ErrorException($errStr, 500, $errNumber, $errFile, $errLine);
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
    }

    $dirPath= dirname($filePath);
    $newpath = $dirPath.DIRECTORY_SEPARATOR.$filename;
    $newname = $filename;
    $counter = 1;
    while (file_exists($newpath))
    {
        $newname = $name .'('. $counter.')'.$ext??"";
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



