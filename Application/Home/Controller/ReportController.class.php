<?php

namespace Home\Controller;
use Home\Controller\ManageController;
class ReportController extends ManageController {
    private $mainmenu="menu-item-report";
	private $tableRecord='checkrecord as c';
	private $tableSetting='setting as s';
	private $tableUser='employee as e';
	private $tableRange='timerange as r';
	private $perPage=15;
    public function index(){
		
    }
    //迟到情况统计
    public function latetimeReport($begin='',$end='',$worker=0,$current=1)
    {
        $this->HighlightMenu($this->mainmenu,'subitem-latetime');
        $this->_assignUserList();
        
        $latetime = $this->_getLateTime();
    
        //迟到时间段的最小值
        $miniLate = M($this->tableRange)->where("rangetype=1")->getField("min(rangeFrom)");
        $latetime += $miniLate;

        $temp =  $this->_assignBasicParam($begin,$end,$worker);  
        $begin = $temp[0];
        $end = $temp[1];
        $workerList = $temp[2];
     
        if($current<1){
        	$current=1;
        }
		$tatalIndex=0;
        foreach($workerList as $key=>$val){
        	$where = $this->_getWhere($begin,$end,$val['workernum']);
        	$personList =  $this->_findFirstCheckOfDay($where);  
			
			foreach($personList as $key1=>$val1){
				$checkdate = $this->_checkWorkDate($val1['checkdate']);
				if(!$checkdate){
					continue;
				}
				list($h,$m)=split(',',$val1['mint']);
				if(($h*60 + $m) <= $latetime){
					continue;
				}
				if($totalIndex>=($current-1)*$this->perPage && $totalIndex<($current)*$this->perPage){
					$val1['checktime']=$h.':'.$m;
					$alllist[] = $val1;  
				}	
				$totalIndex++;
			}
        }
        $this->ShowPageViewWithList($alllist,$totalIndex,$current,$this->perPage);
        $this->assign('selector','Report/latetimeReport');
        $this->assign("pagename", "迟到情况");
        $this->display('report');
    }
    //加班情况统计
    public function overtimeReport($begin='',$end='',$worker=0,$current=1){
        $this->HighlightMenu($this->mainmenu,'subitem-overtime');
        $this->_assignUserList();
        
        $overtime=$this->_getOverTime();
                
        //加班时间段的最小值
        $miniOver = M($this->tableRange)->where("rangetype=2")->getField("min(rangeFrom)");
        $overtime += $miniOver;
         
        $temp =  $this->_assignBasicParam($begin,$end,$worker);  
        $begin = $temp[0];
        $end = $temp[1];
        $workerList = $temp[2];
        
        if($current<1){
        	$current=1;
        }
		$tatalIndex=0;
        foreach($workerList as $key=>$val){
        	$where = $this->_getWhere($begin,$end,$val['workernum']);
        	$personList =  $this->_findLastCheckOfDay($where);  
			
			foreach($personList as $key1=>$val1){
				$checkdate = $this->_checkWorkDate($val1['checkdate']);
				if(!$checkdate){
					continue;
				}
				list($h,$m)=split(',',$val1['maxt']);
				if(($h*60 + $m) <= $overtime){
					continue;
				}
				if($totalIndex>=($current-1)*$this->perPage && $totalIndex<($current)*$this->perPage){
					$alllist[] = $val1;  
				}	
				$totalIndex++;
			}
        }
        $this->ShowPageViewWithList($alllist,$totalIndex,$current,$this->perPage);
        $this->assign('selector','Report/overtimeReport');
        $this->assign("pagename", "加班情况");
        $this->display('report');
    }
    //未打卡统计
    public function nocheckReport($begin='',$end='',$worker=0,$current=1){
        $this->HighlightMenu($this->mainmenu,'subitem-nocheck');
        $this->_assignUserList();
        
        //迟到时间
        $latetime=$this->_getLateTime();
		//加班时间
        $overtime=$this->_getOverTime();
         
        $temp =  $this->_assignBasicParam($begin,$end,$worker);  
        $begin = $temp[0];
        $end = $temp[1];
        $workerList = $temp[2];
            	 
        if($current<1){
        	$current=1;
        }
		$tatalIndex=0;
		$daysArray = $this->_getDaylist($begin, $end);
        foreach($workerList as $key=>$val){ 
        	$worker=$val['workernum'];
        	foreach($daysArray as $key1=>$val1){ //分解成每天查询
        		if(date("D",strtotime($val1))=='Sat' || date("D",strtotime($val1))=='Sun'){
        			continue;
        		}
        	    $where = $this->_getWhere($val1,$val1,$worker);
        		$personList =  $this->_findBothSideCheckOfDay($where); 
				if(count($personList)<=0) {
					$person = M($this->tableUser)->where('workernum='.$worker)->getField('realname');
					$id=1;
					$checktype=3;
					
					if($totalIndex>=($current-1)*$this->perPage && $totalIndex<($current)*$this->perPage){
						$temp = array('checkdate'=>$val1, 'id'=>$id, 'realname'=>$person);
						$temp["checktype"]=$checktype;
						$alllist[] = $temp;  
					}
					$totalIndex++;
				}
    		
    			foreach($personList as $key2=>$val2){
					list($h1,$m1)=split(',',$val2['mint']);
					list($h2,$m2)=split(',',$val2['maxt']);
					$checktype = 0;
					if(($h2*60 + $m2) < $overtime) //下班没打卡
					{
						$checktype=2;
					}
					if(($h1*60 + $m1) >= $overtime && ($h2*60 + $m2) >= $overtime)//上班没打卡
					{
						$checktype=1;
					}
					if($checktype>0){
						if($totalIndex>=($current-1)*$this->perPage && $totalIndex<($current)*$this->perPage){
							$val2["checktype"]=$checktype;
							$alllist[] = $val2;  
						}	
						$totalIndex++;
					}
				}
    		}
        }
        
        $this->ShowPageViewWithList($alllist,$totalIndex,$current,$this->perPage);
        $this->assign('selector','Report/nocheckReport');
        $this->assign("pagename", "未打卡");
        $this->display('nocheck');
    }
    //迟到次数统计
    public function countReportBase($begin='',$end=''){
    	$temp =  $this->_assignBasicParam($begin,$end,$worker);  
        $begin = $temp[0];
        $end = $temp[1];
        $workerList = $temp[2];
    	
    	$lateArray = $this->_getLateTimeCount($begin,$end,$workerList);
    	$overArray = $this->_getOverTimeCount($begin,$end,$workerList);
    	$nocheckArray = $this->_getNoCheckCount($begin,$end,$workerList);
    	
    	//dump($lateArray);
 
    	//将三种数据整理
    	for($i=0; $i<count($workerList);$i++){
    		if(count($lateArray[$i]) > 1 || count($nocheckArray[$i]) > 1){ // 有迟到或者未打卡的情况
    			//组织显示的文字
    			$worker=$workerList[$i]['workernum'];
    			$person = M($this->tableUser)->where('workernum='.$worker)->getField('realname');
    			$late = $this->_getCountString($lateArray[$i]);
    			$over = $this->_getCountString($overArray[$i]);
    			$afterCalc = $this->_caclLaterTime($lateArray[$i], $overArray[$i]);
    			$nocheck=$nocheckArray[$i]['nocheck'];
    			$nocheck=$nocheck?$nocheck.'次':'';
    			$alllist[] = array('realname'=>$person, 'latecount'=>$late,
    			'overtimecount'=>$over,'nocheckcount'=>$nocheck, 'totalcount'=>$afterCalc);
    		}
    	}
    	
    	$this->assign('list',$alllist);
    	
    }
    public function countReport($begin='',$end=''){
    	$this->HighlightMenu($this->mainmenu,'subitem-count');
    	
    	$this->countReportBase($begin,$end);
    	
    	//$this->assign('selector','Report/countReport');
        $this->assign("pagename", "次数");
        $this->display('count');
    }
    
