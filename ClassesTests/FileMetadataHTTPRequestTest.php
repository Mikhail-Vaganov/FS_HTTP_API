<?php

include_once __DIR__.'\..\common.php';

class FileMetadataHTTPRequestTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost:'.S_PORT
        ]);
    }

    public function testMatchNumberOfFilesInDirectoryAndInAnswer()
    {
        $response = $this->client->get('/fsapi/metadata',[ [] ]);
        $data = json_decode($response->getBody(), true);

        $files = scandir(WORKING_FOLDER);
        $files_number = count($files)-2;
        $this->assertEquals($files_number, count($data));
    }

    public function testResponseStatus200ForAllMetadata()
    {
        $response = $this->client->get('/fsapi/metadata');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFieldsInResponseToFileMetadata()
    {
        $test_line = "Hello world!";
        $file_name = "unit_test.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        file_put_contents($file_path,$test_line);

        $response = $this->client->get('/fsapi/metadata/'.$file_name);

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('size', $data);
        $this->assertArrayHasKey('bytes', $data);
        $this->assertArrayHasKey('modified', $data);
        $this->assertArrayHasKey('path', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('extension', $data);
        $this->assertArrayHasKey('mimetype', $data);
    }

    public function testResponseStatus404ForNonExistentFileMetadata()
    {
        $file_name = "unit_test.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        if(file_exists($file_path))
            unlink($file_path);

        $response = $this->client->get('/fsapi/metadata/'.$file_name,['http_errors' => false]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @dataProvider additionProviderResponseCode400
     */
    public function testResponseStatus400ForWrongRequestsFileMetadata($path)
    {
        $response = $this->client->get($path,['http_errors' => false]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function additionProviderResponseCode400()
    {
        return array(
            'wrong path 1' => array("/fsapi"),
            'wrong path 2' => array("/fsapi/wrong"),
            'wrong path 3' => array("/fsapi/metadatas")
        );
    }

    public function testNotImplementedDeleteProcessor()
    {
        $response = $this->client->delete("/fsapi/metadata/",['http_errors' => false]);
        $this->assertEquals(501, $response->getStatusCode());
    }

    public function testNotImplementedPostProcessor()
    {
        $response = $this->client->post("/fsapi/metadata/",['http_errors' => false]);
        $this->assertEquals(501, $response->getStatusCode());
    }

    public function testNotImplementedPutProcessor()
    {
        $response = $this->client->put("/fsapi/metadata/",['http_errors' => false]);
        $this->assertEquals(501, $response->getStatusCode());
    }
}
