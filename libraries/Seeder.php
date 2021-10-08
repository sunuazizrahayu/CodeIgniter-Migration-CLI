<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Seeder
{
    /**
     * @var CI_Controller
     */
	private $CI;

    /**
     * @var CI_DB_query_builder
     */
	protected $db;

    /**
     * @var CI_DB_forge
     */
	protected $dbforge;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->dbforge();
		$this->db = $this->CI->db;
		$this->dbforge = $this->CI->dbforge;

		$this->CI->load->config('seeder', TRUE);
	}

	/**
	 * Run another seeder
	 * 
	 * @param string $seeder Seeder classname
	 */
	public function call($seeder)
	{
		$path = rtrim($this->CI->config->item('seeder_path', 'seeder'), '/');
		$file = $path .'/' . $seeder . '.php';
		require_once $file;

		//remove sequential
        $seeder = preg_replace('/^[- 0-9]*/', '', $seeder); //remove number
        $seeder = ltrim($seeder, '_');
            
		$obj = new $seeder;
		$obj->run();
	}

	public function __get($property)
	{
		return $this->CI->$property;
	}
}
