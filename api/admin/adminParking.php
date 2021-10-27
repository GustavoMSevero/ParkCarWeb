<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");

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
    case 'register owner parking':
        // print_r($data);
        $ownerName = $data->ownerName;
        $moreParkings = $data->moreParkings;
        $ownerEmail = $data->ownerEmail;
        $ownerPassword = md5($data->ownerPassword);

        try {
            // GET owner to check if already exists
            $getOwner=$pdo->prepare("SELECT ownerEmail, ownerPassword 
                                    FROM ownerParking 
                                    WHERE ownerPassword=:ownerPassword 
                                    AND ownerEmail=:ownerEmail");
            $getOwner->bindValue(":ownerPassword", $ownerPassword);
            $getOwner->bindValue(":ownerEmail", $ownerEmail);
            $getOwner->execute();

            $qtd = $getOwner->rowCount();

            if ($qtd > 0) {
                // echo 'já exists ';
                // echo $qtd;
                $status = 2;
                $existsMessagem = utf8_encode('E-mail e senha já cadastrados');

                $return = array(
                    'status' => $status,
                    'existsMessagem' => $existsMessagem
                );

                echo json_encode($return);
            } else {
                // echo 'não existe ';
                // echo $qtd;
                $registerOwnerParking=$pdo->prepare("INSERT INTO ownerParking (idOwnerParking, ownerName, moreParkings, ownerEmail, ownerPassword) VALUES(?,?,?,?,?)");
                $registerOwnerParking->bindValue(1, NULL);
                $registerOwnerParking->bindValue(2, $ownerName);
                $registerOwnerParking->bindValue(3, $moreParkings);
                $registerOwnerParking->bindValue(4, $ownerEmail);
                $registerOwnerParking->bindValue(5, $ownerPassword);
                $registerOwnerParking->execute();

                $idOwnerParking = $pdo->lastInsertId();
                $status = 1;

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
                $mail->addAddress($ownerEmail, $ownerName);

                // Content
                $mail->Subject = "SEJA BEM-VINDO A PARKCAR";
                $mail->Body    = "<html lang='en'>
                                    <head>
                                        <meta charset='UTF-8'>
                                    </head>
                                    <body>
                                        <p>Seja bem-vindo a ParkCar, ".$ownerName.".<br>
                                        Seu cadastro, em nosso sistema, foi efetuado com sucesso!</p>
                                    </body>
                                </html>";
                $mail->IsHTML(true); // Set email format to HTML

                $mail->send();
                // echo 'Message has been sent';

                $return = array(
                    'status' => $status,
                    'id' => $idOwnerParking,
                    'name' => $ownerName,
                    'moreParkings' => $moreParkings
                );

                echo json_encode($return);
            }
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

      case 'register': // register parking

        try {
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

            $vaccantNumber = $data->vaccantNumber;
            
            $lat = $data->lat;
            $lng = $data->lng;

            $registerParking=$pdo->prepare("INSERT INTO parking (idParking, zipcode, address, addressNumber, neighborhood, city, state, parkingName, parkingPass, email, password, vaccantNumber, lat, lng) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $registerParking->bindValue(1, NULL);
            $registerParking->bindValue(2, $zipcode);
            $registerParking->bindValue(3, $address);
            $registerParking->bindValue(4, $addressNumber);
            $registerParking->bindValue(5, $neighborhood);
            $registerParking->bindValue(6, $city);
            $registerParking->bindValue(7, $state);
            $registerParking->bindValue(8, $parkingName);
            $registerParking->bindValue(9, $parkingPass);
            $registerParking->bindValue(10, $email);
            $registerParking->bindValue(11, $password);
            $registerParking->bindValue(12, $vaccantNumber);
            $registerParking->bindValue(13, $lat);
            $registerParking->bindValue(14, $lng);
            $registerParking->execute();

            $idparking = $pdo->lastInsertId();

            $time30 = $data->time30;
            $price30 = $data->price30;
            $time1hour = $data->time1hour;
            $price1hour = $data->price1hour;
            $adicionalPrice = $data->adicionalPrice;

            $price30 = number_format(str_replace(",",".",str_replace(".","", $price30)), 2, '.', '');
            $price1hour = number_format(str_replace(",",".",str_replace(".","", $price1hour)), 2, '.', '');
            $adicionalPrice = number_format(str_replace(",",".",str_replace(".","", $adicionalPrice)), 2, '.', '');

            $registerTimeAndPrices=$pdo->prepare("INSERT INTO parkingTimeAndPrices (id, idParking, time30, price30, time1hour, price1hour, adicionalPrice) VALUES(?,?,?,?,?,?,?)");
            $registerTimeAndPrices->bindValue(1, NULL);
            $registerTimeAndPrices->bindValue(2, $idParking);
            $registerTimeAndPrices->bindValue(3, $time30);
            $registerTimeAndPrices->bindValue(4, $price30);
            $registerTimeAndPrices->bindValue(5, $time1hour);
            $registerTimeAndPrices->bindValue(6, $price1hour);
            $registerTimeAndPrices->bindValue(7, $adicionalPrice);
            $registerTimeAndPrices->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    case 'login': // login owner parking
        $typeUser = $data->typeUser;
        $email = $data->email;
        $password = md5($data->password);

        try {
            $searchOwnerParking=$pdo->prepare("SELECT * FROM ownerParking
                                    WHERE ownerEmail=:email AND ownerPassword=:password");
            $searchOwnerParking->bindValue(":email", $email);
            $searchOwnerParking->bindValue(":password", $password);
            $searchOwnerParking->execute();

            $exists = $searchOwnerParking->rowCount();

            if($exists == 0) {

                $status = 0;
                $msg = 'Email ou senha inválido';

                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );

                echo json_encode($return);

            } else if($exists == 1){
                
                while ($line=$searchOwnerParking->fetch(PDO::FETCH_ASSOC)) {

                    $idOwnerParking = $line['idOwnerParking'];
                    $ownerName = $line['ownerName'];
                    $moreParkings = $line['moreParkings'];
    
                    $status = 1;
                    $return = array(
                        'status' => $status,
                        'id' => $idOwnerParking,
                        'name' => $ownerName,
                        'moreParkings' => $moreParkings
                    );
    
                }
    
                echo json_encode($return);
            }

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get parking address':

        $idParking = $_GET['idParking'];

        try {
            $getParkingAddress=$pdo->prepare("SELECT * FROM parking WHERE idParking=:idParking");
            $getParkingAddress->bindValue(":idParking", $idParking);
            $getParkingAddress->execute();
                
            while ($line=$getParkingAddress->fetch(PDO::FETCH_ASSOC)) {

                $idParking = $line['idParking'];
                $parkingName = $line['parkingName'];
                $zipcode = $line['zipcode'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $neighborhood = $line['neighborhood'];
                $city = $line['city'];
                $state = $line['state'];
                $email = $line['email'];
                $parkingPass = $line['parkingPass'];
                $vaccantNumber = $line['vaccantNumber'];

                if ($parkingPass == 1) {
                    $parkingPass = 'Sim';
                }

                $return = array(
                    'parkingPass' => $parkingPass,
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'zipcode' => $zipcode,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'neighborhood' => $neighborhood,
                    'city' => $city,
                    'state' => $state,
                    'email' => $email,
                    'parkingPass' => $parkingPass,
                    'vaccantNumber' => $vaccantNumber
                );

            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get time and prices':

        $idParking = $_GET['idParking'];

        try {
            $getTimeAndPrices=$pdo->prepare("SELECT * FROM parkingTimeAndPrices WHERE idParking=:idParking");
            $getTimeAndPrices->bindValue(":idParking", $idParking);
            $getTimeAndPrices->execute();

            while ($line=$getTimeAndPrices->fetch(PDO::FETCH_ASSOC)) {

                $time30 = $line['time30'];
                $price30 = str_replace(".",",",$line['price30']);
                $time1hour = $line['time1hour'];
                $price1hour = str_replace(".",",",$line['price1hour']);
                $adicionalPrice = str_replace(".",",",$line['adicionalPrice']);

                $return = array(
                    'time30' => $time30,
                    'price30' => $price30,
                    'time1hour' => $time1hour,
                    'price1hour' => $price1hour,
                    'adicionalPrice' => $adicionalPrice
                );

            }

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'admin area': // login owner parking
       
        $email = $data->email;
        $checkPassword = md5($data->checkPassword);

        try {
            $searchOwnerParking=$pdo->prepare("SELECT * FROM ownerParking
                                    WHERE ownerEmail=:email AND ownerPassword=:checkPassword");
            $searchOwnerParking->bindValue(":email", $email);
            $searchOwnerParking->bindValue(":checkPassword", $checkPassword);
            $searchOwnerParking->execute();

            $exists = $searchOwnerParking->rowCount();

            if($exists == 0) {

                $status = 0;
                $msg = 'Email ou senha inválido';

                $return = array(
                    'status' => $status,
                    'msg' => $msg
                );

                echo json_encode($return);

            } else if($exists == 1){
                
                while ($line=$searchOwnerParking->fetch(PDO::FETCH_ASSOC)) {

                    $idOwnerParking = $line['idOwnerParking'];
                    $ownerName = $line['ownerName'];
                    $moreParkings = $line['moreParkings'];
    
                    $status = 1;
                    $return = array(
                        'status' => $status,
                        'id' => $idOwnerParking,
                        'name' => $ownerName,
                        'moreParkings' => $moreParkings
                    );
    
                }
    
                echo json_encode($return);
            }

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get owner data': // login owner parking
    
        $id = $_GET['id'];

        try {
            $getOwnerData=$pdo->prepare("SELECT * FROM ownerParking WHERE idOwnerParking=:id");
            $getOwnerData->bindValue(":id", $id);
            $getOwnerData->execute();

            while ($line=$getOwnerData->fetch(PDO::FETCH_ASSOC)) {

                $ownerName = $line['ownerName'];
                $ownerEmail = $line['ownerEmail'];

                $status = 1;
                $return = array(
                    'name' => $ownerName,
                    'ownerEmail' => $ownerEmail
                );

            }

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'change admin password': // login owner parking

        @$newPassword = md5($data->newPassword);
        $id = $data->id;

        try {
            $changeAdminPassword=$pdo->prepare("UPDATE ownerParking SET ownerPassword=:newPassword WHERE idOwnerParking=:id");
            $changeAdminPassword->bindValue(":newPassword", $newPassword);
            $changeAdminPassword->bindValue(":id", $id);
            $changeAdminPassword->execute();

            $status = 1;
            $msg = 'Senha alterada com sucesso!';

            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get parking branch': // login owner parking

        $idParking = $_GET['idParking'];
        
        try {
            $getParkingBranch=$pdo->prepare("SELECT parkingName FROM parking WHERE idParking=:idParking");
            $getParkingBranch->bindValue(":idParking", $idParking);
            $getParkingBranch->execute();

            while ($line=$getParkingBranch->fetch(PDO::FETCH_ASSOC)) {

                $parkingName = $line['parkingName'];

                $return = array(
                    'parkingName' => $parkingName
                );

            }

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get owner name': // login owner parking

        $id = $_GET['id'];

        try {
            $getOwnername=$pdo->prepare("SELECT ownerName FROM ownerParking WHERE idOwnerParking=:id");
            $getOwnername->bindValue(":id", $id);
            $getOwnername->execute();

            while ($line=$getOwnername->fetch(PDO::FETCH_ASSOC)) {

                $ownerName = $line['ownerName'];

                $return = array(
                    'name' => $ownerName
                );

            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'update owner name': // login owner parking

        $id = $data->id;
        $name = $data->name;

        try {
            $updateOwnerName=$pdo->prepare("UPDATE ownerParking SET ownerName=:name WHERE idOwnerParking=:id");
            $updateOwnerName->bindValue(":name", $name);
            $updateOwnerName->bindValue(":id", $id);
            $updateOwnerName->execute();

            $return = array(
                'name' => $name
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