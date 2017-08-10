<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class oattendance extends admin {
	
    public function __construct() {
		//继承父类构造函数
        parent::__construct();
		//读取配置缓存文件
        $this->_username = param::get_cookie('admin_username');
		$this->_userid = param::get_cookie('userid');
        $this->_set = $setting[$this->get_siteid()];
		// 加载数据模型
        $this->atten_db = pc_base::load_model('oattendance_model');
		$this->depart_db = pc_base::load_model('member_group_model');
		$this->user_db = pc_base::load_model('member_model');
		// 加载form
		pc_base::load_sys_class('form');
		//1:出勤 绿色\r\n2:请假 黄色\r\n3:迟到 红色\r\n4:公差 蓝色\r\n5:公休 蓝色\r\n6:缺勤 黑色
		$this->attype_str = array("1"=>"出勤","2"=>"请假","3"=>"迟到","4"=>"公差","5"=>"公休","6"=>"缺勤");
		$this->attype_color = array("1"=>"green","2"=>"yellow","3"=>"red","4"=>"blue","5"=>"blue","6"=>"black");
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
    }
    
    public function init() {
		$page = isset($_GET['page']) && intval($_GET['page'])?intval($_GET['page']):1;
		// 默认查询当月的所有人考勤情况
		$start_time = date("Y-m-01");
		$end_time = date("Y-m-d",strtotime("$start_time +1 month -1 day"));
		$where = " `attdate` >= '$start_time' AND `attdate` <= '$end_time' ";
		$order = 'groupid DESC,userid DESC,attdate ASC';
        $ainfos = $this->atten_db->listinfo($where,$order,$page,$pages='15');
		foreach($ainfos as $ainfo){
			$infos[] = array(
				'attid' => $ainfo['attid'],
				'username' => $this->user_status[$ainfo['userid']],
				'dname' => $this->depart_status[$ainfo['groupid']],
				'attdate' => $ainfo['attdate'],
				'addtime' => $ainfo['addtime'],
				'attype' => $this->attype_str[$ainfo['attype']],
				'comment' => $ainfo['comment'],
				'flag' => $ainfo['flag']
			);
		}
		$user_status = $this->user_status;
		$depart_status = $this->depart_status;
		$pages = $this->atten_db->pages;
 		include $this->admin_tpl('atten_select');
    }
	
	/* 考勤查询 */
	public function attenselect(){
		$page = isset($_GET['page']) && intval($_GET['page'])?intval($_GET['page']):1;
		$where = '';
		if(isset($_POST['dosubmit'])){
			extract($_POST['search']);
			if($departid){
				$where .= $where ? " AND `groupid` = '$departid' " : " `groupid` = '$departid' ";
			}
			if($userid){
				$where .= $where ? " AND `userid` = '$userid' " : " `userid` >= '$userid' ";
			} 
			if($start_time && $end_time){
				$start = date("Y-m-d",strtotime($start_time));
				$end = date("Y-m-d",strtotime($end_time));
				$where .= $where ? " AND `attdate` >= '$start' AND `attdate` <= '$end' " : " `attdate` >= '$start' AND `attdate` <= '$end' ";
			}
		}
		$order = 'groupid DESC,userid DESC,attdate ASC';
		$ainfos = $this->atten_db->listinfo($where,$order,$page,$pages='10');
		foreach($ainfos as $ainfo){
			$infos[] = array(
				'attid' => $ainfo['attid'],
				'username' => $this->user_status[$ainfo['userid']],
				'dname' => $this->depart_status[$ainfo['groupid']],
				'attdate' => $ainfo['attdate'],
				'addtime' => $ainfo['addtime'],
				'attype' => $this->attype_str[$ainfo['attype']],
				'comment' => $ainfo['comment'],
				'flag' => $ainfo['flag']
			);
		}
		$user_status = $this->user_status;
		$depart_status = $this->depart_status;
		$pages = $this->atten_db->pages;
 		include $this->admin_tpl('atten_select_list');
	}
	
	/*异步获取处室人员名单*/
	public function public_ajax_getulist(){
		$groupid = trim($_REQUEST['groupid']);
		$where = " `groupid`= $groupid ";
		$infos = $this->user_db->listinfo($where,'userid DESC');
		foreach($infos as $row){
			//json_encode只能接受utf8编码的中文
			$name = mb_convert_encoding($row['username'],"UTF-8","GBK");
			$data[] = array(
				'userid' => $row['userid'],
				'username' => $name
				);
		}	
		$event_data = json_encode($data);
		exit($event_data);
		break;
	}
	
	/*考勤审核*/
	public function check(){
		if(isset($_POST['dosubmit'])){
			$id_arr = intval($_GET['attid']);
			if($id_arr < 1) return false;
			$att = $this->atten_db->get_one(array('attid'=>$id_arr));
			$att['flag']=1;
			$this->atten_db->update($att,array('attid'=>$id_arr));	
			showmessage(L('operation_success'),'?m=oattendance&c=oattendance&a=init','', 'check');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			$atten = $this->atten_db->get_one(array('attid'=>$_GET['attid']));
			if(!$atten)showmessage(L('信息不存在'));
			extract($atten);
			$uname = $this->user_status[$atten['userid']];
			$dname = $this->depart_status[$atten['groupid']];
			$attypelist = $this->attype_str;
			include $this->admin_tpl('oatten_check');	
		}
	}
	
	/*考勤审核2*/
	public function allcheck(){
		if(is_array($_POST['attid'])){
			//批量审核
			foreach($_POST['attid'] as $id_arr) {
				$id_arr = intval($id_arr);
				$att = $this->atten_db->get_one(array('attid'=>$id_arr));
				$att['flag']=1;
				$this->atten_db->update($att,array('attid'=>$id_arr));
			}
		}	
		showmessage(L('operation_success'),HTTP_REFERER);	
	}
	
	/* 考勤修改 */
	public function update(){
		if(isset($_POST['dosubmit'])){
			$attid=intval($_GET['attid']);
			if($attid < 1) return false;
			if(!is_array($_POST['atten']) || empty($_POST['atten'])) return false;
			if((!$_POST['atten']['attype']) || empty($_POST['atten']['attype'])) return false;
			$_POST['atten']['comment'] = trim($_POST['atten']['comment']);
			$_POST['atten']['addtime'] = date("Y-m-d H:i:s");
			$post_data = trim_script($_POST);
			$this->atten_db->update($post_data['atten'],array('attid'=>$attid));
			showmessage(L('operation_success'),'?m=oattendance&c=oattendance&a=init','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			$atten = $this->atten_db->get_one(array('attid'=>$_GET['attid']));
			if(!$atten)showmessage(L('信息不存在'));
			extract($atten);
			$uname = $this->user_status[$atten['userid']];
			$dname = $this->depart_status[$atten['groupid']];
			$attypelist = $this->attype_str;
			include $this->admin_tpl('oatten_edit');
		}
	}
	
	/* 考勤统计 
	 *  param:"1"=>"出勤","2"=>"请假","3"=>"迟到",
	 *        "4"=>"公差","5"=>"公休","6"=>"缺勤"
	*/
	public function attenstatic(){
		//设置查询默认值
		$workdays = 22;
		$year = date("Y");
		$month = date("m");
		$year_status = array($year-2=>$year-2,$year-1=>$year-1,$year=>$year);
		$month_status = array("1"=>"一月","2"=>"二月","3"=>"三月","4"=>"四月","5"=>"五月","6"=>"六月","7"=>"七月","8"=>"八月","9"=>"九月","10"=>"十月","11"=>"十一月","12"=>"十二月");
		$depart_status = $this->depart_status;
		//设置查询条件并查询
		$start = date("Y-m-d",strtotime($year.'-'.$month.'-01'));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where .= $where ? " AND `attdate` >= '$start' AND `attdate` <= '$end' " : " `attdate` >= '$start' AND `attdate` <= '$end' ";
		$order = 'groupid DESC,userid DESC,attdate ASC--';
		$ainfos = $this->atten_db->listinfo($where,$order);
		if(sizeof($ainfos)>0){
			//整理查询数据
			foreach($this->user_status as $key=>$val){
				$tmp=array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0);
				$utlcomment='';
				foreach($ainfos as $ainfo){
					if(intval($ainfo['userid'])==$key && $ainfo['flag']==1){
						$tmp[$ainfo['attype']]++;
						if($ainfo['attype']==3){
							$utlcomment=$utlcomment.$ainfo['comment'].' ';
						}
					}
				}
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'次  '
							 .$this->attype_str['4'].$tmp['4'].'次  '
							 .$this->attype_str['5'].$tmp['5'].'次  '
							 .$this->attype_str['6'].$tmp['6'].'次  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1']-$tmp['3'],//未出勤
					'wcqyy' => $ulcomment,
					'wascq' => $tmp['3'],//未按时出勤
					'wascqyy' => $utlcomment,
					'userid' => $key,
					'username' => $val
				);
			}
		}
 		include $this->admin_tpl('atten_static');
	}
	
	/* 异步获取统计查询结果 */
	public function public_ajax_getslist(){
		$groupid = trim($_REQUEST['groupid']);
		$year = trim($_REQUEST['year']);
		$month = trim($_REQUEST['month']);
		$workdays = trim($_REQUEST['workdays']);
		//设置查询条件并查询
		$user_status = $this->user_db->listinfo($where,'userid DESC');
		$start = date("Y-m-d",strtotime($year.'-'.$month.'-01'));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where = '';
		if($groupid!=0){
			$where .= $where ? " AND `groupid`='$groupid'":" `groupid`='$groupid'";
		}
		$where .= $where ? " AND `attdate` >= '$start' AND `attdate` <= '$end' " : " `attdate` >= '$start' AND `attdate` <= '$end' ";
		$order = 'groupid DESC,userid DESC,attdate ASC--';
		$ainfos = $this->atten_db->listinfo($where,$order);
		if(sizeof($ainfos)>0){
			//整理查询数据
			foreach($user_status as $user){
				$tmp=array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0);
				$utlcomment='';
				foreach($ainfos as $ainfo){
					if(intval($ainfo['userid'])==$user['userid'] && $ainfo['flag']==1){
						$tmp[$ainfo['attype']]++;
						if($ainfo['attype']==3){
							$utlcomment=$utlcomment.$ainfo['comment'].' ';
						}
					}
				}
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'次  '
							 .$this->attype_str['4'].$tmp['4'].'次  '
							 .$this->attype_str['5'].$tmp['5'].'次  '
							 .$this->attype_str['6'].$tmp['6'].'次  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1']-$tmp['3'],//未出勤
					'wcqyy' => mb_convert_encoding($ulcomment,"UTF-8","GBK"),
					'wascq' => $tmp['3'],//未按时出勤
					'wascqyy' => mb_convert_encoding($utlcomment,"UTF-8","GBK"),
					'userid' => $user['userid'],
					'username' => mb_convert_encoding($user['username'],"UTF-8","GBK")
				);
			}
		}
		$event_data = json_encode($infos);
		exit($event_data);
		break;
	}
	
	/* 导出Excel数据 */
	public function export(){
		$groupid = trim($_POST['groupid']);
		if($groupid==null || $groupid='')$groupid=0;
		$year = trim($_POST['year']);
		$month = trim($_POST['month']);
		$workdays = trim($_POST['workdays']);
		//设置查询条件并查询
		$user_status = $this->user_db->listinfo($where,'userid DESC');
		$start = date("Y-m-d",strtotime($year.'-'.$month.'-01'));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where = '';
		if($groupid!=0){
			$where .= $where ? " AND `groupid`='$groupid'":" `groupid`='$groupid'";
		}
		$where .= $where ? " AND `attdate` >= '$start' AND `attdate` <= '$end' " : " `attdate` >= '$start' AND `attdate` <= '$end' ";
		$order = 'groupid DESC,userid DESC,attdate ASC--';
		$ainfos = $this->atten_db->listinfo($where,$order);
		//整理查询数据
		if(sizeof($ainfos)>0){
			foreach($user_status as $user){
				$tmp=array("1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0);
				$utlcomment='';
				foreach($ainfos as $ainfo){
					if(intval($ainfo['userid'])==$user['userid'] && $ainfo['flag']==1){
						$tmp[$ainfo['attype']]++;
						if($ainfo['attype']==3){
							$utlcomment=$utlcomment.$ainfo['comment'].' ';
						}
					}
				}
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'次  '
							 .$this->attype_str['4'].$tmp['4'].'次  '
							 .$this->attype_str['5'].$tmp['5'].'次  '
							 .$this->attype_str['6'].$tmp['6'].'次  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1'],//未出勤
					'wcqyy' => mb_convert_encoding($ulcomment,"UTF-8","GBK"),
					'wascq' => $tmp['3'],//未按时出勤
					'wascqyy' => mb_convert_encoding($utlcomment,"UTF-8","GBK"),
					'userid' => $user['userid'],
					'username' => mb_convert_encoding($user['username'],"UTF-8","GBK")
				);
			}
		}
		//设置导出
		$title=$year.'年'.$month.'月份局域网考勤情况统计表';
		$xlsTitle=mb_convert_encoding($title,"UTF-8","GBK"); //文件名称
		$filename=$title.'.xls';
		error_reporting(E_ALL);
		include 'templates/PHPExcel.php';
		include 'templates/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		//设置表格样式
		$objPHPExcel->getProperties()->setCreator("oattendance");
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(11);
		$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//设置单元格边框
		$styleThinBlackBorderOutline = array(
			'borders' => array (
				'outline' => array (
				'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
				//'style' => PHPExcel_Style_Border::BORDER_THICK,  另一种样式
				'color' => array ('argb' => 'FF000000'),          //设置border颜色
				),
			),
		);
		//设置标题样式		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',$xlsTitle);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleThinBlackBorderOutline);
		//设置表头样式
		$objPHPExcel->getActiveSheet()->setCellValue('A2',mb_convert_encoding('姓名',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('B2',mb_convert_encoding('未登录次数',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('C2',mb_convert_encoding('原因',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('D2',mb_convert_encoding('未按时登录次数',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('E2',mb_convert_encoding('原因',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleThinBlackBorderOutline);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleThinBlackBorderOutline);
		$objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($styleThinBlackBorderOutline);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->applyFromArray($styleThinBlackBorderOutline);
		$objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($styleThinBlackBorderOutline);
		$i=3;
		foreach($infos as $info){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $info['username']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $info['wcq']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $info['wcqyy']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $info['wascq']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $info['wascqyy']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleThinBlackBorderOutline);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleThinBlackBorderOutline);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($styleThinBlackBorderOutline);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($styleThinBlackBorderOutline);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($styleThinBlackBorderOutline);
			$i++;
		}		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();//清除缓冲区,避免乱码
		header("Cache-Control:max-age=0");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Disposition:attachment;filename=".$filename);
		$objWriter->save('php://output');
        return true;
	}	
	
	/* 考勤日历展示 */
    public function show() {
		$siteid = $this->get_siteid();
		$userid = trim($_REQUEST['uid']);
		$year = trim($_REQUEST['year']);
		$month = trim($_REQUEST['month']);
		$start = date("Y-m-01",strtotime($year.'-'.$month));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where = " `userid`=$userid AND `attdate` >= '$start' AND `attdate` <= '$end' ";
        $infos = $this->atten_db->listinfo($where,'attid DESC--'); 
		foreach($infos as $row){
			//json_encode只能接受utf8编码的中文
			$title = mb_convert_encoding($this->attype_str[$row['attype']],"UTF-8","GBK");			
			$data[] = array(
				'id' => $row['attid'],
				'title' => $title,
				'start' => date('Y-m-d',strtotime($row['attdate'])),
				'color' => $this->attype_color[$row['attype']],
				'className' => $row['attype']
			);
		}
		$username = $this->user_status[$userid];
		$event_data = json_encode($data);
        include $this->admin_tpl('atten_show');
    }
}
?>