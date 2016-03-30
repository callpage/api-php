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
    protected $baseUrl = 'api.callpage.io/api/v1/external/';
    /**
     * API Key for secured connection
     * @var string
     */
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    protected function isSSL()
    {
        if( !empty( $_SERVER['https'] ) && $_SERVER['https'] !== 'off' )
            return true;

        if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
            return true;

        return false;
    }

    protected function getEndpoint($endpoint, $params = null)
    {
        return 'https://' . $this->baseUrl . $endpoint . ($params? '?'.http_build_query($params) : '');
    }

    protected function getUrl()
    {
        $url  = 'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
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

    public function request($endpoint, $params = array(), $options = array())
    {

        if (function_exists('curl_version')) {

            return $this->requestCurl($endpoint, $params, $options);

        //curl doesnt exists
        } else {

            return $this->requestFileGetContents($endpoint, $params, $options);

        }

    }

    protected function requestCurl($endpoint, $params = array(), $options = array())
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

        } else {
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

    protected function requestFileGetContents($endpoint, $params = array(), $options = array())
    {
        $params['url'] = $this->getUrl();

        $opts = array('http' =>
            array(
                'content' => http_build_query($params),
                'header' => "Authorization: {$this->apiKey}"
            )
        );

        if ($options['method'] == 'post') {
            $url = $this->getEndpoint($endpoint);
            $opts['http']['method'] = 'POST';
            $opts['http']['header'] = "Authorization: {$this->apiKey}\r\n" .
                                      "Content-Type: application/x-www-form-urlencoded\r\n";
        } else {
            $url = $this->getEndpoint($endpoint);
        }

        $output = '';
        try {
            $output = json_decode(file_get_contents($url, false, stream_context_create($opts)), true);

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

}

