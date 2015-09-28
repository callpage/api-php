<?php
namespace Callpage\ApiPhp;
use Callpage\ApiPhp\Exceptions\CallpageRestException;

/**
 * Class Connector
 * @package Callpage\ApiPhp
 */
class Connector
{
    /**
     * API URL
     * @var string
     */
    protected $baseUrl = 'http://api.callpage.io/api/v1/external/';
    /**
     * API Key for secured connection
     * @var string
     */
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    protected function getEndpoint($endpoint, $params = null)
    {
        return $this->baseUrl . $endpoint . ($params? '?'.http_build_query($params) : '');
    }

    protected function getUrl()
    {
        $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }

    public function request($endpoint, $params = array(), $options = array())
    {
        $headers = array();
        $headers[] = "Authorization: {$this->apiKey}";

        $params['url'] = $this->getUrl();

        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Callpage API Client');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //method options
        if ($options['method'] == 'post') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($curl, CURLOPT_URL, $this->getEndpoint($endpoint));

        } elseif ($options['method' == 'get']) {
            curl_setopt($curl, CURLOPT_URL, $this->getEndpoint($endpoint, $params));
        }

        $output = '';
        try {
            // Send the request & save response to $resp
            $output = json_decode(curl_exec($curl), true);
            // Close request to clear up some resources
            curl_close($curl);

            if (!isset($output['hasError'])) {
                throw new CallpageRestException([
                    'message' => 'Something goes wrong.'
                ]);
            }

            if ($output['hasError']) {
                throw new CallpageRestException($output);
            }

            //return data
            return $output['data'];
        }
        catch (CallpageRestException $e) {
            throw new CallpageRestException($e->getOutput());
        }
        catch (\Exception $e) {
            throw new CallpageRestException($output);
        }
    }

    public function get($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, [
            'method' => 'get'
        ]);
    }

    public function post($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, [
            'method' => 'post'
        ]);
    }

}

