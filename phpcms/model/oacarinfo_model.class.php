<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class oacarinfo_model extends model {
    public function __construct() {
        $this->db_config = pc_base::load_config('database');
        $this->db_setting = 'default';
        
        //修改表名
        $this->table_name = 'oacarinfo';
        parent::__construct();
    }
}
?>