<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;
use app\extend\MyExtend;
use think\facade\Cache;
class MsgService {
    const SUCCESS = "success";
    public function text($appid) {
        $params  = Request::param();
        Cache::store('redis')->set('param',$params,300);
        Log::write('MsgService store to redis done');
        $value = Cache::store('redis')->get('param');

        Log::write('MsgService appid:'. $appid . ',msg type:'. 'text' . ',source:' .json_encode($value));
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