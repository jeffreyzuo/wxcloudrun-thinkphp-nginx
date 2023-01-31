<?php

namespace app\extend\support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

use think\facade\Log;

class HttpClient
{
    private $base_uri="";

    /**
     * @param $base_uri
     */
    public function __construct($base_uri)
    {
        $this->base_uri = $base_uri;
    }

    public function getClientEx($options): Client
    {
        return new Client($options);
    }
    public function getClient($base_uri=""): Client
    {
        if(!empty($base_uri)) {
            $client = $this->getClientEx(['base_uri' =>$base_uri]);
        } else
            $client = $this->getClientEx([]);
        return $client;
    }
    public function getExtractBody($uri,$options) {
        return $this->requestExtractBody('GET',$uri,$options);
    }

    public function formPostExtractBody($uri,$form_fields) {
        return $this->requestExtractBody('POST',$uri,['form_params'=>$form_fields]);
    }

    public function jsonPostExtractBody($uri,$json_array) {
        return $this->requestExtractBody('POST',$uri,['json'=>$json_array]);
    }

    public function request($method,$url,$options) {
        Log::info("request:" . $url . ",with:". json_encode($options));
        $options = $this->fixJsonIssue($options);
        $client = $this->getClient($this->base_uri);
        try {
           return $client->request($method,$url,$options);
        } catch (RequestException $exception) {
            Log::error($exception);
            return false;
        } catch (GuzzleException $e) {
            Log::error($e);

            return false;
        }
    }

    public function requestExtractBody($method,$url,$options) {
        $response = $this->request($method,$url,$options);
        if(empty($response)) return false;
        if($response->getStatusCode()==200) {
            return $response->getBody();
        }
        Log::error("requestExtractBody bad request:".$url);
        return false;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function fixJsonIssue(array $options): array
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }

            unset($options['json']);
        }

        return $options;
    }

}