<?php
defined('IN_PHPCMS') or exit('No permission resources. - oacarmgn.php');
pc_base::load_app_class('admin', 'admin', 0);

class oacarmgn extends admin {
	
    public function __construct() {
		//�̳и��๹�캯��
        parent::__construct();
		//��ȡ���û����ļ�
        $setting = new_html_special_chars(getcache('oacarmgn', 'commons'));
        $this->set = $setting[$this->get_siteid()];
		// ����form
		pc_base::load_sys_class('form');
		// ��������ģ��
        $this->carinfo_db = pc_base::load_model('oacarinfo_model');
		$this->carbook_db = pc_base::load_model('oacarbook_model');
		$this->depart_db = pc_base::load_model('member_group_model');
		$this->user_db = pc_base::load_model('member_model');
		// Ĭ�ϻ�ȡ����user
		$userlist = $this->user_db->listinfo('','userid DESC');
		// Ĭ�ϻ�ȡ����group
		$departlist = $this->depart_db->listinfo('','groupid DESC');
		foreach($userlist as $info){
			$this->user_status[$info['userid']] = $info['nickname'];
		}
 		foreach($departlist as $info) {
			$this->depart_status[$info['groupid']] = $info['name'];
		}
    }
    
    public function init() {
		$page = isset($_GET['page']) && intval($_GET['page'])?intval($_GET['page']):1;
        $depart_status = $this->depart_status;
		// Ĭ�ϲ�ѯ����ԤԼ��Ϣ
		$start_time = date("Y-m-01");
		$end_time = date("Y-m-d",strtotime("$start_time +1 month -1 day"));
		$where = " `bdate` >= '$start_time' AND `bdate` <= '$end_time' ";
        $ainfos = $this->carbook_db->listinfo($where, 'bid DESC', $page, $pages='10');
		foreach($ainfos as $ainfo){
			$infos[] = array(
				'bid' => $ainfo['bid'],
				'carname' =>$ainfo['carname'],
				'bperson' => $this->user_status[$ainfo['userid']],
				'bdeparment' => $this->depart_status[$ainfo['groupid']],
				'bdate' => $ainfo['bdate'],
				'addtime' => $ainfo['addtime'],
				'comment' => $ainfo['comment'],
				'flag' => $ainfo['flag']
			);
		}
		$pages = $this->carbook_db->pages;
        include $this->admin_tpl('oacarbook_list');
    }
    
	/* ��ѯ����ԤԼ�б� */
    public function select() {
		$depart_status = $this->depart_status;
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$where = '';
		if(isset($_POST['dosubmit'])){
			extract($_POST['search']);
			if($departid){
				$where .= $where ? " AND `groupid` = '$departid' " : " `groupid` = '$departid' ";
			}
			if($start_time && $end_time){
				$start = date("Y-m-d",strtotime($start_time));
				$end = date("Y-m-d",strtotime($end_time));
				$where .= $where ? " AND `bdate` >= '$start' AND `bdate` <= '$end' " : " `bdate` >= '$start' AND `bdate` <= '$end' ";
			}
		}
        $ainfos = $this->carbook_db->listinfo($where, 'bid DESC', $page, $pages='10');
		foreach($ainfos as $ainfo){
			$infos[] = array(
				'bid' => $ainfo['bid'],
				'carname' =>$ainfo['carname'],
				'bperson' => $this->user_status[$ainfo['userid']],
				'bdeparment' => $this->depart_status[$ainfo['groupid']],
				'bdate' => $ainfo['bdate'],
				'addtime' => $ainfo['addtime'],
				'comment' => $ainfo['comment'],
				'flag' => $ainfo['flag']
			);
		}
		$pages = $this->carbook_db->pages;
        include $this->admin_tpl('oacarbook_select_list');
    }
	
    /* ���������б� */
    public function carlist() {
        $where = array('siteid'=>$this->get_siteid());        
        $infos = $this->carinfo_db->listinfo($where, 'carid DESC');
        include $this->admin_tpl('oacar_list');
    }
    
