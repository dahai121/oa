<?php
/**
 * 
 * @param ͨ�����ݵ�����
 */

defined('IN_PHPCMS') or exit('No permission resources.');

class do_import {
	private $import_db, $s_db, $queue;
	
	public function __construct() {
		$this->import_db = pc_base::load_model('import_model');
		
	}
	
	/**
	 * 
	 * ͨ�����ݱ��Ӧ����...
	 * @param ���� $import_info
	 * @param ��ʼֵ $offset
	 * @param ����importidֵ $importid
	 */
	function add_other($import_info, $offset,$importid){
		$data = array();
		$number = $import_info['number'];
		//��ȡָ���������ֶ�
		$keyid = $import_info['keyid'];
		
		//��ȡҪ���뵽�����ݱ�ͨ��ָ��������Դ������ѡ�ı�������ֶΣ�
		$pdo_select = $import_info['pdo_select'];
		$into_tables = $import_info['into_tables'];
		
		$database = pc_base::load_config('database');
		pc_base::load_sys_class('db_factory');
		$db = db_factory::get_instance($database)->get_database($pdo_select);
		
		//��������б���ֶ�
		$r = array();
		$db_table = explode(',', $into_tables);
  		foreach ($db_table as $key=>$val){
 			$r[$val] = $db->get_fields($val);
 		}
 		
    	//ȡ���ж�Ӧ��ϵ���ֶΣ��Ա�����ʹ��
    	foreach ($r as $table){//$rΪ��ά���飬����foreach ѭ��
     		foreach ($table as $field=>$val_field){
	 			if($field == $keyid) continue;//��������������
				$oldfield = trim($import_info[$field]['field']);
				$func = trim($import_info[$field]['func']);
				$value = trim($import_info[$field]['value']);
				if($value){
					$data[$field] = $value;
				}else{
					if($oldfield && $func){
						$oldfields[$oldfield] = $field;
						$oldfuncs[$oldfield] = $func;
					}elseif($oldfield){
						$oldfields[$oldfield] = $field;
					}
				}	
			}
    	}
   		
 		//����û������ģ�Ͷ�ʹ��ϵͳ�������ݿ⴦����� 
 		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		//����һ����ǰ��������Ҫ�����ݿ����ӣ���MYSQL / ACCESS  
		if($import_info['dbtype'] == 'mysql'){
			pc_base::load_sys_class('db_factory');
			$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
			$data_charset = pc_base::load_config('database');
      	   	$this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
			
			$result = $this->filter_fields_new($import_info, $offset, $keyid);//��Ҫ����SQL��䣬������ʹ�á���Ϊ�����漰������飬ʹ��left join������SQL
	  		@extract($result); 
	  		$ddata = $data;//�ݲ�֪
 	 		$sql = $result['return_sql'];
 	 		
 	 		
	    	$query = $this->thisdb->query($sql);  
			$importnum = $this->thisdb->num_rows($sql);
			/***
			 * �ӷ��ص����ݼ���������ϳɶ�ά���飬Ȼ����������forѭ����һ��������Ŀ�����ݿ⡣
			 */ 
	   		while ($array = $this->thisdb->fetch_next()){
	 			$data = $ddata;
	     		foreach ($array as $k=>$v){	//�����ݼ���ѭ����ʾ
	 				if(isset($oldfields[$k]) && $v) {
						if($oldfuncs[$k]) {//����д���������ֱ���ô���������
							$data[$oldfields[$k]] = $oldfuncs[$k]($v);
							if(!$data[$k]) continue;//�����Ĭ��ֵ��
						}else{
							$data[$oldfields[$k]] = $v;//û�д�����������Ҳû��
						}
					}
				}
 	  			//$maxid = max($maxid, $array[$keyid]);
	  			$maxid = max($maxid, $array['max_userid']);
	 			$s[] = $data;
	 		}
 		
		}elseif($import_info['dbtype'] == 'access') {//access����Ͳ���db_factory����Ϊ���ܻ���ֲ����������ǵ�����
			pc_base::load_sys_class('access');
  			$this->thisdb = new access();
  			$this->thisdb->connect($import_info[dbhost],$import_info[dbuser],$import_info[dbpassword]);
  			$result = $this->filter_fields_new($import_info, $offset, $keyid);//��Ҫ����SQL��䣬������ʹ�á���Ϊ�����漰������飬ʹ��left join������SQL
	  		@extract($result); 
	  		$ddata = $data;//�ݲ�֪

	  		if($result['total']==0){
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//����Ҫ����
 				showmessage(L('no_data_needimport'), $forward);
	  		}else {
	  			$sql = $result['return_sql'];
 	 	    	$query = $this->thisdb->query($sql);  
				$importnum = $this->thisdb->num_rows($query);
	 			/*��ϱ��β�ѯ���γɵ�SQL ��ز��� Ϊ����*/ 
 	 			
				foreach ($query as $array){
						 $data = $ddata;
		 				 foreach ($array as $k=>$v){
						 	if(isset($oldfields[$k]) && $v) {
								if($oldfuncs[$k]) {//����д���������ֱ���ô���������
									$data[$oldfields[$k]] = $oldfuncs[$k]($v);
									if(!$data[$k]) continue;//�����Ĭ��ֵ��
								}else{
									$data[$oldfields[$k]] = $v;//û�д�����������Ҳû��
								}
							}
						 }
   		   				$maxid = max($maxid, $array['max_userid']);
			 			$s[] = $data;
				}
	  		}
	 		
			 
 			
  		}
    	/***
		 * ѭ����ӵ�Ŀ�����ݿ�
		 */
 		
		foreach ($s as $val){ 
   			/*��������ģ�ͣ������ݲ����Ӧ���ݱ�����*/
			$into_model = $import_info['into_tables'];
  			$tablepre_strlen = strlen($database[$pdo_select]['tablepre']);//ȡ�����ݱ�ǰ׺
  			$table_model = substr($into_model,$tablepre_strlen);//ȡ����������ģ������
   			$this->into_db = pc_base::load_model($table_model.'_model'); 
    		$returnid = $this->into_db->insert($val); 
  		}
   		$finished = 0;
		if($number && ($importnum < $number)){//�����ÿ��ִ�ж����������ҵ�ǰҪ����������Ѿ�С���趨ֵ����˵����������ִ��
			$finished = 1;			
		}
		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//��������Ĳ���ID����ֹ�ظ���������
 		$this->setting($import_info);
 		//�������ݿ⣬���뱾��ִ��ʱ��
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total��Ϊfilter_fields()���صĽ���⿪
 		
	}
	
	
	/**
	 * 
	 * ��Աģ�����ݵ���...
	 * @param ���� $import_info
	 * @param ��ʼֵ $offset
	 * @param ����importidֵ $importid
	 */
	function add_member($import_info, $offset,$importid){
		$data = array();
 		$keyid = $import_info['keyid'];
  		if(!$keyid){	
			echo L('no_keyid');
			return false;
		}
		$import_info['defaultgroupid'] = intval($import_info['defaultgroupid']);
		if(!$import_info['defaultgroupid']){
			echo L('no_default_groupid');
			return false;
		}
		$number = $import_info['number'];
		$data['defaultgroupid'] = $import_info['defaultgroupid'];
		
		//��ȡѡ��ģ�Ͷ�Ӧ���ֶ�
		$fields = getcache('model_field_'.$import_info['modelid'], 'model');
		$memberfields =  getcache('import_fields', 'commons');//�˻������ֶ�д������ġ�����û��޸���Ĭ�ϵĻ�Ա�ֶΣ�����ļ�ҲҪ���ű仯��
		$fields = array_merge($memberfields, $fields);
 		
 		foreach ($fields as $field=>$val_field){
			if($field == 'userid') continue;
			$oldfield = trim($import_info[$field]['field']);
			$func = trim($import_info[$field]['func']);
			$value = trim($import_info[$field]['value']);
			if($value){
				$data[$field] = $value;
			}else{
				if($oldfield && $func){
					$oldfields[$oldfield] = $field;
					$oldfuncs[$oldfield] = $func;
				}elseif($oldfield ){
					$oldfields[$oldfield] = $field;
				}
			}	
		}
		
		//����û������ģ�Ͷ�ʹ��ϵͳ�������ݿ⴦����� 
 		//��ʱ����  ����ģ�����ñ�
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		

		//����һ����ǰ��������Ҫ�����ݿ�����  
		pc_base::load_sys_class('db_factory');
		$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
		
		/*��ϱ��β�ѯ���γɵ�SQL ��ز��� Ϊ����*/
      	$result = $this->filter_fields_new($import_info, $offset, $keyid);//��Ҫ����SQL��䣬������ʹ�á���Ϊ�����漰������飬ʹ��left join������SQL
  		@extract($result);
  		
		if($result['total']==0){
  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//����Ҫ����
 			showmessage(L('no_data_needimport'), $forward);
	  	} 
	  	
  		$sql = $result['return_sql']; 
       	$data_charset = pc_base::load_config('database');
      	 $this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
		 
 		$query = $this->thisdb->query($sql); 
		$importnum = $this->thisdb->num_rows($sql); 
         /***
		 * �ӷ��ص����ݼ���������ϳɶ�ά���飬Ȼ����������forѭ����һ��������Ŀ�����ݿ⡣
		 */ 
       	while ($r = $this->thisdb->fetch_next()){
  			$data = $ddata;
        		foreach ($r as $k=>$v){	
  				if(isset($oldfields[$k]) && $v) {
					if($oldfuncs[$k]) {
						$data[$oldfields[$k]] = $oldfuncs[$k]($v);
						if(!$data[$k]) continue;
					}else{
						$data[$oldfields[$k]] = $v;
					}
				}
			}
     		$maxid = max($maxid, $r['max_userid']);
 			$s[] = $data;
 		}
     	/***
		 * ѭ����ӵ�Ŀ�����ݿ�
		 */
		foreach ($s as $val){ 
     		/*�������Ĭ���û�������滻*/
 			//��ȡ���������  ��� �滻������ 
  			$default_groupid = $import_info['defaultgroupid'];
 			$replace_groupids = $import_info['groupids'];
 			if(in_array($val['groupid'], $replace_groupids)){
  				$val['groupid'] = array_search($val['groupid'], $replace_groupids);
 			}else {
 				$val['groupid'] = $default_groupid;
 			}
  			//��Ա����ģ��ID��Ĭ��ֱ����ȡ����ʱѡ���ģ��IDֵ
 			$val['modelid']	= $import_info['modelid'];
  			/*����û�����*/
			if(!$member_import){
				$member_import = pc_base::load_app_class('member_import');
			}
 			$memberid = $member_import->add($val,$import_info['membercheck']);//�ڶ�������Ϊ�Ƿ�Ҫ��EMAIL��⡣ 
  		}
 		
 		$finished = 0;
 		
		if($number && ($importnum < $number)){//�����ÿ��ִ�ж����������ҵ�ǰҪ����������Ѿ�С���趨ֵ����˵����������ִ��
			$finished = 1;			
		}
 		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//��������Ĳ���ID����ֹ�ظ���������
 		$this->setting($import_info);
 		//�������ݿ⣬���뱾��ִ��ʱ��
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total��Ϊfilter_fields()���صĽ���⿪
 		
	}
	
