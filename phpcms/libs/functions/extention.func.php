<?php
/**
 *  extention.func.php �û��Զ��庯����
 *
 * @copyright			(C) 2005-2010 PHPCMS
 * @license				http://www.phpcms.cn/license/
 * @lastmodify			2010-10-27
 */


 /** 
 * ͨ��ָ��keyid��ʽ��ʾ���������˵� 
 * @param  $keyid �˵���id 
 * @param  $linkageid  �����˵�id,0���ö��� 
 * @param  $modelid ģ��id 
 * @param  $fieldname  �ֶ����� 
 */  
function show_linkage($keyid, $linkageid = 0, $modelid = '', $fieldname='zone') {  
        $datas = $infos = $array = array();  
        $keyid = intval($keyid);  
        $linkageid = intval($linkageid);  
        //��ǰ�˵�id  
        $field_value = intval($_GET[$fieldname]);  
        $urlrule = structure_filters_url($fieldname,$array,1,$modelid);  
        if($keyid == 0) return false;  
        $datas = getcache($keyid,'linkage');  
        $infos = $datas['data'];  
  
        foreach($infos as $k=>$v){  
                if($v['parentid']==$field_value){  
                        $array[$k]['name'] = $v['name'];  
                        $array[$k]['value'] = $k;  
                        $array[$k]['url'] = str_replace('{'.$fieldname.'}',$k,$urlrule);  
                        $array[$k]['menu'] = $field_value == $k ? '<em>'.$v['name'].'</em>' : '<a href='.$array[$k]['url'].'>'.$v['name'].'</a>' ;  
                        }  
                        }  
                        return $array;  
                        }  
                          
/** 
 * ����ɸѡURL 
 */                       
function structure_filters_url($fieldname,$array=array(),$type = 1,$modelid) {  
        if(empty($array)) {  
                $array = $_GET;  
                } else {  
                        $array = array_merge($_GET,$array);  
                        }  
        //TODO  
        $fields = getcache('model_field_'.$modelid,'model');  
        if(is_array($fields) && !empty($fields)) {  
                        ksort($fields);  
                        foreach ($fields as $_v=>$_k) {  
                                if($_k['filtertype'] || $_k['rangetype']) {  
                                        if(strpos(URLRULE,'.html') === FALSE) $urlpars .= '&'.$_v.'={$'.$_v.'}';  
                                        else $urlpars .= '-{$'.$_v.'}';  
                                        }  
                                        }  
                                        }  
        //��������α��̬������url���������apacheα��̬֧��9������  
        if(strpos(URLRULE,'.html') === FALSE) $urlrule =APP_PATH.'index.php?m=content&c=index&a=lists&catid={$catid}'.$urlpars.'&page={$page}' ;  
        else $urlrule =APP_PATH.'list-{$catid}'.$urlpars.'-{$page}.html';  
        //����get��ֵ����URL  
        if (is_array($array)) foreach ($array as $_k=>$_v) {  
                        if($_k=='page') $_v=1;  
                        if($type == 1) if($_k==$fieldname) continue;  
                        $_findme[] = '/{\$'.$_k.'}/';  
                        $_replaceme[] = $_v;  
                        }  
     //type ģʽ��ʱ�򣬹����ų����ֶ����Ƶ�����  
        if($type==1) $filter = '(?!'.$fieldname.'.)';  
        $_findme[] = '/{\$'.$filter.'([a-z0-9_]+)}/';  
        $_replaceme[] = '';  
        $urlrule = preg_replace($_findme, $_replaceme, $urlrule);  
        return         $urlrule;  
}  
  
/** 
 * ���ɷ�����Ϣ�е�ɸѡ�˵� 
 * @param $field   �ֶ����� 
 * @param $modelid  ģ��ID 
 */  
function filters($field,$modelid,$diyarr = array()) {  
        $fields = getcache('model_field_'.$modelid,'model');  
        $options = empty($diyarr) ?  explode("\n",$fields[$field]['options']) : $diyarr;  
        $field_value = intval($_GET[$field]);  
        foreach($options as $_k) {  
                $v = explode("|",$_k);  
                $k = trim($v[1]);  
                $option[$k]['name'] = $v[0];  
                $option[$k]['value'] = $k;  
                $option[$k]['url'] = structure_filters_url($field,array($field=>$k),2,$modelid);  
                $option[$k]['menu'] = $field_value == $k ? '<em>'.$v[0].'</em>' : '<a href='.$option[$k]['url'].'>'.$v[0].'</a>' ;  
        }  
        $all['name'] = 'ȫ��';  
        $all['url'] = structure_filters_url($field,array($field=>''),2,$modelid);  
        $all['menu'] = $field_value == '' ? '<em>'.$all['name'].'</em>' : '<a href='.$all['url'].'>'.$all['name'].'</a>';  
  
        array_unshift($option,$all);  
        return $option;  
}  
  
