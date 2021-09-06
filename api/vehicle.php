<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../jwt.php");
include_once("../oneSignal.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'get vehicle':

        $userId = verifyJWT();

        $vehicleParkStatus = isset($_GET['vehicleParkStatus']) ? $_GET['vehicleParkStatus'] : null;  //not parked
        $licensePlate = strtoupper($_GET['licensePlate']);


        try {

            $getAllVehicles=$pdo->prepare("SELECT clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate, client.name 
                                            FROM clientVehicle, client 
                                            WHERE clientVehicle.vehicleParkStatus=:vehicleParkStatus
                                            AND clientVehicle.licensePlate=:licensePlate
                                            AND clientVehicle.idclient=client.idclient");
            $getAllVehicles->bindValue(':vehicleParkStatus', $vehicleParkStatus);
            $getAllVehicles->bindValue(':licensePlate', $licensePlate);
            $getAllVehicles->execute();

            $exists = $getAllVehicles->rowCount();

            if ($exists != 0) {
                while ($line=$getAllVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
                    $clientName = $line['name'];
    
                    $return = array(
                        'licensePlate' => $licensePlate,
                        'brand' => $brand,
                        'model' => $model,
                        'clientName' => $clientName
                    );
    
                }
    
                echo json_encode($return);
            } else {
                $status = 0;
                $msg = 'Nenhum veículo encontrado.';

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

    case 'get all vehicles parked':

        $vehicleParkStatus = $_GET['vehicleParkStatus'];  //parked

        try {

            $getAllVehicles=$pdo->prepare("SELECT p.`licensePlate`, p.entrance, p.`vehicleParkStatus` ,c.name, v.model, v.`brand`
                                            FROM  parkedVehicles p, `client` c, clientVehicle v
                                            WHERE p.`vehicleParkStatus`=:vehicleParkStatus
                                            AND c.idClient = p.idClient
                                            AND v.`idClient` = c.`idClient`;");
            $getAllVehicles->bindValue(':vehicleParkStatus', $vehicleParkStatus);
            $getAllVehicles->execute();

            $exists = $getAllVehicles->rowCount();

            if ($exists != 0) {
                while ($line=$getAllVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
                    $clientName = $line['name'];
                    $entrance = $line['entrance'];
    
                    $return[] = array(
                        'licensePlate' => $licensePlate,
                        'brand' => $brand,
                        'model' => $model,
                        'clientName' => $clientName,
                        'entrance' => $entrance
                    );
    
                }
    
                echo json_encode($return);
            }
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get my vehicles parked':

        $idClient = $_GET['idClient'];
        $vehicleParkStatus = $_GET['vehicleParkStatus'];

        try {

            $getMyVehiclesParked=$pdo->prepare("SELECT * FROM parkedVehicles 
                                            WHERE idClient=:idClient AND vehicleParkStatus=:vehicleParkStatus");
            $getMyVehiclesParked->bindValue(':idClient', $idClient);
            $getMyVehiclesParked->bindValue(':vehicleParkStatus', $vehicleParkStatus);
            $getMyVehiclesParked->execute();

            $exists = $getMyVehiclesParked->rowCount();

            if ($exists == 0) {
                $status = 0;
                $msg = 'Não há nenhum veículo estacionado';

                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );
                echo json_encode($return);
            } else {
                while ($line=$getMyVehiclesParked->fetch(PDO::FETCH_ASSOC)) {

                    $licensePlate = $line['licensePlate'];
                    $idParking = $line['idParking'];
                    $idClient = $line['idClient'];

                    // $getInformations=$pdo->prepare("SELECT p.`licensePlate`, p.`entrance`, p.`vehicleParkStatus`, pa.`parkingName`, v.`model`, v.`brand`
                    //                                 FROM  parkedVehicles p, `client` c, clientVehicle v, parking pa
                    //                                 WHERE c.idClient=:idClient
                    //                                 AND pa.`idParking`=:idParking
                    //                                 AND v.`idClient`=c.idClient
                    //                                 AND v.`vehicleParkStatus`=:vehicleParkStatus");
                    $getInformations=$pdo->prepare("SELECT p.licensePlate, p.entrance, p.vehicleParkStatus,  v.model, v.brand, pa.parkingName
                                                    FROM  parkedVehicles p, client c, clientVehicle v, parking pa
                                                    WHERE c.idClient=p.idClient
                                                    AND c.idClient=v.idClient
                                                    AND p.vehicleParkStatus=:vehicleParkStatus
                                                    AND c.idClient=:idClient
                                                    AND pa.idParking = p.idParking");

                    $getInformations->bindValue(":vehicleParkStatus", $vehicleParkStatus);
                    // $getInformations->bindValue(":idParking", $idParking);
                    $getInformations->bindValue(":idClient", $idClient);
                    $getInformations->execute();

                    while ($line=$getInformations->fetch(PDO::FETCH_ASSOC)) {
                        $parkingName = $line['parkingName'];
                        $brand = $line['brand'];
                        $model = $line['model'];
                        $licensePlate = $line['licensePlate'];
                        $entrance = $line['entrance'];

                        $return[] = array(
                            'parkingName' => $parkingName,
                            'brand' => $brand,
                            'model' => $model,
                            'licensePlate' => $licensePlate,
                            'entrance' => $entrance
                        );
                    }
                }

                echo json_encode($return);

            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get all clients vehicles':

        $idClient = $_GET['idClient'];

        try {

            $getAllClientsVehicles=$pdo->prepare("SELECT * FROM clientVehicle WHERE idClient=:idClient");
            $getAllClientsVehicles->bindValue(':idClient', $idClient);
            $getAllClientsVehicles->execute();

            $exists = $getAllClientsVehicles->rowCount();

            if ($exists == 1) {
                while ($line=$getAllClientsVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $idClientVehicle = $line['idClientVehicle'];
                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
    
                    $return[] = array(
                        'idClientVehicle' => $idClientVehicle,
                        'licensePlate' => $licensePlate,
                        'brand' => $brand,
                        'model' => $model
                    );
    
                }
    
                echo json_encode($return);
            } else {
                $status = 0;
                $msg = 'Você não possui veículo no seu nome.';
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

    case 'start counting time':
    
        $licensePlate = $data->licensePlate;
        $idParking = $data->idParking;
        $vehicleParkStatus = $data->vehicleParkStatus;

        date_default_timezone_set('America/Sao_Paulo');
        $entrance = date("Y-m-d H:i:s");

        try {
            //GET IDCLIENT BY LICENSEPLATE
            $getIdClient=$pdo->prepare("SELECT idClient FROM clientVehicle WHERE licensePlate=:licensePlate");
            $getIdClient->bindValue(":licensePlate", $licensePlate);
            $getIdClient->execute();

            while ($line=$getIdClient->fetch(PDO::FETCH_ASSOC)) {
                $idClient = $line['idClient'];
            }

            //GET ID USER FROM DEVICEIDS TABLE
            $getDeviceID=$pdo->prepare("SELECT * FROM deviceIds WHERE userId=:userId");
            $getDeviceID->bindValue(":userId", $idClient);
            $getDeviceID->execute();

            while ($line=$getDeviceID->fetch(PDO::FETCH_ASSOC)) {
                $id = $line['id'];
            }

            //UPDATE VEHICLE PARK STATUS
            $updateVehicleParkStatus=$pdo->prepare("UPDATE clientVehicle SET vehicleParkStatus=:vehicleParkStatus
                                            WHERE licensePlate=:licensePlate");
            $updateVehicleParkStatus->bindValue(":vehicleParkStatus", $vehicleParkStatus);
            $updateVehicleParkStatus->bindValue(":licensePlate", $licensePlate);
            $updateVehicleParkStatus->execute();

            //REGISTER PARKED VEHICLE
            $saveParkedVehicle=$pdo->prepare("INSERT INTO parkedVehicles (id, idParking, idClient, licenseplate, vehicleParkStatus, entrance, departureTime) VALUES(?,?,?,?,?,?,?)");
            $saveParkedVehicle->bindValue(1, NULL);
            $saveParkedVehicle->bindValue(2, $idParking);
            $saveParkedVehicle->bindValue(3, $idClient);
            $saveParkedVehicle->bindValue(4, $licensePlate);
            $saveParkedVehicle->bindValue(5, $vehicleParkStatus);
            $saveParkedVehicle->bindValue(6, $entrance);
            $saveParkedVehicle->bindValue(7, $entrance);
            $saveParkedVehicle->execute();

            // GET NUMBER VACCANTS AND NAME
            $getVaccantsNumber=$pdo->prepare("SELECT vaccantNumber, parkingName FROM parking WHERE idParking=:idParking");
            $getVaccantsNumber->bindValue(":idParking", $idParking);
            $getVaccantsNumber->execute();

            while ($line=$getVaccantsNumber->fetch(PDO::FETCH_ASSOC)) {
                $vaccantNumber = $line['vaccantNumber'];
                $parkingName = $line['parkingName'];
            }

            // $return = array(
            //     'licensePlate' => $licensePlate,
            //     'idParking' => $idParking,
            //     'vehicleParkStatus' => $vehicleParkStatus,
            //     'idClient' => $idClient,
            //     'id_device' => $id,
            //     'parkingName' => $parkingName
            // );
    
            // echo json_encode($return);

             // SEND MESSAGE TO USER BY ONESIGNAL
             $message = utf8_encode('Contagem de permanência no estacionamento $parkingName começou...');
             sendMessage($message, $id);

            $vaccantsQuantity = $vaccantNumber - 1;

            $updateVaccantsNumber=$pdo->prepare("UPDATE parking SET vaccantNumber=:vaccantsQuantity WHERE idParking=:idParking");
            $updateVaccantsNumber->bindValue(":idParking", $idParking);
            $updateVaccantsNumber->bindValue(":vaccantsQuantity", $vaccantsQuantity);
            $updateVaccantsNumber->execute();

            file_get_contents('http://smarttraffic.velcisribeiro.online/vacancies/'.$idParking.'/'.$vaccantNumber);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'stop counting time':
    
        $licensePlate = $data->licensePlate;
        $vehicleParkStatus = $data->vehicleParkStatus;
        $idParking = $data->idParking;

        date_default_timezone_set('America/Sao_Paulo');
        $departureTime = date("Y-m-d H:i:s");

        try {

            // UPDATE VEHICLE PARK STATUS AND DEPARTURE TIME
            $updateVehicleParkStatus=$pdo->prepare("UPDATE clientVehicle SET vehicleParkStatus=:vehicleParkStatus
                                            WHERE licensePlate=:licensePlate");
            $updateVehicleParkStatus->bindValue(":vehicleParkStatus", $vehicleParkStatus);
            $updateVehicleParkStatus->bindValue(":licensePlate", $licensePlate);
            $updateVehicleParkStatus->execute();

            $updateParkedVehicles=$pdo->prepare("UPDATE parkedVehicles SET vehicleParkStatus=:vehicleParkStatus, departureTime=:departureTime
                                            WHERE licensePlate=:licensePlate");
            $updateParkedVehicles->bindValue(":vehicleParkStatus", $vehicleParkStatus);
            $updateParkedVehicles->bindValue(":departureTime", $departureTime);
            $updateParkedVehicles->bindValue(":licensePlate", $licensePlate);
            $updateParkedVehicles->execute();

            //GET IDCLIENT BY LICENSEPLATE
            $getIdClient=$pdo->prepare("SELECT idClient FROM clientVehicle WHERE licensePlate=:licensePlate");
            $getIdClient->bindValue(":licensePlate", $licensePlate);
            $getIdClient->execute();

            while ($line=$getIdClient->fetch(PDO::FETCH_ASSOC)) {
                $idClient = $line['idClient'];
            }

            //GET ID USER FROM DEVICEIDS TABLE
            $getDeviceID=$pdo->prepare("SELECT * FROM deviceIds WHERE userId=:userId");
            $getDeviceID->bindValue(":userId", $idClient);
            $getDeviceID->execute();

            while ($line=$getDeviceID->fetch(PDO::FETCH_ASSOC)) {
                $id = $line['id'];
            }

            // GET NUMBER VACCANTS
            $getVaccantsNumber=$pdo->prepare("SELECT vaccantNumber, parkingName FROM parking WHERE idParking=:idParking");
            $getVaccantsNumber->bindValue(":idParking", $idParking);
            $getVaccantsNumber->execute();

            while ($line=$getVaccantsNumber->fetch(PDO::FETCH_ASSOC)) {
                $vaccantNumber = $line['vaccantNumber'];
                $parkingName = $line['parkingName'];
            }

            // SEND MESSAGE TO USER BY ONESIGNAL
            $message = 'Finalizado a contagem de permanência no estacionamento '.$parkingName;
            sendMessage($message, $id);

            $vaccantsQuantity = $vaccantNumber + 1;

            $updateVaccantsNumber=$pdo->prepare("UPDATE parking SET vaccantNumber=:vaccantsQuantity WHERE idParking=:idParking");
            $updateVaccantsNumber->bindValue(":idParking", $idParking);
            $updateVaccantsNumber->bindValue(":vaccantsQuantity", $vaccantsQuantity);
            $updateVaccantsNumber->execute();

            file_get_contents('http://smarttraffic.velcisribeiro.online/vacancies/'.$idParking.'/'.$vaccantNumber);

            // //GET ENTRANCE DATA TO CALCULATE LENGTH OF STAY
            // $getVehicleEntrance=$pdo->prepare("SELECT entrance FROM parkedVehicles WHERE licensePlate=:licensePlate");
            // $getVehicleEntrance->bindValue(":licensePlate", $licensePlate);
            // $getVehicleEntrance->execute();

            // while ($line=$getVehicleEntrance->fetch(PDO::FETCH_ASSOC)) {
            //     $entrance = $line['entrance'];
            // }

            // $start_t = new DateTime($entrance);
            // $current_t = new DateTime($departureTime);
            // $difference = $start_t ->diff($current_t );
            // $return_time = $difference ->format('%H:%I:%S');

            // print_r($return_time);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get existing vehicles':

        $licensePlate = $_GET['licensePlate'];
        $licensePlate = strtoupper($licensePlate);

        try {

            $getAllVehicles=$pdo->prepare("SELECT clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate, clientVehicle.idClientVehicle, client.name, client.idClient
                                            FROM clientVehicle, client 
                                            WHERE clientVehicle.idclient=client.idclient
                                            AND clientVehicle.`licensePlate`=:licensePlate");
            $getAllVehicles->bindValue(':licensePlate', $licensePlate);
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

    case 'get my vehicles':

        $idClient = $_GET['idClient'];

        try {

            $getMyVehicles=$pdo->prepare("SELECT * FROM clientVehicle WHERE idClient=:idClient");
            $getMyVehicles->bindValue(':idClient', $idClient);
            $getMyVehicles->execute();

            $exists = $getMyVehicles->rowCount();

            if ($exists === 1) {
                while ($line=$getMyVehicles->fetch(PDO::FETCH_ASSOC)) {
                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
    
                    $return[] = array(
                        'brand' => $brand,
                        'model' => $model,
                        'licensePlate' => $licensePlate
                    );
                }
    
                echo json_encode($return);
            } else {
                $status = 0;
                $return = array(
                    'status' => $status
                );

                echo json_encode($return);
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get shared vehicles':

        $idClient = $_GET['idClient'];

        try {

            $getSharedVehicles=$pdo->prepare("SELECT idVehicle, clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate
                                            FROM sharedVehicles, clientVehicle 
                                             WHERE idOtherUser=:idClient AND clientVehicle.idClientVehicle=sharedVehicles.idVehicle");
            $getSharedVehicles->bindValue(':idClient', $idClient);
            $getSharedVehicles->execute();

            $exists = $getSharedVehicles->rowCount();

            if ($exists === 1) {
                while ($line=$getSharedVehicles->fetch(PDO::FETCH_ASSOC)) {
                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
    
                    $sharedVehicle = 'Compartilhado';
    
                    $return[] = array(
                        'brand' => $brand,
                        'model' => $model,
                        'licensePlate' => $licensePlate,
                        'sharedVehicle' => $sharedVehicle
                    );
                }
    
                echo json_encode($return);
            } else {
                $status = 0;
                $return = array(
                    'status' => $status
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