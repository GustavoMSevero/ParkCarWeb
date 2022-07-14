<?php
header("Access-Control-Allow-Origin: *");
// header('Content-Type: application/json');
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);


if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'register customer':
        
        $costumer=$data->costumer;
        $licensePlate=$data->licensePlate;
        $type=$data->type;
        $idParking=$data->idParking;

        $insertParkingCustomer=$pdo->prepare("INSERT INTO parkingCustomer (idParkingCustomer, idParking, customer, licensePlate, type)
                                    VALUES(?,?,?,?,?)");
        $insertParkingCustomer->bindValue(1, NULL);
        $insertParkingCustomer->bindValue(2, $idParking);
        $insertParkingCustomer->bindValue(3, $costumer);
        $insertParkingCustomer->bindValue(4, $licensePlate);
        $insertParkingCustomer->bindValue(5, $type);
        $insertParkingCustomer->execute();

        break;

    case 'get parking customers':
    
        $idParking = $_GET["idParking"];

        $getParkingCustomers=$pdo->prepare("SELECT * FROM parkingCustomer WHERE idParking=:idParking");
        $getParkingCustomers->bindValue(":idParking", $idParking);
        $getParkingCustomers->execute();

        while ($line=$getParkingCustomers->fetch(PDO::FETCH_ASSOC)) {
            $idParkingCustomer = $line['idParkingCustomer'];
            $customer = $line['customer'];
            $type = $line['type'];
            $licensePlate = $line['licensePlate'];

            $return[] = array(
                'idParkingCustomer' => $idParkingCustomer,
                'idParking' => $idParking,
                'customer' => $customer,
                'licensePlate' => $licensePlate,
                'type' => $type,
            );
        }

        echo json_encode($return);

        break;
    
    default:
        # code...
        break;
}


?>