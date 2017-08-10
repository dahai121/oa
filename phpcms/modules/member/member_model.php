<?php
/**
 * ����Ա��̨��Աģ�Ͳ�����
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);
//ģ��ԭ�ʹ洢·��
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'member'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
//ģ�ͻ���·��
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

class member_model extends admin {
	
	private $db;
	
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('sitemodel_model');
	}

	/**
	 * ��Աģ���б�
	 */
	function manage() {
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$member_model_list = $this->db->listinfo(array('type'=>2, 'siteid'=>$this->get_siteid()), 'sort', $page, 10);

        $pages = $this->db->pages;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=member&c=member_model&a=add\', title:\''.L('add_model').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_model_add'));
        include $this->admin_tpl('member_model_list');
	}
			
	/**
	 * ��ӻ�Աģ��
	 */
	function add() {
		if(isset($_POST['dosubmit'])) {
			$info = array();
			$info['name'] = $_POST['info']['modelname'];
			$info['tablename'] = 'member_'.$_POST['info']['tablename'];
			$info['description'] = $_POST['info']['description'];
			$info['type'] = 2;
			$info['siteid'] = $this->get_siteid();
			
			if(!empty($_FILES['model_import']['tmp_name'])) {
				$model_import = @file_get_contents($_FILES['model_import']['tmp_name']);
				if(!empty($model_import)) {
					$model_import_data = string2array($model_import);				
				}
			}

			$is_exists = $this->db->table_exists($info['tablename']);
			if($is_exists) showmessage(L('operation_failure'),'?m=member&c=member_model&a=manage', '', 'add');

			$modelid = $this->db->insert($info, 1);
			if($modelid) {
				$model_sql = file_get_contents(MODEL_PATH.'model.sql');
				$tablepre = $this->db->db_tablepre;
				$tablename = $info['tablename'];
				$model_sql = str_replace('$tablename', $tablepre.$tablename, $model_sql);
				$this->db->sql_execute($model_sql);
				if(!empty($model_import_data)) {
					$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
					$tablename = $tablepre.$tablename;
					foreach($model_import_data as $v) {
						//�޸�ģ�ͱ��ֶ�
						$field = $v['field'];
						$minlength = $v['minlength'] ? $v['minlength'] : 0;
						$maxlength = $v['maxlength'] ? $v['maxlength'] : 0;
						$field_type = $v['formtype'];
						require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';	
						if(isset($v['setting']['fieldtype'])) {
							$field_type = $v['setting']['fieldtype'];
						}
						require MODEL_PATH.'add.sql.php';
			
						$v['setting'] = array2string($v['setting']);
						$v['modelid'] = $modelid;
						unset($v['fieldid']);
							
						$fieldid = $this->sitemodel_field_db->insert($v, 1);
					}
				}

				//����ģ�ͻ���
				pc_base::load_app_class('member_cache','','');
				member_cache::update_cache_model();
				showmessage(L('operation_success'),'?m=member&c=member_model&a=manage', '', 'add');
			} else {
				showmessage(L('operation_failue'),'?m=member&c=member_model&a=manage', '', 'add');
			}
		} else {
			$show_header = $show_scroll = true;
			include $this->admin_tpl('member_model_add');
		}
		
	}
	
	/**
	 * �޸Ļ�Աģ��
	 */
	function edit() {
		if(isset($_POST['dosubmit'])) {
			$modelid = isset($_POST['info']['modelid']) ? $_POST['info']['modelid'] :showmessage(L('operation_success'),'?m=member&c=member_model&a=manage', '', 'edit');
			$info['name'] = $_POST['info']['modelname'];
			$info['disabled'] = $_POST['info']['disabled'] ? 1 : 0;
			$info['description'] = $_POST['info']['description'];
			
			$this->db->update($info, array('modelid'=>$modelid));
			
			//����ģ�ͻ���
			pc_base::load_app_class('member_cache','','');
			member_cache::update_cache_model();
			showmessage(L('operation_success'),'?m=member&c=member_model&a=manage', '', 'edit');
		} else {					
			$show_header = $show_scroll = true;
			$modelinfo = $this->db->get_one(array('modelid'=>$_GET['modelid']));
			include $this->admin_tpl('member_model_edit');		
		}
	}
	
	/**
	 * ɾ����Աģ��
	 */
	function delete() {
		$modelidarr = isset($_POST['modelid']) ? $_POST['modelid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
		$newmodelarr = '';
		foreach ($modelidarr as $modelarr) {
			if($modelarr!=10) {
				$newmodelarr[] = intval($modelarr);
			}
		}
		$where = to_sqls($newmodelarr, '', 'modelid');

		$modelinfo = $this->db->select($where);
		foreach($modelinfo as $v) {
			$this->db->drop_table($v['tablename']);
		}

		if ($this->db->delete($where)) {
			//ɾ��ģ���ֶ�
			$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
			$this->sitemodel_field_db->delete($where);
			//�޸��û�ģ����Ϊ��ͨ��Ա
			$this->member_db = pc_base::load_model('members_model');
			$this->member_db->update(array('modelid'=>10), $where);
			
			//����ģ�ͻ���
			pc_base::load_app_class('member_cache','','');
			member_cache::update_cache_model();
			
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'), HTTP_REFERER);
		}
	}

	/**
	 * �����Աģ��
	 */
	function import(){
		include $this->admin_tpl('member_model_import');
	}
	
	/**
	 * ������Աģ��
	 */
	function export() {
		$modelid = isset($_GET['modelid']) ? $_GET['modelid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
		$modelarr = getcache('member_model', 'commons');
		
		$this->sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
		$modelinfo = $this->sitemodel_field_db->select(array('modelid'=>$modelid));
		foreach($modelinfo as $k=>$v) {
			$modelinfoarr[$k] = $v;
			$modelinfoarr[$k]['setting'] = string2array($v['setting']);
		}

		$res = var_export($modelinfoarr, TRUE);
		header('Content-Disposition: attachment; filename="'.$modelarr[$modelid]['tablename'].'.model"');
		echo $res;exit;
	}
	
	/**
	 * �޸Ļ�Աģ��
	 */
	function move() {
		if(isset($_POST['dosubmit'])) {
			$from_modelid = isset($_POST['from_modelid']) ? $_POST['from_modelid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
			$to_modelid = !empty($_POST['to_modelid']) && $_POST['to_modelid'] != $from_modelid ? $_POST['to_modelid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
			
			//���»�Ա��modelid
			$this->db->change_member_modelid($from_modelid, $to_modelid);
			
			showmessage(L('member_move').L('operation_success'), HTTP_REFERER, '', 'move');
		} else {
			$show_header = $show_scroll = true;
			$modelarr = $this->db->select(array('type'=>2));
			foreach($modelarr as $v) {
				$modellist[$v['modelid']] = $v['name'];
			}
					
			include $this->admin_tpl('member_model_move');
		}
	}
	
	/**
	 * �����Աģ��
	 */
	function sort() {		
		if(isset($_POST['sort'])) {
			foreach($_POST['sort'] as $k=>$v) {
				$this->db->update(array('sort'=>$v), array('modelid'=>$k));
			}
			
			//����ģ�ͻ���
			pc_base::load_app_class('member_cache','','');
			member_cache::update_cache_model();
			
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'), HTTP_REFERER);
		}
	}
	
	/**
	 * ���ģ������
	 * @param string $username	ģ����
	 * @return $status {0:ģ�����Ѿ����� ;1:�ɹ�}
	 */
	public function public_checkmodelname_ajax() {
		$modelname = isset($_GET['modelname']) ? trim($_GET['modelname']) : exit('0');
		if(CHARSET != 'utf-8') {
			$modelname = iconv('utf-8', CHARSET, $modelname);
			$modelname = addslashes($modelname);
		}
		
		$oldmodelname = $_GET['oldmodelname'];
		if($modelname==$oldmodelname) exit('1');
		
		$status = $this->db->get_one(array('name'=>$modelname));		
		if($status) {
			exit('0');
		} else {
			exit('1');
		}
		
	}
	
	/**
	 * ���ģ�ͱ��Ƿ����
	 * @param string $username	ģ����
	 * @return $status {0:ģ�ͱ����Ѿ����� ;1:�ɹ�}
	 */
	public function public_checktablename_ajax() {
		$tablename = isset($_GET['tablename']) ? trim($_GET['tablename']) : exit('0');
		
		$status = $this->db->table_exists('member_'.$tablename);
		if($status) {
			exit('0');
		} else {
			exit('1');
		}
		
	}
	
}
?>