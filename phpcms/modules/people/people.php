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

class people extends admin {

    private $db, $verify_db;

    function __construct() {
        parent::__construct();
        //��ȡģ��id ��name
        $modelid = $_REQUEST['modelid'];
        $data = getcache('people_model', 'commons');
        foreach ($data as $k=>$v){
            if($v['modelid'] == $modelid ){
                $this->p_model = $v;
            }
        }
        //��ȡģ�ͱ���
        $table_name = $this->p_model['tablename'];
        if(empty($table_name)){
            $this->db = pc_base::load_model('people_model');
        }else{
            $this->db = pc_base::load_model($table_name.'_model');
        }



        //$this->db2 = pc_base::load_model('people1_model');
        $this->xlala = pc_base::load_model('xiala_model');
    }

    /**
     * defalut
     */
    function init() {
        $show_header = $show_scroll = true;
        pc_base::load_sys_class('form', '', 0);
        $this->verify_db = pc_base::load_model('member_verify_model');

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $groupid = isset($_GET['groupid']) ? $_GET['groupid'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $grouplist = getcache('grouplist');
        foreach($grouplist as $k=>$v) {
            $grouplist[$k] = $v['name'];
        }

        $memberinfo['totalnum'] = $this->db->count();

        $memberinfo['vipnum'] = $this->db->count(array('vip'=>1));
        $memberinfo['verifynum'] = $this->verify_db->count(array('status'=>0));

        $todaytime = strtotime(date('Y-m-d', SYS_TIME));
        $memberinfo['today_member'] = $this->db->count("`regdate` > '$todaytime'");
        include $this->admin_tpl('member_init');
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

            $where_start_time = date("Y-m-d",$where_start_time);
            $where_end_time = date("Y-m-d",$where_end_time);


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
            $where .= "`addtime` BETWEEN '$where_start_time' AND '$where_end_time' AND ";

            if($rylx){
                if($rylx != 80){
                    $where .= "`rylx` = '$rylx' AND ";
                }else{
                    $where .= "`rylx` = '78' OR `rylx` = '79'AND ";
                }
            }

            if($keyword) {
                    $where .= "`title` like '%$keyword%'";
            } else {
                $where .= '1';
            }

        } else {
            $where = '';
        }


        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
//        var_dump( $memberlist);die;
        $pages = $this->db->pages;
        //��̨ģ��
        $template = $this->p_model['admin_list_template'] ? $this->p_model['admin_list_template'] : 'people_list';
        include $this->admin_tpl($template);
    }

    /**
     * ������Ա
     */
    function tx_manage() {
        $modelid = $_GET['modelid'];
        $sitelistarr = getcache('sitelist', 'commons');
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }
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

        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        $pages = $this->db->pages;

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $grouplist = getcache('grouplist');
        foreach($grouplist as $k=>$v) {
            $grouplist[$k] = $v['name'];
        }

        //����Ա��Ϣ��ӵ�����
        $this->cache_people();
        //��̨ģ��
        $template = $this->p_model['admin_list_template'] ? $this->p_model['admin_list_template'] : 'people_list';
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add_tx&modelid='.$modelid.'\', title:\''.L('�����Ա').'\', width:\'1200\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����Ա'));
        include $this->admin_tpl($template);
    }

    /**
     * ��������
     */
    function dangan_manage() {
        $modelid = $_GET['modelid'];
        $sitelistarr = getcache('sitelist', 'commons');
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }
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
        //��ȡ����
        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        //��������
        foreach ( $memberlist_arr as $key =>$val){
            $this->pinfo =  pc_base::load_model('uinfo_model');
            $this->xiala =  pc_base::load_model('xiala_model');
            $where = array('id'=>"$val[name]");
            //��Ա��Ϣ
            $uinfo = $this->pinfo->get_one($where);
            //����
            $dy = $this->xiala->get_one(array('id'=>$val['dy']));
            //ְ��
            $zj = $this->xiala->get_one(array('id'=>$uinfo['zj']));
            $memberlist_arr[$key]['name'] = $uinfo['title'];
            $memberlist_arr[$key]['zj'] = $zj['name'];
            $memberlist_arr[$key]['dy'] = $dy['name'];

        }
        $this->xiala =  pc_base::load_model('xiala_model');

