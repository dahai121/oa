<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('foreground','member');//����foreground Ӧ����. �Զ��ж��Ƿ��½.
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);

class index extends foreground {
	function __construct() {
		parent::__construct();
		$this->message_db = pc_base::load_model('message_model');
		$this->message_group_db = pc_base::load_model('message_group_model');
		$this->message_data_db = pc_base::load_model('message_data_model');
		$this->message_info_db = pc_base::load_model('message_info_model');
		$this->_username = param::get_cookie('_username');
		$this->_userid = param::get_cookie('_userid');
		$this->_nickname = param::get_cookie('_nickname');
		$this->_groupid = get_memberinfo($this->_userid,'groupid');
		pc_base::load_app_func('global');
		//����վ��ID������ѡ��ģ��ʹ��
		$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
  		define("SITEID",$siteid);
  	}

/*
	public function init() {
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$where = array('send_to_id'=>$this->_username,'replyid'=>'0');
 		$infos = $this->message_db->listinfo($where,$order = 'messageid DESC',$page, 10);
 		$infos = new_html_special_chars($infos);
 		$pages = $this->message_db->pages;
		include template('message', 'inbox');
	}
*/	
	
	/**
	 * ������Ϣ 
	 */
	public function send() {
		//�жϵ�ǰ��Ա���Ƿ�ɷ�������Ϣ��
		$this->message_db->messagecheck($this->_userid);
		if(isset($_POST['dosubmit'])) {
			
		$username= $this->_username;
		$send_to_id =$_POST['info']['send_to_id'];
		$useridarr = $_POST['info']['send_to_id'];
		$userid_arr = explode(',',$useridarr);
		$num=0;//ͳ�Ʒ����˶��ٸ�
		$subject = $_POST['info']['subject'];
		$content = $_POST['info']['content'];

		$files = $_POST[downfiles_fileurl];
		$files_alt = $_POST[downfiles_filename];
		$array = $temp = array();
		if(!empty($files)) {
			foreach($files as $key=>$file) {
					$temp['fileurl'] = $file;
					$temp['filename'] = $files_alt[$key];
					$array[$key] = $temp;
			}
		}
		$downfilesarray = array2string($array);
		//var_dump($downfilesarray);
		
		
		
		$message_time = SYS_TIME;
		$messageid=$this->message_db->add_message($send_to_id,$username,$message_time,$subject,$content,$downfilesarray,true);
		//echo $messageid;
			if($messageid){
				foreach ($userid_arr as $_k) {
					if(is_numeric($_k)) {
					$member_db = pc_base::load_model('member_model');
					$memberinfo = $member_db->get_one(array('userid'=>$_k));
					//echo $memberinfo['username'];
						if(isset($memberinfo['username'])){
						$tousernameid=$memberinfo['userid'];	
						$tousername=$memberinfo['username'];
						$tousernick=$memberinfo['nickname'];
						$receivetype='to';
						$this->message_info_db->add_message_info($messageid,$message_time,$tousername,$tousernameid,$tousernick,$receivetype,true);
						$num=$num+1; //�ɹ�����һ������ô��1
						}
					}
				}
			}
	showmessage(L('�ɹ�����'.$num.'���ʼ���'),HTTP_REFERER);
			
			

			
			/*
			
			$username = $this->_username;
			$tousername = safe_replace($_POST['info']['send_to_id']);
			//$r = $this->db->get_one(array('username'=>$tousername));
			//if(!$r) showmessage(L('user_not_exist','','member'));
			if($tousername==$username){
				showmessage(L('not_myself','','message'));
			}
			$subject = new_html_special_chars($_POST['info']['subject']);
			$content = new_html_special_chars($_POST['info']['content']);
			$this->message_db->add_message($tousername,$username,$subject,$content,true);
			showmessage(L('operation_success'),HTTP_REFERER);*/
		} else {
			$show_validator = $show_scroll = $show_header = true;
			include template('message', 'send');
		}
	}
	