    private function _getLateTimeCount($begin,$end, $workerList){
    	$latetime = $this->_getLateTime();
    	$range = M($this->tableRange)->where("rangetype=1")->select();
 		$maxiRange = M($this->tableRange)->where("rangetype=1")->getField("max(rangeTo)");
 		
    	foreach($workerList as $key=>$val){
    		$worker = $val['workernum'];
    		$where = $this->_getWhere($begin,$end,$worker);
        	$personList =  $this->_findFirstCheckOfDay($where);  
			
			$modelPerson = array('workernum'=>$worker);
			foreach($personList as $key1=>$val1){
			if(date("D",strtotime($val1['checkdate']))=='Sat' || date("D",strtotime($val1['checkdate']))=='Sun'){
        			continue;
        		}
				list($h,$m)=split(',',$val1['mint']);
				if(($h*60 + $m) <= $latetime){
					continue;
				}
				$lateMinute = $h*60+$m-$latetime;
				foreach($range as $key2=>$val2){
					if($val2['rangefrom']<$lateMinute 
					&& $val2['rangeto']>=$lateMinute){
						$modelPerson[''.$val2['rangeto'].'分钟 ']++;
					}
					
					if($maxiRange<$lateMinute){
						$modelPerson[' 超过'.$maxiRange.'分钟 ']++;
						break;
					}
				}
			}
			$timeArray[] = $modelPerson;
    	}
    	return $timeArray;
    }    
    private function _getOverTimeCount($begin,$end, $workerList){
    	$overtime = $this->_getOverTime();
    	$range = M($this->tableRange)->where("rangetype=2")->select();
		$maxiRange = M($this->tableRange)->where("rangetype=2")->getField("max(rangeTo)");
    	foreach($workerList as $key=>$val){
    		$worker = $val['workernum'];
    		$where = $this->_getWhere($begin,$end,$worker);
        	$personList =  $this->_findLastCheckOfDay($where);  
			
			$modelPerson = array('workernum'=>$worker);
			foreach($personList as $key1=>$val1){
				if(date("D",strtotime($val1['checkdate']))=='Sat' || date("D",strtotime($val1['checkdate']))=='Sun'){
        			continue;
        		}
				list($h,$m)=split(',',$val1['maxt']);
				if(($h*60 + $m) <= $overtime){
					continue;
				}
				$overMinute = $h*60+$m-$overtime;
				foreach($range as $key2=>$val2){
					if($val2['rangefrom']<$overMinute 
					&& $val2['rangeto']>=$overMinute){
						$modelPerson[''.$this->_overMinuteToTime($val2['rangefrom']).'后 ']++;
					}
					if($maxiRange<$overMinute){
						$modelPerson[''.$this->_overMinuteToTime($maxiRange).'后 ']++;
						break;
					}
				}
			}
			$timeArray[] = $modelPerson;
    	}
    	return $timeArray;
    }
    private function _getNoCheckCount($begin,$end, $workerList){
    	$daysArray = $this->_getDaylist($begin, $end);
		//加班时间
        $overtime=$this->_getOverTime();
    	foreach($workerList as $key=>$val){ 
        	$worker=$val['workernum'];
        	$modelPerson = array('workernum'=>$worker);
        	foreach($daysArray as $key1=>$val1){ //分解成每天查询
        		if(date("D",strtotime($val1))=='Sat' || date("D",strtotime($val1))=='Sun'){
        			continue;
        		}
        	    $where = $this->_getWhere($val1,$val1,$worker);
        		$personList =  $this->_findBothSideCheckOfDay($where); 
				if(count($personList)<=0) {
					//计算两次未刷卡
					$modelPerson['nocheck']+=2;
				}
    			foreach($personList as $key2=>$val2){
					list($h1,$m1)=split(',',$val2['mint']);
					list($h2,$m2)=split(',',$val2['maxt']);
					$checktype = 0;
					if(($h2*60 + $m2) < $overtime) //下班没打卡
					{
						$checktype=2;
					}
					if(($h1*60 + $m1) >= $overtime && ($h2*60 + $m2) >= $overtime)//上班没打卡
					{
						$checktype=1;
					}
					if($checktype>0){
						$modelPerson['nocheck']++;
					}
				}
    		}
    		$timeArray[] = $modelPerson;
        }
        return $timeArray;
    }
    
