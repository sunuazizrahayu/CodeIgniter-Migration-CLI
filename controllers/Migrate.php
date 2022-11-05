<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

	public $migration_name_length = 4;
	public $migration_path;

	public function __construct()
	{
		parent::__construct();
		if (!$this->input->is_cli_request()) {
			show_error("You don't have permission for this action.");
			return;
		}
		$this->load->library('migration');
		$this->load->config('migration');
		$this->migration_path = $this->config->item('migration_path');
	}

	public function version($version=null)
	{
		if ($version === null) {
			$current_version = $this->migration->get_version();
			echo "Current version is " . $current_version .'.' . PHP_EOL;
			die;
		}


		//migrate to latest version
		if ($version === 'latest') {
			$this->latest();
			die;
		}

		//check version is valid
		$version = $this->migration->get_migration_number($version);
		if (empty($version) && $version !== '0') {
			echo "Version invalid or not found." .PHP_EOL;
			die;
		}

		//migrate to specific version
		print("Migrating to: ".$version . "\n");
		$migration = $this->migration->version($version);
		


		//migration status
		if ($migration) {
			echo "Migration done to version: " . $version . PHP_EOL;
		} else {
			echo $this->migration->error_string()."\n";
		}
	}

	public function latest()
	{
		print("Migrating to: latest\n");
		$migration = $this->migration->latest();
		if ($migration) {
			$version = $this->migration->find_migrations();
			$version = array_key_last($version);
			echo "Migration done to version: ".$version . PHP_EOL;
		} else {
			echo $this->migration->error_string()."\n";
		}
	}

	public function generate($name=false)
	{
		if (!$name) {
			echo "Please define migration name" . PHP_EOL;
			return;
		}

		if (!preg_match('/^[a-z_]+$/i', $name)) {
			if (strlen($name) < $this->migration_name_length) {
				echo "Migration must be at least " . $this->migration_name_length . " character long" . PHP_EOL;
			}
			echo "Wrong migration name, allowed characters: a-z and _ \nExample: first_migration" . PHP_EOL;
		}


		//create migration file name
		$directory = $this->migration_path;
		switch ($this->config->item('migration_type')) {
			case 'timestamp':
				$file = date('YmdHis') . '_'.$name;
				break;
			case 'sequential':
				$count = (glob($directory . "*.php") != false) ? count(glob($directory . "*.php")) + 1 : 1;
				$file = str_pad($count, 3, '0', STR_PAD_LEFT)."_$name";
				break;
			default:
				$file = '';
				break;
		}



		$this->load->helper('file');

		$data = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_".ucwords($name)." extends CI_Migration {

	public function __construct()
	{
		\$this->load->dbforge();
		\$this->load->database();
	}

	public function up()
	{
		
	}

	public function down()
	{
		
	}
	
}

/* End of file $file.php */
/* Location: ./application/migrations/001_first_migration.php */";



				
		if (!write_file($directory."$file.php", $data))
		{
			echo "Unable to write the migration file\n";
			echo PHP_EOL;
		}
		else
		{
			echo "Migration file written!\n";
		}
	}

	public function seed()
	{
		$this->load->library('seeder');
		$path = rtrim($this->config->item('seeder_path', 'seeder'), '/');
		foreach (glob($path . '/*Seeder.php') as $file) {
			$seeder = basename($file, '.php');
			$this->seeder->call($seeder);
			
			echo "Seeder done: ". $seeder . PHP_EOL;
		}
	}

}

/* End of file Migrate.php */
/* Location: ./application/controllers/Migrate.php */
