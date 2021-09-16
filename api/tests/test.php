<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");
include_once("stayCount-GaragemCarlosGomes.php");

$pdo = conectar();

try {
    $idClient = 29;
    $licensePlate = 'IXW3620';
    $idParking = 15;
    
    stayCount($idClient, $licensePlate, $idParking);
    //code...
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";//throw $th;
}


