<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");
include_once("../functions/oneSignal.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'register':
        $name = $data->clientName;
        $rg = $data->rg;
        $cpf = $data->cpf;
        $cnh = $data->cnh;
        $email = $data->email;
        $password = md5($data->password);

        // $licensePlate = $data->licensePlate;
        // $idClientVehicle = $data->idClientVehicle;

        // $onesignalId = $data->onesignalId;

        // $vehicleParkStatus = 0; // 0 not parked, 1 parked
        $parkingPass = 1; // 1 parking pass agree

            try {
                $checkIfUserExists=$pdo->prepare("SELECT idClient FROM client WHERE email=:email AND cpf=:cpf");
                $checkIfUserExists->bindValue(":email", $email);
                $checkIfUserExists->bindValue(":cpf", $cpf);
                $checkIfUserExists->execute();

                $exists = $checkIfUserExists->rowCount();

                if ($exists != 0) {
                    $status = 1;
                    $msg = 'Usuário já existente';
                    $return = array(
                        'status' => $status,
                        'msg' => $msg
                    );

                    echo json_encode($return);
                } else {
                    $registerClient=$pdo->prepare("INSERT INTO client (idClient, name, rg, cpf, cnh, parkingPass, email, password) VALUES(?,?,?,?,?,?,?,?)");
                    $registerClient->bindValue(1, NULL);
                    $registerClient->bindValue(2, $name);
                    $registerClient->bindValue(3, $rg);
                    $registerClient->bindValue(4, $cpf);
                    $registerClient->bindValue(5, $cnh);
                    $registerClient->bindValue(6, $parkingPass);
                    $registerClient->bindValue(7, $email);
                    $registerClient->bindValue(8, $password);
                    $registerClient->execute();

                    $idClient=$pdo->lastInsertId();

                    //ONE SIGNAL
                    // $userType = 'client';
                    // $oneSignalId = 0; 
                    // updateOneSignalId($idClient, $userType, $oneSignalId);

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
                    $mail->addAddress($email, $name);

                    // Content
                    $mail->Subject = "SEJA BEM-VINDO A PARKCAR";
                    $mail->Body    = "<html lang='en'>
                                        <head>
                                            <meta charset='UTF-8'>
                                        </head>
                                        <body>
                                            <p>Seja bem-vindo a ParkCar, ".$name.".<br>
                                            Seu cadastro, em nosso sistema, foi efetuado com sucesso!</p>
                                        </body>
                                    </html>";
                    $mail->IsHTML(true); // Set email format to HTML

                    $mail->send();
                    //echo 'Message has been sent';
                    $msg = 'E-mail de boas-vindas enviada com sucesso para '.$name.'! '.$email;

                    $return = array(
                        'msg' => $msg
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
        $oneSignalId = $data->onesignalId;

        try {
            $searchClient=$pdo->prepare("SELECT idClient, name FROM client 
                                    WHERE email=:email AND password=:password");
            $searchClient->bindValue(":email", $email);
            $searchClient->bindValue(":password", $password);
            $searchClient->execute();

            $exists = $searchClient->rowCount();

            if($exists == 0) {
                $msg = 'Email ou senha inválido';
                $status = 0;

                $svr = $_SERVER['REMOTE_ADDR'];
                if ($svr == '127.0.0.1') {
                    $msg = utf8_encode($msg);
                }

                $return = array(
                    'msg' => $msg,
                    'status' => $status
                );
        
                echo json_encode($return);
            } else {
                while ($line=$searchClient->fetch(PDO::FETCH_ASSOC)) {

                    $idClient = $line['idClient'];
                    $name = $line['name'];

                    $jwt = jwt($idClient, 'client');
                    $token = $jwt['token'];
                    $refreshToken = $jwt['refreshToken'];
                    $status = 1;

                    // ONE SIGNAL
                    $userType = 'client';
                    updateOneSignalId($idClient, $userType, $oneSignalId);

                    $return = array(
                        'status' => $status,
                        'id' => $idClient,
                        'name' => $name,
                        'token' => $token,
                        'refreshToken' => $refreshToken
                    );
        
                }
        
                echo json_encode($return, JSON_UNESCAPED_SLASHES);
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