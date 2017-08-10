<?php
/**
 * 
 * @param 会员数据导入类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
class member_import {
	private $import_db, $member_db, $memberdetail_db ,$phpsso_db,$queue;
	
	public function __construct() {
		$this->import_db = pc_base::load_model('import_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->phpsso_db = pc_base::load_model('sso_members_model');
		$this->memberdetail_db = pc_base::load_model('memberdetail_model');
		$this->_init_phpsso();
		
	}
	
	/**
	 * 
	 * 会员数据导入 ...
	 * @param $val 用户数据数组
	 * @param $check_email 是否要检测EMAIL
	 */
	function add($info,$check_email) {
		if($check_email==1){
			//执行EMAIL or username 同名检测
			$username = $this->phpsso_db->get_one(array("username"=>$info['username']));
 			if($username) return false;
		}
		/*先插入phpsso_member表，生成的PHPSSOid,再用来插入mbmer基本表*/
		//判断是否存在随机码，
		$sso_array = array();
		$sso_array['username'] = $info['username'];
 		$sso_array['email'] = $info['email'];
		$sso_array['regip'] = $info['regip'];
		$sso_array['random'] = create_randomstr($lenth = 6);
		$sso_array['password'] = md5(md5($info['password']).$sso_array['random']);

		//插入SSO members 表中
 		$this->phpsso_db->insert($sso_array);
 		$sso_uid = $this->phpsso_db->insert_id();
 		$sso_username = $sso_array[username];
 		$sso_encrypt = $sso_array[random];
 		$sso_password = $sso_array[password];
		if(!$sso_uid) return FALSE; 
		
		//插入v9_member基本表,只需加入phpssouid值
		$info1['phpssouid'] = $sso_uid;
		$info1['username'] = $sso_username;
		$info1['password'] = $sso_password;
		$info1['encrypt'] = $sso_encrypt;
		$info1['modelid'] = 10;

		$userid = $this->member_db->insert($info1);

		$info2['userid'] = $sso_uid;
		$info2['birthday'] = $info['birthday'];
		$info2['officename'] = $info['officename'];
		$info2['constrctor'] = $info['constrctor'];
		$info2['telephone'] = $info['telephone'];
		$info2['shouji'] = $info['shouji'];
		$info2['fax'] = $info['fax'];
		$info2['address'] = $info['address'];
		$info2['post_code'] = $info['post_code'];
		$info2['note'] = $info['note'];
		$userid =$this->memberdetail_db->insert($info2);
		if($userid){
			return $userid;
		}

		
  	}
	
	/**
	 * 初始化phpsso
	 * about phpsso, include client and client configure
	 * @return string phpsso_api_url phpsso地址
	 */
	private function _init_phpsso() {
		pc_base::load_app_class('client', 'member', 0);
		define('APPID', pc_base::load_config('system', 'phpsso_appid'));
		$phpsso_api_url = pc_base::load_config('system', 'phpsso_api_url');
		$phpsso_auth_key = pc_base::load_config('system', 'phpsso_auth_key');
		$this->client = new client($phpsso_api_url, $phpsso_auth_key);
		return $phpsso_api_url;
	}
	  
}
?>