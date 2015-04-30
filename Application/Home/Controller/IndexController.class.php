<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends ReportController {
    public function index(){
        $this->assign("selectitem","menu-item-index");

       
        $this->countReportBase('','');
        $this->display();
    }
}