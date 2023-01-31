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

    public function jsonPostExtractBody($uri,$json_array,$query=[],$headers=[]) {
        if(!empty($this->base_uri)) {
            $uri = $this->base_uri . $uri;
        }
        return $this->curlRequest($uri,true,$json_array,[],true);
        //return $this->requestExtractBody('POST',$uri,['json'=>$json_array,'query'=>$query,'headers'=>$headers]);
    }


    /**
     * Notes: curl发送http请求
     * User: 闻铃
     * DateTime: 2021/9/8 下午6:27
     * @param string $url 请求的url
     * @param bool $isPost 是否为post请求
     * @param array $data 请求参数
     * @param array $header 请求头 说明：应这样格式设置请求头才生效 ['Authorization:0f5fc4730e21048eae936e2eb99de548']
     * @param bool $isJson 是否为json请求，默认为Content-Type:application/x-www-form-urlencoded
     * @param int $timeOut 超时时间 单位秒，0则永不超时
     * @return mixed
     */
    function curlRequest(string $url, bool $isPost = true, array $data = [], array $header = [], bool $isJson = false, int $timeOut = 0): array
    {
        if (empty($url)) {
            return false;
        }

        //初始化curl
        $curl = curl_init();

        //如果curl版本，大于7.28.1，得是2才行 。 而7.0版本的php自带的curl版本为7.40.1.  使用php7以上的，就能确保没问题
        $ssl     = (strpos($url, 'https') !== false) ? 2 : 0;
        $options = [
            //设置url
            CURLOPT_URL            => $url,

            //将头文件的信息作为数据流输出
            CURLOPT_HEADER         => false,

            // 请求结果以字符串返回,不直接输出
            CURLOPT_RETURNTRANSFER => true,

            // 禁止 cURL 验证对等证书
            CURLOPT_SSL_VERIFYPEER => false,

            //identity", "deflate", "gzip“，三种编码方式，如果设置为空字符串，则表示支持三种编码方式。当出现乱码时，可设置此字符串
            CURLOPT_ENCODING       => '',

            //设置http版本。HTTP1.1是主流的http版本
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,

            //连接对方主机时的最长等待时间。设置为10秒时，如果对方服务器10秒内没有响应，则主动断开链接。为0则，不限制服务器响应时间
            CURLOPT_CONNECTTIMEOUT => 0,

            //整个cURL函数执行过程的最长等待时间，也就是说，这个时间是包含连接等待时间的
            CURLOPT_TIMEOUT        => $timeOut,

            //检查服务器SSL证书中是否存在一个公用名
            CURLOPT_SSL_VERIFYHOST => $ssl,

            //设置头信息
            CURLOPT_HTTPHEADER     => $header
        ];

        //post和get特殊处理
        if ($isPost) {
            // 设置POST请求
            $options[CURLOPT_POST] = true;

            if ($isJson && $data) {
                //json处理
                $data   = json_encode($data);
                $header = array_merge($header, ['Content-Type: application/json']);
                //设置头信息
                $options[CURLOPT_HTTPHEADER] = $header;

                //如果是json字符串的方式，不能用http_build_query函数
                $options[CURLOPT_POSTFIELDS] = $data;
            } else {
                //x-www-form-urlencoded处理
                //如果是数组的方式,要加http_build_query，不加的话，遇到二维数组会报错。
                $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            }
        } else {
            // GET
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';

            //没有？且data不为空,将参数拼接到url中
            if (strpos($url, '?') === false && !empty($data) && is_array($data)) {
                $params_arr = [];
                foreach ($data as $k => $v) {
                    array_push($params_arr, $k . '=' . $v);
                }
                $params_string        = implode('&', $params_arr);
                $options[CURLOPT_URL] = $url . '?' . $params_string;
            }
        }

        //数组方式设置curl，比多次使用curl_setopt函数设置在速度上要快
        curl_setopt_array($curl, $options);

        // 执行请求
        $response = curl_exec($curl);

        if(curl_errno($curl)!=0) {
            //关闭请求
            curl_close($curl);
            return false;
        }
        //返回的http状态码
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //关闭请求
        curl_close($curl);
        if($httpCode==200) {
            return $response;
        }
        return false;


    }


    private function curlRequestEx($method,$url,$options,$verify_ssl = false) {
        $ch = curl_init();
        if(!empty($this->base_uri)) {
            $url = $this->base_uri.$url;
        }
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        if($method=='POST') {
            curl_setopt($ch,CURLOPT_POST,true);
            if(isset($options['query'])) {
                curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($options['query']));
            }
        }
        // HTTPS
        if (!$verify_ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

    }

    private function guzzleRequest($method, $url, $options) {

        $options = $this->fixJsonIssue($options);
        $client = new Client();
        if(!empty($this->base_uri)) {
            if(empty($options['base_uri'])) {
                $options['base_uri'] = $this->base_uri;
            }
        }
        Log::info("request:" . $url . ",with:". json_encode($options));
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
        $response = $this->guzzleRequest($method,$url,$options);
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