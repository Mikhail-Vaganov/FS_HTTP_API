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

define('WORKING_FOLDER', 'c:\\local_store');

function handle_error($request)
{

}

function EchoArray (Array $array_to_echo)
{
    echo "<table>";
    foreach ($array_to_echo as $value)
        echo "<tr><td>$value</td></tr>";
    echo "</table>";
}

function NormalizeBytesSize($bytes):string
{
    if($bytes<1024)
        return $bytes.'B';
    elseif ($bytes>=1024 && $bytes<1048576)
        return ($bytes/1024).'KB';
    elseif ($bytes>=1048576)
        return ($bytes/1024/1024).'MB';
}

function HumanFilesize($bytes, int $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = intval(floor((strlen($bytes) - 1) / 3));
    $unitName=($factor==0)?"B":($sz[$factor]."B");
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $unitName;
}

function NewFileNewnameIfExists($filePath) : string
{
    $name = $this->name=basename($filePath);
    $ext = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));


    $dirPath=dirname($filePath);
    $newpath = $dirPath.'/'.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
        $newname = $name .'_'. $counter . $ext;
        $newpath = $path.'/'.$newname;
        $counter++;
    }

    return $newname;
}

function NewFilePathIfFileExists($filePath)
{
    $filename = basename($filePath);
    if ($pos = strrpos($filename, '.'))
    {
        $name = substr($filename, 0, $pos);
        $ext = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));
    }
    else
    {
        $name = $filename;
    }

    $dirPath= dirname($filePath);
    $newpath = $dirPath.DIRECTORY_SEPARATOR.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
        $newname = $name .'('. $counter.').'. $ext;
        $newpath = $dirPath.DIRECTORY_SEPARATOR.$newname;
        $counter++;
    }
    return $newpath;
}