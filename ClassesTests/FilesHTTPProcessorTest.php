<?php

include_once __DIR__.'\..\common.php';

class FilesHTTPProcessorTest extends PHPUnit_Framework_TestCase
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
        $response = $this->client->get('/fsapi/files',[ [] ]);
        $data = json_decode($response->getBody(), true);

        $files = scandir(WORKING_FOLDER);
        $files_number = count($files)-2;
        $this->assertEquals($files_number, count($data));
    }

    public function testResponseStatus200ForAllFiles()
    {
        $response = $this->client->get('/fsapi/files');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResponseStatus404ForGetNonExistentFile()
    {
        $file_name = "unit_test.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        if(file_exists($file_path))
            unlink($file_path);

        $response = $this->client->get('/fsapi/files/'.$file_name,['http_errors' => false]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResponseStatus404ForDeleteNonExistentFile()
    {
        $file_name = "unit_test.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        if(file_exists($file_path))
            unlink($file_path);

        $response = $this->client->delete('/fsapi/files/'.$file_name,['http_errors' => false]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testSuccessFileDelete()
    {
        $file_name = "unit_test_delete.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        $test_line = "Hello world!";
        file_put_contents($file_path,$test_line);

        $response = $this->client->delete('/fsapi/files/'.$file_name);
        $this->assertFileNotExists($file_path, "File hasn't been deleted!");
    }

    public function testSuccessFileGet()
    {
        $file_name = "unit_test_create.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;

        $test_line = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ipsum ante, blandit vel luctus quis, pulvinar ut neque.
        Donec nulla ante, sodales sit amet sapien quis, vehicula tempor leo. Praesent cursus ac dui vel consequat. Nunc ultrices libero et porta dapibus.
        In vitae eleifend elit. Praesent rutrum elit tempus eros tempor, quis tristique orci semper. Curabitur auctor bibendum lorem vel consequat. Nullam a
         enim, vel mattis tellus. Integer blandit varius lorem, ut imperdiet sem aliquet in.";

        file_put_contents($file_path,$test_line);

        $response = $this->client->get('/fsapi/files/'.$file_name);

        $got_data=$response->getBody();
        $this->assertEquals($test_line, $got_data);
    }

    public function testSuccessFilePost()
    {
        $file_name = "unit_test_create_new_file.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        if(file_exists($file_path))
            unlink($file_path);

        $test_line = "Suspendisse finibus sagittis lorem a pulvinar. Aenean vulputate nulla rhoncus aliquam consequat.
         Fusce tristique magna id pretium porta. Aenean eu orci tincidunt, consequat massa et, porta arcu.
         Duis quis placerat urna. Aenean quis facilisis sem. Phasellus pulvinar auctor pretium.
          Duis pretium ante porttitor ex cursus aliquet. Aenean semper vulputate lorem id laoreet.";

        $response = $this->client->post('/fsapi/files/'.$file_name, ['body'=> $test_line]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertFileExists($file_path, "File hasn't been created!");
        $this->assertEquals($test_line, file_get_contents($file_path));
    }

    public function testCreateTheSameFileError409()
    {
        $file_name = "unit_test_create_the_same.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        $test_line = "Template";
        file_put_contents($file_path,$test_line);

        $response = $this->client->post('/fsapi/files/'.$file_name, ['http_errors' => false], ['body'=> "Template."]);

        $this->assertEquals(409, $response->getStatusCode());
    }

    public function testCreateTheSameFileUsingAutorename0Error409()
    {
        $file_name = "unit_test_create_the_same.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        $test_line = "Template";
        file_put_contents($file_path,$test_line);

        $response = $this->client->post('/fsapi/files/'.$file_name, ['http_errors' => false],
            [
                'query' => ['autorename' => 0],
                'body'=>"Template."
            ]
        );

        $this->assertEquals(409, $response->getStatusCode());
    }

    public function testCreateNewFileWithAlmostTheSameName200()
    {
        $file_name = "unit_test_rename.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        $test_line = "Template";
        file_put_contents($file_path,$test_line);

        $file_name_new="unit_test_rename(1).txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name_new;
        if(file_exists($file_path))
            unlink($file_path);

        $response = $this->client->post('/fsapi/files/'.$file_name,
            [
                'query' => ['autorename' => 1],
                'body'=>"Template."
            ]
        );

        $this->assertFileNotExists($file_name_new, "File hasn't been renamed and created!");
    }

    public function testSuccessFilePutUpdate()
    {
        $file_name = "unit_test_put_update.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;

        $test_line = "Suspendisse finibus sagittis lorem a pulvinar. Aenean vulputate nulla rhoncus aliquam consequat.
         Fusce tristique magna id pretium porta. Aenean eu orci tincidunt, consequat massa et, porta arcu.
         Duis quis placerat urna. Aenean quis facilisis sem. Phasellus pulvinar auctor pretium.
          Duis pretium ante porttitor ex cursus aliquet. Aenean semper vulputate lorem id laoreet.";

        file_put_contents($file_path,$test_line);

        $response = $this->client->put('/fsapi/files/'.$file_name,
            [
                'query' => ['autorename' => 1],
                'body'=>$test_line
            ]
        );

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($test_line, file_get_contents($file_path));
    }

    public function testSuccessFilePutCreate()
    {
        $file_name = "unit_test_put_create.txt";
        $file_path = WORKING_FOLDER.DIRECTORY_SEPARATOR.$file_name;
        if(file_exists($file_path))
            unlink($file_path);

        $test_line = "Suspendisse finibus sagittis lorem a pulvinar. Aenean vulputate nulla rhoncus aliquam consequat.
         Fusce tristique magna id pretium porta. Aenean eu orci tincidunt, consequat massa et, porta arcu.
         Duis quis placerat urna. Aenean quis facilisis sem. Phasellus pulvinar auctor pretium.
          Duis pretium ante porttitor ex cursus aliquet. Aenean semper vulputate lorem id laoreet.";


        $response = $this->client->put('/fsapi/files/'.$file_name,
            [
                'query' => ['autorename' => 1],
                'body'=>$test_line
            ]
        );

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertFileExists($file_path, "File hasn't been created!");
        $this->assertEquals($test_line, file_get_contents($file_path));
    }

    /**
     * @dataProvider additionProviderResponseCode400
     */
    public function testResponseStatus400ForWrongFileRequests($path)
    {
        $response = $this->client->get($path,['http_errors' => false]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function additionProviderResponseCode400()
    {
        return array(
            'wrong path 1' => array("/fsapi"),
            'wrong path 2' => array("/fsapi/wrong"),
            'wrong path 3' => array("/fsapi/file")
        );
    }
}
