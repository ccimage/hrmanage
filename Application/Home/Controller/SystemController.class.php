<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\ManageController;
class SystemController extends ManageController {
    private $mainmenu = "menu-item-system";
    public function index(){
        $this->HighlightMenu($this->mainmenu, 'subitem-sysconfig');
		$model = M('setting')->find();
		$this->assign('model',$model);
        $this->display('index');
    }
	
	public function savesetting(){
		$model = M('setting')->find();
		$update = $model;
		$model['starttime']=I('post.starttime');
		$model['offtime']=I('post.offtime');
		if($update){
			M('setting')->where('starttime=\''.$update['starttime'].'\' and offtime=\''.$update['offtime'].'\'')->save($model);
		}
		else{
			M('setting')->add($model);
		}
		$this->index();
	}
}