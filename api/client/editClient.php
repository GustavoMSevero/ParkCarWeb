<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'get client data to update':
        $idClient = $_GET['idClient'];

        try {
            $getClientToUpdate=$pdo->prepare("SELECT * FROM client 
                                            WHERE idClient=:idClient");
            $getClientToUpdate->bindValue(":idClient", $idClient);
            $getClientToUpdate->execute();

            $exists = $getClientToUpdate->rowCount();

            if($exists == 0) {
                $msg = 'Email ou senha inválido';
                $status = 0;

                $return = array(
                    'msg' => utf8_encode($msg),
                    'status' => $status
                );
        
                echo json_encode($return);
            } else {
                while ($line=$getClientToUpdate->fetch(PDO::FETCH_ASSOC)) {

                    $idClient = $line['idClient'];
                    $name = $line['name'];
                    $rg = $line['rg'];
                    $cpf = $line['cpf'];
                    $cnh = $line['cnh'];
                    $email = $line['email'];
        
                    $status = 1;
                    $return = array(
                        'status' => $status,
                        'idClient' => $idClient,
                        'name' => $name,
                        'rg' => $rg,
                        'cpf' => $cpf,
                        'cnh' => $cnh,
                        'email' => $email
                    );
        
                }
        
                echo json_encode($return);
            }
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'update user data':
        $idClient = $data->idClient;
        $name = $data->name;
        $email = $data->email;
        $rg = $data->rg;
        $cpf = $data->cpf;
        $cnh = $data->cnh;

        try {
            $updateUserData=$pdo->prepare("UPDATE client SET name=:name, email=:email, rg=:rg, cpf=:cpf, cnh=:cnh 
                                            WHERE idClient=:idClient");
            $updateUserData->bindValue(":idClient", $idClient);
            $updateUserData->bindValue(":name", $name);
            $updateUserData->bindValue(":email", $email);
            $updateUserData->bindValue(":rg", $rg);
            $updateUserData->bindValue(":cpf", $cpf);
            $updateUserData->bindValue(":cnh", $cnh);
            $updateUserData->execute();

            $status = 1;
            $msg = 'Dados atualizados com sucesso';

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