	/**
	 * 
	 * ����ģ�����ݵ���...
	 * @param ���� $import_info
	 * @param ��ʼֵ $offset
	 * @param ����importidֵ $importid
	 */
	function add_content($import_info, $offset,$importid){
   		$data = array();
 		$keyid = $import_info['keyid'];
  		if(!$keyid){	
			echo L('no_keyid');
			return false;
		}
		$import_info['defaultcatid'] = intval($import_info['defaultcatid']);
		if(!$import_info['defaultcatid']){
			echo L('no_default_catid');
			return false;
		}
		$number = $import_info['number'];//ÿ��ִ������
		$data['catid'] = $import_info['defaultcatid'];
 		//��ȡѡ��Ķ�Ӧ�ֶ�
		$fields = getcache('model_field_'.$import_info['modelid'], 'model');
 		foreach ($fields as $field=>$val_field){
			if($field == 'contentid') continue;
			$oldfield = trim($import_info[$field]['field']);
			$func = trim($import_info[$field]['func']);
			$value = trim($import_info[$field]['value']);
			if($value){
				$data[$field] = $value;
			}else{
				if($oldfield && $func){
					$oldfields[$oldfield] = $field;//oldfieldsΪ��ѡ�У������浼�����ݵ��ֶ�
					$oldfuncs[$oldfield] = $func;
				}elseif($oldfield ){
					$oldfields[$oldfield] = $field;
				}
			}	
		}
 		//����û������ģ�Ͷ�ʹ��ϵͳ�������ݿ⴦����� 
 		//��ʱ����  ����ģ�����ñ�
		$db_conf = array();
		$db_conf['import_array'] = array();
		$db_conf['import_array']['type']= $import_info['dbtype'];
		$db_conf['import_array']['hostname']= $import_info[dbhost];
		$db_conf['import_array']['username']= $import_info[dbuser];
		$db_conf['import_array']['password']= $import_info[dbpassword];
		$db_conf['import_array']['database']= $import_info[dbname];
		$db_conf['import_array']['charset']= $import_info[dbcharset];
		
		if($import_info['dbtype'] == 'mysql'){
			
			//����һ����ǰ��������Ҫ�����ݿ�����  
			pc_base::load_sys_class('db_factory');
			$this->thisdb = db_factory::get_instance($db_conf)->get_database('import_array');
			
			/*��ϱ��β�ѯ���γɵ�SQL ��ز��� Ϊ����*/
	      	$result = $this->filter_fields($import_info, $offset, $keyid);
	  		@extract($result);
	  		
	  		if($result['total']==0){//û�������ݲ�������ִ��
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//����Ҫ����
	 			showmessage(L('no_data_needimport'), $forward);
	  		} 
	  	
	  		$ddata = $data;//�ݲ�֪
	   		$sql = "SELECT $selectfield FROM ".$result['dbtables']." ".$result['condition']." $limit";//��$limit Ϊ$result �⿪�ı���
   	   		$data_charset = pc_base::load_config('database');
      	   	$this->thisdb->query('SET NAMES '.$data_charset['default']['charset']); 
			
	 		$query = $this->thisdb->query($sql); 
	 		$importnum = $this->thisdb->num_rows($sql); 
	 		
			/***
			 * �ӷ��ص����ݼ���������ϳɶ�ά���飬Ȼ����������forѭ����һ��������Ŀ�����ݿ⡣
			 */ 
	   		while ($r = $this->thisdb->fetch_next()){
	 			$data = $ddata;
	     		foreach ($r as $k=>$v){	//�����ݼ���ѭ����ʾ
	 				if(isset($oldfields[$k]) && $v) {
						if($oldfuncs[$k]) {//����д���������ֱ���ô���������
							$data[$oldfields[$k]] = $oldfuncs[$k]($v);
							if(!$data[$k]) continue;//�����Ĭ��ֵ��
						}else{
							$data[$oldfields[$k]] = $v;//û�д�����������Ҳû��
						}
					}
				}
	  			$maxid = max($maxid, $r[$keyid]);
	 			$s[] = $data;
	 		}
		}elseif($import_info['dbtype'] == 'access'){
			
			pc_base::load_sys_class('access');
  			$this->thisdb = new access();
  			$this->thisdb->connect($import_info[dbhost],$import_info[dbuser],$import_info[dbpassword]);
  			$result = $this->filter_fields_new($import_info, $offset, $keyid);//��Ҫ����SQL��䣬������ʹ�á���Ϊ�����漰������飬ʹ��left join������SQL
	  		@extract($result); 
	  		
	  		if($result['total']==0){//û�������ݲ�������ִ��
	  			$forward = $forward ? $forward: "?m=import&c=import&a=init";//����Ҫ����
	 			showmessage(L('no_data_needimport'), $forward);
	  		} 
	  	
	  		$ddata = $data;//�ݲ�֪
 	 		$sql = $result['return_sql'];
	    	$query = $this->thisdb->query($sql);  
			$importnum = $this->thisdb->num_rows($query);
			/*��ϱ��β�ѯ���γɵ�SQL ��ز��� Ϊ����*/ 
 			foreach ($query as $array){
				 $data = $ddata;
 				 foreach ($array as $k=>$v){
				 	if(isset($oldfields[$k]) && $v) {
						if($oldfuncs[$k]) {//����д���������ֱ���ô���������
							$data[$oldfields[$k]] = $oldfuncs[$k]($v);
							if(!$data[$k]) continue;//�����Ĭ��ֵ��
						}else{
							$data[$oldfields[$k]] = $v;//û�д�����������Ҳû��
						}
					}
				 }
  				$maxid = max($maxid, $array[$keyid]);
	 			$s[] = $data;
			}
			
		}
 		
    	//ѭ����ӵ�Ŀ�����ݿ�
 		
		foreach ($s as $val){
   			/*�������CATID�����滻*/
 			//��ȡ���������catid������ 
 			$default_catid = $import_info['defaultcatid'];
 			$replace_catids = $import_info['catids'];
  			if(in_array($val['catid'], $replace_catids)){
  				$val['catid'] = array_search($val['catid'], $replace_catids);
 			}else {
  				$val['catid'] = $default_catid;
 			}
 			//echo $val['catid'];
 			/**���ݲ���Ŀ�����**/
   			$content = pc_base::load_model('content_model');
 			$content->set_model($import_info['modelid']);//����Ҫ�����ģ��id
			$contentid = $content->add_content($val, 1);
			
 		}
 		$finished = 0;
		if($number && ($importnum < $number)){//�����ÿ��ִ�ж����������ҵ�ǰҪ����������Ѿ�С���趨ֵ����˵����������ִ��
			$finished = 1;			
		}
		$import_info['maxid'] = $maxid;
		$import_info['importtime'] = SYS_TIME;
		//��������Ĳ���ID����ֹ�ظ���������
 		$this->setting($import_info);
 		//�������ݿ⣬���뱾��ִ��ʱ��
   		$this->import_db->update(array("lastinputtime"=>SYS_TIME,"last_keyid"=>$maxid),array('id'=>$importid));
 		return $finished.'-'.$total; //$total��Ϊfilter_fields()���صĽ���⿪
 		
	}

