<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;

class MsgService {
    const SUCCESS = "success";
    public function process($appid,$type) {
        $params  = Request::param();
        Log::write('MsgService appid:'. $appid . ',type:'.$type . ',source:' .json_encode($params));
        return self::SUCCESS;
    }

}
?>