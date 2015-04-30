<?php
namespace Home\Controller;
use Home\Controller\ManageController;
class UserController extends ManageController {
    private $tableUsers='employee';

    private $mainmenu="menu-item-data";

    public function index(){
		$this->users();
    }
    
    public function users(){
        $this->HighlightMenu($this->mainmenu,'subitem-users');
        $alllist = M($this->tableUsers)->where(' workernum<>\'admin\'')->select();
        $this->assign("list",$alllist);
        $this->display(T("Data:users"));
    }
     

    public function adduser(){
        $table = $this->tableUsers;
        if(!$this->CheckRequiredField('workernum','工号')){
            $this->users();
            return;
        }

        $pwd1 =  I('post.password');
        $pwd2 =  I('post.passwordAgain');

        if(strlen($pwd1)<=0 && strlen($pwd2)<=0){
        	$pwd1='111111';
        	$pwd2='111111';
        }
        if(strlen($pwd1)>0 && strlen($pwd1)<6){
            $this->onAddUserError("密码长度太短，至少6位");
            return;
        }
        if($pwd1!=$pwd2){
            $this->onAddUserError("两次输入的密码不一致");
            return;
        }
        $id = trim(I('post.editId'));
        $datamodel = $this->FillDataModel(array('workernum','password','realname'));
        $datamodel["password"] = encrypt($pwd1);
        if($this->CheckDuplicateField('workernum',$datamodel['workernum'],$id,'工号',$table)){
            $this->users();
            return;
        }

        if($id==''||$id==null){
            if(strlen($pwd1)<=0){
                $this->users();
                return;
            }
			$id=M($this->tableUsers)->getField("max(id)") + 1;
			$datamodel["id"]=$id;
            //新增记录
            M($table)->data($datamodel)->add();
        }
        else{
            //修改记录
            $datamodel["id"] = $id;
            M($table)->save($datamodel);

        }
        $this->users();
    }
    private function onAddUserError($errormsg){
        $this->assign("errorstring",$errormsg);
        $this->users();
    }

    //修改用户数据
    public function useredit($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        $model = M($this->tableUsers)->where('id='.$id)->find();
        if(!$model){
            $this->assign("errorstring","修改用户基础数据，但却找不到记录");
        }
        else{
            $this->assign("model",$model);
        }
        $this->assign("op","user");
        $this->users();
    }
    //删除用户数据
    public function userdelete($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        M($this->tableUsers)->where('id='.$id)->delete();

        $this->users();
    }
    public function passwordedit($id=0){
        if(!is_numeric($id) || $id<=0){
            $this->assign('errormessage', '参数错误--错误的页面入口');
            $this->display('Index/error');
            return;
        }
        $model = M($this->tableUsers)->where('id='.$id)->find();
        if(!$model){
            $this->assign("errorstring","修改用户密码，但却找不到记录");
        }
        else{
            $this->assign("model",$model);
        }
        $this->assign("op","pwd");
        $this->users();
    }
}