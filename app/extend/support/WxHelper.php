<?php

namespace app\extend\support;
use app\extend\support\HttpClient;
use think\facade\Log;

class WxHelper
{
    private $wx_api="http://api.weixin.qq.com";


    public function sendTextCustomerMessage($touser,$content) {
        $message = ['touser'=>$touser,'msgtype'=>"text",'text'=>['content'=>$content]];
        return $this->sendCustomerMessage($message);
    }

    /**
     * @param $message array
     * @return false|mixed
     */
    public function sendCustomerMessage(array $message) {
        $http_client = new HttpClient($this->wx_api);
        //TODO 改成异步发送消息
        $response = $http_client->jsonPostExtractBody('/cgi-bin/message/custom/send',$message);
        if(empty($response)) return false;
        return $this->wxResponseExtract($response);
    }
    private function wxResponseExtract($response) {
        $obj = json_decode($response);
        if(isset($obj->errcode)) {
            if ($obj->errcode != 0) {
                Log::error($response);
                return false;
            }
        }
        return $obj;
    }
}