	 /* ��ӳ��� */
    public function addcar() {
        if(isset($_POST['dosubmit'])){
			//$colors = array("#360","#f30","#06c");
			//$key = array_rand($colors);
			//$color = $colors[$key];
            $_POST['oacar']['cardate'] = date("Y-m-d H:i:s");
			$_POST['oacar']['siteid'] = $this->get_siteid();
			$cid = $this->carinfo_db->insert($_POST['oacar'],true);
			if(!$cid)return FALSE;
			showmessage(L('operation_success'),HTTP_REFERER);

         } else {
			$show_validator = $show_scroll = true;
			include $this->admin_tpl('car_add');
        }
    }
	
    /**
    * ɾ��������Ϣ
    */
    public function deletecar() {
        if((!isset($_GET['carid']) || empty($_GET['carid'])) && (!isset($_POST['carid']) || empty($_POST['carid']))) {
            showmessage(L('δѡ��'), HTTP_REFERER);
        }
        if(is_array($_POST['carid'])){
			//����ɾ��
            foreach($_POST['carid'] as $id_arr) {
                $id_arr = intval($id_arr);
                $this->carinfo_db->delete(array('carid'=>$id_arr)); 
            }
            showmessage(L('ɾ���ɹ�'),HTTP_REFERER);
        }else{
            $id = intval($_GET['carid']);
            if($id < 1) return false;
            $result = $this->carinfo_db->delete(array('carid'=>$id));
            if($result){
                showmessage(L('ɾ���ɹ�'),HTTP_REFERER);
            }else {
                showmessage(L("ɾ��ʧ��"),HTTP_REFERER);
            }
        }
    }
	/**
	* �޸ĳ�����Ϣ
	*/
	public function updatecar(){
		if(isset($_POST['dosubmit'])){
			$carid=intval($_GET['carid']);
			if($carid < 1) return false;
			if(!is_array($_POST['oacar']) || empty($_POST['oacar'])) return false;
			if((!$_POST['oacar']['carname']) || empty($_POST['oacar']['carname'])) return false;
			$post_data = trim_script($_POST);
			$this->carinfo_db->update($post_data['oacar'],array('carid'=>$carid));
			showmessage(L('operation_success'),'?m=oacarmgn&c=oacarmgn&a=updatecar','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			$oacar = $this->carinfo_db->get_one(array('carid'=>$_GET['carid']));
			if(!$oacar)showmessage(L('��Ϣ������'));
			extract($oacar);
			include $this->admin_tpl('car_edit');
		}
	}
	/**
    * ɾ������ԤԼ��Ϣ
    */
    public function deletecarbook() {
        if((!isset($_GET['bid']) || empty($_GET['bid'])) && (!isset($_POST['bid']) || empty($_POST['bid']))) {
            showmessage(L('δѡ��'), HTTP_REFERER);
        }
        if(is_array($_POST['bid'])){
			//����ɾ��
            foreach($_POST['bid'] as $id_arr) {
                $id_arr = intval($id_arr);
                $this->carbook_db->delete(array('bid'=>$id_arr)); 
            }
            showmessage(L('ɾ���ɹ�'),HTTP_REFERER);
        }else{
            $bid = intval($_GET['bid']);
            if($bid < 1) return false;
            $result = $this->carbook_db->delete(array('bid'=>$bid));
            if($result){
                showmessage(L('ɾ���ɹ�'),HTTP_REFERER);
            }else {
                showmessage(L("ɾ��ʧ��"),HTTP_REFERER);
            }
        }
    }
	/**
	* �޸ĳ���ԤԼ��Ϣ
	*/
	public function updatecarbook(){
		if(isset($_POST['dosubmit'])){
			$bid=intval($_GET['bid']);
			if($bid < 1) return false;
			if(!is_array($_POST['oacarbook']) || empty($_POST['oacarbook'])) return false;
			if((!$_POST['oacarbook']['carname']) || empty($_POST['oacarbook']['carname'])) return false;
			$post_data = trim_script($_POST);
			$this->carbook_db->update($post_data['oacarbook'],array('bid'=>$bid));
			showmessage(L('operation_success'),'?m=oacarmgn&c=oacarmgn&a=updatecarbook','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			$carbook = $this->carbook_db->get_one(array('bid' => $_GET['bid']));
			if(!$carbook)showmessage(L('��Ϣ������'));
			//LOAD carlist
			$where = array('siteid'=>$this->get_siteid());        
			$carlist = $this->carinfo_db->listinfo($where, 'carid DESC');
			extract($carbook);
			include $this->admin_tpl('book_edit');
		}
	}
}
?>