    private function _defaultBeginDate(){
        $week=date("D");
    	if($week == 'Mon'){
        	$lastmonday = date('Y-m-d',strtotime('-1Mon',strtotime('now')));
        }
        else{
        	$lastmonday = date('Y-m-d',strtotime('-2Mon',strtotime('now')));
        }
        return $lastmonday;
    }
    private function _defaultEndDate(){
        $week=date("D");
    	if($week == 'Fri'){
        	$lastfriday = date('Y-m-d',strtotime('now'));
        }
        else{
        	$lastfriday = date('Y-m-d',strtotime('-1Fri',strtotime('now')));
        }
        return $lastfriday;
    }
    private function _assignUserList(){
    	$userList = M($this->tableUser)->field("workernum as value,realname as display")->where(' disable=0')->select();
        $userList[] = array('value'=>'0','display'=>'全部');
        sort($userList);
        $this->assign("userList", $userList);
    }
    private function _assignBasicParam($begin,$end,$worker){
    	if(!$begin){
        	$begin=$this->_defaultBeginDate();;
        }
        if(!$end){
        	$end=$this->_defaultEndDate();
        }
        $this->assign("begindate",$begin);
        $this->assign("enddate",$end);
        $this->assign("workernum",$worker);
        
        $where=(strLen($worker)>0 && $worker>0)?' workernum='.$worker : ' disable=0';
        $workerList = M($this->tableUser)->field('workernum')->where($where)->select();
        return array($begin,$end,$workerList);
    }
    private function _getWhere($begin,$end,$worker){
        if(!is_numeric($worker)){
            $this->_empty();
            return;
        }
        if($worker <= 0){
            $where = ' 1=1 ';
        }
        else{
            $where = 'e.workernum='.$worker;
        }
    	
        $where.=' and e.disable=0';
        $where.=' and strftime(\'%Y%m%d\',checkdate)>=strftime(\'%Y%m%d\',\''.$begin.'\')';
        $where.=' and strftime(\'%Y%m%d\',checkdate)<=strftime(\'%Y%m%d\',\''.$end.'\')';
        return $where;
    }
    private function _findFirstCheckOfDay($where){
    	return M($this->tableRecord)
			->join('hr_employee as e on c.workernum = e.workernum')
			->field('c.id, checktime, realname,checkdate,min(strftime(\'%H,%M\',checktime)) mint')
			->where($where)
			->group('checkdate,c.workernum')->select();  
    }
    private function _findLastCheckOfDay($where){
    	return M($this->tableRecord)
			->join('hr_employee as e on c.workernum = e.workernum')
			->field('c.id, checktime, realname,checkdate,max(strftime(\'%H,%M\',checktime)) maxt')
			->where($where)
			->group('checkdate,c.workernum')->select();  
    }
    //获取最早和最迟的打卡记录
    private function _findBothSideCheckOfDay($where){
    	return M($this->tableRecord)
			->join('hr_employee as e on c.workernum = e.workernum')
			->field('c.id, checktime, realname,checkdate,min(strftime(\'%H,%M\',checktime)) mint,max(strftime(\'%H,%M\',checktime)) maxt')
			->where($where)
			->group('checkdate,c.workernum')->select();  
    }
    //迟到开始计算，也就是上班时间
    private function _getLateTime(){
    	$latetime = M($this->tableSetting)->limit('0,1')->field('strftime(\'%H\',s.starttime) hour, strftime(\'%M\',s.starttime) minute')->find();
    	$hour = $latetime['hour'];
        $minute = $latetime['minute'];
        return $hour * 60 + $minute;
    }
    //加班时间开始计算，也就是下班时间
    private function _getOverTime(){
    	$overtime = M($this->tableSetting)->limit('0,1')->field('strftime(\'%H\',s.offtime) hour, strftime(\'%M\',s.offtime) minute')->find();
        $hour = $overtime['hour'];
        $minute = $overtime['minute'];
        return $hour * 60 + $minute;
    }
    //获取日期列表，文本形式
    private function _getDaylist($begin,$end){
    	$begintime = strtotime($begin);
    	$endtime = strtotime($end);
    	if($begintime>$endtime){
    		return;
    	}
    	$daysArray = array();
    	for(;;$i++){
    		$theday = $i==0? $begin : date('Y-m-d',strtotime('+'.$i.'day',$begintime));
    		$daysArray[] = $theday;
    		if($theday==$end)
    			break;
 			
    	}
    	return $daysArray;
    }
    
