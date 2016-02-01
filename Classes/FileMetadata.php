<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 30-Jan-16
 * Time: 16:12
 */
class FileMetaData implements JsonSerializable
{

    private $size;
    private $bytes;
    private $modified;
    private $path;
    private $name;
    private $height;
    private $width;
    private $copyright;
    private $mimetype;
    private $extension;

    public function __construct(string $filePath)
    {
        $this->modified =date('l jS \of F Y h:i:s A', filemtime($filePath));
        $this->name=basename($filePath);
        $this->path=basename(WORKING_FOLDER).DIRECTORY_SEPARATOR.$this->name;
        $this->bytes=filesize($filePath);
        $this->size=HumanFilesize($this->bytes,2);

        $extension =strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));
        $this->extension=$extension;
        if($extension=="JPG" || $extension=="JPEG")
        {
            $exif = exif_read_data($filePath, 0, true);

            if(isset($exif['COMPUTED']))
            {
                $this->height=$exif['COMPUTED']['Height'] ?? null;
                $this->width=$exif['COMPUTED']['Width'] ?? null;
                $this->copyright=$exif['COMPUTED']['Copyright'] ?? null;
            }
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->mimetype=finfo_file($finfo, $filePath);
        finfo_close($finfo);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $arr = Array();
        $arr['size'] = $this->size;
        $arr['bytes'] = $this->bytes;
        $arr['modified'] = $this->modified;
        $arr['path'] = "fsapi/files/".rawurldecode($this->name);
        $arr['name'] = rawurldecode($this->name);
        $arr['extension'] = $this->extension;
        $arr['mimetype'] = $this->mimetype;

        if(isset($this->height))
            $arr['height'] = $this->height;
        if(isset($this->width))
            $arr['width'] = $this->width;
        if(isset($this->copyright))
            $arr['copyright'] = $this->copyright;
        return $arr;
    }

    public function GetFileSize()
    {
        return $this->bytes;
    }

    public function GetFileName()
    {
        return $this->name;
    }

    public function GetMime()
    {
        return $this->mimetype;
    }

}