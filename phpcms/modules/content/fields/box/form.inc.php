	function box($field, $value, $fieldinfo) {

		$setting = string2array($fieldinfo['setting']);
		$_id=$setting['boxtype'];
	$info = $this->xiala->select(array('pid'=>"$_id"));
	$string="<select  name=\"info[$field]\">";
	foreach($info as $k=>$v){
		$string.="<option    value=\"$v[id]\">$v[name]</option>";
	}
	$string.="</select>";


	/*
		if($value=='') $value = $this->fields[$field]['defaultvalue'];
		$options = explode("\n",$this->fields[$field]['options']);
		foreach($options as $_k) {
			$v = explode("|",$_k);
			$k = trim($v[1]);
			$option[$k] = $v[0];
		}
		$values = explode(',',$value);
		$value = array();
		foreach($values as $_k) {
			if($_k != '') $value[] = $_k;
		}
		$value = implode(',',$value);
		switch($this->fields[$field]['boxtype']) {
			case 'radio':
				$string = form::radio($option,$value,"name='info[$field]' $fieldinfo[formattribute]",$setting['width'],$field);
			break;

			case 'checkbox':
				$string = form::checkbox($option,$value,"name='info[$field][]' $fieldinfo[formattribute]",1,$setting['width'],$field);
			break;

			case 'select':
				$string = form::select($option,$value,"name='info[$field]' id='$field' $fieldinfo[formattribute]");
			break;

			case 'multiple':
				$string = form::select($option,$value,"name='info[$field][]' id='$field' size=2 multiple='multiple' style='height:60px;' $fieldinfo[formattribute]");
			break;
		}
	*/
		return $string;
	}
