<?php

namespace Home\Controller;
use Home\Controller\ManageController;
class RecordController extends ManageController {
    private $tableRecord='checkrecord';
	private $tableUser='employee';
    private $mainmenu="menu-item-data";
    private $perPage=15; // 每一页显示的数据条数

    public function index(){
        $this->record();
    }
    
    public function record($current=1){
        $this->HighlightMenu($this->mainmenu,'subitem-record');
		$userList = M($this->tableUser)->field("workernum as value,realname as display")->select();
        $this->assign("userList", $userList);
        
    	$joinStr = "hr_employee as a on hr_checkrecord.workernum = a.workernum";
    	$whereStr= '';
    	$order='checkdate desc,workernum'; 
    	$this->ShowPageView($this->tableRecord, $joinStr, $whereStr,$current, $this->perPage,$order);
         
        $this->display(T('Data:record'));
    }

    //表单提交
    public function addrecord()
    {
        $table = $this->tableRecord;
        if(!$this->CheckRequiredField('checkdate','刷卡日期')){
            $this->record();
            return;
        }
        $id = trim(I('post.editId'));
        $datamodel = $this->FillDataModel(array('checkdate','checktime','workernum'));
        if($id==''||$id==null){
			$id=M($this->tableRecord)->getField("max(id)") + 1;
			$datamodel["id"]=$id;
            //新增记录
            M($table)->data($datamodel)->add();
        }
        else{
            //修改记录
            $datamodel["id"] = $id;
            M($table)->save($datamodel);

        }
        $this->record();
    }

    //
    public function recordedit($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        $model = M($this->tableRecord)->where('id='.$id)->find();
        if(!$model){
            $this->assign("errorstring","修改刷卡记录，但却找不到记录");
        }
        else{
            //dump($model);
            $this->assign("model",$model);
        }
        $this->record();
    }
    //
    public function recorddelete($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        M($this->tableRecord)->where('id='.$id)->delete();

        $this->record();
    }
	
	//导入数据
	public function importrecord(){
		$filename = $_FILES['file']['tmp_name'];
		if (empty ($filename)) {
			echo '请选择要导入的CSV文件！';
			exit;
		}
		$handle = fopen($filename, 'r');
		$result = $this->input_csv($handle); //解析csv
		$len_result = count($result);
		if($len_result==0){
			echo '没有任何数据！';
			exit;
		}
		//dump($result);

		$id=M($this->tableRecord)->getField("max(id)") + 1;
		$successCount = 0;
		for ($i = 0; $i < $len_result; $i++) { //循环获取各字段值
			$worker = $result[$i][0]; 
			$record = $result[$i][1];
			list($date, $time) = split(' ', $record);
			$vartime = date('H:i:s',strtotime($time));
			if($this->checkduplicate($worker, $date, $vartime)){
				continue;
			}			
			$data["workernum"]=$worker;
			$data["checkdate"]=$date;
			
			$data["checktime"]=$vartime;
			$data["id"]=$i+$id;
			M($this->tableRecord)->add($data);
			$successCount++;
		}
		fclose($handle); //关闭指针

		$this->assign('succstring', '导入成功，增加了'.$successCount.'条记录');
		$this->index();
	}
	private function input_csv($handle) {
		$out = array ();
		$n = 0;
		while ($data = fgetcsv($handle, 1000, ',')) {
			$num = count($data);
			for ($i = 0; $i < $num; $i++) {
				$out[$n][$i] = $data[$i];
			}
			$n++;
		}
		return $out;
	}
	private function checkduplicate($worker, $date, $time){
		$where = ' workernum='.$worker.' and checkdate=\''.$date.'\' and checktime=\''.$time.'\'';        
        $duplicate = M($this->tableRecord)->where($where)->find();
        if($duplicate != null){
            return true;
        }
        return false;
	}
}