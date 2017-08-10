	function office($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		$linkageid = $setting['linkageid'];
		$this->_userid = param::get_cookie('_userid');
		if(defined('IN_ADMIN') && ROUTE_A=='add'){
			return menu_linkage($linkageid,$field,$value);
		}
			return '<input type="hidden" name="info['.$field.']" value="'.get_linkage(get_memberdetail($this->_userid,'dept'),$linkageid,'>>',4).'">'.get_linkage(get_memberdetail($this->_userid,'dept'),$linkageid,'>>',2);

	}
