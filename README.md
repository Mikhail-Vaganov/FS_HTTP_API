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



## HTTP API description
All the maintenance of the file system might be handled using the following RESTful API:


Url structure for working with **files** resources:

http://\<server_host\>:\<server_port\>/fsapi/files/\<file_name\>


1. /fsapi/files
  * Method: GET
  * Returns: The list of all files stored on the server.
  
2. /fsapi/files/\<file_name\>
  * Method: GET
  * Returns: The specified file's contents.

3. /fsapi/files/\<file_name\>
  * Method: POST
  * Returns: Uploads a file with name *file_name* using POST semantics.
  * Parameters: autorename - define is server should rename the uploading file, if the file with the same name already exists. Values are 1 for true anв 0 for false
  * Request Body: The submitted file from a form or a random contents to fill the created file.

4. /fsapi/files/\<file_name\>
  * Method: PUT
  * Returns: Update or create file with name *file_name* with the content of the request body.
  * Parameters: autorename - define is server should rename the uploaded file, if the file with the same name already exists. Values are 1 for true anв 0 for false.
  * Request Body: The file contents to be uploaded.

5. /fsapi/files/\<file_name\>
  * Method: DELETE
  * Returns: Deletes the file specified with *file_name*.




The path for file managing requests is fsapi/files.
The main request parameter is the file_name.
