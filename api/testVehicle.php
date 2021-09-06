<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");
include_once("jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'get existing vehicles':

        $lisencePlate = strtoupper($_GET['lisencePlate']);
        // echo $lisencePlate;

        try {

            $getAllVehicles=$pdo->prepare("SELECT clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate, clientVehicle.idClientVehicle, client.name, client.idClient
                                            FROM clientVehicle, client 
                                            WHERE clientVehicle.idclient=client.idclient
                                            AND clientVehicle.`licensePlate`=:lisencePlate");
            $getAllVehicles->bindValue(':lisencePlate', $lisencePlate);
            $getAllVehicles->execute();

            $exists = $getAllVehicles->rowCount();

            if ($exists != 0) {
                while ($line=$getAllVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $idClient = $line['idClient'];
                    $idClientVehicle = $line['idClientVehicle'];
                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
                    $clientName = $line['name'];
    
                    $return = array(
                        'idClient' => $idClient,
                        'licensePlate' => $licensePlate,
                        'idClientVehicle' => $idClientVehicle,
                        'brand' => $brand,
                        'model' => $model,
                        'clientName' => $clientName
                    );
    
                }
    
                echo json_encode($return);
            } else {
                $status = 0;
                $msg = 'Nenhum veículo encontrado!        Digita as letras em miúsculo.';

                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );

                echo json_encode($return);
            }
                
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;
    
    
    default:
        # code...
        break;
}


?>