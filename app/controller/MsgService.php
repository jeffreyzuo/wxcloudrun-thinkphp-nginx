<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;

class MsgService {
    const SUCCESS = "success";
    public function text($appid) {
        $params  = Request::param();

        Log::write('MsgService appid:'. $appid . ',msg type:'. 'text' . ',source:' .json_encode($params));
        return self::SUCCESS;
    }

}
?>