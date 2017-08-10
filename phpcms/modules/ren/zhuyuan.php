<?php
/**
 * ����Ա��̨��Ա������
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//ģ�ͻ���·��
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class zhuyuan extends admin {

    function __construct() {
        parent::__construct();
            $this->db = pc_base::load_model('zhuyuan_model');
            $this->uinfo = pc_base::load_model('uinfo_model');
            $this->xlala = pc_base::load_model('xiala_model');
    }

    /**
     * defalut
     */
    function init() {
        $show_header = $show_scroll = true;
        pc_base::load_sys_class('form', '', 0);

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $groupid = isset($_GET['groupid']) ? $_GET['groupid'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);


        //������Ա��Ϣ
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->db->listinfo(array('siteid'=>$this->get_siteid()),'id DESC',$page, '30');
        $pages = $this->db->pages;

        include $this->admin_tpl('health_list');
    }

    /**
     * ��Ա����
     */
    function search() {
        $modelid = $_GET['modelid'];
        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $rylx = isset($_GET['rylx']) ? $_GET['rylx'] : '';
        //վ����Ϣ
        $sitelistarr = getcache('sitelist', 'commons');
        $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : '0';
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }
        //���ʱ��
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        //��Ա����ģ��
        $modellistarr = getcache('people_model', 'commons');
        foreach ($modellistarr as $k=>$v) {
            $modellist[$k] = $v['name'];
        }

        if (isset($_GET['search'])) {

            //Ĭ��ѡȡһ�����ڵ��û�����ֹ�û�������������������
            $where_start_time = strtotime($start_time) ? strtotime($start_time) : 0;
            $where_end_time = strtotime($end_time) + 86400;
            //��ʼʱ����ڽ���ʱ�䣬�û�����
            if($where_start_time > $where_end_time) {
                $tmp = $where_start_time;
                $where_start_time = $where_end_time;
                $where_end_time = $tmp;
                $tmptime = $start_time;

                $start_time = $end_time;
                $end_time = $tmptime;
                unset($tmp, $tmptime);
            }
            $where = '';
            //����ǳ�������Ա��ɫ����ʾ�����û���������ʾ��ǰվ���û�
            if($_SESSION['roleid'] == 1) {
                if(!empty($siteid)) {
                    $where .= "`siteid` = '$siteid' AND ";
                }
            } else {
                $siteid = get_siteid();
                $where .= "`siteid` = '$siteid' AND ";
            }
            $where .= "`inputtime` BETWEEN '$where_start_time' AND '$where_end_time' AND ";

            if($rylx){
                if($rylx != 80){
                    $where .= "`rylx` = '$rylx' AND ";
                }else{
                    $where .= "`rylx` = '78' OR `rylx` = '79'AND ";
                }
            }

            if($keyword) {
                    $where .= "`name` like '%$keyword%'";
            } else {
                $where .= '1';
            }

        } else {
            $where = '';
        }

        $xiala = $this->xia_list();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        $pages = $this->db->pages;
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ren&c=master&a=add\', title:\''.L('add_model').'\', width:\'580\', height:\'420\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_model'));



        include $this->admin_tpl('master_list');
    }

    /**
     * סԺ��Ϣ
     */
    function manage() {

        $groupid = isset($_GET['groupid']) ? intval($_GET['groupid']) : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        //����ǳ�������Ա��ɫ����ʾ�����û���������ʾ��ǰվ���û�
        if($_SESSION['roleid'] == 1) {
            $where = '';
        } else {
            $siteid = get_siteid();
            $where = "`siteid` = '$siteid' and ";
            $where .= "`islock` = '0'";
        }

        $memberlist_arr = $this->db->select($where);

        foreach ($memberlist_arr as $key => $val){
            $memberlist_arr[$key]['uinfo'] =  $this->uinfo->get_one(array('id'=>$val['peopleid']));
            $memberlist_arr[$key]['uinfo']['zj'] = $this->xiala_name($memberlist_arr[$key]['uinfo']['zj']);
        }
        $data = $memberlist_arr;
        $xiala = $this->xia_list();
        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        $pages = $this->db->pages;
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ren&c=zhuyuan&a=add\', title:\''.L('�����Ա').'\', width:\'800\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����Ա'));
        include $this->admin_tpl('zhuyuan_list');
    }


    /**
     * add ��������
     */
    function add() {
        header("Cache-control: private");
        if(isset($_POST['dosubmit'])) {
            $info = $_POST['info'];
            $info['inputtime'] = $info['edittime'] = SYS_TIME;
            if($this->db->insert($info)){
                showmessage(L('operation_success'),'?m=ren&c=health&a=add', '', 'add');
            }
        } else {
            pc_base::load_sys_class('form', '', 0);
            $show_header = $show_scroll = true;
            $siteid = get_siteid();
            $xiala = $this->xia_list();
            include $this->admin_tpl('zhuyuan_add');
        }

    }
    //��ȡ��Ա��Ϣ
    public function ajax_get_people(){
        $id =$_POST['id'];
        if(empty($id)){
            $data = $this->uinfo->select();
        }else{
            $data = $this->uinfo->select(array('rylx'=>$id));
        }
        $str = "<select name='info[peopleid]'>";
        foreach ($data as $key => $val){
                $str .= "<option  value='".$val['id']."'>".$val['title']."</option>";
        }
        $str .= "</select>";
        echo $str;
    }

    //��ȡҽ����Ϣ
    public function ajax_get_dy(){
        $id =$_POST['id'];
        $this->health = pc_base::load_model('health_model');
       $health_info =  $this->health->get_one(array('peopleid'=>$id));
        if($health_info['yldy'] == 41 ){
            $health_info['yldy'] = "ҽ��";
        }else{
            $health_info['yldy'] = "��ҽ��";
        }

       $data[] = "<div>".$health_info['ybxh']."</div>";
       $data[] = "<div>".$health_info['yldy']."</div>";
       echo json_encode($data)  ;




    }


    public function edit() {
        //����cookie �ڸ�����Ӵ�����
        param::set_cookie('module', 'content');
        if(isset($_POST['dosubmit']) || isset($_POST['dosubmit_continue'])) {
            define('INDEX_HTML',true);
            $id = $_POST['info']['id'] = intval($_POST['id']);
            $data = $_POST['info'];
            $this->db->update($data, array('id'=>$id));
            if(isset($_POST['dosubmit'])) {
                showmessage(L('update_success'), '?m=ren&c=health&a=edit', '', 'edit');
            } else {
                showmessage(L('update_success'),HTTP_REFERER);
            }
        } else {
            $show_header = $show_dialog = $show_validator = '';
            //�����ݿ��ȡ����
            $id = intval($_GET['id']);
            $data = $this->db->get_one(array('id'=>$id));
            $uinfo = $this->uinfo->get_one(array('id'=>$data['peopleid']));
            //������ѡ��
            $set = $this->xlala->select();
            $set = node($set);
            $xiala = array();
            foreach($set  as $key=>$val){
                $xiala[$val['id']] = $val;
            }
            $data = array_map('htmlspecialchars_decode',$data);
            include $this->admin_tpl('health_edit');
        }
        header("Cache-control: private");
    }

    /**
     * delete ��Ա
     */
    function delete() {
        $uidarr = isset($_POST['userid']) ? $_POST['userid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
        $uidarr = array_map('intval',$uidarr);
        $where = to_sqls($uidarr, '', 'id');
        if ($this->db->delete($where)) {
            //ɾ���û�ģ���û�����
            foreach($uidarr as $v) {
                if(!empty($userinfo[$v])) {
                    $this->db->set_model($userinfo[$v]);
                    $this->db->delete(array('userid'=>$v));
                }
            }
                showmessage(L('operation_success'), HTTP_REFERER);
            } else {
                if ($this->db->delete($where)) {
                    showmessage(L('operation_success'), HTTP_REFERER);
                } else {
                    showmessage(L('operation_failure'), HTTP_REFERER);
                }
        }
    }

    /**
     * ���� ��Ա
     */
    function lock() {
        if(isset($_POST['userid'])) {
            $uidarr = isset($_POST['userid']) ? $_POST['userid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
            $where = to_sqls($uidarr, '', 'id');
            $this->db->update(array('islock'=>1), $where);
            showmessage(L('member_lock').L('operation_success'), HTTP_REFERER);
        } else {
            showmessage(L('operation_failure'), HTTP_REFERER);
        }
    }

    /**
     * ������� ��Ա
     */
    function unlock() {
        if(isset($_POST['userid'])) {
            $uidarr = isset($_POST['userid']) ? $_POST['userid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
            $where = to_sqls($uidarr, '', 'id');
            $this->db->update(array('islock'=>0), $where);
            showmessage(L('member_unlock').L('operation_success'), HTTP_REFERER);
        } else {
            showmessage(L('operation_failure'), HTTP_REFERER);
        }
    }

    /**
     * move member
     */
    function move() {
        if(isset($_POST['dosubmit'])) {
            $uidarr = isset($_POST['userid']) ? $_POST['userid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
            $groupid = isset($_POST['groupid']) && !empty($_POST['groupid']) ? $_POST['groupid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);

            $where = to_sqls($uidarr, '', 'userid');
            $this->db->update(array('groupid'=>$groupid), $where);
            showmessage(L('member_move').L('operation_success'), HTTP_REFERER, '', 'move');
        } else {
            $show_header = $show_scroll = true;
            $grouplist = getcache('grouplist');
            foreach($grouplist as $k=>$v) {
                $grouplist[$k] = $v['name'];
            }

            $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']): showmessage(L('illegal_parameters'), HTTP_REFERER);
            array_pop($ids);
            if(!empty($ids)) {
                $where = to_sqls($ids, '', 'userid');
                $userarr = $this->db->listinfo($where);
            } else {
                showmessage(L('illegal_parameters'), HTTP_REFERER, '', 'move');
            }

            include $this->admin_tpl('member_move');
        }
    }

    function memberinfo() {
        $show_header = false;

        $userid = !empty($_GET['userid']) ? intval($_GET['userid']) : '';
        $username = !empty($_GET['username']) ? trim($_GET['username']) : '';
        if(!empty($userid)) {
            $memberinfo = $this->db->get_one(array('userid'=>$userid));
        } elseif(!empty($username)) {
            $memberinfo = $this->db->get_one(array('username'=>$username));
        } else {
            showmessage(L('illegal_parameters'), HTTP_REFERER);
        }

        if(empty($memberinfo)) {
            showmessage(L('user').L('not_exists'), HTTP_REFERER);
        }

        $memberinfo['avatar'] = get_memberavatar($memberinfo['phpssouid'], '', 90);

        $grouplist = getcache('grouplist');
        //��Աģ�ͻ���
        $modellist = getcache('member_model', 'commons');

        $modelid = !empty($_GET['modelid']) ? intval($_GET['modelid']) : $memberinfo['modelid'];
        //վȺ����
        $sitelist =getcache('sitelist', 'commons');

        $this->db->set_model($modelid);
        $member_modelinfo = $this->db->get_one(array('userid'=>$userid));
        //ģ���ֶ�����
        $model_fieldinfo = getcache('model_field_'.$modelid, 'model');

        //ͼƬ�ֶ���ʾͼƬ
        foreach($model_fieldinfo as $k=>$v) {
            if($v['formtype'] == 'image') {
                $member_modelinfo[$k] = "<a href='.$member_modelinfo[$k].' target='_blank'><img src='.$member_modelinfo[$k].' height='40' widht='40' onerror=\"this.src='$phpsso_api_url/statics/images/member/nophoto.gif'\"></a>";
            } elseif($v['formtype'] == 'images') {
                $tmp = string2array($member_modelinfo[$k]);
                $member_modelinfo[$k] = '';
                if(is_array($tmp)) {
                    foreach ($tmp as $tv) {
                        $member_modelinfo[$k] .= " <a href='$tv[url]' target='_blank'><img src='$tv[url]' height='40' widht='40' onerror=\"this.src='$phpsso_api_url/statics/images/member/nophoto.gif'\"></a>";
                    }
                    unset($tmp);
                }
            } elseif($v['formtype'] == 'box') {	//box�ֶΣ���ȡ�ֶ����ƺ�ֵ������
                $tmp = explode("\n",$v['options']);
                if(is_array($tmp)) {
                    foreach($tmp as $boxv) {
                        $box_tmp_arr = explode('|', trim($boxv));
                        if(is_array($box_tmp_arr) && isset($box_tmp_arr[1]) && isset($box_tmp_arr[0])) {
                            $box_tmp[$box_tmp_arr[1]] = $box_tmp_arr[0];
                            $tmp_key = intval($member_modelinfo[$k]);
                        }
                    }
                }
                if(isset($box_tmp[$tmp_key])) {
                    $member_modelinfo[$k] = $box_tmp[$tmp_key];
                } else {
                    $member_modelinfo[$k] = $member_modelinfo_arr[$k];
                }
                unset($tmp, $tmp_key, $box_tmp, $box_tmp_arr);
            } elseif($v['formtype'] == 'linkage') {	//���Ϊ�����˵�
                $tmp = string2array($v['setting']);
                $tmpid = $tmp['linageid'];
                $linkagelist = getcache($tmpid, 'linkage');
                $fullname = $this->_get_linkage_fullname($member_modelinfo[$k], $linkagelist);

                $member_modelinfo[$v['name']] = substr($fullname, 0, -1);
                unset($tmp, $tmpid, $linkagelist, $fullname);
            } else {
                $member_modelinfo[$k] = $member_modelinfo[$k];
            }
        }

        $member_fieldinfo = array();
        //��������keyֵ
        foreach($model_fieldinfo as $v) {
            if(!empty($member_modelinfo) && array_key_exists($v['field'], $member_modelinfo)) {
                $tmp = $member_modelinfo[$v['field']];
                unset($member_modelinfo[$v['field']]);
                $member_fieldinfo[$v['name']] = $tmp;
                unset($tmp);
            } else {
                $member_fieldinfo[$v['name']] = '';
            }
        }

        include $this->admin_tpl('member_moreinfo');
    }

    /*
     * ͨ��linkageid��ȡ����·��
     */
    private function _get_linkage_fullname($linkageid,  $linkagelist) {
        $fullname = '';
        if($linkagelist['data'][$linkageid]['parentid'] != 0) {
            $fullname = $this->_get_linkage_fullname($linkagelist['data'][$linkageid]['parentid'], $linkagelist);
        }
        //���ڵ�������
        $return = $fullname.$linkagelist['data'][$linkageid]['name'].'>';
        return $return;
    }

    private function _checkuserinfo($data, $is_edit=0) {
        if(!is_array($data)){
            showmessage(L('need_more_param'));return false;
        } elseif (!is_username($data['username']) && !$is_edit){
            showmessage(L('username_format_incorrect'));return false;
        } elseif (!isset($data['userid']) && $is_edit) {
            showmessage(L('username_format_incorrect'));return false;
        }  elseif (empty($data['email']) || !is_email($data['email'])){
            showmessage(L('email_format_incorrect'));return false;
        }
        return $data;
    }

    private function _checkpasswd($password){
        if (!is_password($password)){
            return false;
        }
        return true;
    }

    private function _checkname($username) {
        $username =  trim($username);
        if ($this->db->get_one(array('username'=>$username))){
            return false;
        }
        return true;
    }

    /**
     * ����û���
     * @param string $username	�û���
     * @return $status {-4���û�����ֹע��;-1:�û����Ѿ����� ;1:�ɹ�}
     */
    public function public_checkname_ajax() {
        $username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit(0);
        if(CHARSET != 'utf-8') {
            $username = iconv('utf-8', CHARSET, $username);
            $username = addslashes($username);
        }

        $status = $this->client->ps_checkname($username);

        if($status == -4 || $status == -1) {
            exit('0');
        } else {
            exit('1');
        }

    }

    /**
     * �������
     * @param string $email
     * @return $status {-1:email�Ѿ����� ;-5:�����ֹע��;1:�ɹ�}
     */
    public function public_checkemail_ajax() {
        $email = isset($_GET['email']) && trim($_GET['email']) ? trim($_GET['email']) : exit(0);

        $status = $this->client->ps_checkemail($email);
        if($status == -5) {	//��ֹע��
            exit('0');
        } elseif($status == -1) {	//�û����Ѵ��ڣ������޸��û���ʱ����Ҫ�ж������Ƿ��ǵ�ǰ�û���
            if(isset($_GET['phpssouid'])) {	//�޸��û�����phpssouid
                $status = $this->client->ps_get_member_info($email, 3);
                if($status) {
                    $status = unserialize($status);	//�ӿڷ������л��������ж�
                    if (isset($status['uid']) && $status['uid'] == intval($_GET['phpssouid'])) {
                        exit('1');
                    } else {
                        exit('0');
                    }
                } else {
                    exit('0');
                }
            } else {
                exit('0');
            }
        } else {
            exit('1');
        }
    }

    /****������**/
    public function xiala(){
        $data =   $this->xlala->select();
        $data = sortDept($data);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add_xiala\', title:\''.L('member_add').'\', width:\'600\', height:\'400\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����������'));
        include $this->admin_tpl('xiala');
    }

    public function add_xiala(){

        if(isset($_POST['dosubmit'])){
            if(empty($_POST['info']['name'])){
                    showmessage(L('�����б����ݲ���Ϊ��'), HTTP_REFERER);
            }
            $info = $_POST['info'];

           if($this->xlala->insert($info)){
               showmessage(L('����������ݳɹ�'),'?m=people&c=people&a=xiala', '', 'add');
           }else{
               showmessage(L('���ʧ��'), HTTP_REFERER);
           };

        }else{
          $data =   $this->xlala->select(array('pid'=>'0'));
            include $this->admin_tpl('xiala_add');
        }
    }

    public function edit_xiala(){
        if(isset($_POST['dosubmit'])){
            if(empty($_POST['info']['name'])){
                showmessage(L('�����б����ݲ���Ϊ��'), HTTP_REFERER);
            }
            $name = $_POST['info']['name'];
            $id = $_POST['info']['id'];
            if($this->xlala->update(array('name'=>"$name"),array('id'=>"$id"))){
                showmessage(L('�޸��������ݳɹ�'),'?m=people&c=people&a=xiala', '', 'edit');
            }else{
                showmessage(L('�޸�ʧ��'), HTTP_REFERER);
            };
        }else{
            $id = $_GET['id'];
            $data =   $this->xlala->get_one(array('id'=>"$id"));
            include $this->admin_tpl('xiala_edit');
        }
    }

    public function delete_xiala(){
        $id = $_GET['id'];
        $pid = $_GET['pid'];
        if($pid != '0'){
            $this->xlala->delete(array('id'=>"$id"));
            $this->xlala->delete(array('pid'=>"$id"));
            showmessage(L('operation_success'), HTTP_REFERER);

        }else{
            $this->xlala->delete(array('id'=>"$id"));
            showmessage(L('operation_success'), HTTP_REFERER);

        }
    }

    /**
     * ������Ա��Ϣ����
     *
     * @param $modelid ģ��id
     */
    public function cache_people() {
        $this->uinfo = pc_base::load_model('uinfo_model');
        $people_array = array();

        $peoples = $this->uinfo->select('','*',100,'id ASC');

        foreach($peoples as $key=>$v) {
            $people_array["$key"] = $v;
        }
        setcache('people_array',$people_array,'people');
        return true;
    }

    //��ȡ�����б��name
    public function xiala_name($id){
        $xiala = $this->xlala->get_one(array('id'=>$id));
        return $xiala['name'];
    }

    //�����б�
    public function xia_list(){

        $data = $this->xlala->select();
        $data = node($data);
        $xiala = array();
        foreach($data  as $key=>$val){
            $xiala[$val['id']] = $val;
        }

        return $xiala;

    }


}
?>