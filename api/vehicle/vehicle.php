<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");
include_once("../functions/oneSignal.php");
include_once("../functions/generateEntryTicket.php");
include_once("../functions/generateExitTicket.php");
include_once("../functions/stayCount-GaragemCarlosGomes.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'register vehicle':

        $licensePlate = $data->licensePlate;
        $idClient = $data->idClient;
        $typeVehicle = $data->typeVehicle;
        $renavam = $data->renavam;

        // $onesignalId = $data->onesignalId;

        $vehicleParkStatus = 0; // 0 not parked, 1 parked
        $sharedVehicle = 0;

        if ( preg_match('/\s/',$licensePlate) ) {
            // echo 'tem espaços';
            $lp = explode(' ', $licensePlate);
            $licensePlate = $lp[0].$lp[1];
        } else {
            // echo 'não tem espaços';
        }
        // echo $licensePlate;
        $carBrand = $data->carBrand;
        $carModel = $data->carModel;

        $checkVehicleAlreadyExists=$pdo->prepare("SELECT idClientVehicle FROM clientVehicle WHERE licensePlate=:licensePlate");
        $checkVehicleAlreadyExists->bindValue(":licensePlate", $licensePlate);
        $checkVehicleAlreadyExists->execute();

        $qty = $checkVehicleAlreadyExists->rowCount();

        if ($qty > 0) {

            $status = 0;
            $msg = 'Veículo já cadastrado';
            
            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);

        } else {

            $registerVehicle=$pdo->prepare("INSERT INTO clientVehicle (idClientVehicle, idClient, typeVehicle, brand, model, licensePlate, renavam, sharedVehicle, vehicleParkStatus)
                                            VALUES(?,?,?,?,?,?,?,?,?)");
            $registerVehicle->bindValue(1, NULL);
            $registerVehicle->bindValue(2, $idClient);
            $registerVehicle->bindValue(3, $typeVehicle);
            $registerVehicle->bindValue(4, $carBrand);
            $registerVehicle->bindValue(5, $carModel);
            $registerVehicle->bindValue(6, $licensePlate);
            $registerVehicle->bindValue(7, $renavam);
            $registerVehicle->bindValue(8, $sharedVehicle);
            $registerVehicle->bindValue(9, $vehicleParkStatus);
            $registerVehicle->execute();

            $status = 1;
            $msg = 'Veículo cadastrado';
            
            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);

        }
 
        break;

    case 'get vehicle':

        $userId = verifyJWT();
        $vehicleParkStatus = isset($_GET['vehicleParkStatus']) ? $_GET['vehicleParkStatus'] : null;  //not parked
        $licensePlate = strtoupper($_GET['licensePlate']);

        try {

            if ($licensePlate == null) {

                $msg = 'Placa não informada!';
                $status = 0.0;
                
                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );
                echo json_encode($return);
            } else {

                $getAllVehicles=$pdo->prepare("SELECT clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate, client.name 
                                                FROM clientVehicle, client 
                                                WHERE clientVehicle.vehicleParkStatus=:vehicleParkStatus
                                                AND clientVehicle.licensePlate=:licensePlate
                                                AND clientVehicle.idclient=client.idclient");
                $getAllVehicles->bindValue(':vehicleParkStatus', $vehicleParkStatus);
                $getAllVehicles->bindValue(':licensePlate', $licensePlate);
                $getAllVehicles->execute();

                $exists = $getAllVehicles->rowCount();
                // echo $exists;

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
            }
                
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    case 'get all vehicles parked':

        $vehicleParkStatus = $_GET['vehicleParkStatus'];  //parked

        try {

            $getAllVehicles=$pdo->prepare("SELECT p.id, p.licensePlate, p.entrance, p.vehicleParkStatus ,c.name, v.model, v.brand
                                            FROM  parkedVehicles p, client c, clientVehicle v
                                            WHERE p.vehicleParkStatus=:vehicleParkStatus
                                            AND c.idClient = p.idClient
                                            AND v.idClient = c.idClient
                                            AND p.licensePlate = v.licensePlate");
            $getAllVehicles->bindValue(':vehicleParkStatus', $vehicleParkStatus);
            $getAllVehicles->execute();

            $exists = $getAllVehicles->rowCount();

            if ($exists != 0) {
                while ($line=$getAllVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['id'];
                    $licensePlate = $line['licensePlate'];
                    $brand = $line['brand'];
                    $model = $line['model'];
                    $clientName = $line['name'];
                    $entrance = $line['entrance'];
    
                    $return[] = array(
                        'id' => $id,
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

                    $getInformations=$pdo->prepare("SELECT p.id, p.licensePlate, p.entrance, p.vehicleParkStatus ,c.name, v.model, v.brand
                                                    FROM  parkedVehicles p, client c, clientVehicle v
                                                    WHERE p.vehicleParkStatus=:vehicleParkStatus
                                                    AND c.idClient = p.idClient
                                                    AND v.idClient = c.idClient
                                                    AND p.licensePlate = v.licensePlate");

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

            if ($exists > 0) {
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
        $vehicleParkStatus = 1;
        $lenghtOfStay = null;
        $valuePaid = null;

        date_default_timezone_set('America/Sao_Paulo');
        $entrance = date("Y-m-d H:i:s");
        $parkDate = date("Y-m-d");

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
                $id[] = $line['id'];
            }

            //UPDATE VEHICLE PARK STATUS
            $updateVehicleParkStatus=$pdo->prepare("UPDATE clientVehicle SET vehicleParkStatus=:vehicleParkStatus
                                            WHERE licensePlate=:licensePlate");
            $updateVehicleParkStatus->bindValue(":vehicleParkStatus", $vehicleParkStatus);
            $updateVehicleParkStatus->bindValue(":licensePlate", $licensePlate);
            $updateVehicleParkStatus->execute();

            //REGISTER PARKED VEHICLE
            $saveParkedVehicle=$pdo->prepare("INSERT INTO parkedVehicles (id, idParking, idClient, licenseplate, vehicleParkStatus, parkDate, entrance, departureTime, lenghtOfStay, valuePaid) VALUES(?,?,?,?,?,?,?,?,?,?)");
            $saveParkedVehicle->bindValue(1, NULL);
            $saveParkedVehicle->bindValue(2, $idParking);
            $saveParkedVehicle->bindValue(3, $idClient);
            $saveParkedVehicle->bindValue(4, $licensePlate);
            $saveParkedVehicle->bindValue(5, $vehicleParkStatus);
            $saveParkedVehicle->bindValue(6, $parkDate);
            $saveParkedVehicle->bindValue(7, $entrance);
            $saveParkedVehicle->bindValue(8, $entrance);
            $saveParkedVehicle->bindValue(9, $lenghtOfStay);
            $saveParkedVehicle->bindValue(10, $valuePaid);
            $saveParkedVehicle->execute();

            // GET NUMBER VACCANTS AND NAME
            $getVaccantsNumber=$pdo->prepare("SELECT vaccantNumber, parkingName FROM parking WHERE idParking=:idParking");
            $getVaccantsNumber->bindValue(":idParking", $idParking);
            $getVaccantsNumber->execute();

            while ($line=$getVaccantsNumber->fetch(PDO::FETCH_ASSOC)) {
                $vaccantNumber = $line['vaccantNumber'];
                $parkingName = $line['parkingName'];
            }

             // SEND MESSAGE TO USER BY ONESIGNAL
             $message = utf8_encode("Contagem no $parkingName iniciou...");
             sendMessage($message, $id);

            $vaccantsQuantity = $vaccantNumber - 1;

            $updateVaccantsNumber=$pdo->prepare("UPDATE parking SET vaccantNumber=:vaccantsQuantity WHERE idParking=:idParking");
            $updateVaccantsNumber->bindValue(":idParking", $idParking);
            $updateVaccantsNumber->bindValue(":vaccantsQuantity", $vaccantsQuantity);
            $updateVaccantsNumber->execute();

            file_get_contents('http://ws.parkcar.app.br/vacancies/'.$idParking.'/'.$vaccantNumber);

            // GET ENTRY TICKET INFORMATIONS AND SAVE IN THE DATABASE
            generateEntryTicket($idParking, $licensePlate);
            
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'stop counting time':
        $idParkedVehicle = $data->id;
        $licensePlate = $data->licensePlate;
        $idParking = $data->idParking;


        date_default_timezone_set('America/Sao_Paulo');
        $departureTime = date("Y-m-d H:i:s");

        stayCount($idParkedVehicle, $licensePlate, $idParking);

        try {
            $vehicleParkStatus = 0;
            // UPDATE VEHICLE PARK STATUS AND DEPARTURE TIME IN clientVehicle
            $updateVehicleParkStatus=$pdo->prepare("UPDATE clientVehicle SET vehicleParkStatus=:vehicleParkStatus
                                            WHERE licensePlate=:licensePlate");
            $updateVehicleParkStatus->bindValue(":vehicleParkStatus", $vehicleParkStatus);
            $updateVehicleParkStatus->bindValue(":licensePlate", $licensePlate);
            $updateVehicleParkStatus->execute();

            // UPDATE VEHICLE PARK STATUS AND DEPARTURE TIME IN parkedVehicles
            $updateParkedVehicles=$pdo->prepare("UPDATE parkedVehicles SET vehicleParkStatus=0, departureTime=:departureTime
                                            WHERE licensePlate=:licensePlate");
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

            //GET ONSINGAL ID USER FROM DEVICEIDS TABLE
            $getDeviceID=$pdo->prepare("SELECT * FROM deviceIds WHERE userId=:userId");
            $getDeviceID->bindValue(":userId", $idClient);
            $getDeviceID->execute();

            while ($line=$getDeviceID->fetch(PDO::FETCH_ASSOC)) {
                $id[] = $line['id'];
            }

            // GET NUMBER VACCANTS OF THE PARKING
            $getVaccantsNumber=$pdo->prepare("SELECT vaccantNumber, parkingName FROM parking WHERE idParking=:idParking");
            $getVaccantsNumber->bindValue(":idParking", $idParking);
            $getVaccantsNumber->execute();

            while ($line=$getVaccantsNumber->fetch(PDO::FETCH_ASSOC)) {
                $vaccantNumber = $line['vaccantNumber'];
                $parkingName = $line['parkingName'];
            }

            // SEND MESSAGE TO USER BY ONESIGNAL
            $message = utf8_encode("Finalizada a contagem no $parkingName");
            sendMessage($message, $id);

            $vaccantsQuantity = $vaccantNumber + 1;

            $updateVaccantsNumber=$pdo->prepare("UPDATE parking SET vaccantNumber=:vaccantsQuantity WHERE idParking=:idParking");
            $updateVaccantsNumber->bindValue(":idParking", $idParking);
            $updateVaccantsNumber->bindValue(":vaccantsQuantity", $vaccantsQuantity);
            $updateVaccantsNumber->execute();

            file_get_contents('http://ws.parkcar.app.br/vacancies/'.$idParking.'/'.$vaccantNumber);

            generateExitTicket($licensePlate);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get existing vehicles':

        $renavam = $_GET['renavam'];
        // $licensePlate = strtoupper($licensePlate);

        try {

            $getAllVehicles=$pdo->prepare("SELECT clientVehicle.brand, clientVehicle.model, clientVehicle.licensePlate, clientVehicle.idClientVehicle, clientVehicle.renavam, client.name, client.idClient
                                            FROM clientVehicle, client 
                                            WHERE clientVehicle.idclient=client.idclient
                                            AND clientVehicle.`renavam`=:renavam");
            $getAllVehicles->bindValue(':renavam', $renavam);
            $getAllVehicles->execute();

            $exists = $getAllVehicles->rowCount();

            if ($exists != 0) {
                while ($line=$getAllVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $idClient = $line['idClient'];
                    $idClientVehicle = $line['idClientVehicle'];
                    $licensePlate = $line['licensePlate'];
                    $renavam = $line['renavam'];
                    $brand = $line['brand'];
                    $model = $line['model'];
                    $clientName = $line['name'];
    
                    $return = array(
                        'idClient' => $idClient,
                        'licensePlate' => $licensePlate,
                        'renavam' => $renavam,
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

    case 'get paid vehicles':

        // $parkDate = date("Y-m-d");
        $idClient = $_GET['idClient'];
        $vehicleParkStatus=0;

        try {
            $getPaidVehicles=$pdo->prepare("SELECT parkedVehicles.*, parking.parkingName 
                                            FROM parkedVehicles, parking 
                                            WHERE idClient=:idClient 
                                            AND vehicleParkStatus=:vehicleParkStatus 
                                            AND parking.idParking=parkedVehicles.idParking");
            $getPaidVehicles->bindvalue(":idClient", $idClient);
            $getPaidVehicles->bindvalue(":vehicleParkStatus", $vehicleParkStatus);
            $getPaidVehicles->execute();

            $quantity = $getPaidVehicles->rowCount();

            if ($quantity != 0) {
                
                while ($line=$getPaidVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['id'];
                    $licensePlate = $line['licensePlate'];

                    $parkingName = $line['parkingName'];
                    $parkDate = $line['parkDate'];
                    $entrance = $line['entrance'];
                    $departureTime = $line['departureTime'];
                    $lenghtOfStay = $line['lenghtOfStay'];
                    $valuePaid = $line['valuePaid'];

                    $parkDateP = explode('-', $parkDate);
                    $parkDate = $parkDateP[2].'/'.$parkDateP[1].'/'.$parkDateP[0];

                    $entranceP = explode(' ', $entrance);
                    $entranceDate = $entranceP[0];
                    $entranceTime = $entranceP[1];

                    $departureTimeP = explode(' ', $departureTime);
                    $departureTime = $departureTimeP[1];

                    $return[] = array(
                        'id' => $id,
                        'parkingName' => $parkingName,
                        'licensePlate' => $licensePlate,
                        'entranceDate' => $parkDate,
                        'entranceTime' => $entranceTime,
                        'departureTime' => $departureTime,
                        'lenghtOfStay' => $lenghtOfStay,
                        'valuePaid' => $valuePaid
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

    case 'check status ticket':

        $licensePlate = $_GET['licensePlate'];
        $dateEntry = $_GET['dateEntry'];
        $timeEntry = $_GET['timeEntry'];

        try {
            $getTicketStatus=$pdo->prepare("SELECT * FROM ticket WHERE licensePlate=:licensePlate
                                            AND entryDate=:entryDate
                                            AND entryTime=:entryTime");
            $getTicketStatus->bindValue(":licensePlate", $licensePlate);
            $getTicketStatus->bindValue(":entryDate", $dateEntry);
            $getTicketStatus->bindValue(":entryTime", $timeEntry);
            $getTicketStatus->execute();

            while ($line=$getTicketStatus->fetch(PDO::FETCH_ASSOC)) {

                $statusTicket = $line['statusTicket'];

                $return = array(
                    'statusTicket' => $statusTicket
                );
    
            }

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