/** 
 * ��ȡ�����˵��㼶 
 * @param  $keyid     �����˵�����id 
 * @param  $linkageid �˵�id 
 * @param  $leveltype ��ȡ���� parentid ��ȡ����id child ��ȡʱ��������Ŀ arrchildid ��ȡ����Ŀ���� 
 */  
function get_linkage_level($keyid,$linkageid,$leveltype = 'parentid') {  
        $child_arr = $childs = array();  
        $leveltypes = array('parentid','child','arrchildid','arrchildinfo');  
        $datas = getcache($keyid,'linkage');  
        $infos = $datas['data'];  
        if (in_array($leveltype, $leveltypes)) {  
                if($leveltype == 'arrchildinfo') {  
                        $child_arr = explode(',',$infos[$linkageid]['arrchildid']);  
                        foreach ($child_arr as $r) {  
                                $childs[] = $infos[$r];  
                        }  
                        return $childs;  
                } else {  
                        return $infos[$linkageid][$leveltype];  
                }  
        }          
}  
  
// ����linkageid�ݹ鵽����  
function get_parent_url($modelid,$field,$linkageid=0,$array = array()){  
        $modelid = intval($modelid);  
        if(!$modelid || empty($field)) return false;  
        $fields = getcache('model_field_'.$modelid,'model');  
        $keyid = $fields[$field]['linkageid'];  
        $datas = getcache($keyid,'linkage');  
        $infos = $datas['data'];  
                  
        if(empty($linkageid)){  
                $linkageid = intval($_GET[$field]);  
                if(!$linkageid) return false;  
                }  
                  
                $urlrule = structure_filters_url($field,array(),1,$modelid);  
                $urlrule = str_replace('{$'.$field.'}',$infos[$linkageid]['parentid'],$urlrule);  
                array_unshift($array,array('name'=> $infos[$linkageid]['name'],'url'=>$urlrule));  
                if($infos[$linkageid]['parentid']){  
                        return get_parent_url($modelid,$field,$infos[$linkageid]['parentid'],$array);  
                        }  
                        return $array;  
                        }  
/** 
 * ����ɸѡʱ���sql��� 
 */  
function structure_filters_sql($modelid) {  
        $sql = $fieldname = $min = $max = '';  
        $fieldvalue = array();  
        $modelid = intval($modelid);  
        $model =  getcache('model','commons');  
        $fields = getcache('model_field_'.$modelid,'model');  
        $fields_key = array_keys($fields);  
        //TODO  
        $sql = '`status` = \'99\'';  
        foreach ($_GET as $k=>$r) {  
                if(in_array($k,$fields_key) && intval($r)!=0 && ($fields[$k]['filtertype'] || $fields[$k]['rangetype'])) {  
                        if($fields[$k]['formtype'] == 'linkage') {  
                                $datas = getcache($fields[$k]['linkageid'],'linkage');  
                                $infos = $datas['data'];  
                                if($infos[$r]['arrchildid']) {  
                                        $sql .=  ' AND `'.$k.'` in('.$infos[$r]['arrchildid'].')';  
                                        }  
                                        } elseif($fields[$k]['rangetype']) {  
                                                if(is_numeric($r)) {  
                                                        $sql .=" AND `$k` = '$r'";  
                                                        } else {  
                                                                $fieldvalue = explode('_',$r);  
                                                                $min = intval($fieldvalue[0]);  
                                                                $max = $fieldvalue[1] ? intval($fieldvalue[1]) : 999999;  
                                                                $sql .=" AND `$k` >= '$min' AND  `$k` < '$max'";  
                                                                }  
                                                                } else {  
                                                                        $sql .=" AND `$k` = '$r'";  
                                                                        }  
                                                                        }  
                                                                        }  
                                                                        return $sql;  
                                                                        }  
  
/** 
 * ��ҳ����ȥ�����ҳ�������� 
 */  
function makeurlrule() {  
        if(strpos(URLRULE,'.html') === FALSE) {  
                return url_par('page={$'.'page}');  
        }  
        else {  
                $url = preg_replace('/-[0-9]+.html$/','-{$page}.html',get_url());  
                return $url;  
        }  
}  

/** 
 * ���ɷ�����Ϣ�е�ɸѡ�˵� 
 * @param $field   �ֶ����� 
 * @param $modelid  ģ��ID 
 */  
