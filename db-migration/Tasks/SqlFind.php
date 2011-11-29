<?php

class Tasks_SqlFind extends Task {

	protected $directories;

	protected $mysql;

	protected $hostname;

	protected $username;

	protected $password;

	public function main() {

		if (null === $this->getDirectories()) {
				throw new BuildException('"directories" is required parameter');
		}

		if (null === $this->getHostname()) {
				throw new BuildException('"hostname" is required parameter');
		}

		if (null === $this->getUsername()) {
				throw new BuildException('"username" is required parameter');
		}

		if (null === $this->getPassword()) {
				throw new BuildException('"password" is required parameter');
		}

		$conn = mysqli_connect($this->hostname, $this->username, $this->password);



		$files = glob($this->directories);

		foreach ($files as $file) {

			$sqlContent = file_get_contents($file);

			preg_match("~`([-_a-z0-9]+)`.`([-_a-z0-9]+)`~isU", $sqlContent, $sqlDetails);

			if (isset($sqlDetails[0])) {
				throw new BuildException('"'.$file.'" is not correctly formated SQL');
			}

			$sqlDatabase = $sqlDetails[1];
			$sqlTable = $sqlDetails[2];

			mysqli_select_db($conn, $sqlDatabase);
			$resultRsc = mysqli_query("SELECT UPDATE_TIME FROM tables WHERE TABLE_SCHEMA = '".$sqlDatabase."' AND TABLE_NAME = '".$sqlTable."'");
			$result = mysqli_fetch_assoc($resultRsc);

			if (strtotime($result['UPDATE_TIME']) < filemtime($file)) {
				mysqli_query($sqlContent);
			}

		}
		
	}

	public function setDirectories($directories) {
		$this->directories = $directories;
	}

	public function getDirectories() {
		return $this->directories;
	}

	public function setMysql($mysql) {
		$this->mysql = $mysql;
	}

	public function getMysql() {
		$this->mysql;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function getHostname() {
		return $this->hostname;
	}

	public function setHostname($hostname) {
		$this->hostname = $hostname;
	}

}