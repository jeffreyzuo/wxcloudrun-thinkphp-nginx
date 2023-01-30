<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;

class MsgService {
    const SUCCESS = "success";
    public function process(Request $request,$appid,$type) {
        //$params  = Request::param();
        $params = $request->all();
        Log::write('MsgService appid:'. $appid . ',msg type:'.$type . ',source:' .json_encode($params));
        return self::SUCCESS;
    }

}
?>