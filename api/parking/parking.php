<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");

$pdo = conectar();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
      case 'register':

        $idOwnerParking = $data->idOwnerParking;
        $zipcode = $data->zipcode;
        $address = $data->address;
        $addressNumber = $data->addressNumber;
        $neighborhood = $data->neighborhood;
        $city = $data->city;
        $state = $data->state;
        $parkingName = $data->parkingName;
        $email = $data->email;
        $password = md5($data->password);
        $parkingPass = 1;
        $activate = 1;

        try {
            
            $encode = urlencode("$address $addressNumber $city $neighborhood $state");

            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$encode&key=AIzaSyByiGVOIjoR_NWHz9TpYtDixGx7GZuxoEU";
            // get the json response
            $resp_json = file_get_contents($url);
            // decode the json
            $resp = json_decode($resp_json, true);

            $latitude = $resp['results'][0]['geometry']['location']['lat'];
            $longitude = $resp['results'][0]['geometry']['location']['lng'];

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $vaccantNumber = $data->vaccantNumber;

        try {

            $checkIfparkingExists=$pdo->prepare("SELECT idParking FROM parking WHERE email=:email AND parkingName=:parkingName");
            $checkIfparkingExists->bindValue(":email", $email);
            $checkIfparkingExists->bindValue(":parkingName", $parkingName);
            $checkIfparkingExists->execute();

            $exists = $checkIfparkingExists->rowCount();

            if ($exists != 0) {
                $status = 1;
                $msg = 'Estacionamento já existente';
                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );

                echo json_encode($return);

            } else {
                $registerParking=$pdo->prepare("INSERT INTO parking (idParking, idOwnerParking, zipcode, address, addressNumber, neighborhood, city, state, parkingName, parkingPass, email, password, activate, vaccantNumber, latitude, longitude) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $registerParking->bindValue(1, NULL);
                $registerParking->bindValue(2, $idOwnerParking);
                $registerParking->bindValue(3, $zipcode);
                $registerParking->bindValue(4, $address);
                $registerParking->bindValue(5, $addressNumber);
                $registerParking->bindValue(6, $neighborhood);
                $registerParking->bindValue(7, $city);
                $registerParking->bindValue(8, $state);
                $registerParking->bindValue(9, $parkingName);
                $registerParking->bindValue(10, $parkingPass);
                $registerParking->bindValue(11, $email);
                $registerParking->bindValue(12, $password);
                $registerParking->bindValue(13, $activate);
                $registerParking->bindValue(14, $vaccantNumber);
                $registerParking->bindValue(15, $latitude);
                $registerParking->bindValue(16, $longitude);
                $registerParking->execute();

                $parkingEmail = $email;

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host       = 'smtp.uni5.net';
                // $mail->Host       = 'smtp.parkcar.app.br';
                $mail->SMTPAuth   = true; 
                $mail->Username   = 'boasvindas@parkcar.app.br';
                $mail->Password   = 'STbv@2021';
                $mail->Port       = '587';
                $image = '../imgs/logo.png';
                // <img src='cid:".$image." width='100' height='50' >

                //Recipients
                $site = 'ParkCar';
                $mail->setFrom('boasvindas@parkcar.app.br', $site);
                $mail->addAddress($email, $parkingName);

                // Content
                $mail->Subject = "SEJA BEM-VINDO A PARKCAR";
                $mail->Body    = "<html lang='en'>
                                    <head>
                                        <meta charset='UTF-8'>
                                    </head>
                                    <body>
                                        <p>Seja bem-vindo a ParkCar, ".$parkingName.".<br>
                                        Seu cadastro, em nosso sistema, foi efetuado com sucesso!</p>
                                    </body>
                                </html>";
                $mail->IsHTML(true); // Set email format to HTML

                $mail->send();
                echo 'Message has been sent';
                $msg = 'E-mail de boas-vindas enviada com sucesso para '.$parkingName.'! '.$parkingEmail;
                $msgRegisterOK = 'Estacionamento '.$parkingName.' adicionado com sucesso';

                $return = array(
                    'msg' => $msg,
                    'msgRegisterOK' => $msgRegisterOK
                );

                echo json_encode($return);
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    case 'login':
        $email = $data->email;
        $password = md5($data->password);

        try {
            $searchParking=$pdo->prepare("SELECT idParking, parkingName FROM parking
                                    WHERE email=:email AND password=:password");
            $searchParking->bindValue(":email", $email);
            $searchParking->bindValue(":password", $password);
            $searchParking->execute();

            $exists = $searchParking->rowCount();

            if($exists == 0) {

                $status = 0;
                $msg = 'Email ou senha inválido';

                $return = array(
                    'status' => $status,
                    'msg' => utf8_encode($msg)
                );

                echo json_encode($return);

            } else if($exists == 1){
                
                while ($line=$searchParking->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['idParking'];
                    $name = utf8_encode($line['parkingName']);

                    $jwt = jwt($id, 'parking');
                    $token = $jwt['token'];
                    $refreshToken = $jwt['refreshToken'];
                    $status = 1;
    
                    $status = 1;
                    $return = array(
                        'status' => $status,
                        'id' => $id,
                        'name' => $name,
                        'token' => $token,
                        'refreshToken' => $refreshToken
                    );
    
                }
    
                echo json_encode($return);
            }

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get my parkings':
        $idOwnerParking = $_GET['idOwnerParking'];

        try {
            $getMyParkings=$pdo->prepare("SELECT * FROM parking WHERE idOwnerParking=:idOwnerParking");
            $getMyParkings->bindvalue(":idOwnerParking", $idOwnerParking);
            $getMyParkings->execute();
                
            while ($line=$getMyParkings->fetch(PDO::FETCH_ASSOC)) {

                $idParking = $line['idParking'];
                $parkingName = $line['parkingName'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $city = $line['city'];
                $state = $line['state'];
                $latitude = $line['latitude'];
                $longitude = $line['longitude'];
                $vaccantNumber = $line['vaccantNumber'];

                $status = 1;
                $return[] = array(
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'city' => $city,
                    'state' => $state,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get local parkings lots':

        try {
            $getLocalParkingsLots=$pdo->prepare("SELECT * FROM parking");
            $getLocalParkingsLots->execute();
                
            while ($line=$getLocalParkingsLots->fetch(PDO::FETCH_ASSOC)) {

                $idParking = $line['idParking'];
                $parkingName = $line['parkingName'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $city = $line['city'];
                $state = $line['state'];
                $latitude = $line['latitude'];
                $longitude = $line['longitude'];
                $vaccantNumber = $line['vaccantNumber'];

                $status = 1;
                $return[] = array(
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'city' => $city,
                    'state' => $state,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'near parkings':

        $geometry = $_GET['geometry'];
        $geometry = json_decode($geometry);

        $km = 1;
        $KmToGrandes = $km * 0.009;

        $lat = $geometry->latitude;
        $lng = $geometry->longitude;

        $latMax = $lat + $KmToGrandes;
        $latMin = $lat - $KmToGrandes;
        $lngMax = $lng + $KmToGrandes;
        $lngMin = $lng - $KmToGrandes;

        try {
            $searchForNearbyParking=$pdo->prepare("SELECT idParking, parkingName, address, addressNumber, vaccantNumber, latitude, longitude
                                                FROM parking
                                                WHERE latitude BETWEEN :latMin AND :latMax 
                                                AND longitude BETWEEN :lngMin AND :lngMax 
                                                AND activate=1");
            $searchForNearbyParking->bindvalue(":latMin", $latMin);
            $searchForNearbyParking->bindvalue(":latMax", $latMax);
            $searchForNearbyParking->bindvalue(":lngMin", $lngMin);
            $searchForNearbyParking->bindvalue(":lngMax", $lngMax);
            $searchForNearbyParking->execute();

            $searchForNearbyParking->execute();

            $quantity = $searchForNearbyParking->rowCount();

            if ($quantity != 0) {
                
                while ($line=$searchForNearbyParking->fetch(PDO::FETCH_ASSOC)) {

                    $idParking = $line['idParking'];
                    $parkingName = $line['parkingName'];
                    $address = $line['address'];
                    $addressNumber = $line['addressNumber'];
                    $vaccantNumber = $line['vaccantNumber'];
                    $latitude = $line['latitude'];
                    $longitude = $line['longitude'];

                    $getParkingLogo=$pdo->prepare("SELECT logoName FROM parkingLogo WHERE idParking=:idParking");
                    $getParkingLogo->bindvalue(":idParking", $idParking);
                    $getParkingLogo->execute();

                    $exists = $getParkingLogo->rowCount();

                    if ($exists != 0) {
                        while ($line=$getParkingLogo->fetch(PDO::FETCH_ASSOC)) {
                            $logoName = $line['logoName'];
                            $image = 'https://www.parkcar.app.br/web/api/parking/uploadLogoParking/'.$logoName;
                        }
                    } else {
                        $logoName = 'estacionamento.png';
                        $image = 'https://www.parkcar.app.br/web/imgs/'.$logoName;
                    }

                    // Verify is allow booking
                    $getParkingAllowBooking=$pdo->prepare("SELECT allow FROM allowBooking WHERE idParking=:idParking");
                    $getParkingAllowBooking->bindvalue(":idParking", $idParking);
                    $getParkingAllowBooking->execute();

                    $existsAllow = $getParkingAllowBooking->rowCount();

                    if ($existsAllow != 0) {
                        while ($line=$getParkingAllowBooking->fetch(PDO::FETCH_ASSOC)) {
                            $allow = $line['allow'];
                        }
                    } else {
                        $allow = 0;
                    }

                    $return[] = array(
                        'allow' => $allow,
                        'image' => $image,
                        'idParking' => $idParking,
                        'parkingName' => $parkingName,
                        'address' => $address,
                        'addressNumber' => $addressNumber,
                        'vaccantNumber' => $vaccantNumber,
                        'latitude' => floatval($latitude),
                        'longitude' => floatval($longitude)
                    );
        
                }

                echo json_encode($return);
            
            } else {

                $status = 0;
                $msg = 'Nenhum estacionamento próximo do seu destino';
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

    case 'get paid vehicles':

        $parkDate = date("Y-m-d");
        $idParking = $_GET['idParking'];

        try {
            $getPaidVehicles=$pdo->prepare("SELECT * FROM parkedVehicles WHERE idParking=:idParking 
                                            AND vehicleParkStatus=0 AND parkDate=:parkDate");
            $getPaidVehicles->bindvalue(":idParking", $idParking);
            $getPaidVehicles->bindvalue(":parkDate", $parkDate);
            $getPaidVehicles->execute();

            $quantity = $getPaidVehicles->rowCount();

            if ($quantity != 0) {
                
                while ($line=$getPaidVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['id'];
                    $licensePlate = $line['licensePlate'];

                    $parkDate = $line['parkDate'];
                    $entrance = $line['entrance'];
                    $departureTime = $line['departureTime'];
                    $lenghtOfStay = $line['lenghtOfStay'];
                    $valuePaid = $line['valuePaid'];

                    $entranceP = explode(' ', $entrance);
                    $entranceDate = $entranceP[0];
                    $entranceTime = $entranceP[1];

                    $departureTimeP = explode(' ', $departureTime);
                    $departureTime = $departureTimeP[1];

                    $return[] = array(
                        'id' => $id,
                        'licensePlate' => $licensePlate,
                        // 'entranceDate' => $entranceDate,
                        'entranceTime' => $entranceTime,
                        'departureTime' => $departureTime,
                        'lenghtOfStay' => $lenghtOfStay,
                        'valuePaid' => $valuePaid
                    );
        
                }

                echo json_encode($return);
            
            } else {

                $status = 0;
                $msg = 'Nenhum veículo entrou ainda hoje.';
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

    case 'get paid vehicles by date':

        $parkDate = $_GET['date'];
        $idParking = $_GET['idParking'];

        $parkDateP = explode('/', $parkDate);
        $parkDate = $parkDateP[2].'-'.$parkDateP[1].'-'.$parkDateP[0];

        try {
            $getPaidVehicles=$pdo->prepare("SELECT * FROM parkedVehicles WHERE idParking=:idParking 
                                            AND vehicleParkStatus=0 AND parkDate=:parkDate");
            $getPaidVehicles->bindvalue(":idParking", $idParking);
            $getPaidVehicles->bindvalue(":parkDate", $parkDate);
            $getPaidVehicles->execute();

            $quantity = $getPaidVehicles->rowCount();

            if ($quantity > 0) {
                
                while ($line=$getPaidVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['id'];
                    $licensePlate = $line['licensePlate'];

                    $parkDate = $line['parkDate'];
                    $entrance = $line['entrance'];
                    $departureTime = $line['departureTime'];
                    $lenghtOfStay = $line['lenghtOfStay'];
                    $valuePaid = $line['valuePaid'];

                    $entranceP = explode(' ', $entrance);
                    $entranceDate = $entranceP[0];
                    $entranceTime = $entranceP[1];

                    $parkDateP = explode('-', $parkDate);
                    $parkDate = $parkDateP[2].'/'.$parkDateP[1].'/'.$parkDateP[0];

                    $departureTimeP = explode(' ', $departureTime);
                    $departureTime = $departureTimeP[1];

                    $return[] = array(
                        'id' => $id,
                        'licensePlate' => $licensePlate,
                        'parkDate' => $parkDate,
                        'entranceTime' => $entranceTime,
                        'departureTime' => $departureTime,
                        'lenghtOfStay' => $lenghtOfStay,
                        'valuePaid' => $valuePaid
                    );
        
                }

                echo json_encode($return);
            
            } else {

                $status = 0;
                $msg = 'Nenhum veículo entrou ainda hoje.';
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

    case 'get parking history by parking':

        $idParking = $_GET['idParking'];

        try {
            $getPaidVehicles=$pdo->prepare("SELECT * FROM parkedVehicles WHERE idParking=:idParking 
                                            AND vehicleParkStatus=0");
            $getPaidVehicles->bindvalue(":idParking", $idParking);
            $getPaidVehicles->execute();
                
                while ($line=$getPaidVehicles->fetch(PDO::FETCH_ASSOC)) {

                    $id = $line['id'];
                    $licensePlate = $line['licensePlate'];

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
                        'licensePlate' => $licensePlate,
                        'parkDate' => $parkDate,
                        'entranceTime' => $entranceTime,
                        'departureTime' => $departureTime,
                        'lenghtOfStay' => $lenghtOfStay,
                        'valuePaid' => $valuePaid
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