function filters_select($field,$modelid,$diyarr = array()) {  
        $fields = getcache('model_field_'.$modelid,'model');  
        $options = empty($diyarr) ?  explode("\n",$fields[$field]['options']) : $diyarr;  
        $field_value = intval($_GET[$field]);  
        foreach($options as $_k) {  
                $v = explode("|",$_k);  
                $k = trim($v[1]);  
                $option[$k]['name'] = $v[0];
                $option[$k]['value'] = $k;  
                $option[$k]['url'] = structure_filters_url($field,array($field=>$k),2,$modelid);  
              	// $option[$k]['menu'] = $field_value == $k ? '<a href="#" class="ac">'.$v[0].'</a>' : '<a href='.$option[$k]['url'].'>'.$v[0].'</a>' ;  
                $option[$k]['menu'] = $field_value == $k ? '<option selected value='.$option[$k]['value'].'>'.$v[0].'</option>' : '<option value='.$option[$k]['value'].'>'.$v[0].'</option>' ;  
                
        }  
        $all['name'] = L('all');  
        $all['url'] = structure_filters_url($field,array($field=>''),2,$modelid);  
        //$all['menu'] = $field_value == '' ? '<a href="#" class="ac">'.$all['name'].'</a>' : '<a href='.$all['url'].'>'.$all['name'].'</a>';  
        $all['menu'] = $field_value == '' ? '<option selected>'.$all['name'].'</option>' : '<option value='.$all['url'].'>'.$all['name'].'</option>';  
          
        array_unshift($option,$all);  
        return $option;  
}  


/**
 * ����box�����ֶλ�ȡ��ʾ����
 * @param $field �ֶ�����
 * @param $value �ֶ�ֵ
 * @param $modelid �ֶ�����ģ��id
 */
function box($field, $value, $modelid='') {
        $fields = getcache('model_field_'.$modelid,'model');
        extract(string2array($fields[$field]['setting']));
        $options = explode("\n",$fields[$field]['options']);
        foreach($options as $_k) {
                $v = explode("|",$_k);
                $k = trim($v[1]);
                $option[$k] = $v[0];
        }
        $string = '';
        switch($fields[$field]['boxtype']) {
                        case 'radio':
                                $string = $option[$value];
                        break;

                        case 'checkbox':
                                $value_arr = explode(',',$value);
                                foreach($value_arr as $_v) {
                                        if($_v) $string .= $option[$_v];
                                }
                        break;

                        case 'select':
                                $string = $option[$value];
                        break;

                        case 'multiple':
                                $value_arr = explode(',',$value);
                                foreach($value_arr as $_v) {
                                        if($_v) $string .= $option[$_v].' ��';
                                }
                        break;
                }
                        return $string;
}

/**
 * ��ȡ�û���ϸ��Ϣ
 * ������$field�����û�������Ϣ,
 * ����field��ȡ�û�$field�ֶ���Ϣ
 */
function get_memberdetail($userid, $field='') {
if(!is_numeric($userid)) {
return false;
} else {
static $memberinfo;
if (!isset($memberinfo[$userid])) {
$member_db = pc_base::load_model('member_detail_model');
$memberinfo[$userid] = $member_db->get_one(array('userid'=>$userid));
}
if(!empty($field) && !empty($memberinfo[$userid][$field])) {
return $memberinfo[$userid][$field];
} else {
//return $memberinfo[$userid]; //����ֶ�û������ ��ʱ��ִ��
}
}

}

#�ݹ鷽��ʵ�����޼�����
function sortDept($data, $pid = 0, $level = 0){
        static $arr = array();
        foreach($data as $key=>$value){
                if($value['pid'] == $pid){
                        $value['level'] = $level;
                        $arr[] = $value;
                        sortDept($data, $value['id'], $level+1);
                }
        }
        return $arr;
}

function node($node, $pid=0){
        $arr = array();
        foreach($node as $value){
                if($value['pid'] == $pid){
                        $value['child'] = node($node, $value['id']);
                        $arr[] = $value;
                }
        }
        return $arr;
}




/**
 * game_swf�����ֶλ�ȡ��Ա��Ϣ
 */
function get_peoples(){
        $peoples = getcache('people_array','people');
        foreach($peoples as $key=>$val)
        {
                $arr[] = $val;
        }

        $str = "<select name='info[name]' >";
        foreach ($arr as $value) {
                $str .= "<option value='{$value['id']}'>{$value['title']} </option>";
        }
        $str .= "</select>";
        return $str;
}






?>