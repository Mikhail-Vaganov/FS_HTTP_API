# HTTP API for file system handling
The test task

The project represents the backend part of a kind of File store server.
HTTP API allows users to manage their files, namely:

* Read the list of files in a working directory.
* Retrieve a file stored on the server.
* Upload a file on the server using PUT or POST HTTP methods.
* Update a file on the server.
* Delete a file
* Get metadata of a file.


## Development
- This project was developed using PHP 7.0.1, PhpStorm IDE, Xdebug extention and XAMPP web-server solution pack.
- The project is provided with test templates for built-in REST Client of PhpStorm.
- Unit tests were created by means of PHPUnit and Guzzle frameworks (use composer.json for managing dependences) 

## Necessary settings
- the request routing mechanism uses .htaccess file in case Apache server is running. In case of using built-in web-server routing.php should be sent as a parameter of the command line during the start of the server;
- xtension=php_fileinfo.dll string should be uncommented in php.ini;
- working directory should be specified in configuration.php as WORKING_FOLDER constant;
- max_execution_time value should be set in appropriate value in configuration.php (40 sec. by default);
- the current project should be placed in **fsapi** directory in root folder of the web-server.


## HTTP API description
All the maintenance of the file system might be handled using the following RESTful API:

### /fsapi/files
Url structure for work with **files** resources:

http://\<server_host\>:\<server_port\>/fsapi/files/\<file_name\>


1. /fsapi/files
  * Method: GET
  * Returns: The list of all the files stored on the server in the working directory.
  
2. /fsapi/files/\<file_name\>
  * Method: GET
  * Returns: The specified file's contents.
  
  An example of HTTP headers in the response:
  ```
  HTTP/1.1 200 OK
  Date: Mon, 01 Feb 2016 12:30:10 GMT
  Server: Apache/2.4.18 (Win32) OpenSSL/1.0.2e PHP/7.0.1
  X-Powered-By: PHP/7.0.1
  Content-Length: 4334969
  Content-Disposition: attachment; filename=audio.mp3
  Keep-Alive: timeout=5, max=100
  Connection: Keep-Alive
  Content-Type: audio/mpeg
  ```

3. /fsapi/files/\<file_name\>
  * Method: POST
  * Returns: Uploads a file with name *file_name* using POST semantics.
  * Parameters: **autorename** - defines if the server should rename the uploading file, provided the file with the same name already exists. Values: 1 for true and 0 for false
  * Request Body: The submitted file from a form or a random content to fill the created file.

4. /fsapi/files/\<file_name\>
  * Method: PUT
  * Returns: Update or create file with name *file_name* with the content of the request body
  * Request Body: The file contents to be uploaded.

5. /fsapi/files/\<file_name\>
  * Method: DELETE
  * Returns: Deletes the file specified with *file_name*.


### /fsapi/metadata
Url structure for work with **metadata** resources:

http://\<server_host\>:\<server_port\>/fsapi/metadata/\<file_name\>

1. /fsapi/metadata
  * Method: GET
  * Returns: The metadatas of all the files stored on the server in the working directory.
  
2. /fsapi/metadata/\<file_name\>
  * Method: GET
  * Returns: The specified file's metadata. The metadata of images has additional fields like width and height, etc.

An example of a metadata JSON response:
```
{
    "size": "192.69KB",
    "bytes": 197312,
    "modified": "Sunday 2nd of August 2015 12:26:21 PM",
    "path": "local_store\\image.jpg",
    "name": "image.jpg",
    "extension": "JPG",
    "mimetype": "image/jpeg",
    "height": 915,
    "width": 855
}
```
This is the standard unit answer to any **metadata** request. 

The basic fields are:
- **size** - the user-friendly size of the file;
- **bytes** - size of the files in bytes;
- **modified** -  the file modified date;
- **path** - the path to the file resource;
- **name** - the name of the requested file;
- **extension** - the extension of the requested file;
- **mimetype** - MIME type of the file's content

Additional fields for images are:
- **height** - the height of the requested image
- **width** - the width of the requested image

## Exception messages
If there is an exception, the appropriate HTTP code will be set up in HTTP response.
The body of such response will contain serialized exception, e.g. in response to the reading metadata of a non-existent file:
```
HTTP/1.1 404 Not Found
```
```
{
    "errorMessage": "File doesn't exist",
    "code": 404,
    "requestedFile": "file.txt",
    "request": "/fsapi/metadata/file.txt",
    "httpMethod": "GET"
}
```
This is the standard error answer to any failed request. The fields are:
- **errorMessage** - the short report about the error occurred;
- **code** - matches the HTTP response code number;
- **requestedFile** -  the name of the file requested;
- **request** - initial request line;
- **httpMethod** - HTTP method of the request;

## Testing
- Test samples for REST client of PhpStorm can be found in REST_tests folder
- Another useful application for testing API is [Postman](https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?utm_source=chrome-ntp-launcher) Chrome plugin
- One might use the built-in PHP web server. In this case create **fsapi** folder in the document root of the server  and copy the project in the created directory. Run the web server from the command line:
```
php.exe -S localhost:8080 -t <document_root> <document_root>\fsapi\routing.php
```
- There are some important unit tests which were created for the main features of the API:
  - FilesHTTPProcessorTest->testSuccessFileGet
  - FilesHTTPProcessorTest->testSuccessFilePost
  - FilesHTTPProcessorTest->testSuccessFilePutCreate
  - FilesHTTPProcessorTest->testSuccessFilePutUpdate
  - FilesHTTPProcessorTest->testSuccessFileDelete
  - FileMetadataHTTPRequestTest->testMatchNumberOfFilesInDirectoryAndInAnswer
  - FileMetadataHTTPRequestTest->testFieldsInResponseToFileMetadata
  
