<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_app_func('global','import');//�����������
class import extends admin {
	function __construct() {
		parent::__construct();
		$this->import = pc_base::load_model('import_model');
  	}
 	
	public function init() {
		//Ĭ�ϵ������е������
		$type = $_GET['type'];
		if($type!=''){
			$where = array("type"=>$type);
		}
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->import->listinfo($where,$order = 'id DESC',$page, $pages = '9');
		$pages = $this->import->pages; 
		include $this->admin_tpl('import_list');
 	}
 	
 	
 	/**
 	 * 
 	 * ��֤���������Ƿ��Ѵ��� ...
 	 */
	public function check_import_name(){
 		$import_name = isset($_GET['import_name']) && trim($_GET['import_name']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['import_name'])) : trim($_GET['import_name'])) : exit('0');
 		$importid = $_GET['importid'];
  		if(!$importid){//û������ID��˵�����½�����ʱ����ͬ��
  			$array = $this->import->get_one(array("import_name"=>$import_name),'import_name');
  			if(!empty($array) && $array['import_name']==$import_name){
  				exit('0');
  			}else {
  				exit('1');
  			}
 		}else{
 			exit('1');
 		}
		
 	}
 	
	//������ݵ������
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
  			if($_POST['info']['type']=='content'){
 				$modelid = $_POST['info']['contentmodelid'];
 			}elseif ($_POST['info']['type']=='member'){
 				$modelid = $_POST['info']['membermodelid'];
 			}else {
 				$modelid = 'other';
 			}
 			$url = "?m=import&c=import&a=import_setting&type=".$_POST['info']['type']."&modelid=".$modelid."&pc_hash=".$_SESSION['pc_hash'];
 			showmessage('������һ��',$url,'0');
  		}else {
 			$models = getcache('model','commons');
	 		$members = getcache('member_model','commons');
	  		pc_base::load_sys_class('form', '', 1);
	 		include $this->admin_tpl('import_add_setting');
 		}
 		
	}
	
	//��ʽ�������ݵ������
 	public function import_setting() {
 		if(isset($_POST['dosubmit'])) {
  			//д�뻺�������ļ�
 			$forward = "?m=import&c=import&a=init";
 			$setting = $_POST['setting'];
  			if(empty($setting['import_name'])){
				showmessage('��������������');
			}
  			setcache($setting['import_name'], $setting, 'import'); 
   			//д�����ݿ�,�����/�޸�����
   			$into_array = array();
  			$into_array['addtime'] = SYS_TIME;
			$into_array['type'] = $_POST['type'];
 			$into_array['import_name'] = $setting['import_name'];
 			$into_array['desc'] = $setting['desc'];
 			
 			//��ȡimportid�������Ƿ��д�ֵ�����ж�����ӻ����޸Ĳ���
 			$importid = $_GET['importid'];
   			if($importid){
   				$this->import->update($into_array,array('id'=>$importid));
   			}else {
   				$importid = $this->import->insert($into_array,true);
    		}
  			showmessage('�����ɹ�', $forward);
   		} else {
  			$show_validator = $show_scroll = $show_header = true;
 			pc_base::load_sys_class('form', '', 0);
  			//��ȡ���ݵĲ�������������ʾ�Ǹ�ģ���ֶ�  
  			$type = $_GET['type'];
 			$modelid = $_GET['modelid'];
 			
   			//�����Ƿ����importid ����ȡ������Ϣ
   			$importid = $_GET['importid'];
   			if(!empty($importid)){
   				$info = $this->import->get_one(array('id'=>$importid));
	 			$import_name = $info['import_name'];
	 			$setting = getcache($import_name,'import');
   			}
       		if($type == 'other'){
    			/*���ѡ��other ���ͣ�������ݿ������ж�ȡ*/
    			$database = pc_base::load_config('database');
	    		foreach($database as $name=>$value) {
					$pdos[$name] = $value['database'].'['.$value['hostname'].']';
				}
				
				/*��Ϊ���ݺͶ����ö�����*/
	    		if($_GET['pdoname'] || $setting['pdo_select']) {
	    			$pdo_name = $_GET['pdoname'] ? trim($_GET['pdoname']) : trim($setting['pdo_select']);
	    			
  					$to_tables = array();
					$db = db_factory::get_instance($database)->get_database($pdo_name);
					$tbl_show = $db->query("SHOW TABLES"); 
    				$to_table = '<select id="selecte_to_tables" onchange="to_tables(this.value)">';
 					while(($rs = $db->fetch_next()) != false) {
 			 			$to_table .= "<option value='".$rs['Tables_in_'.$database[$pdo_name]['database']]."'>".$rs['Tables_in_'.$database[$pdo_name]['database']]."</option>";
 						$r[] = $rs;
 					} 
 					$to_table .= "</select>"; 
					$db->free_result($tbl_show);
					//���ݴ��ݵ���ѡ��������ʾ��ѡ��������ֶ�
					if($_GET['into_tables'] || $setting['into_tables']){
 						$into_tables = $_GET['into_tables'] ? trim($_GET['into_tables']) : trim($setting['into_tables']);
						//�ֲ���
						$get_tables = array();
						$db_table = explode(',', $into_tables);
				  		foreach ($db_table as $key=>$val){
				  			if(!empty($val)){
				  				$get_tables[$val] = $db->get_fields($val);
				  			}
  						}
  						//��������ֶ�����
						$get_keywords = '';
						foreach ($get_tables as $key=>$val){
				 			foreach ($val as $v=>$true_key){
								$get_keywords[]= $v;
							}
				  		}
 					}
				}
 				
				
    		}else {
    			//other���ͣ�û��modelid�����԰�modelidֵ���жϣ��Ƶ�������
    			if (empty($type) || empty($modelid)){
				showmessage('��ѡ��ģ�ͣ�');
				}
 			
    			/*ֻ��ָ��ģ�Ͳ��õ��Ż�ȡģ�Ͷ�Ӧ�ֶ�*/
	    		$fields = getcache('model_field_'.$modelid, 'model');
	    		//������Ŀѡ���
	   			if($type == 'content'){
	   				$site = $this->get_siteid();
	   				$category = getcache('category_content_'.$site, 'commons');
	    				foreach ($category as $cat){
						if($modelid == $cat['modelid'] && $cat['arrchildid'] == $cat['catid']){//$cat['arrchildid'] == $cat['catid'] �������ȷ�����ռ�Ŀ¼
							$arr_cat[$cat['catid']] = $cat['catname'];
						}
					}
	   			}elseif($type == 'member') {
	   				//��ȡ��Աģ��Ĭ���ֶΣ������Զ����ֶΣ��ϲ�Ϊ�������顣
	   				$memberfields =  getcache('import_fields', 'commons');//�˻������ֶ�д������ġ�����û��޸���Ĭ�ϵĻ�Ա�ֶΣ�����ļ�ҲҪ���ű仯��
	 				$fields = array_merge($memberfields, $fields);
	 				//��ȡ�û���
	   				$group = getcache('grouplist', 'member');
	   				$new_group = array();
	   				foreach ($group as $mem){
	   					$new_group[$mem['groupid']] = $mem['name'];
	   				}
	    		}
    		}
  			include $this->admin_tpl($type.'_add');
  		}

	}

	/*
	 * �������ݿ��Ƿ���������
	 */
	public function testdb(){
		//����ת�������� import.class.php
  		pc_base::load_sys_class('import_test', '', 0);
  		$import_test = new import_test();
 		$dbtype = $_GET['dbtype'];
 		$dbhost = $_GET['dbhost'];
 		$dbuser = $_GET['dbuser'];
 		$dbpw = $_GET['dbpassword'];
 		$dbname = $_GET['dbname'];
   		$r = $import_test->testdb($dbtype, $dbhost, $dbuser, $dbpw, $dbname);
    	if ($r=='OK') {
			echo 'OK';
		}
	}
	
	/*
	 * ��ȡ��ϵͳָ������Դ�ģ��������ݱ�
	 * */
	public function get_into_tables(){
		$pdo_select = $_GET['pdo_select'];
   		if (empty($pdo_select)){
			exit();
		}
		
		/*���ѡ��other ���ͣ�������ݿ������ж�ȡ*/
    	$database = pc_base::load_config('database');
 		$db = db_factory::get_instance($database)->get_database($pdo_select);
		$tbl_show = $db->query("SHOW TABLES"); 
    	$to_table = '<select id="selecte_to_tables" onchange="to_tables(this.value)">';
    	$to_table .= "<option value=''>��ѡ��</option>";
 		while(($rs = $db->fetch_next()) != false) {
  			$to_table .= "<option value='".$rs['Tables_in_'.$database[$pdo_select]['database']]."'>".$rs['Tables_in_'.$database[$pdo_select]['database']]."</option>";
 			$r[] = $rs;
 		} 
 		$to_table .= "</select>";
 		echo $to_table;		 
	}
	
	//��ȡָ�����ݿ�ģ��������ݱ�
	public function get_tables(){
		$dbtype = $_GET['dbtype'];
 		$dbhost = $_GET['dbhost'];
 		$dbuser = $_GET['dbuser'];
 		$dbpassword = $_GET['dbpassword'];
 		$dbname = $_GET['dbname'];
 		$dbcharset = $_GET['dbcharset'];
 		if($dbtype == 'mysql'){
 			if (empty($dbhost) || empty($dbuser) || empty($dbtype) || empty($dbpassword) || empty($dbname) || empty($dbcharset)){
			exit();
			}
 		}
  		
   		$database = '<select id="tables" onchange="in_tables(this.value)">';
   		$database .= "<option value=''>��ѡ��</option>";
  		if($dbtype == 'access'){
  				/*��ȡaccess.class.php*/
  				pc_base::load_sys_class('access');
  				$access = new access();
  				$access->connect($dbhost,$dbuser,$dbpassword);
   				$array = $access->select("SELECT name from MSysObjects where type = 1 and flags = 0"); 
    			foreach ($array as $arr){
  					$database .= "<option value='".$arr['name']."'>".$arr['name']."</option>";
   				} 
  		}else{//��MYSQL���ݿ�
  			//��ʱ����һ������ģ�����ñ�
			$db_conf = array();
			$db_conf['import_array'] = array();
			$db_conf['import_array']['type']= $dbtype;
			$db_conf['import_array']['hostname']= $dbhost;
			$db_conf['import_array']['username']= $dbuser;
			$db_conf['import_array']['password']= $dbpassword;
			$db_conf['import_array']['database']= $dbname;
			$db_conf['import_array']['charset']= $dbcharset;
	    	//����û������ģ�Ͷ�ʹ��ϵͳ�������ݿ⴦����� 
			pc_base::load_sys_class('db_factory');
			//$database = pc_base::load_config('database');
			$db = db_factory::get_instance($db_conf)->get_database('import_array');
			
  			$query =$db->query("SHOW TABLES");
  			while(($rs = $db->fetch_next()) != false) {
 			$database .= "<option value='".$rs['Tables_in_'.$dbname]."'>".$rs['Tables_in_'.$dbname]."</option>";
			$r[] = $rs;
			}
  		}
  		 
		echo $database."</select>"; 
	}
	
	//��ȡ��ѡ���ݱ���ֶ��б�
	public function get_fields(){
		//����ת�������� import.class.php
		//echo '��Һ�';exit;
 		$dbtype = $_GET['dbtype'];
 		$dbhost = $_GET['dbhost'];
 		$dbuser = $_GET['dbuser'];
 		$dbpassword = $_GET['dbpassword'];
 		$dbname = $_GET['dbname'];
 		$tables = $_GET['tables'];
		if(empty($dbhost) || empty($dbuser) || empty($dbtype) || empty($dbpassword) || empty($dbname) || empty($tables)){
		exit();
		}
		//��ʱ����һ������ģ�����ñ�
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $dbtype;
		$db_conf['import_array']['hostname']= $dbhost;
		$db_conf['import_array']['username']= $dbuser;
		$db_conf['import_array']['password']= $dbpassword;
		$db_conf['import_array']['database']= $dbname;
		$db_conf['import_array']['charset']= $dbcharset;
    	//����û������ģ�Ͷ�ʹ��ϵͳ�������ݿ⴦����� 
		pc_base::load_sys_class('db_factory');
		$database = pc_base::load_config('database');
		$db = db_factory::get_instance($db_conf)->get_database('import_array');
		
		//��������ֶ�����
		$r = array();
		$db_table = explode(',', $tables);
  		foreach ($db_table as $key=>$val){
  			if(!empty($val)){
  				$r[$val] = $db->get_fields($val);
  			}
 		}
  		//��������б�
		$database = '<select onchange="if(this.value!=\'\'){put_fields(this.value)}"><option value="">��ѡ��</option>';
		foreach ($r as $key=>$val){
 			foreach ($val as $v=>$true_key){
				$database .= '<option value="'.$v.'">'.$key.'.'.$v.'</option>';
			}
  		}
 		echo $database."</select>";  
   		exit; 
	} 
	
	
	/**
	 * 
	 * ajax ��ȡ��ϵͳ��ѡ���ݱ������ֶ� ...
	 */
	public function get_keywords(){
 		$into_tables = trim($_GET['into_tables'],',');
 		$pdo_select = $_GET['pdo_select']; 
 		if(empty($pdo_select) || empty($into_tables)){
		exit();
		}
 		
		//���ݴ��ݹ������������ã���������������
		$database = pc_base::load_config('database');
		pc_base::load_sys_class('db_factory');
		$db = db_factory::get_instance($database)->get_database($pdo_select);
		
		//��������ֶ�����
		$r = array();
		$db_table = explode(',', $into_tables);
  		foreach ($db_table as $key=>$val){
  			$val = trim($val,',');
  			$r[$val] = $db->get_fields($val);
 			
		}
   		//��������б�
		//$database = '<select onchange="if(this.value!=\'\'){put_fields(this.value)}"><option value="">��ѡ��</option>';
		$return_str = '';
		foreach ($r as $key=>$val){
 			foreach ($val as $v=>$true_key){
				$return_str .= '<tr height="40">
				<th width="80" align="right">'.$v.':</th>
				<th width="200" align="left" class="list_fields">
				<input name="setting['.$v.'][field]" id="field_'.$v.'" class="input_blur" type="text" value="'.$v.'"><span id="test"></span>
				</th>
				<th width="200" align="left"><input name="setting['.$v.'][value]" class="input_blur" type="text" value='.$v.'></th>
				<th width="200" align="left">
				<input require="false" datatype="ajax" url="?m=import&c=import&a=test_func" msg="" name="setting['.$v.'][func]" type="text" value="'.$v.'" /></th></tr>';
			}
  		}
  		echo $return_str;
	} 
	
 	/*
	 * �����޸� 
	 */
	public function choice() {
		if(isset($_POST['dosubmit'])){
			$type = $_GET['type'];
			$importid = $_GET['importid'];
			if($type=='content'){
				$modelid = $_POST['info']['contentmodelid'];
			}elseif ($type=='member'){
				$modelid = $_POST['info']['membermodelid'];
			}
			$url = "?m=import&c=import&a=import_setting&type=".$type."&importid=".$importid."&modelid=".$modelid."&pc_hash=".$_SESSION['pc_hash'];
 			showmessage('������һ��',$url,'0');
		}else {
			$importid = intval($_GET['importid']);
			if($importid < 1) return false;
			$array = $this->import->get_one(array("id"=>$importid));
			$type = $array['type'];
			$import_name = $array['import_name'];
	 		//��ȡ�ļ�������Ϣ
	 		$setting = getcache($import_name,'import');
	 		$now_modelid = $setting['modelid'];
	 		
			$models = getcache('model','commons');
	 		$members = getcache('member_model','commons');
	  		pc_base::load_sys_class('form', '', 1);
	  		include $this->admin_tpl('import_choice');
		}
		
 	} 
	 
	
	/*
	 * �༭�޸���Ϣ
	 * */
	public function edit() {
		if(isset($_POST['dosubmit'])){
			$setting = $_POST['setting'];
  			if(empty($setting['name'])){
				showmessage('��������������');
			}
  			setcache($setting['name'], $setting, 'import'); 
   			//д�����ݿ�
   			$importid = $_GET['importid'];
  			$into_array = array();
  			$into_array['addtime'] = SYS_TIME;
			$into_array['type'] = $_POST['type'];
 			$into_array['import_name'] = $setting['name'];
			$this->import->update($into_array,array('id'=>$importid));
  			showmessage('�޸Ĳ����ɹ�');
 		}else{
 			//�жϲ�����ȷ
			$importid = intval($_GET['importid']);
			if($importid < 1) {
				return false;
			}
			$type = $_GET['type'];
			if($type!='content' && $type!='member' && $type!='other'){
				return false;
			}
			
			$modelid = $_GET['modelid'];
 			if($type == 'content'){
   				$site = $this->get_siteid();
   				$category = getcache('category_content_'.$site, 'commons');
    				foreach ($category as $cat){
					if($modelid == $cat['modelid'] && $cat['arrchildid'] == $cat['catid']){//$cat['arrchildid'] == $cat['catid'] �������ȷ�����ռ�Ŀ¼
						$arr_cat[$cat['catid']] = $cat['catname'];
					}
				}
   			}elseif($type == 'member') {
   				$group = getcache('grouplist', 'member');
   				$new_group = array();
   				foreach ($group as $mem){
   					$new_group[$mem['groupid']] = $mem['name'];
   					
   				}
    				$fields = array_merge($memberfields, $fields);//��ϻ�Ա��
   			}
 			//��ѯ��Ӧ�ļ���
			$info = $this->import->get_one(array('id'=>$importid));
 			$import_name = $info['import_name'];
 			//��ȡ�ļ�������Ϣ
 			$setting = getcache($import_name,'import');
 			//������ѡ���ģ��ID��ȡ��ϵͳ��Ӧ�ֶ�
 			
   			$fields = getcache('model_field_'.$modelid, 'model');
  			pc_base::load_sys_class('form', '', 1);
 			include $this->admin_tpl('import_'.$type.'_edit');
		}

	}
	
	/**
	 * ɾ������������Ϣ  
	 * @param	intval	$sid	��¼ID���ݹ�ɾ��
	 */
	public function delete() {
  		if((!isset($_GET['importid']) || empty($_GET['importid'])) && (!isset($_POST['importid']) || empty($_POST['importid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['importid'])){
				foreach($_POST['importid'] as $importid_arr) {
					//ɾ�������ļ�
					$array = $this->import->get_one(array("id"=>$importid_arr));
					$import_name = $array['import_name'];
 					delcache($import_name,'import');
 					//ɾ�����ݿ���Ϣ
					$this->import->delete(array('id'=>$importid_arr));
				}
				showmessage(L('operation_success'),'?m=import&c=import');
			}else{
				$importid = intval($_GET['importid']);
				if($importid < 1) return false;
				//ɾ����¼
				$result = $this->import->delete(array('id'=>$importid));
				if($result){
					showmessage(L('operation_success'),'?m=import&c=import');
				}else {
					showmessage(L("operation_failure"),'?m=import&c=import');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	} 
	
	/*��⴦�����Ƿ����*/
	public function test_func(){
		$value = $_GET['value'];
    	if(!function_exists($value)) {
			echo  $value.'�ú���������';
		}else{
			echo '��ȷ';
 		}
	}
	
	
	/*
	 * ִ�е������ 
	 */
	public function do_import() {
		//��ȡimportid type 
		$type = $_GET['type'];
		$importid = $_GET['importid'];
		//��ȡ��Ӧ�����ļ���
		$import_array = $this->import->get_one(array("id"=>$importid));
  		$import_name = $import_array['import_name']; 
 		//��ȡ���û���
		$import_info = getcache($import_name,'import');
 	 		
 		if($import_info['expire']) set_time_limit($import_info['expire']);//��ʱʱ������
		$name = $import_info['import_name'];
	    $number = $import_info['number'];
	    
	    $offset = $_GET['offset'];
	    $offset = isset($offset) ? intval($offset) : 0 ;
	    
	    //��Ϣ�ĵ���֣���Ϣ����Ա��������������
		if(!$do_import){
			$do_import = pc_base::load_app_class('do_import');
		}
  		if($type == 'content'){
   			$result = $do_import->add_content($import_info, $offset,$importid); //ͨ����ͬ��offset��ѭ��ȡ���ݲ���⡣
 		}elseif($type == 'member'){
 			$result = $do_import->add_member($import_info, $offset,$importid); //ͨ����ͬ��offset��ѭ��ȡ���ݲ���⡣
  		}elseif($type == 'other'){
			$result = $do_import->add_other($import_info, $offset,$importid); //ͨ����ͬ��offset��ѭ��ȡ���ݲ���⡣
		}
   		if(!$result){
			showmessage('�������������⣬��鿴!');
		}
 		//�ӷ��ص���Ϣ�У��ֽ�����飬���ж��Ƿ���� 
 		list($finished, $total) = explode('-', $result);
 		$newoffset = $offset + $number;
 		
   		//��ת˵��
 		$start = $_GET['start'] ? $_GET['start'] : 0;
		$end_start = $start + $number;
   		$forward = $finished ? "?m=import&c=import&a=init" : "?m=import&c=import&a=do_import&type=".$type."&importid=".$importid."&offset=$offset&start=$end_start&total=$total";//�������������б�δ������������
   		showmessage('���ڽ������ݵ���<br>'.$start.' - '.$end_start, $forward);
   	}
   
}
?>