//        echo "<pre>";
//        var_dump($memberlist_arr);die;

        $pages = $this->db->pages;

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $grouplist = getcache('grouplist');
        foreach($grouplist as $k=>$v) {
            $grouplist[$k] = $v['name'];
        }
        //��̨ģ��
        $template = $this->p_model['admin_list_template'] ? $this->p_model['admin_list_template'] : 'people_list';
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add_tx&modelid='.$modelid.'\', title:\''.L('�����Ա').'\', width:\'1200\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����Ա'));
        include $this->admin_tpl($template);
    }

    /**
     * ��Ҫ��Ϣ����
     */
    function zy_manage() {
        $modelid = $_GET['modelid'];
        $sitelistarr = getcache('sitelist', 'commons');
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }
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
        //��ȡ����
        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        //��������
        foreach ( $memberlist_arr as $key =>$val){
            $this->pinfo =  pc_base::load_model('uinfo_model');
            $this->xiala =  pc_base::load_model('xiala_model');
            $where = array('id'=>"$val[name]");
            //��Ա��Ϣ
            $uinfo = $this->pinfo->get_one($where);
            //����
            $dy = $this->xiala->get_one(array('id'=>$val['dy']));
            //ְ��
            $zj = $this->xiala->get_one(array('id'=>$uinfo['zj']));
            $memberlist_arr[$key]['name'] = $uinfo['title'];
            $memberlist_arr[$key]['zj'] = $zj['name'];
            $memberlist_arr[$key]['dy'] = $dy['name'];

        }
        $this->xiala =  pc_base::load_model('xiala_model');

//        echo "<pre>";
//        var_dump($memberlist_arr);die;

        $pages = $this->db->pages;

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $grouplist = getcache('grouplist');
        foreach($grouplist as $k=>$v) {
            $grouplist[$k] = $v['name'];
        }
        //��̨ģ��
        $template = $this->p_model['admin_list_template'] ? $this->p_model['admin_list_template'] : 'people_list';
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add_tx&modelid='.$modelid.'\', title:\''.L('�����Ա').'\', width:\'1200\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����Ա'));
        include $this->admin_tpl($template);
    }




    /**
     * member list
     */
    function manage() {
        $sitelistarr = getcache('sitelist', 'commons');
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }

        $groupid = isset($_GET['groupid']) ? intval($_GET['groupid']) : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        //����ǳ�������Ա��ɫ����ʾ�����û���������ʾ��ǰվ���û�
        if($_SESSION['roleid'] == 1) {
            $where = '';
        } else {
            $siteid = get_siteid();
            $where .= "`siteid` = '$siteid'";
        }

        $memberlist_arr = $this->db->listinfo($where, 'id DESC', $page, 15);
        $pages = $this->db->pages;

        //������
        if(isset($_GET['name']) && !empty($_GET['name'])) {
            $name = $_GET['posids']==1 ? intval($_GET['posids']) : 0;
            $where .= " AND `name` = '$name'";
        }
        $modelid = $_GET['modeid'];

        //��Ա����ģ��
//        $modellistarr = getcache('member_model', 'commons');
//        foreach ($modellistarr as $k=>$v) {
//            $modellist[$k] = $v['name'];
//        }

