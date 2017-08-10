	function textarea($field, $value) {
		if(!$this->fields[$field]['enablehtml']) $value = strip_tags($value);
		return $value;
	}
	function editor($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$enablesaveimage = $setting['enablesaveimage'];
		if(isset($_POST['spider_img'])) $enablesaveimage = 0;
		if($enablesaveimage) {
			$site_setting = string2array($this->site_config['setting']);
			$watermark_enable = intval($site_setting['watermark_enable']);
			$value = $this->attachment->download('content', $value,$watermark_enable);
		}
		return $value;
	}
	function box($field, $value) {
		if($this->fields[$field]['boxtype'] == 'checkbox') {
			if(!is_array($value) || empty($value)) return false;
			array_shift($value);
			$value = ','.implode(',', $value).',';
			return $value;
		} elseif($this->fields[$field]['boxtype'] == 'multiple') {
			if(is_array($value) && count($value)>0) {
				$value = ','.implode(',', $value).',';
				return $value;
			}
		} else {
			return $value;
		}
	}
	function image($field, $value) {
		$value = remove_xss(str_replace(array("'",'"','(',')'),'',$value));
		$value  = safe_replace($value);
		return trim($value);
	}
	function images($field, $value) {
		//取得图片列表
		$pictures = $_POST[$field.'_url'];
		//取得图片说明
		$pictures_alt = isset($_POST[$field.'_alt']) ? $_POST[$field.'_alt'] : array();
		$array = $temp = array();
		if(!empty($pictures)) {
			foreach($pictures as $key=>$pic) {
				$temp['url'] = $pic;
				$temp['alt'] = str_replace(array('"',"'"),'`',$pictures_alt[$key]);
				$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
	function datetime($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		if($setting['fieldtype']=='int') {
			$value = strtotime($value);
		}
		return $value;
	}
	function posid($field, $value) {
		$number = count($value);
		$value = $number==1 ? 0 : 1;
		return $value;
	}
	function copyfrom($field, $value) {
		$field_data = $field.'_data';
		if(isset($_POST[$field_data])) {
			$value .= '|'.safe_replace($_POST[$field_data]);
		}
		return $value;
	}
	function groupid($field, $value) {
		$datas = '';
		if(!empty($_POST[$field]) && is_array($_POST[$field])) {
			$datas = implode(',',$_POST[$field]);
		}
		return $datas;
	}
	function downfile($field, $value) {
		//取得镜像站点列表
		$result = '';
		$server_list = count($_POST[$field.'_servers']) > 0 ? implode(',' ,$_POST[$field.'_servers']) : '';
		$result = $value.'|'.$server_list;
		return $result;
	}
	function downfiles($field, $value) {
		$files = $_POST[$field.'_fileurl'];
		$files_alt = $_POST[$field.'_filename'];
		$array = $temp = array();
		if(!empty($files)) {
			foreach($files as $key=>$file) {
					$temp['fileurl'] = $file;
					$temp['filename'] = $files_alt[$key];
					$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
	
	function video($field, $value) {
		$post_f = $field.'_video';
		if (isset($_POST[$post_f]) && !empty($_POST[$post_f])) {
			$value = 1;
			$video_store_db = pc_base::load_model('video_store_model');
			$setting = getcache('video', 'video');
			pc_base::load_app_class('ku6api', 'video', 0);
			$ku6api = new ku6api($setting['sn'], $setting['skey']);
			pc_base::load_app_class('v', 'video', 0);
			$v_class =  new v($video_store_db);
			$GLOBALS[$field] = '';
			foreach ($_POST[$post_f] as $_k => $v) {
				if (!$v['vid'] && !$v['videoid']) unset($_POST[$post_f][$_k]);
				$info = array();
				if (!$v['title']) $v['title'] = safe_replace($this->data['title']);
				if ($v['vid']) { 
					$info = array('vid'=>$v['vid'], 'title'=>$v['title'], 'cid'=>intval($this->data['catid']));
					$info['channelid'] = intval($_POST['channelid']);
					if ($this->data['keywords']) $info['tag'] = addslashes($this->data['keywords']);
					if ($this->data['description']) $info['description'] = addslashes($this->data['description']);
					$get_data = $ku6api->vms_add($info);
					if (!$get_data) {
						continue;
					}
					$info['vid'] = $get_data['vid'];
					$info['addtime'] = SYS_TIME;
					$info['keywords'] = $info['tag'];
					unset($info['cid'], $info['tag']);
					$info['userupload'] = 1;
					$videoid = $v_class->add($info);
					$GLOBALS[$field][] = array('videoid' => $videoid, 'listorder' => $v['listorder']);
				} else {
					$v_class->edit(array('title'=>$v['title']), $v['videoid']);
					$GLOBALS[$field][] = array('videoid' => $v['videoid'], 'listorder' => $v['listorder']);
				}
			}
		} else {
			$value = 0;
		}
		return $value;
	}
	function video_51cto($field, $value) {
		//取得视频列表
		$pictures = $_POST[$field.'_url'];
		//取得视频说明
		$pictures_alt = isset($_POST[$field.'_alt']) ? $_POST[$field.'_alt'] : array();
		$array = $temp = array();
		if(!empty($pictures)) {
			foreach($pictures as $key=>$pic) {
				$temp['url'] = $pic;
				$temp['alt'] = $pictures_alt[$key];
				$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}

 } 
?>