<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

	function conectar(){
	
		try {

			$first = "mysql:host=localhost;dbname=";
			$dbname = "parkcar";
			$user = "root";
			$password = "root";

			// $first = "mysql:host=mysql.parkcar.app.br;dbname=";
			// $dbname = "parkcar";
			// $user = "parkcar";
			// $password = "ParkCar2021";

			$opcoes = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			// $pdo = new PDO("mysql:host=localhost;dbname=smarttraffic", "root", "root", $opcoes);
			$pdo = new PDO($first.$dbname, $user, $password, $opcoes);	
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
		return $pdo;
	
	}
	
	conectar();

?>