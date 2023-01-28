<?php
namespace app\controller;

use think\facade\Log;
use think\facade\Request;
class MsgRoute {
    public function service() {
        $params  = Request::param();
        Log::debug($params);
        return "done";
    }
}
?>