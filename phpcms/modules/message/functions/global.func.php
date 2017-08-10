<?php

	/**
	 * yp_show_linkage ��ȡ�������˵�
	 * @param $linkageid �����˵�ID
	 * @param $fieldid �ֶ�����
	 * @param $lid ��ǰ�˵�ID
	 * @param $modelid ��ǰģ��ID
	 */
	function bizorder_show_linkage($linkageid = 0, $fieldid = '', $lid = 0, $modelid = 0) {
		$linkage_db = pc_base::load_model('linkage_model');
		$r = $linkage_db->select(array('keyid'=>$linkageid, 'parentid'=>$lid), 'linkageid, name');
		$data = array();
		if (is_array($r) && !empty($r)) {
			foreach ($r as $d) {
				$data[$d['linkageid']]['title'] = $d['name'];
				$data[$d['linkageid']]['linkageid'] = $d['linkageid'];
				//$data[$d['linkageid']]['url'] = yp_filters_url($fieldid, array($fieldid=>$d['linkageid']), 2, $modelid);
			}
		}
		return $data;
	}


	function bizorder_show_area($linkageid = 0) {
		$linkage_db = pc_base::load_model('linkage_model');
		$r = $linkage_db->select(array('linkageid'=>$linkageid), 'linkageid, name');
		$data = array();
		if (is_array($r) && !empty($r)) {
			foreach ($r as $d) {
				$data['title'] = $d['name'];
				$data['linkageid'] = $d['linkageid'];
				//$data[$d['linkageid']]['url'] = yp_filters_url($fieldid, array($fieldid=>$d['linkageid']), 2, $modelid);
			}
			
		}
		return $data;
	}



?>
