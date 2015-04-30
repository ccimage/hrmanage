<?php

function encrypt($data)
{
    //return md5($data);
    return md5(C("AUTH_CODE") . md5($data));
}

/**
 * 输出表单的一项
 * @param $label=表单左边label的文字；
 * $type=[text,radio,checkbox,button,submit,rest等...]；
 * $require=是否必填；
 * $name=提交到服务端的名称
 * $textalign=文字对齐；
 * $descttext=第三列说明，可以空
 */
function outputTextItem($label, $type, $require, $name, $desctext, $maxlength,$value='',$id){
    $resultHtml = '<div class="am-g am-margin-top">';
    $resultHtml .= '<div class="am-u-sm-4 am-u-md-2 am-text-right">';
    $resultHtml .= $label;
    $resultHtml .= '</div>';
    $finish = !($desctext != null && $desctext != '');
    if($finish){
        $resultHtml .= '<div class="am-u-sm-8 am-u-md-4 am-u-end">';
    }
    else{
        $resultHtml .= '<div class="am-u-sm-8 am-u-md-4">';
    }
    if($maxlength && $maxlength!=''){
        $maxlength = 'maxlength='.$maxlength;
    }
    $idhtml = $id ? 'id='.$id : '';
    if($type == 'textarea'){
        $resultHtml .= '<textarea '.$idhtml.' class="am-input-sm" name="'.$name.'" rows="4" '.$maxlength.'>'.$value.'</textarea>';
    }
    elseif($type == 'datetime'){
        $resultHtml .= '<div class="am-form-group am-form-icon">';
        $resultHtml .= '<i class="am-icon-calendar"></i>';
        $resultHtml .= '<input '.$idhtml.' type="text" class="am-form-field am-input-sm" readonly name="'.$name.'" placeholder="时间" value="'.$value.'">';
        $resultHtml .= '</div>';
    }
    else{
        $resultHtml .= ' <input '.$idhtml.'  type="'.$type.'" class="am-input-sm" name="'.$name.'" '.$require.' '.$maxlength.' value="'.$value.'" />';
    }
    $resultHtml .= ' </div>';
    if(!$finish){
        $resultHtml .= ' <div class="am-hide-sm-only am-u-md-6"><small>'.$desctext.'</small></div>';
    }

    $resultHtml .= ' </div>';
    return $resultHtml;
}
/**
 * 输出表单中的下拉列表
 * @param $label=表单左边label的文字；
 * $options 数据array，格式为每个item都包括value和display
 * $name=提交到服务端的名称
 * $dropup=是否上拉菜单；false为下拉
 */
function outputSelectItem($label, $options, $value=0, $name, $dropup){
    $resultHtml  = '<div class="am-g am-margin-top">';
    $resultHtml .= '<div class="am-u-sm-4 am-u-md-2 am-text-right">'.$label.'</div>';
    $resultHtml .= '<div class="am-u-sm-8 am-u-md-10">';
    $resultHtml .= '<select data-am-selected1="{btnSize: \'sm\', dropUp: '.$dropup.'}" name="'.$name.'">';
    foreach ($options as $val=>$o) {
        $selectStr = $value == $o['value'] ? 'selected' : '';
        $resultHtml .= '<option value="'.$o['value'].'" '.$selectStr.'>'.$o['display'].'</option>';
    }

    $resultHtml .= '</select>';
    $resultHtml .= '</div>';
    $resultHtml .= '</div>';
    return $resultHtml;
}
/**
 * 输出可以折叠的菜单列表
 * @param $parentId=可折叠项目的id，作用是根据id显示选中的高亮；
 * $ulId 子项目的外层容器id，用于显示/隐藏
 * $name=提交到服务端的名称
 * $dropup=是否上拉菜单；false为下拉
 */
function outputCollapseMenu($parentIcon,$parentId,$ulId,$parentTitle, $collapsed, $subItems ){
    $resultHtml = '<li class="admin-parent" id="'.$parentId.'">';
    $resultHtml .= '<a class="am-cf am-collapsed" data-am-collapse="{target: \'#'.$ulId.'\'}">';
    $resultHtml .= '<span class="'.$parentIcon.'"></span> '.$parentTitle.' <span class="am-icon-angle-right am-fr am-margin-right"></span></a>';
    $classname = $collapsed == $parentId ? 'am-in' : '';
    $resultHtml .= '<ul class="am-list am-collapse admin-sidebar-sub '.$classname.'" id="'.$ulId.'">';
    foreach($subItems as $key=>$vo){
        $resultHtml .= '<li id="'.$vo['id'].'"><a href="'.$vo['link'].'"><span class="'.$vo['icon'].'"></span> &nbsp; '.$vo['title'];
        if($vo["badge"]){
            $resultHtml .= '     <span class="am-badge am-badge-secondary am-margin-right am-fr">'.$vo["badge"].'</span>';
        }
        $resultHtml .= '</a></li>';
    }
    $resultHtml .= '</ul>';
    $resultHtml .= '</li>';
    return $resultHtml;
}
function outputSingleMenu($title, $link, $icon, $id='',$badge=''){
    $item=getSubItem($title, $link, $icon, $id,$badge);
    $resultHtml = '<li id="'.$item['id'].'"><a href="'.$item['link'].'"><span class="'.$item['icon'].'"></span> &nbsp; '.$item['title'];
    if($item["badge"]){
        $resultHtml .= '     <span class="am-badge am-badge-secondary am-margin-right am-fr">'.$item["badge"].'</span>';
    }
    $resultHtml .= '</a></li>';
    return $resultHtml;
}
function getCollaseSubMenus(){
    $args = func_get_args();
    $result = array();
    foreach ( $args as $key => $value ){
        $result[] = $value;
    }
    return $result;
}
function getSubItem($title, $link, $icon, $id='',$badge=''){
    return array("id"=>$id, "title"=>$title,"link"=>$link,'icon'=>$icon,"badge"=>$badge);
}

function P(){
	$args = func_get_args();
    $url=$args[0];
    $param='';
    foreach ( $args as $key => $value ){
        if($key>0){
            if(strlen($param)>0){
				$param.='&';
        	}
        	$param.='id'.$key.'='.$value;
        }
    }
    return U($url,$param);
}
function CheckEqual($val01, $val02, $output){
    if($val01 == $val02) {
        return 'class='.$output;
    }
    return 'class='.$val01.' funck='.$val02;
}