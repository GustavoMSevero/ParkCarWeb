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
    case 'allow booking':
        $allow = $data->allow;
        $idParking = $data->idParking;

        try {
            
            $checkIfAllowBooking=$pdo->prepare("SELECT * FROM allowBooking WHERE idParking=:idParking");
            $checkIfAllowBooking->bindValue(":idParking", $idParking);
            $checkIfAllowBooking->execute();

            $exists = $checkIfAllowBooking->rowCount();

            if ($exists != 0) {
                // UPDATE
                $updateAllowBooking=$pdo->prepare("UPDATE allowBooking SET allow=:allow WHERE idParking=:idParking");
                $updateAllowBooking->bindValue(":allow", $allow);
                $updateAllowBooking->bindValue(":idParking", $idParking);
                $updateAllowBooking->execute();

            } else {
                // INSERT
                $registerAllowBooking=$pdo->prepare("INSERT INTO allowBooking (idAllowBooking, idParking, allow) VALUES(?,?,?)");
                $registerAllowBooking->bindValue(1, NULL);
                $registerAllowBooking->bindValue(2, $idParking);
                $registerAllowBooking->bindValue(3, $allow);
                $registerAllowBooking->execute();
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get allow booking';

        $idParking = $_GET['idParking'];

        try {
                
            $getAllowBooking=$pdo->prepare("SELECT allow FROM allowBooking WHERE idParking=:idParking");
            $getAllowBooking->bindValue(":idParking", $idParking);
            $getAllowBooking->execute();

            while ($line=$getAllowBooking->fetch(PDO::FETCH_ASSOC)) {

                $allow = $line['allow'];

                if ($allow == 1) {
                    $allow = 'Sim';
                } else {
                    $allow = 'Não';
                }

                $return = array(
                    'allow' => $allow
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