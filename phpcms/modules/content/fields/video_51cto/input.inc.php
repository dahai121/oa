	function video_51cto($field, $value) {
		//ȡ����Ƶ�б�
		$pictures = $_POST[$field.'_url'];
		//ȡ����Ƶ˵��
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
