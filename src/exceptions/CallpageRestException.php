<?php
namespace Callpage\ApiPhp\Exceptions;

use Exception;

/**
 * Class CallpageRestException
 * @package Callpage\ApiPhp\Exceptions
 */
class CallpageRestException extends Exception
{
    protected $output;

    public function __construct($response)
    {
        $this->output = $response;
        $this->code = (isset($response) && isset($response['errorCode']))? $response['errorCode'] : 0;
        $this->message = (isset($response) && isset($response['message']))? $response['message'] : null;
        parent::__construct();
    }

    public function getOutput()
    {
        return $this->output;
    }

}