# HTTP API for the file system management

The project represents the backend part of a kind of File store server.
HTTP API allows users to manage their files, namely:

* Read the list of files in a working directory.
* Retrieve a file stored on the server.
* Upload a file on the server using PUT or POST HTTP methods.
* Update a file on the server.
* Delete a file.
* Get metadata of a file.


## Development
- This project was developed using PHP 7.0.1, PhpStorm IDE, Xdebug extention and XAMPP web-server solution pack.
- The project is provided with test templates for built-in REST Client of PhpStorm.
- Unit tests were created by means of PHPUnit and Guzzle frameworks (use composer.json for managing dependences) .

## Necessary settings
- **xtension=php_fileinfo.dll** or \(fileinfo.so\) string should be uncommented in **php.ini**;
- the full path to the working directory should be specified in **configuration.php** as **WORKING_FOLDER** constant;
- local server port should be specified in **configuration.php** as S_PORT constant for the successful running of unit tests;
- max_execution_time value should be set at the appropriate value in configuration.php (40 sec. by default);
- the current project should be placed in **fsapi** directory in the document root of the web server;
- the request routing mechanism uses .htaccess file in case Apache server is running. In case of using built-in web-server routing.php should be sent as a parameter of the command line during the start of the server.

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
  * Returns: The specified file's content.
  * Errors: 404 - the file specified hasn't been found.
  
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
  * Uploads a file with name *file_name* using POST semantics.
  * Method: POST
  * Returns: The metadata of the created file.
  * Parameters: **autorename** - defines if the server should rename the uploading file, provided the file with the same name already exists. Values: 1 for true and 0 for false.
  * Request Body: The submitted file from a form or a random content to fill the created file.
  * Errors: 409 - the file with the same name already exists, 400 - the file name hasn't been specified.

4. /fsapi/files/\<file_name\>
  * Updates or creates a file with name *file_name* with the content of the request body.
  * Method: PUT
  * Returns: The metadata of the created or updated file.
  * Request Body: The file contents to be uploaded.
  * Errors: 400 - the file name hasn't been specified.

5. /fsapi/files/\<file_name\>
  * Deletes the file specified with *file_name*.
  * Method: DELETE
  * Returns: The metadata of the deleted file.
  * Errors: 404 - the file specified hasn't been found, 400 - the file name hasn't been specified.


### /fsapi/metadata
Url structure for work with **metadata** resources:

http://\<server_host\>:\<server_port\>/fsapi/metadata/\<file_name\>

1. /fsapi/metadata
  * Method: GET
  * Returns: The metadatas of all the files stored on the server in the working directory.
  
2. /fsapi/metadata/\<file_name\>
  * Method: GET
  * Returns: The specified file's metadata. The metadata of images has additional fields of width and height parameters.
  * Errors: 404 - the file specified hasn't been found.

An example of the metadata JSON response:
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
- **mimetype** - MIME type of the file's content.

Additional fields for images are:
- **height** - the height of the requested image;
- **width** - the width of the requested image.

## Exception messages
If there is an exception, the appropriate HTTP code will be set up in the HTTP response.
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
- **request** - the initial request line;
- **httpMethod** - HTTP method of the request.

## Testing
- Test samples for REST client of PhpStorm can be found in REST_tests folder.
- Another useful application for testing API is [Postman](https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?utm_source=chrome-ntp-launcher) Chrome plugin.
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
  
## Further development
- It will be useful to implement support of the gzip-encoded responses. In order to receive a gzip-encoded response one will have to fill two request headers:
```
  Accept-Encoding: gzip
  User-Agent: my program (gzip)
```
      On the server side the "gzencode* function will be used
  
- In order to implement authentication and authorization to this API, it is proposed to use a database, which will contain user rights information. All the necessary fields, e.g. user_name, password, etc, should be sent in a query string but not in the request body. In the case of using such database, we can also restrict user access to different files and folders even on different servers.
- SSL might be used with an appropriate web server and certificates.
