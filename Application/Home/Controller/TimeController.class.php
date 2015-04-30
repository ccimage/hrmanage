<?php
/**
 * Created by project: car4sweb.
 * User: zhang
 * Date: 2015/4/15
 * Time: 21:00
 */

namespace Home\Controller;
use Home\Controller\ManageController;
class TimeController extends ManageController {
    private $tableRange='timerange';

    private $mainmenu="menu-item-data";

    public function index(){
			$this->timerange();
    }
    public function timerange(){
        $alllist = M($this->tableRange)->select();
        $this->assign("list",$alllist);
		
		//选项
        $usertypelist=array();
        $usertypelist[0] = array('value'=>'1','display'=>'迟到');
        $usertypelist[1] = array('value'=>'2','display'=>'加班');
        $this->assign("rangeTypeList",$usertypelist);
		
        $this->HighlightMenu($this->mainmenu,'subitem-timerange');
		
        $this->display(T('Data:timerange'));
    }
    
    //表单提交  
    public function addrange()
    {
        $table = $this->tableRange;
        if(!$this->CheckRequiredField('rangefrom','时间范围开始')){
            $this->timerange();
            return;
        }
		if(!$this->CheckRequiredField('rangeto','时间范围结束')){
            $this->timerange();
            return;
        }
		
		$from = I("post.rangefrom");
		$to = I("post.rangeto");
		if(!is_numeric($from) || !is_numeric($to)){
			$this->assign("errorstring","只能填写数字");
			$this->timerange();
			return;
		}
		if($from >= $to){
			$this->assign("errorstring","不能一样大，前者必须小于后者");
			$this->timerange();
			return;
		}
        $id = trim(I('post.editId'));
        $datamodel = $this->FillDataModel(array('rangefrom','rangeto','rangetype'));

        if($id==''||$id==null){
			$id=M($this->tableRange)->getField("max(id)") + 1;
			$datamodel["id"]=$id;
            //新增记录
            M($table)->data($datamodel)->add();
        }
        else{
            //修改记录
            $datamodel["id"] = $id;
            M($table)->save($datamodel);

        }
        $this->timerange();
    }

  
    //
    public function rangeedit($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        $model = M($this->tableRange)->where('id='.$id)->find();
        if(!$model){
            $this->assign("errorstring","修改服务类型基础数据，但却找不到记录");
        }
        else{
            $this->assign("model",$model);
        }
        $this->timerange();
    }
    //
    public function rangedelete($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        M($this->tableRange)->where('id='.$id)->delete();

        $this->timerange();
    }

    
}