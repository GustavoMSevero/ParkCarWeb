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
    case 'get total value':

        $idParking = $data->idParking;
        $monthDate = $data->month;
        $totalValuePaid = 0;

        try {
            $getTotalValue=$pdo->prepare("SELECT valuePaid FROM parkedVehicles WHERE idParking=:idParking AND MONTH(parkDate)=:monthDate");
            $getTotalValue->bindvalue(":idParking", $idParking);
            $getTotalValue->bindvalue(":monthDate", $monthDate);
            $getTotalValue->execute();

            $quantity = $getTotalValue->rowCount();

            if ($quantity > 0) {
                
                while ($line=$getTotalValue->fetch(PDO::FETCH_ASSOC)) {

                    $valuePaid = $line['valuePaid'];
                    $totalValuePaid = $totalValuePaid + $valuePaid;

                    $return = array(
                        'totalValuePaid' => $totalValuePaid
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