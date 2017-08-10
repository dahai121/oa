<?php
defined('IN_PHPCMS') or exit('No permission resources.');

class index {
    function __construct() {
        pc_base::load_app_func('global');
        // 加载数据模型
        $this->atten_db = pc_base::load_model('oattendance_model');
        $this->user_db = pc_base::load_model('member_model');

        // 取得当前登录会员的会员名(username)和会员ID(userid)
        $this->_username = param::get_cookie('_username');
        $this->_userid = param::get_cookie('_userid');
        $this->_groupid = param::get_cookie('_groupid');

        //定义站点ID常量，选择模版使用
        $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
        define("SITEID", $siteid);
        //1:出勤 绿色\r\n2:请假 黄色\r\n3:迟到 红色\r\n4:公差 蓝色\r\n5:公休 蓝色\r\n6:缺勤 黑色
        $this->attype_str = array("1"=>"出勤","2"=>"请假","3"=>"迟到","4"=>"公差","5"=>"公休","6"=>"缺勤");
        $this->attype_color = array("1"=>"green","2"=>"yellow","3"=>"red","4"=>"blue","5"=>"blue","6"=>"black");
    }

    /**
     *  前端页面展示车辆预约信息
     */
    public function init() {
        // 加载前台模板
        include template('oattendance', 'daily');
    }

    public function ajax_getblist(){
        $siteid = SITEID;
        $seldate = trim($_REQUEST['seldate']);
        if(!isset($_REQUEST['seldate'])||empty($_REQUEST['seldate'])){
            $seldate = date("Y-m-d");
        }
        $start = date("Y-m-01",strtotime($seldate));
        $end = date("Y-m-d",strtotime("$start +1 month -1 day"));
        $where = " `siteid`= $siteid AND `attdate` >= '$start' AND `attdate` <= '$end' ";
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
        $event_data = json_encode($data);
        $attypelist = $this->attype_str;
        exit($event_data);
        break;
    }

    /* 考勤登记 */
    public function addAtten() {
        $siteid = SITEID;
        $selDate = str_replace(' ','-',trim($_GET['selDate']));
        $personid = $this->_userid;
        $where = " `siteid`= $siteid AND `userid` = '$personid' AND `attdate` = '$selDate'";
        $atten = $this->atten_db->get_one($where);
        if($atten){
            //extract($atten);
            //include template('oattendance','edit_atten');
            return;
        }else {
            if(isset($_POST['dosubmit'])){
                $_POST['atten']['attype'] = trim($_POST['atten']['attype']);
                $_POST['atten']['comment'] = trim($_POST['atten']['comment']);
                $_POST['atten']['addtime'] =  date("Y-m-d H:i:s");
                $_POST['atten']['siteid'] = $siteid;
                $_POST['atten']['userid'] = $personid;
                $group = $this->user_db->get_one(" userid='$personid' ");
                $_POST['atten']['groupid'] = $group['groupid'];
                $_POST['atten']['flag'] = 0;
                $bookid = $this->atten_db->insert($_POST['atten'],true);
                if(!$bookid){
                    showmessage(L('登记失败!'),HTTP_REFERER);
                }
                showmessage(L('登记成功'),HTTP_REFERER,'', 'add');
            } else {
                $show_validator = $show_scroll = $show_header = true;
                $attypelist = array("2"=>"请假","3"=>"迟到","4"=>"公差","5"=>"公休","6"=>"缺勤");
                include template('oattendance','add_atten');
            }
        }
    }
    /**
     * 考勤补录条件：1）选择日期为当月
     *               2）选择日期无考勤
     **/
    public function ajax_isatten(){
        $data = "0";
        $siteid = SITEID;
        $seldate = trim($_REQUEST['seldate']);
        $start_time = date("Y-m-01");
        $end_time = date("Y-m-d",strtotime("$start_time +1 month -1 day"));
        if(strtotime($start_time)<=strtotime($seldate) &&
            strtotime($seldate)<=strtotime($end_time)){
            $userid = 0;//$this->_userid;
            $where = " `siteid`= $siteid AND `userid` = $userid AND `attdate` = '$seldate'";
            $atten = $this->atten_db->get_one($where);
            if(!$atten){
                $data = "1";
            }
        }
        exit($data);
        break;
    }
    //////
    public function editAtten() {
        $siteid = SITEID;
        $selDate = str_replace(' ','-',trim($_GET['selDate']));
        $personid = $this->_userid;
        $where = " `siteid`= $siteid AND `personid` = '$personid' AND `attdate` = '$selDate'";
        $atten = $this->atten_db->get_one($where);
        if(isset($_POST['dosubmit'])){
            $_POST['atten']['addtime'] = date("Y-m-d H:i:s");
            $_POST['atten']['siteid'] = $siteid;
            //若前台使用，修改admin_username
            $_POST['atten']['personid'] = param::get_cookie('_userid');
            $bookid = $this->atten_db->insert($_POST['atten'],true);
            if(!$bookid){
                showmessage(L('登记失败!'),HTTP_REFERER);
            }
            showmessage(L('登记成功'),HTTP_REFERER,'', 'add');
        } else {
            $show_validator = $show_scroll = $show_header = true;
            if(!$atten)showmessage(L('信息不存在'));
            extract($atten);
            include template('oattendance','edit_atten');
        }
    }
}
?>