//        //��ѯ��Աͷ��
//        foreach($memberlist_arr as $k=>$v) {
//            $memberlist[$k] = $v;
//            $memberlist[$k]['avatar'] = get_memberavatar($v['phpssouid']);
//        }
        //��̨ģ��
        $template = $this->p_model['admin_list_template'] ? $this->p_model['admin_list_template'] : 'people_list';

        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add_tx\', title:\''.L('member_add').'\', width:\'1200\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_add'));
        include $this->admin_tpl($template);
    }



    function manage1() {


        $sitelistarr = getcache('sitelist', 'commons');
        foreach ($sitelistarr as $k=>$v) {
            $sitelist[$k] = $v['name'];
        }

        $groupid = isset($_GET['groupid']) ? intval($_GET['groupid']) : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        //����ǳ�������Ա��ɫ����ʾ�����û���������ʾ��ǰվ���û�
        if($_SESSION['roleid'] == 1) {
            $where = '';
        } else {
            $siteid = get_siteid();
            $where .= "`siteid` = '$siteid'";
        }

        $memberlist_arr = $this->db2->listinfo($where, 'id DESC', $page, 15);
        $pages = $this->db2->pages;

        //������
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        $grouplist = getcache('grouplist');
        foreach($grouplist as $k=>$v) {
            $grouplist[$k] = $v['name'];
        }

        //��Ա����ģ��
//        $modellistarr = getcache('member_model', 'commons');
//        foreach ($modellistarr as $k=>$v) {
//            $modellist[$k] = $v['name'];
//        }

//        //��ѯ��Աͷ��
//        foreach($memberlist_arr as $k=>$v) {
//            $memberlist[$k] = $v;
//            $memberlist[$k]['avatar'] = get_memberavatar($v['phpssouid']);
//        }

        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=people&c=people&a=add\', title:\''.L('�����Ա').'\', width:\'1200\', height:\'600\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('�����Ա'));
        include $this->admin_tpl('people_list');
    }





    /**
     * add member
     */
    function add() {
        header("Cache-control: private");
        if(isset($_POST['dosubmit'])) {
            $info = $_POST['info'];
            $info['inputtime'] = $info['edittime'] = SYS_TIME;

            $this->db->insert($info);
                if($this->db->insert_id()){
                    showmessage(L('operation_success'),'?m=people&c=people&a=add', '', 'add');
                }
        } else {
            $modelid = $_GET['modeid'];
            $show_header = $show_scroll = true;
            $siteid = get_siteid();
            //��Աģ�ͻ���
            $member_model_cache = getcache("model_field_$modelid", 'model');
            //ȡģ��ID����ģ��ID�����ɶ�Ӧ�ı�
            require CACHE_MODEL_PATH.'content_form.class.php';
            $content_form = new content_form("$modelid",'',$this->categorys);
            $forminfos = $content_form->get();

            $formValidator = $content_form->formValidator;

            $workflows = getcache('workflow_'.$this->siteid,'commons');
            $workflows_setting = string2array($workflows['setting']);
            $nocheck_users = $workflows_setting['nocheck_users'];
            $admin_username = param::get_cookie('admin_username');
            if(!empty($nocheck_users) && in_array($admin_username, $nocheck_users)) {
                $priv_status = true;
            } else {
                $priv_status = false;
            }
            include $this->admin_tpl('content_add');
            //include $this->admin_tpl('member_add');
        }

    }


    /**
     * add ������Ա
     */
    function add_tx() {
        header("Cache-control: private");
        if(isset($_POST['dosubmit'])) {
            $info = $_POST['info'];
            $info['addtime'] = $info['edittime'] = date('Y-m-d', SYS_TIME);
            $this->db->insert($info);
            if($this->db->insert_id()){
                showmessage(L('operation_success'),'?m=people&c=people&a=add', '', 'add');
            }
        } else {
            $modelid = $_GET['modelid'];
            $show_header = $show_scroll = true;
            $siteid = get_siteid();
            //��Աģ�ͻ���
            $member_model_cache = getcache("model_field_$modelid", 'model');
            //ȡģ��ID����ģ��ID�����ɶ�Ӧ�ı�
            require CACHE_MODEL_PATH.'content_form.class.php';
            $content_form = new content_form("$modelid",'',$this->categorys);
            $forminfos = $content_form->get();
            $data = getcache('model_field_'.$modelid,'model');
//            echo "<pre>";
//            var_dump($forminfos['base']['rylx']['formtype']);die;

            $formValidator = $content_form->formValidator;

            $workflows = getcache('workflow_'.$this->siteid,'commons');
            $workflows_setting = string2array($workflows['setting']);
            $nocheck_users = $workflows_setting['nocheck_users'];
            $admin_username = param::get_cookie('admin_username');
            if(!empty($nocheck_users) && in_array($admin_username, $nocheck_users)) {
                $priv_status = true;
            } else {
                $priv_status = false;
            }
            include $this->admin_tpl('content_add');
        }

    }

    /**
     * add member
     */
    function add2() {
        header("Cache-control: private");
        if(isset($_POST['dosubmit'])) {
            $info = $_POST['info'];
            $info['addtime'] = $info['edittime'] = SYS_TIME;
            $this->db2->insert($info);
            if($this->db2->insert_id()){
                showmessage(L('operation_success'),'?m=people&c=people&a=add', '', 'add');
            }
        } else {
            $show_header = $show_scroll = true;
            $siteid = get_siteid();
            //��Աģ�ͻ���
            $member_model_cache = getcache('model_field_95', 'model');
            //ȡģ��ID����ģ��ID�����ɶ�Ӧ�ı�
            require CACHE_MODEL_PATH.'content_form.class.php';
            $content_form = new content_form('95','',$this->categorys);
            $forminfos = $content_form->get();


            $formValidator = $content_form->formValidator;
            $workflows = getcache('workflow_'.$this->siteid,'commons');
            $workflows_setting = string2array($workflows['setting']);
            $nocheck_users = $workflows_setting['nocheck_users'];
            $admin_username = param::get_cookie('admin_username');
            if(!empty($nocheck_users) && in_array($admin_username, $nocheck_users)) {
                $priv_status = true;
            } else {
                $priv_status = false;
            }
            include $this->admin_tpl('content_add');
            //include $this->admin_tpl('member_add');
        }

    }


    public function edit() {
        //����cookie �ڸ�����Ӵ�����
        param::set_cookie('module', 'content');
        $modelid= $_GET['modelid'];
        if(isset($_POST['dosubmit']) || isset($_POST['dosubmit_continue'])) {
            define('INDEX_HTML',true);
            $id = $_POST['info']['id'] = intval($_POST['id']);
            if(trim($_POST['info']['title'])=='') showmessage(L('title_is_empty'));
            $data = $_POST['info'];
            $this->db->update($data, array('id'=>$id));
            if(isset($_POST['dosubmit'])) {
                showmessage(L('update_success'), '?m=people&c=people&a=tx_manage', '', 'add');
            } else {
                showmessage(L('update_success'),HTTP_REFERER);
            }
        } else {
            $show_header = $show_dialog = $show_validator = '';
            //�����ݿ��ȡ����
            $id = intval($_GET['id']);
            //if(!isset($_GET['catid']) || !$_GET['catid']) showmessage(L('missing_part_parameters'));
            $catid = $_GET['catid'] = intval($_GET['catid']);

            $this->model = getcache('people_model', 'commons');

            param::set_cookie('catid', $catid);
            //$category = $this->categorys[$catid];
            //$modelid = $category['modelid'];
           // $this->db->table_name = $this->db->db_tablepre.$this->model[$modelid]['tablename'];

            $data = $this->db->get_one(array('id'=>$id));
//            echo "<pre>";
//            var_dump($r);die;
//            $this->db->table_name = $this->db->table_name.'_data';
//            $r2 = $this->db->get_one(array('id'=>$id));
//            if(!$r2) showmessage(L('subsidiary_table_datalost'),'blank');
//            $data = array_merge($r,$r2);
            $data = array_map('htmlspecialchars_decode',$data);
            require CACHE_MODEL_PATH.'content_form.class.php';
            $content_form = new content_form($modelid,$catid,$this->categorys);

            $forminfos = $content_form->get($data);
            echo "<pre>";
            var_dump( $forminfos);die;
            $formValidator = $content_form->formValidator;
            include $this->admin_tpl('content_edit');
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



}
?>