<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
      case 'get my vehicle to edit':

        $idClientVehicle = $_GET['idClientVehicle'];

        try {

            $getMyVehicleToEdit=$pdo->prepare("SELECT * FROM clientVehicle
                                    WHERE idClientVehicle=:idClientVehicle");
            $getMyVehicleToEdit->bindValue(':idClientVehicle', $idClientVehicle);
            $getMyVehicleToEdit->execute();

            while ($line=$getMyVehicleToEdit->fetch(PDO::FETCH_ASSOC)) {

                $licensePlate = $line['licensePlate'];
                $brand = $line['brand'];
                $model = $line['model'];

                $return = array(
                    'licensePlate' => $licensePlate,
                    'brand' => $brand,
                    'model' => $model
                );
               
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    case 'update data vehicle':

        $idClientVehicle = $data->idClientVehicle;
        $brand = $data->brand;
        $model = $data->model;
        $licensePlate = $data->licensePlate;

        try {

            $updateDataVehicles=$pdo->prepare("UPDATE clientVehicle SET brand=:brand, model=:model, licensePlate=:licensePlate
                                                WHERE idClientVehicle=:idClientVehicle");
            $updateDataVehicles->bindValue(':brand', $brand);
            $updateDataVehicles->bindValue(':model', $model);
            $updateDataVehicles->bindValue(':licensePlate', $licensePlate);
            $updateDataVehicles->bindValue(':idClientVehicle', $idClientVehicle);
            $updateDataVehicles->execute();

            $msg = 'Dados do veículo atualizados';
            $status = 1;

            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;
    
    default:
        # code...
        break;
}


?>