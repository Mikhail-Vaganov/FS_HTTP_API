<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 23:39
 */

include_once 'common.php';

interface iRestRequestProcessorSelector
{
    public function GetAppropriateRequestProcessor() : iRestRequestProcessor;
}