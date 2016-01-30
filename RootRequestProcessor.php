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
        //echo "_SERVER['REQUEST_URI']".$_SERVER['REQUEST_URI']."<br/>";

        $restRequestProcessor = $this->_restRequestProcessorSelector->GetAppropriateRequestProcessor();

        switch ($method) {
            case 'PUT':
                $result=$restRequestProcessor->put();
                break;
            case 'POST':
                $result=$restRequestProcessor->post();
                break;
            case 'GET':
                $result=$restRequestProcessor->get();
                break;
            case 'DELETE':
                $result=$restRequestProcessor->delete();
                break;
            default:
                $result="Incorrect request";
                break;
        }
    }
}