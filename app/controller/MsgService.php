<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;
use app\extend\MyExtend;
use app\extend\support\WxHelper;
class MsgService {
    const SUCCESS = "success";
    public function text($appid) {
        $params  = Request::param();
        $fromUserName=Request::param('FromUserName');
        $toUserName = Request::param('ToUserName');
        $time = time();
        //Cache::store('redis')->set('param',$params,300);
        //Log::write('MsgService store to redis done');
        //$value = Cache::store('redis')->get('param');
//{"ToUserName":"gh_55a1a8fa07b4","FromUserName":"ojcLx6LEX7RchYEKU0m6Xa1oOwmA","CreateTime":1675078376,"MsgType":"text","Content":"\u5927\u5bb6\u597d","MsgId":23981356077141011,"appid":"wx8c6c5bc9d58d3b80"}
        Log::write('MsgService appid:'. $appid . ',msg type:'. 'text' . ',from user:' .$fromUserName);
        $wxHelper = new WxHelper();
        $ret = $wxHelper->sendTextCustomerMessage($fromUserName,"欢迎留言！");
        if(!empty($ret)) {
            Log::write('MsgService sendTextCustomerMessage:'.json_encode($ret));
        }
        return self::SUCCESS;
    }

    public function image($appid) {
        $params  = Request::param();
        $my_extend = new MyExtend();
        $new_appid = $my_extend->test($appid);
        Log::write('MsgService appid:'. $new_appid . ',msg type:'. 'image' . ',source:' .json_encode($params));
        return self::SUCCESS;
    }

    public function voice($appid) {
        $params  = Request::param();

        Log::write('MsgService appid:'. $appid . ',msg type:'. 'voice' . ',source:' .json_encode($params));
        return self::SUCCESS;
    }

    public function video($appid) {
        $params  = Request::param();

        Log::write('MsgService appid:'. $appid . ',msg type:'. 'video' . ',source:' .json_encode($params));
        return self::SUCCESS;
    }
}
?>