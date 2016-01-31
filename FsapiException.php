<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 31-Jan-16
 * Time: 17:34
 */
class FsapiException extends Exception implements JsonSerializable
{
    private $file_name;
    private $http_method;
    private $request_url;

    public function __construct($message, $code, $file_name, $http_method, $request_url, Exception $previous = null)
    {
        $this->file_name=$file_name;
        $this->http_method=$http_method;
        $this->request_url=$request_url;

        parent::__construct($message, $code, $previous);
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
        $answer = Array();
        $answer['errorMessage']=$this->message;
        $answer['code']=$this->code;
        $answer['requestedFile']=$this->file_name;
        $answer['request']=$this->request_url;
        $answer['httpMethod']=$this->http_method;
        return $answer;
    }
}