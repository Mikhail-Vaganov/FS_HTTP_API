<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29-Jan-16
 * Time: 23:36
 */

include_once 'common.php';

interface iRestRequestProcessor
{
    public function post();
    public function get();
    public function put();
    public function delete();
}