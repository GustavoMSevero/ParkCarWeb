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
        // print_r($data);
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

            $lat = $resp['results'][0]['geometry']['location']['lat'];
            $lng = $resp['results'][0]['geometry']['location']['lng'];

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
                $registerParking=$pdo->prepare("INSERT INTO parking (idParking, idOwnerParking, zipcode, address, addressNumber, neighborhood, city, state, parkingName, parkingPass, email, password, activate, vaccantNumber, lat, lng) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
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
                $registerParking->bindValue(15, $lat);
                $registerParking->bindValue(16, $lng);
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
                $lat = $line['lat'];
                $lng = $line['lng'];
                $vaccantNumber = $line['vaccantNumber'];

                $status = 1;
                $return[] = array(
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'city' => $city,
                    'state' => $state,
                    'lat' => $lat,
                    'lng' => $lng,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);

            
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
                $lat = $line['lat'];
                $lng = $line['lng'];
                $vaccantNumber = $line['vaccantNumber'];

                $status = 1;
                $return[] = array(
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'city' => $city,
                    'state' => $state,
                    'lat' => $lat,
                    'lng' => $lng,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);

            
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

    default:
        # code...
        break;
}


?>