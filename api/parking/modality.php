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
    case 'register modality':
        
        $idParking=$data->idParking;
        $price=$data->price;
        $type=$data->type;

        $insertModality=$pdo->prepare("INSERT INTO parkingModality (idParkingModality, idParking, type, price)
                                    VALUES(?,?,?,?)");
        $insertModality->bindValue(1, NULL);
        $insertModality->bindValue(2, $idParking);
        $insertModality->bindValue(3, $type);
        $insertModality->bindValue(4, $price);
        $insertModality->execute();

        break;

    case 'get modalities':
    
        $idParking = $_GET["idParking"];

        $getModalities=$pdo->prepare("SELECT * FROM parkingModality WHERE idParking=:idParking");
        $getModalities->bindValue(":idParking", $idParking);
        $getModalities->execute();

        while ($line=$getModalities->fetch(PDO::FETCH_ASSOC)) {
            $idParkingModality = $line['idParkingModality'];
            $idParking = $line['idParking'];
            $type = $line['type'];
            $price = $line['price'];

            $return[] = array(
                'idParkingModality' => $idParkingModality,
                'idParking' => $idParking,
                'type' => $type,
                'price' => $price,
            );
        }

        echo json_encode($return);

        break;
    
    default:
        # code...
        break;
}


?>