	/*
	 *�ж��ռ����Ƿ���� 
	 */
	public function public_name() {
		$username = isset($_GET['username']) && trim($_GET['username']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['username'])) : trim($_GET['username'])) : exit('0');
		$member_interface = pc_base::load_app_class('member_interface', 'member');
		if ($username) {
			$username = safe_replace($username);
			//�ж��ռ��˲���Ϊ�Լ�
			if($username == $this->_username){
				exit('0');
			}
			$data = $member_interface->get_member_info($username, 2);
			if ($data!='-1') {
				exit('1');
			} else {
				exit('0');
			}
		} else {
			exit('0');
		}
		
	}
	
	/**
	 * ������
	 */
	public function outbox() { 
		/*
		$where = array('send_from_id'=>$this->_username,'del_type'=>'0');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->message_db->listinfo($where,$order = 'messageid DESC',$page, $pages = '8');
		$infos = new_html_special_chars($infos);
		$pages = $this->message_db->pages;
		include template('message', 'outbox');
		*/
		
		
		$sql = "select d.*,GROUP_CONCAT(e.recipientname) as recipientname,GROUP_CONCAT(e.recipientid) as recipientid FROM v9_message_info as e,v9_message as d WHERE e.messageid=d.messageid and d.del_type=0 and d.send_id='$this->_username'" ;
		$sql= $sql." GROUP BY d.messageid order by d.messageid desc"; 
		$search = pc_base::load_model('get_model');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $search->multi_listinfo($sql,$page,$pages = '10'); //���ز�ѯ���
		$pages = $search->pages;//���ط�ҳ
		include template('message', 'outbox');
	}
	
	/**
	 * �ռ���
	 */
	public function inbox() { 
		$sql = "select * from v9_message as a left JOIN v9_message_info as b on a.messageid = b.messageid where b.recipientid = $this->_userid and b.dele=0";
		//if($keyword){
		//    $where = "and (e.title like '%$keyword%' or d.expcon like '%$keyword%') ";
		//}
		//$sql= $sql.$where." order by e.id desc"; 
		$sql= $sql." order by a.messageid desc"; 
		$search = pc_base::load_model('get_model');
		$page = intval($_GET['page'])?intval($_GET['page']) :'1';
		$infos = $search->multi_listinfo($sql,$page,$pages = '10'); //���ز�ѯ���
		$pages = $search->pages;//���ط�ҳ
		if (is_array($infos) && !empty($infos)) {
			foreach ($infos as $infoid=>$info){ 
				$reply_num = $this->message_db->count(array("replyid"=>$info['messageid']));
				$infos[$infoid]['reply_num'] = $reply_num;
	 		}
		}
		include template('message', 'inbox');
	}
	
	/**
	 * Ⱥ���ʼ�
	 */
	public function group() {
		//��ѯ�Լ���Ȩ�޿�����Ϣ
  		$where = array('typeid'=>1,'groupid'=>$this->_groupid,'status'=>1);
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->message_group_db->listinfo($where,$order = 'id DESC',$page, $pages = '8');
		$infos = new_html_special_chars($infos);
		$status = array();
		if (is_array($infos) && !empty($infos)) {
			foreach ($infos as $info){
				$d = $this->message_data_db->select(array('userid'=>$this->_userid,'group_message_id'=>$info['id']));
	 			if(!$d){
	 				$status[$info['id']] = 0;//δ�� ��ɫ
	 			}else {
	 				$status[$info['id']] = 1;
	 			}
			}
		}
 		$pages = $this->message_group_db->pages;
		include template('message', 'group');
	}
	
	/**
	 * ɾ���ռ���-����Ϣ 
	 * @param	intval	$sid	����ϢID���ݹ�ɾ��(�޸�״̬Ϊoutbox)
	 */
	public function delete() {
		if((!isset($_GET['messageid']) || empty($_GET['messageid'])) && (!isset($_POST['messageid']) || empty($_POST['messageid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['messageid'])){
				foreach($_POST['messageid'] as $messageid_arr) {
					$messageid_arr = intval($messageid_arr);
					$this->message_info_db->update(array('dele'=>1),array('messageid'=>$messageid_arr,'recipientid'=>$this->_userid));
				}
				showmessage(L('operation_success'), HTTP_REFERER);
			}
 		}
	}
	
	/**
	 * ɾ�������� - ����Ϣ 
	 * @param	intval	$sid	����ϢID���ݹ�ɾ��( �޸�״̬Ϊdel_type =1 )
	 */
	public function del_type() {
		if((!isset($_POST['messageid']) || empty($_POST['messageid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				if(is_array($_POST['messageid'])){
					foreach($_POST['messageid'] as $messageid_arr) {
						$messageid_arr = intval($messageid_arr);
 						$this->message_db->update(array('del_type'=>'1'),array('messageid'=>$messageid_arr,'send_from_id'=>$this->_username));
					}
					showmessage(L('operation_success'), HTTP_REFERER);
				} 
		}
	}
	
	/**
	 * �鿴����Ϣ - �Ե�ǰ�û��Ƿ���Ȩ�޲鿴
	 */
	public function check_user($messageid,$where){
		$userid = $this->_userid;
		$username = $this->_username;
		$messageid = intval($messageid);
		if($where=="to"){
			$result = $this->message_info_db->get_one(array("recipientid"=>$userid,"messageid"=>$messageid));
		}else{
			$result = $this->message_db->get_one(array("send_id"=>$username,"messageid"=>$messageid));
		}
 		if(!$result){//���ǵ�ǰ�û�����Ϣ�����ܲ鿴
			showmessage('����Ƿ����ʣ�', HTTP_REFERER);echo '0';
 		} 
	}
	
	
	/**
	 * �鿴����Ϣ
	 */
	public function read() { 
		if((!isset($_GET['messageid']) || empty($_GET['messageid'])) && (!isset($_POST['messageid']) || empty($_POST['messageid']))) return false;
		$messageid = $_GET['messageid'] ? $_GET['messageid'] : $_POST['messageid'];
		$messageid = intval($messageid);
		$userid = $this->_userid;
		//�ж��Ƿ����ڵ�ǰ�û�
		$check_user = $this->check_user($messageid,'to'); 
		
 		//�鿴���޸�״̬ Ϊ 0 
		$this->message_info_db->update(array('viewed'=>'1'),array('messageid'=>$messageid,'recipientid'=>$userid));
		//��ѯ��Ϣ����
		$infos = $this->message_db->get_one(array('messageid'=>$messageid));
		if($infos['send_id']!='SYSTEM') $infos = new_html_special_chars($infos);
		//����һ��
		$info['send_id'] = safe_replace($infos['send_id']);
		$info['send_to_id'] = safe_replace($infos['send_to_id']);
		//��ѯ�ظ���Ϣ
		$where = array('replyid'=>$infos['messageid']);
		$reply_infos = $this->message_db->listinfo($where,$order = 'messageid ASC',$page, $pages = '10');
		$show_validator = $show_scroll = $show_header = true;
		include template('message', 'read');
	}
	
	/**
	 * �鿴�Լ����Ķ���Ϣ
	 */
	public function read_only() { 
		$messageid = $_GET['messageid'] ? $_GET['messageid'] : $_POST['messageid'];
		$messageid = intval($messageid);
		if(!$messageid || empty($messageid)){
			showmessage('����Ƿ����ʣ�', HTTP_REFERER);
		}
		//�ж��Ƿ����ڵ�ǰ�û�
		$check_user = $this->check_user($messageid,'from'); 
		
		//��ѯ��Ϣ����
		$infos = $this->message_db->get_one(array('messageid'=>$messageid));
		//$infos = new_html_special_chars($infos);
		//��ѯ�ظ���Ϣ
		$where = array('replyid'=>$infos['messageid']);
		$reply_infos = $this->message_db->listinfo($where,$order = 'messageid ASC',$page, $pages = '10');
		$show_validator = $show_scroll = $show_header = true;
		include template('message', 'read_only');
	}
	
	/**
	 * �鿴ϵͳ����Ϣ
	 */
	public function read_group(){
		if((!isset($_GET['group_id']) || empty($_GET['group_id'])) && (!isset($_POST['group_id']) || empty($_POST['group_id']))) return false;
		//��ѯ��Ϣ����
		$infos = $this->message_group_db->get_one(array('id'=>$_GET['group_id']));
		$infos = new_html_special_chars($infos);
		if(!is_array($infos))showmessage(L('message_not_exist'),'blank');
		//���鿴���Ƿ��м�¼,������message_data ���������¼  
		$check = $this->message_data_db->select(array('userid'=>$this->_userid,'group_message_id'=>$_GET['group_id']));
		if(!$check){
			$this->message_data_db->insert(array('userid'=>$this->_userid,'group_message_id'=>$_GET['group_id']));
		}
 		include template('message', 'read_group');
	}
	
	/**
	 * �ظ�����Ϣ 
	 */
	public function reply() {
		if(isset($_POST['dosubmit'])) {
			$messageid = intval($_POST['info']['replyid']);
			//�жϵ�ǰ��Ա���Ƿ�ɷ�������Ϣ��
			$this->message_db->messagecheck($this->_userid);
			//������Ϣ�Ƿ���Ȩ�޻ظ� 
			$this->check_user($messageid,'to');
			
			$info = array();

 			$info['send_id'] = $this->_username;
			$info['message_time'] = SYS_TIME;
			$info['status'] = '1';
			$info['folder'] = 'inbox';
			$info['content'] = safe_replace($_POST['info']['content']);
			$info['subject'] = safe_replace($_POST['info']['subject']);
			$info['replyid'] = intval($_POST['info']['replyid']);

			//�ظ���ID���а�ȫ����
			$send_to_id = safe_replace($_POST['info']['send_to_id']);
			if(empty($send_to_id)) {
				showmessage(L('user_noempty'),HTTP_REFERER);
			} else {
				$info['send_to_id'] = $send_to_id;
			}
			$messageid = $this->message_db->insert($info,true);
			
			if(!$messageid) return FALSE; 
			$fbinfo = array();
			$fbinfo['messageid'] = $messageid;
			$fbinfo['viewdate'] = SYS_TIME;
			$fbinfo['recipient'] = safe_replace($_POST['info']['send_to_id']);
			//echo $_POST['info']['send_to_id'];
			$fbinfo['recipientid'] = get_memberinfo_buyusername($_POST['info']['send_to_id'],userid);
			if (empty($fbinfo['recipientid'])){
			$fbinfo['recipientname'] = safe_replace($_POST['info']['send_to_id']);
			}else{
			$fbinfo['recipientname'] = get_memberinfo_buyusername($_POST['info']['send_to_id'],nickname);
			}
			$fbinfo['receivetype'] ='to';
			$messageid_info = $this->message_info_db->insert($fbinfo,true);
			
			showmessage(L('operation_success'),HTTP_REFERER);
			
		} else {
			$show_validator = $show_scroll = $show_header = true; 
			include template('message', 'send');
		}

	}
	 
	
}	
?>	