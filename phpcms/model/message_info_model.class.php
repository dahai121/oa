<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class message_info_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'message_info';
		$this->_username = param::get_cookie('_username');
		$this->_userid = param::get_cookie('_userid');
		parent::__construct();
	}
	
		public function add_message_info($messageid,$message_time,$tousername,$tousernameid,$tousernick,$receivetype) {
			$message = array ();
      $message['messageid'] = $messageid;
      $message['viewdate'] = $message_time;
			$message['recipient'] = $tousername;
			$message['recipientid'] = $tousernameid;
			$message['recipientname'] = $tousernick;
			$message['receivetype'] = $receivetype;
			$messageid = $this->insert($message,true);
			if(!$messageid){
				return FALSE;
			}else {
				return true;
			}
		}
	
}
?>