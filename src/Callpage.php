<?php
namespace Callpage\ApiPhp;

use Callpage\ApiPhp\Connector;

/**
 * Class Callpage
 * @package Callpage\ApiPhp
 */
class Callpage
{
    /** @var Connector */
    protected $connector;

    /**
     * Initialise API
     * @param $apiKey
     */
    public function __construct($apiKey, $widgetId)
    {
        //create api instance
        $this->connector = new Connector($apiKey);
        $this->encrypted_id = $widgetId;
    }

    /**
     * Make call and if no managers available send SMS to user and add scheduled call to the system
     * @param $to
     */
    public function callOrSchedule($to)
    {
        return $this->connector->post('widgets/callorschedule', array(
            'encrypted_id' => $this->encrypted_id,
            'tel' => $to,
        ));
    }

    /**
     * Make a call and throws an exception if no managers available
     * @param $to
     */
    public function call($to)
    {
        return $this->connector->post('widgets/call', array(
            'encrypted_id' => $this->encrypted_id,
            'tel' => $to
        ));
    }

}