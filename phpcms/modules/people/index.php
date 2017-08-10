<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
    function __construct() {
		pc_base::load_app_func('global');
        // 取得当前登录会员的会员名(username)和会员ID(userid)
        $this->_username = param::get_cookie('_username');
        $this->_userid = param::get_cookie('_userid');
		$this->groupid = param::get_cookie('_groupid'); 
        // 加载数据模型
        $this->carinfo_db = pc_base::load_model('oacarinfo_model');
		$this->carbook_db = pc_base::load_model('oacarbook_model');
		$this->user_db = pc_base::load_model('member_model');
		$this->depart_db = pc_base::load_model('member_group_model');
		
		// 默认获取所有user
		$userlist = $this->user_db->listinfo('','userid DESC');
		// 默认获取所有group
		$departlist = $this->depart_db->listinfo('','groupid DESC');
		foreach($userlist as $info){
			$this->user_status[$info['userid']] = $info['username'];
		}
 		foreach($departlist as $info) {
			$this->depart_status[$info['groupid']] = $info['name'];
		}
        //定义站点ID常量，选择模版使用
        //$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
    }
    
	/**
	*  前端页面展示车辆预约信息
	*/
    public function init() {
        // 加载前台模板
        include template('oacarmgn', 'show');
    }
	
	public function ajax_getblist(){
		$seldate = trim($_REQUEST['seldate']);
		if(!isset($_REQUEST['seldate'])||empty($_REQUEST['seldate'])){
			$seldate = date("Y-m-d");
		}
		$start = date("Y-m-01",strtotime($seldate));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where = " `bdate` >= '$start' AND `bdate` <= '$end' ";
        $infos = $this->carbook_db->listinfo($where,'bid DESC');    
		foreach($infos as $row){
			$btime = $row['btime'];
			switch($btime){
				case '1':
					$is_btime = "上午";
					break;
				case '2':
					$is_btime = "下午";
					break;
				case '3':
					$is_btime = "全天";
					break;
			}
			//json_encode只能接受utf8编码的中文
			$title = mb_convert_encoding($row['carname'].' '.$is_btime,"UTF-8","GBK");
			$uname = $this->user_status[$row['userid']];
			$class = mb_convert_encoding($uname.' '.$row['flag'].' '.$row['comment'],"UTF-8","GBK");
			$data[] = array(
				'id' => $row['bid'],
				'title' => $title,
				'start' => date('Y-m-d',strtotime($row['bdate'])),
				'color' => 'red',
				'className' => $class
			);
		}
		$event_data = json_encode($data);
		exit($event_data);
		break;
	}
	
	/* 添加车辆预约 */
    public function addbook() {
        if(isset($_POST['dosubmit'])){
			//判断当前预约的车辆是否已经被预约
			$bdate = trim($_POST['oacarbook']['bdate']);
			$btime = trim($_POST['oacarbook']['btime']);
			$carname = trim($_POST['oacarbook']['carname']);
			$userid = trim($_POST['oacarbook']['userid']);				
			$isbookwhere = " `carname` = '$carname' AND `bdate` = '$bdate' AND (`btime` = '$btime' OR `btime` = 3)";
			$isbook = $this->carbook_db->get_one($isbookwhere);
			if($isbook){
				showmessage(L($carname.'已经被预约!'),HTTP_REFERER);
				return;
			}
            $_POST['oacarbook']['addtime'] =  date("Y-m-d H:i:s");
			$group = $this->user_db->get_one(" userid='$userid' ");
			$_POST['oacarbook']['groupid'] = $group['groupid'];
			$bookid = $this->carbook_db->insert($_POST['oacarbook'],true);
			if(!$bookid){
				showmessage(L('预约失败!'),HTTP_REFERER);
			}
			showmessage(L('预约成功'),HTTP_REFERER,'', 'add');
         } else {
			$show_validator = $show_scroll = $show_header = true;
			$selDate = str_replace(' ','-',trim($_GET['selDate']));
			$userlist = $this->user_status;
			$carlist = $this->carinfo_db->listinfo('','carid desc');
			include template('oacarmgn','book_car');
        }
    }
}
?>