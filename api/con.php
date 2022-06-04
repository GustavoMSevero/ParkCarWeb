<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

	function conectar(){
	
		try {

			$first = "mysql:host=localhost;dbname=";
			$dbname = "parkcar";
			$user = "root";
			$password = "root";

			$opcoes = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			$pdo = new PDO($first.$dbname, $user, $password, $opcoes);	
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
		return $pdo;
	
	}
	
	conectar();

?>