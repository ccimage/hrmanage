<?php
/**
 * Created by project: car4sweb.
 * User: zhang
 * Date: 2015/4/12
 * Time: 23:04
 */

namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function index()
    {
        $ctrl = CONTROLLER_NAME;

        $this->assign("errormessage", "不存在的控制器访问：".$ctrl);
        $this->display("index/error");
    }
    public function _empty(){
        $this->index();
    }
}