<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Migration extends CI_Migration {

	protected $CI;

	public function __construct($config=array())
	{
		parent::__construct($config);
		$this->CI =& get_instance();
		$this->_config_rules = $config;
	}

	public function get_version()
	{
		return $this->CI->migration->_get_version();
	}

	public function get_migration_number($migration)
	{
		return $this->CI->migration->_get_migration_number($migration);
	}

}

/* End of file MY_Migration.php */
/* Location: ./application/libraries/MY_Migration.php */