    private function _getCountString($modelM){
		if(!$modelM){
			return '0';
		}
		$count = count($modelM);
    	for($i=1;$i<$count;$i++){
    		$keys = array_keys($modelM);
    		foreach($keys as $i=>$v){
    			if($i==0) continue;
    			$string.=$v.' '.$modelM[$v].'次 <br />';
    		}
    	}
    	return $string;
    }
    
    private function _overMinuteToTime($minute){
    	$overtime = $this->_getOverTime();
    	$hour = (int)($overtime+$minute) / 60;
    	$minute = (int)($overtime+$minute) % 60;
    	$hour = sprintf('%02d', $hour);
    	$minute = sprintf('%02d', $minute);
    	return $hour.':'.$minute;
    }
    
    //检查排除的日期，目前只排除周末
    private function _checkWorkDate($datestr){
    	$date = strtotime($datestr);
    	$week = date('D',$date);

    	if($week == 'Sat' || $week == 'Sun'){
    		return false;
    	}
    	return true;
    }  
    //计算迟到次数的最终结果
    //用加班次数抵扣迟到次数
    private function _caclLaterTime($laterM, $overM){
    	$over1 = intval($overM['19:00后 ']);
    	$over2 = intval($overM['20:00后 ']);
    	$over3 = intval($overM['22:00后 ']);
    	
    	$later1 = intval($laterM['15分钟 ']);
    	$later2 = intval($laterM['30分钟 ']);
    	$later3 = intval($laterM['60分钟 ']);
    	$later4 = intval($laterM[' 超过60分钟 ']);
    	
    	$total3 = max(0, $later3 - $over3);
    	$total2 = max(0, $later2 - $over2 - max(0, $over3-$later3));
    	$total1 = max(0, $later1 - $over1 - max(0, $over2+$over3-$later2-$later3)); 
    	
    	$resultstr .= $total1 > 0 ? '15分钟内 '.$total1.' 次 <br />' : '';
    	$resultstr .= $total2 > 0 ? '半小时内 '.$total2.' 次 <br />' : '';
    	$resultstr .= $total3 > 0 ? '1小时内 '.$total3.' 次 <br />' : '';
    	$resultstr .= $later4 > 0 ? '超1小时 '.$later4.' 次 <br />' : '';
    	return $resultstr;
    }
    
    //---------------
    //数据过滤
	public function datalist($begindate=null, $enddate=null, $workernum=0,$current=1)
    {
        if(!is_numeric($workernum)){
            $this->_empty();
            return;   
        }
        if(!$begindate){
        	$begindate=$this->_defaultBeginDate();;
        }
        if(!$enddate){
        	$enddate=$this->_defaultEndDate();
        }
        $this->HighlightMenu($this->mainmenu,'subitem-datalist');
		$this->_assignUserList();
        
    	$joinStr = "hr_employee as e on c.workernum = e.workernum";
    	$whereStr= $this->_getWhere($begindate,$enddate,$workernum);
         
    	$order='checkdate desc,workernum'; 
    	$this->ShowPageView($this->tableRecord, $joinStr, $whereStr,$current, $this->perPage,$order);
         
        $this->assign('model',array("begindate"=>$begindate,"enddate"=>$enddate,"workernum"=>$workernum)); 
        $this->display(T('Report:datalist'));
    }
}