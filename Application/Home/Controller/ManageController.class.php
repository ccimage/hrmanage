<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class ManageController extends BaseController {
    protected $MenuItemSelect = 'selectitem';
    protected $SubMenuItemSelect = 'selectsubitem';
	private $pageMax = 10; //分页显示时，显示的页码上限
    protected function HighlightMenu($mainmenu, $submenu)
    {
        $this->assign($this->MenuItemSelect,$mainmenu);
        $this->assign($this->SubMenuItemSelect,$submenu);
    }

    //检查数据是否为空
    protected function CheckRequiredField($fieldname, $label){
        $postname = 'post.'.$fieldname;
        if(trim(I($postname))=='' || I($postname) == null){
            $this->assign("errorstring",$label." 不能为空");
            return false;
        }
        return true;
    }
    //检查是否重复
    protected  function CheckDuplicateField($fieldname, $value,$id, $label, $table){

        $where = is_numeric($value) ? ' '.$fieldname.'='.$value.' ' : ' '.$fieldname.'=\''.$value.'\' ';
        if($id){
            $where .= 'and id<>'.$id;
        }
        
        $duplicate = M($table)->where($where)->find();
        if($duplicate != null){
            $this->assign("errorstring",'[数据重复]'.$label.' '.$value.' 已经存在');
            return true;
        }
        return false;
    }
    //根据表单数据填充数据模型
    protected function FillDataModel($filedArray){
        foreach($filedArray as $vo){
            $postname = 'post.'.$vo;
            $postValue = trim(I($postname));
            $datamodel[$vo] = $postValue;
        }
        return $datamodel;
    }
	
	protected function ShowPageView($table, $joinStr, $whereStr,$current=1, $perPage=15,$order=''){
		$totalcount = M($table)->join($joinStr)->where($whereStr)->getField('count(*)');
	
		$resArray = $this->_CheckPageValue($current, $totalcount,$perPage);
    	$current=$resArray[0];
    	$pagecount=$resArray[1];
    	
    	$viewcount = min($perPage, $totalcount - $current * $perPage);
    	$limitStr=$current*$perPage.','.$viewcount;
        $alllist = M($table)->join($joinStr)->where($whereStr)->limit($limitStr)->order($order)->select();
        $this->assign("list",$alllist);
 
        $this->_AssignPager($current,$pagecount,$perPage);
	}
	protected function ShowPageViewWithList($alllist, $totalcount, $current=1, $perPage=15){
		$resArray = $this->_CheckPageValue($current, $totalcount,$perPage);
    	$current=$resArray[0];
    	$pagecount=$resArray[1];
        $this->assign("list",$alllist);
 
        $this->_AssignPager($current,$pagecount,$perPage);
	}
	private function _CheckPageValue($current, $totalcount, $perPage){
		if($current<1){
    		$current = 1;
    	}
    	$current=$current-1;
    	$pagecount = (int)($totalcount / $perPage);
    	if($totalcount % $perPage != 0){
    		$pagecount++;
    	}
    	
    	if($current>=$pagecount){
    		$current=$pagecount-1;
    	}
    	return array($current, $pagecount);
	}
	private function _AssignPager($current,$pagecount,$perPage){
		$minPage=max(0,$current+1-$this->pageMax/2);
        $maxPage=min($pagecount,$current+1+$this->pageMax/2);
        $pagelist=array();
        for ($i=$minPage; $i<$maxPage; $i++) {
  			$pagelist[]=$i+1;
		}
        $pager['prev']=$current==0?0:1;
        $pager['next']=$current==$pagecount-1?0:1;
        $pager['current']=$current+1;
        $pager['before']=$current;
        $pager['pagecount']=$pagecount;
        $pager['after']=$current+2;
        $pager['perpage']=$perPage;
        
        $this->assign("pager", $pager);
        $this->assign("pagelist", $pagelist);
	}
	
}