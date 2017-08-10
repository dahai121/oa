<?php 
/**
 * ����ƽ̨API�ӿ���
 */

class smsapi {
	public $userid;
	public $statuscode;
	private $productid, $sms_key, $smsapi_url;
	
	/**
	 * 
	 * ��ʼ���ӿ���
	 * @param int $userid �û�id
	 * @param int $productid ��Ʒid
	 * @param string $sms_key ��Կ
	 */
	public function __construct($userid = '', $productid = '', $sms_key = '') {
		$this->smsapi_url = 'http://sms.phpip.com/api.php?';
		$this->userid = $userid;
		$this->productid = $productid;
		$this->sms_key = $sms_key;
	}
		
	/**
	 * 
	 * ��ȡ���Ų�Ʒ�б���Ϣ
	 */
	public function get_price() {
		$this->param = array('op'=>'sms_get_productlist');
		$res = $this->pc_file_get_contents();
		
		return !empty($res) ? json_decode($res, 1) : array();	
	}
	
	/**
	 * 
	 * ��ȡ���Ų�Ʒ�����ַ
	 */
	public function get_buyurl($productid = 0) {
		return 'http://sms.phpip.com/index.php?m=sms_service&c=center&a=buy&sms_pid='.$this->productid.'&productid='.$productid;
	}
	public function show_qf_url() {
		return $this->smsapi_url.'op=sms_qf_url&sms_uid='.$this->userid.'&sms_pid='.$this->productid.'&sms_key='.$this->sms_key;
	}
	/**
	 * ��ȡ����ʣ�����������ƶ��ŷ���ip
	 */
	public function get_smsinfo() {
		$this->param = array('op'=>'sms_get_info');
		$res = $this->pc_file_get_contents();
		return !empty($res) ? json_decode($res, 1) : array();	
	}	

	/**
	 * ��ȡ��ֵ��¼
	 */
	public function get_buyhistory() {
		$this->param = array('op'=>'sms_get_paylist');
		$res = $this->pc_file_get_contents();
		return !empty($res) ? json_decode($res, 1) : array();			
	}

	/**
	 * ��ȡ���Ѽ�¼
	 * @param int $page ҳ��
	 */
	public function get_payhistory($page=1) {
		$this->param = array('op'=>'sms_get_report','page'=>$page);
		$res = $this->pc_file_get_contents();
		return !empty($res) ? json_decode($res, 1) : array();		
	}

	/**
	 * ��ȡ����api����
	 */
	public function get_sms_help() {
		$this->param = array('op'=>'sms_help','page'=>$page);
		$res = $this->pc_file_get_contents();
		return !empty($res) ? json_decode($res, 1) : array();		
	}
	
	/**
	 * 
	 * �������Ͷ���
	 * @param array $mobile �ֻ�����
	 * @param string $content ��������
	 * @param datetime $send_time ����ʱ��
	 * @param string $charset �����ַ����� gbk / utf-8
	 * @param string $id_code Ψһֵ ����������֤��
	 */
	public function send_sms($mobile='', $content='', $send_time='', $charset='gbk',$id_code = '',$tplid = '',$return_code = 0) {
		//���ŷ���״̬
		$status = $this->_sms_status();
		if(is_array($mobile)){
			$mobile = implode(",", $mobile);
		}
		$content = safe_replace($content);
		if(strtolower($charset)=='utf-8') {
			$send_content = iconv('utf-8','gbk',$content);//����IS GBK
		}else{
			$send_content = $content;
		}
		$send_time = strtotime($send_time);
	
		$data = array(
						'sms_pid' => $this->productid,
						'sms_passwd' => $this->sms_key,
						'sms_uid' => $this->userid,
						'charset' => CHARSET,
						'send_txt' => urlencode($send_content),
						'mobile' => $mobile,
						'send_time' => $send_time,
						'tplid' => $tplid,
					);
		$post = '';
		foreach($data as $k=>$v) {
			$post .= $k.'='.$v.'&';
		}

		$smsapi_senturl = $this->smsapi_url.'op=sms_service_new';
		$return = $this->_post($smsapi_senturl, 0, $post);
		$arr = explode('#',$return);
		$this->statuscode = $arr[0];
		//���ӵ��������ݿ�
		$sms_report_db = pc_base::load_model('sms_report_model');
		$send_userid = param::get_cookie('_userid') ? intval(param::get_cookie('_userid')) : 0;
		$ip = ip();
		
		$new_content = $content;
		if(isset($this->statuscode)) {
 			$sms_report_db->insert(array('mobile'=>$mobile,'posttime'=>SYS_TIME,'id_code'=>$id_code,'send_userid'=>$send_userid,'status'=>$this->statuscode,'msg'=>$new_content,'return_id'=>$return,'ip'=>$ip));
		} else {
		$sms_report_db->insert(array('mobile'=>$mobile,'posttime'=>SYS_TIME,'send_userid'=>$send_userid,'status'=>'-2','msg'=>$new_content,'ip'=>$ip));
		}
		if($this->statuscode==0) {
			$barr = explode(':',$arr[1]);
			if($barr[0]=='KEY') {
				return '�������ύ����ȴ�����������ʱ��Ϊ��9:00-18:00�� �������ղ��������������������ϵphpcms.cn������';
			}
		}
		//end
		if($return_code) {
			return $arr[0];
		} else {
			return isset($status[$arr[0]]) ? $status[$arr[0]] : $arr[0];
		}
	}
		
