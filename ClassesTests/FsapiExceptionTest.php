<?php


include_once __DIR__.'\..\common.php';

class FsapiExceptionTest extends PHPUnit_Framework_TestCase
{
    private $file_name;

    protected function setUp()
    {
        $this->file_name="unit_test.txt";
        $file_path= GetFilePathInWorkingDir($this->file_name);

        if(file_exists($file_path))
            unlink($file_path);
    }

    /**
     * @expectedException FsapiException
     */
    public function testExceptionFromFileGet()
    {
        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/files/".$this->file_name;
        $_SERVER['REQUEST_METHOD'] = "GET";
        $fileSystemRequestProcessor = new FileSystemRequestProcessor();
        $fileSystemRequestProcessor->get();
    }

    /**
     * @expectedException FsapiException
     */
    public function testExceptionFromFileDelete()
    {
        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/files/".$this->file_name;
        $_SERVER['REQUEST_METHOD'] = "DELETE";
        $fileSystemRequestProcessor = new FileSystemRequestProcessor();
        $fileSystemRequestProcessor->delete();
    }

    /**
     * @expectedException FsapiException
     * @expectedExceptionCode 404
     */
    public function testExceptionHasRight404CodeInGETFileProcessor()
    {
        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/files/".$this->file_name;
        $_SERVER['REQUEST_METHOD'] = "GET";
        $fileSystemRequestProcessor = new FileSystemRequestProcessor();
        $fileSystemRequestProcessor->get();
    }

    /**
     * @expectedException FsapiException
     * @expectedExceptionCode 404
     */
    public function testExceptionHasRight404CodeInDeleteFileProcessor()
    {
        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/files/".$this->file_name;
        $_SERVER['REQUEST_METHOD'] = "DELETE";
        $fileSystemRequestProcessor = new FileSystemRequestProcessor();
        $fileSystemRequestProcessor->delete();
    }

    /**
     * @expectedException FsapiException
     * @expectedExceptionCode 404
     */
    public function testExceptionHasRight404CodeInGETMetadataProcessor()
    {
        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/metadata/".$this->file_name;
        $_SERVER['REQUEST_METHOD'] = "GET";
        $metadataSystemRequestProcessor = new FileMetadataRequestProcessor();
        $metadataSystemRequestProcessor->get();
    }

    /**
     * @expectedException FsapiException
     * @expectedExceptionCode 409
     */
    public function testExceptionHasRight409CodeInPOSTFileProcessor()
    {
        $new_file_name = "new_unit_test.txt";
        $this->test_line = "Hello world!";
        $this->file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$new_file_name;
        file_put_contents($this->file_path,$this->test_line);

        $_SERVER['REQUEST_URI'] = "http://localhost:8080/fsapi/files/".$new_file_name;
        $_SERVER['REQUEST_METHOD'] = "POST";
        $fileSystemRequestProcessor = new FileSystemRequestProcessor();
        $fileSystemRequestProcessor->post();
    }

    /**
     * @expectedException FsapiException
     * @expectedExceptionCode 400
     * @dataProvider additionProviderFileGet400
     */
    public function testExceptionHasRight400CodeInProcessors($url, $method)
    {
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['REQUEST_METHOD'] = $method;

        $processorSelector = new FileSystemRequestSellector();
        $restProcessor = new RootRequestProcessor($processorSelector);
        $restProcessor->ProcessRequest();
    }

    public function additionProviderFileGet400()
    {
        return array(
            'wrong path GET' => array("http://localhost:8080/fsapi/".$this->file_name, 'GET'),
            'wrong path PUT' => array("http://localhost:8080/fsapi/".$this->file_name, 'PUT'),
            'wrong path DELETE' => array("http://localhost:8080/fsapi/".$this->file_name, 'DELETE'),
            'wrong path POST' => array("http://localhost:8080/fsapi/".$this->file_name, 'POST'),
            'wrong resource GET' => array("http://localhost:8080/fsapi/sdf/".$this->file_name, 'GET'),
            'wrong resource PUT'  => array("http://localhost:8080/fsapi/sdf/".$this->file_name, 'PUT'),
            'wrong resource DELETE'  => array("http://localhost:8080/fsapi/sdf/".$this->file_name, 'DELETE'),
            'wrong resource POST'  => array("http://localhost:8080/fsapi/sdf/".$this->file_name, 'POST'),
            'wrong request GET' => array("http://localhost:8080/".$this->file_name, 'GET'),
            'wrong request PUT' => array("http://localhost:8080/".$this->file_name, 'PUT'),
            'wrong request DELETE' => array("http://localhost:8080/".$this->file_name, 'DELETE'),
            'wrong request POST' => array("http://localhost:8080/".$this->file_name, 'POST'),
            'wrong path GET metadata' => array("http://localhost:8080/metadata/", 'GET')
        );
    }

}
