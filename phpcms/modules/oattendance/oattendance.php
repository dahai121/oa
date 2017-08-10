<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class oattendance extends admin {
	
    public function __construct() {
		//�̳и��๹�캯��
        parent::__construct();
		//��ȡ���û����ļ�
        $this->_username = param::get_cookie('admin_username');
		$this->_userid = param::get_cookie('userid');
        $this->_set = $setting[$this->get_siteid()];
		// ��������ģ��
        $this->atten_db = pc_base::load_model('oattendance_model');
		$this->depart_db = pc_base::load_model('member_group_model');
		$this->user_db = pc_base::load_model('member_model');
		// ����form
		pc_base::load_sys_class('form');
		//1:���� ��ɫ\r\n2:��� ��ɫ\r\n3:�ٵ� ��ɫ\r\n4:���� ��ɫ\r\n5:���� ��ɫ\r\n6:ȱ�� ��ɫ
		$this->attype_str = array("1"=>"����","2"=>"���","3"=>"�ٵ�","4"=>"����","5"=>"����","6"=>"ȱ��");
		$this->attype_color = array("1"=>"green","2"=>"yellow","3"=>"red","4"=>"blue","5"=>"blue","6"=>"black");
		// Ĭ�ϻ�ȡ����user
		$userlist = $this->user_db->listinfo('','userid DESC');
		// Ĭ�ϻ�ȡ����group
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
		// Ĭ�ϲ�ѯ���µ������˿������
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
	
	/* ���ڲ�ѯ */
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
	
	/*�첽��ȡ������Ա����*/
	public function public_ajax_getulist(){
		$groupid = trim($_REQUEST['groupid']);
		$where = " `groupid`= $groupid ";
		$infos = $this->user_db->listinfo($where,'userid DESC');
		foreach($infos as $row){
			//json_encodeֻ�ܽ���utf8���������
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
	
	/*�������*/
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
			if(!$atten)showmessage(L('��Ϣ������'));
			extract($atten);
			$uname = $this->user_status[$atten['userid']];
			$dname = $this->depart_status[$atten['groupid']];
			$attypelist = $this->attype_str;
			include $this->admin_tpl('oatten_check');	
		}
	}
	
	/*�������2*/
	public function allcheck(){
		if(is_array($_POST['attid'])){
			//�������
			foreach($_POST['attid'] as $id_arr) {
				$id_arr = intval($id_arr);
				$att = $this->atten_db->get_one(array('attid'=>$id_arr));
				$att['flag']=1;
				$this->atten_db->update($att,array('attid'=>$id_arr));
			}
		}	
		showmessage(L('operation_success'),HTTP_REFERER);	
	}
	
	/* �����޸� */
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
			if(!$atten)showmessage(L('��Ϣ������'));
			extract($atten);
			$uname = $this->user_status[$atten['userid']];
			$dname = $this->depart_status[$atten['groupid']];
			$attypelist = $this->attype_str;
			include $this->admin_tpl('oatten_edit');
		}
	}
	
	/* ����ͳ�� 
	 *  param:"1"=>"����","2"=>"���","3"=>"�ٵ�",
	 *        "4"=>"����","5"=>"����","6"=>"ȱ��"
	*/
	public function attenstatic(){
		//���ò�ѯĬ��ֵ
		$workdays = 22;
		$year = date("Y");
		$month = date("m");
		$year_status = array($year-2=>$year-2,$year-1=>$year-1,$year=>$year);
		$month_status = array("1"=>"һ��","2"=>"����","3"=>"����","4"=>"����","5"=>"����","6"=>"����","7"=>"����","8"=>"����","9"=>"����","10"=>"ʮ��","11"=>"ʮһ��","12"=>"ʮ����");
		$depart_status = $this->depart_status;
		//���ò�ѯ��������ѯ
		$start = date("Y-m-d",strtotime($year.'-'.$month.'-01'));
		$end = date("Y-m-d",strtotime("$start +1 month -1 day"));
		$where .= $where ? " AND `attdate` >= '$start' AND `attdate` <= '$end' " : " `attdate` >= '$start' AND `attdate` <= '$end' ";
		$order = 'groupid DESC,userid DESC,attdate ASC--';
		$ainfos = $this->atten_db->listinfo($where,$order);
		if(sizeof($ainfos)>0){
			//�����ѯ����
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
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'��  '
							 .$this->attype_str['4'].$tmp['4'].'��  '
							 .$this->attype_str['5'].$tmp['5'].'��  '
							 .$this->attype_str['6'].$tmp['6'].'��  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1']-$tmp['3'],//δ����
					'wcqyy' => $ulcomment,
					'wascq' => $tmp['3'],//δ��ʱ����
					'wascqyy' => $utlcomment,
					'userid' => $key,
					'username' => $val
				);
			}
		}
 		include $this->admin_tpl('atten_static');
	}
	
	/* �첽��ȡͳ�Ʋ�ѯ��� */
	public function public_ajax_getslist(){
		$groupid = trim($_REQUEST['groupid']);
		$year = trim($_REQUEST['year']);
		$month = trim($_REQUEST['month']);
		$workdays = trim($_REQUEST['workdays']);
		//���ò�ѯ��������ѯ
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
			//�����ѯ����
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
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'��  '
							 .$this->attype_str['4'].$tmp['4'].'��  '
							 .$this->attype_str['5'].$tmp['5'].'��  '
							 .$this->attype_str['6'].$tmp['6'].'��  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1']-$tmp['3'],//δ����
					'wcqyy' => mb_convert_encoding($ulcomment,"UTF-8","GBK"),
					'wascq' => $tmp['3'],//δ��ʱ����
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
	
	/* ����Excel���� */
	public function export(){
		$groupid = trim($_POST['groupid']);
		if($groupid==null || $groupid='')$groupid=0;
		$year = trim($_POST['year']);
		$month = trim($_POST['month']);
		$workdays = trim($_POST['workdays']);
		//���ò�ѯ��������ѯ
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
		//�����ѯ����
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
				$ulcomment=''.$this->attype_str['2'].$tmp['2'].'��  '
							 .$this->attype_str['4'].$tmp['4'].'��  '
							 .$this->attype_str['5'].$tmp['5'].'��  '
							 .$this->attype_str['6'].$tmp['6'].'��  ';
				$infos[] = array(
					'cq' => $tmp['1'],
					'wcq' => $workdays-$tmp['1'],//δ����
					'wcqyy' => mb_convert_encoding($ulcomment,"UTF-8","GBK"),
					'wascq' => $tmp['3'],//δ��ʱ����
					'wascqyy' => mb_convert_encoding($utlcomment,"UTF-8","GBK"),
					'userid' => $user['userid'],
					'username' => mb_convert_encoding($user['username'],"UTF-8","GBK")
				);
			}
		}
		//���õ���
		$title=$year.'��'.$month.'�·ݾ������������ͳ�Ʊ�';
		$xlsTitle=mb_convert_encoding($title,"UTF-8","GBK"); //�ļ�����
		$filename=$title.'.xls';
		error_reporting(E_ALL);
		include 'templates/PHPExcel.php';
		include 'templates/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		//���ñ����ʽ
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
		//���õ�Ԫ��߿�
		$styleThinBlackBorderOutline = array(
			'borders' => array (
				'outline' => array (
				'style' => PHPExcel_Style_Border::BORDER_THIN,   //����border��ʽ
				//'style' => PHPExcel_Style_Border::BORDER_THICK,  ��һ����ʽ
				'color' => array ('argb' => 'FF000000'),          //����border��ɫ
				),
			),
		);
		//���ñ�����ʽ		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',$xlsTitle);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleThinBlackBorderOutline);
		//���ñ�ͷ��ʽ
		$objPHPExcel->getActiveSheet()->setCellValue('A2',mb_convert_encoding('����',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('B2',mb_convert_encoding('δ��¼����',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('C2',mb_convert_encoding('ԭ��',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('D2',mb_convert_encoding('δ��ʱ��¼����',"UTF-8","GBK"));
		$objPHPExcel->getActiveSheet()->setCellValue('E2',mb_convert_encoding('ԭ��',"UTF-8","GBK"));
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
		ob_end_clean();//���������,��������
		header("Cache-Control:max-age=0");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Disposition:attachment;filename=".$filename);
		$objWriter->save('php://output');
        return true;
	}	
	
	/* ��������չʾ */
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
			//json_encodeֻ�ܽ���utf8���������
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