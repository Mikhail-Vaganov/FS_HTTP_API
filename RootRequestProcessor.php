<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 23:37
 */

include_once 'common.php';

class RootRequestProcessor
{
    private $_restRequestProcessorSelector;

    public function __construct(iRestRequestProcessorSelector $restRequestProcessorSelector )
    {
        $this->_restRequestProcessorSelector=$restRequestProcessorSelector;
    }

    public function ProcessRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $restRequestProcessor = $this->_restRequestProcessorSelector->GetAppropriateRequestProcessor();

        switch ($method) {
            case 'PUT': return $restRequestProcessor->put();
            case 'POST':return $restRequestProcessor->post();
            case 'GET': return $restRequestProcessor->get();
            case 'DELETE': return $restRequestProcessor->delete();
            default: throw new FsapiException('Wrong HTTP method',400,null,null,$_SERVER['REQUEST_URI']);
        }
    }
}