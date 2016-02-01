<?php

include_once __DIR__.'\..\common.php';

class FileMetaDataTest extends PHPUnit_Framework_TestCase
{
    private $test_line;
    private $file_name;
    private $file_path;

    protected function setUp()
    {
        $this->test_line = "Hello world!";
        $this->file_name = "unit_test.txt";
        $this->file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$this->file_name;
        file_put_contents($this->file_path,$this->test_line);
    }

    protected function tearDown()
    {
        if(file_exists($this->file_path))
            unlink($this->file_path);
    }

    public function testFileName()
    {
        $meta=new FileMetaData($this->file_path);
        $this->assertEquals($this->file_name, $meta->GetFileName());
    }

    public function testFileCyrillicName()
    {
        file_put_contents($this->file_path,$this->test_line);

        $meta=new FileMetaData($this->file_path);

        $this->assertEquals($this->file_name, $meta->GetFileName());
    }

    public function testFilePath()
    {
        file_put_contents($this->file_path,$this->test_line);

        $meta=new FileMetaData($this->file_path);

        $this->assertEquals('fsapi/files/'.$this->file_name, $meta->jsonSerialize()['path']);
    }

    public function testFileSize()
    {
        file_put_contents($this->file_path,$this->test_line);

        $meta=new FileMetaData($this->file_path);

        $this->assertEquals(strlen($this->test_line), $meta->GetFileSize());
    }
}
