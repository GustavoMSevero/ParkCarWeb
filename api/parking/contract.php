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
    case 'register contract':
        // print_r($data);
        $contract = $data->contractType;
        $idParking = $data->idParking;

        $insertContract=$pdo->prepare("INSERT INTO customerContract (idCustomerContract, idParking, contractType)
                                    VALUES(?,?,?)");
        $insertContract->bindValue(1, NULL);
        $insertContract->bindValue(2, $idParking);
        $insertContract->bindValue(3, $contract);
        $insertContract->execute();

        break;

    case 'get contracts':
    
        $idParking = $_GET["idParking"];

        $getContracts=$pdo->prepare("SELECT * FROM customerContract WHERE idParking=:idParking");
        $getContracts->bindValue(":idParking", $idParking);
        $getContracts->execute();

        while ($line=$getContracts->fetch(PDO::FETCH_ASSOC)) {
            $idCustomerContract = $line['idCustomerContract'];
            $idParking = $line['idParking'];
            $contractType = $line['contractType'];

            $return[] = array(
                'idCustomerContract' => $idCustomerContract,
                'idParking' => $idParking,
                'contractType' => $contractType
            );
        }

        echo json_encode($return);

        break;
    
    default:
        # code...
        break;
}


?>