	/**
	 * �����û�ģ�������ļ�
	 *
	 * @param array $setting
	 * @param strong $type
	 * @return true
	 */
	function setting($setting){
		if(empty($setting) || !is_array($setting)) return false;
		$setting['edittime'] = TIME;
 		setcache($setting['import_name'], $setting, 'import'); 
 		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $info ����
	 * @param $offset ��ʼֵ
	 * @param $keyid �ؼ��ֶ�
	 * @param $result Ҫ���صĽ��  
	 */
	public function filter_fields($info, $offset, $keyid){
		$result = array();
		if(empty($keyid)){
			$keyid = '';
		}
		$result['dbtables'] = trim($info['dbtables']);
   		$firstdot = strpos($result['dbtables'], ',');//���ص�һ��,�ų��ֵ�λ��ֵ
  		if($firstdot){//����Ƕ�����飬�����firstdot 
			$startpos = intval(strpos($result['dbtables'], ' '));
 			$firsttable = trim(substr($result['dbtables'], $startpos, $firstdot-$startpos));
		}
 		$result['maxid'] = intval($info['maxid']);//�ϴε������ֵ
		$result['condition'] = " WHERE ".$info['condition'];//����
		/*�������������ID�����ú������ֶΣ����������ݻ�ȡ������*/
		if($info['maxid']>0){
			$result['condition'] = $result['condition'].' and '.$firsttable.'.'.$info['keyid'].'>'.$info['maxid'];
		}
 		/*����ÿ�ε������ã�������limit���*/
		$number = $info['number'];//ÿ�ε���������
		if($number){
			$result['limit'] = " LIMIT $offset,$number";
		}
		//ͳ��Ҫ��ѯ����������
 		$sql = "SELECT count(*) AS total FROM ".$result['dbtables']." ".$result['condition'];
 		$result['total'] = $this->thisdb->result($sql);
 		$result['orderby'] = $firsttable ? $firsttable.'.'.$keyid : $keyid;//�����ѯ����ȡ��һ���keyidΪorder by ���塣
 		$result['selectfield'] = $info['selectfield'] ? $info['selectfield'] : '*';
  		return $result;
 	}
 	
	/**
	 * 
	 * Enter description here ...
	 * @param $info ����
	 * @param $offset ��ʼֵ
	 * @param $keyid �ؼ��ֶ�
	 * @param $result Ҫ���صĽ��  
	 */
	public function filter_fields_new($info, $offset, $keyid){
		$result = array();
		if(empty($keyid)){
			$keyid = '';
		}
		$result['dbtables'] = trim($info['dbtables']);
   		$firstdot = strpos($result['dbtables'], ',');//���ص�һ��,�ų��ֵ�λ��ֵ
  		if($firstdot){//����Ƕ�����飬�����firstdot 
			$startpos = intval(strpos($result['dbtables'], ' '));
 			$firsttable = trim(substr($result['dbtables'], $startpos, $firstdot-$startpos));
 		}
 		/*���ַ�������,�ŷָ�Ϊ���飬�������left join �õ�*/
 		$table_array = explode(',',$result['dbtables']);

 		$result['maxid'] = intval($info['maxid']);//�ϴε������ֵ
 		
 		if($result['condition']){
 			$result['condition'] = " WHERE ".$info['condition'];//����
 		}
 		/*�������������ID�����ú������ֶΣ����������ݻ�ȡ������*/
		if($info['maxid']>0){
			if($result['condition']){
				$result['condition'] = $result['condition'].' and '.$table_array[0].'.'.$info['keyid'].'>'.$info['maxid'];
			}else {
				$result['condition'] = " WHERE ".$table_array[0].'.'.$info['keyid'].'>'.$info['maxid'];
			}
		}
		
 		/*����ÿ�ε������ã�������limit���*/
		$number = $info['number'];//ÿ�ε���������
		if($number){ 
 				$result['limit'] = " LIMIT $offset,$number"; 
 		}
		$result['orderby'] = $firsttable ? $firsttable.'.'.$keyid : $keyid;//�����ѯ����ȡ��һ���keyidΪorder by ���塣
 		$result['selectfield'] = $info['selectfield'] ? $info['selectfield'] : '*, '.$table_array[0].'.'.$info['keyid'].' as max_userid';
 		
  		
		//ͳ��Ҫ��ѯ����������
 		if(count($table_array)>1){
 			//������left join �����ɲ�ѯ���
 			$left_join ='';
 			foreach ($table_array as $k=>$table){
 				if($k>0){
 					$left_join .=" left join ".$table." on ".$table_array[0].".".$info['keyid']."=".$table.".".$info['keyid']." ";
 				}
  			}
  			$sql = "SELECT count(*) AS total FROM ".$table_array[0].$left_join." ".$result['condition'];
  			$return_sql = "SELECT ".$result['selectfield']." FROM ".$table_array[0].$left_join." ".$result['condition']." order by ".$result['orderby']." asc".$result['limit'];
 		}else{
 			$sql = "SELECT count(*) AS total FROM ".$result['dbtables']." ".$result['condition'];
  		 	$return_sql = "SELECT ".$result['selectfield']." FROM ".$result['dbtables']." ".$result['condition']." order by ".$result['orderby']." asc".$result['limit'];

 		}
 		$result['return_sql'] = $return_sql;
    		if($info['dbtype']=='access'){
   			$total = $this->thisdb->get_one($sql);
 			$result['total'] = $total['total'];
   		}else {
   			$result['total'] = $this->thisdb->result($sql);
   		}
    	return $result;
 	}
}
?>