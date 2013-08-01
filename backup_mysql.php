<?php
	/**
	 * PHP Backup script: reads a manifest.json file and back's up the databases in the file
	 **/


	include 'database_util.php';



	

	$manifest = json_decode(file_get_contents("manifest.json"));

	foreach ($manifest as $connections) {
		$host = $connections->host == null ? "localhost" : $connections->host;
		$port = $connections->port == null ? "3306" : $connections->port;
		$username = $connections->username == null ? "root" : $connections->username;
		$password = $connections->password == null ? "" : $connections->password;

		$db = new mysqli($host,$username,$password,"",$port);
		if ($db->connect_errno){
			dolog("Error connecting to $host as $username");
		} else {
			dolog("Connected to $host as $username");
		}

		foreach ($connections->databases as $database => $settings) {

			$tables = $settings->tables == null ? "all" : $settings->tables;
			$output = $settings->output == null ? "sql/$database.sql.gz" : $settings->output;

			$f = gzopen($output, "w") or die("Couldn't open $output\n");

			getCreateDatabase($f,$db,$database);
			// if not tables specified, process all tables.
			if($tables != "all"){
				dolog("Processing ".sizeof($tables)." tables on $database");
				$tables = $settings->tables;
			} else {
				dolog("Processing all tables on database $database");
				$res = $db->query("SHOW TABLES in `$database`");
				$tables = array();
				while ($row = $res->fetch_assoc()) {
					$tables[$row["Tables_in_$database"]] = [];
				}
			}


			foreach ($tables as $table => $settings) {
				if ($settings->where){
					$where = $settings->where;
				}
				dolog("\tProcessing $database.$table");
				getCreateTable($f,$db,"`$database`.`$table`");

				getInserts($f,$db,"`$database`.`$table`");
			}

			gzclose($f);
		}

	}
?>