	/**
	 * 
	 * ��ȡԶ������
	 * @param $timeout ��ʱʱ��
	 */
	public function pc_file_get_contents($timeout=30) {
		
		$this->setting = array(
							'sms_uid'=>$this->userid,
							'sms_pid'=>$this->productid,
							'sms_passwd'=>$this->sms_key,	
							);
									
		$this->param = array_merge($this->param, $this->setting);
		
		$url = $this->smsapi_url.http_build_query($this->param);
		$stream = stream_context_create(array('http' => array('timeout' => $timeout)));
		return @file_get_contents($url, 0, $stream);
	}
	
	/**
	 *  post����
	 *  @param string $url		post��url
	 *  @param int $limit		���ص����ݵĳ���
	 *  @param string $post		post���ݣ��ַ�����ʽusername='dalarge'&password='123456'
	 *  @param string $cookie	ģ�� cookie���ַ�����ʽusername='dalarge'&password='123456'
	 *  @param string $ip		ip��ַ
	 *  @param int $timeout		���ӳ�ʱʱ��
	 *  @param bool $block		�Ƿ�Ϊ����ģʽ
	 *  @return string			�����ַ���
	 */
	
	private function _post($url, $limit = 0, $post = '', $cookie = '', $ip = '', $timeout = 30, $block = true) {
		$return = '';
		$matches = parse_url($url);
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;
		$siteurl = $this->_get_url();
		if($post) {
			$out = "POST $path HTTP/1.1\r\n";
			$out .= "Accept: */*\r\n";
			$out .= "Referer: ".$siteurl."\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n" ;
			$out .= 'Content-Length: '.strlen($post)."\r\n" ;
			$out .= "Connection: Close\r\n" ;
			$out .= "Cache-Control: no-cache\r\n" ;
			$out .= "Cookie: $cookie\r\n\r\n" ;
			$out .= $post ;
		} else {
			$out = "GET $path HTTP/1.1\r\n";
			$out .= "Accept: */*\r\n";
			$out .= "Referer: ".$siteurl."\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if(!$fp) return '';
	
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
	
		if($status['timed_out']) return '';	
		while (!feof($fp)) {
			if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n"))  break;				
		}
		
		$stop = false;
		while(!feof($fp) && !$stop) {
			$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
			$return .= $data;
			if($limit) {
				$limit -= strlen($data);
				$stop = $limit <= 0;
			}
		}
		@fclose($fp);
		
		//������������������ֵ�����ݲ�ȷ��ԭ�򣬹��˷������ݸ�ʽ
		$return_arr = explode("\n", $return);
		if(isset($return_arr[1])) {
			$return = trim($return_arr[1]);
		}
		unset($return_arr);
		
		return $return;
	}

	/**
	 * ��ȡ��ǰҳ������URL��ַ
	 */
	private function _get_url() {
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER['PHP_SELF'] ? $this->_safe_replace($_SERVER['PHP_SELF']) : $this->_safe_replace($_SERVER['SCRIPT_NAME']);
		$path_info = isset($_SERVER['PATH_INFO']) ? $this->_safe_replace($_SERVER['PATH_INFO']) : '';
		$relate_url = isset($_SERVER['REQUEST_URI']) ? $this->_safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$this->_safe_replace($_SERVER['QUERY_STRING']) : $path_info);
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}
	
	/**
	 * ��ȫ���˺���
	 *
	 * @param $string
	 * @return string
	 */
	private function _safe_replace($string) {
		$string = str_replace('%20','',$string);
		$string = str_replace('%27','',$string);
		$string = str_replace('%2527','',$string);
		$string = str_replace('*','',$string);
		$string = str_replace('"','&quot;',$string);
		$string = str_replace("'",'',$string);
		$string = str_replace('"','',$string);
		$string = str_replace(';','',$string);
		$string = str_replace('<','&lt;',$string);
		$string = str_replace('>','&gt;',$string);
		$string = str_replace("{",'',$string);
		$string = str_replace('}','',$string);
		$string = str_replace('\\','',$string);
		return $string;
	}
	
	/**
	 * 
	 * �ӿڶ���״̬
	 */
	private function _sms_status() {
		pc_base::load_app_func('global','sms');
		return sms_status(0,1);
	}
	
}



?>