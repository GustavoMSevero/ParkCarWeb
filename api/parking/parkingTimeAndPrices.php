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
    case 'register time and prices':
        // print_r($data);

        $idParkingTimeAndPrices = $data->idParkingTimeAndPrices;
        $idSubparking = $data->idSubparking;

        @$tolerancePeriod = $data->tolerancePeriod;
        if ($tolerancePeriod == '') {
            $tolerancePeriod = null;
        }
        $parkingTime_1 = $data->parkingTime_1;
        $parkingPrice_1 = $data->parkingPrice_1;

        $parkingTime_2 = $data->parkingTime_2;
        $parkingPrice_2 = $data->parkingPrice_2;

        $additionalTime_1 = $data->additionalTime_1;
        $additionalPrice_1 = $data->additionalPrice_1;

        $additionalTime_2 = $data->additionalTime_2;
        $additionalPrice_2 = $data->additionalPrice_2;

        $daily = $data->daily;

        $idParking = $data->idParking;
        $idParkingBranch = $data->idParkingBranch;

        $parkingPrice_1 = str_replace(",",".",$parkingPrice_1);
        $parkingPrice_2 = str_replace(",",".",$parkingPrice_2);
        $additionalPrice_1 = str_replace(",",".",$additionalPrice_1);
        $additionalPrice_2 = str_replace(",",".",$additionalPrice_2);
        $daily = str_replace(",",".",$daily);

        try {
            $checkParkingTimeNadPricesExists=$pdo->prepare("SELECT * FROM timeAndPrices WHERE idParking=:idParking AND idSubparking=:idSubparking");
            $checkParkingTimeNadPricesExists->bindValue(":idParking", $idParking);
            $checkParkingTimeNadPricesExists->bindValue(":idSubparking", $idSubparking);
            $checkParkingTimeNadPricesExists->execute();
            $qtd = $checkParkingTimeNadPricesExists->rowCount();

            if ($qtd != 0) {

                $updateTimeAndPrices=$pdo->prepare("UPDATE timeAndPrices SET tolerancePeriod=:tolerancePeriod, parkingTime_1=:parkingTime_1, parkingPrice_1=:parkingPrice_1,
                                                    parkingTime_2=:parkingTime_2, parkingPrice_2=:parkingPrice_2, additionalTime_1=:additionalTime_1, additionalPrice_1=:additionalPrice_1,
                                                    additionalTime_2=:additionalTime_2, additionalPrice_2=:additionalPrice_2, daily=:daily
                                                    WHERE idParkingTimeAndPrices=:idParkingTimeAndPrices");
                $updateTimeAndPrices->bindValue(":tolerancePeriod", $tolerancePeriod); 
                $updateTimeAndPrices->bindValue(":parkingTime_1", $parkingTime_1); 
                $updateTimeAndPrices->bindValue(":parkingPrice_1", $parkingPrice_1); 
                $updateTimeAndPrices->bindValue(":parkingTime_2", $parkingTime_2); 
                $updateTimeAndPrices->bindValue(":parkingPrice_2", $parkingPrice_2); 
                $updateTimeAndPrices->bindValue(":additionalTime_1", $additionalTime_1); 
                $updateTimeAndPrices->bindValue(":additionalPrice_1", $additionalPrice_1); 
                $updateTimeAndPrices->bindValue(":additionalTime_2", $additionalTime_2); 
                $updateTimeAndPrices->bindValue(":additionalPrice_2", $additionalPrice_2); 
                $updateTimeAndPrices->bindValue(":daily", $daily); 
                $updateTimeAndPrices->bindValue(":idParkingTimeAndPrices", $idParkingTimeAndPrices); 
                $updateTimeAndPrices->execute();

                $msg = "Dados atualizados com sucesso!";
                
                $return = array(
                    'msg' => $msg
                );

                echo json_encode($return);

            } else {

                $registerTimeAndPrices=$pdo->prepare("INSERT INTO timeAndPrices (idParkingTimeAndPrices, idParking, idSubparking, tolerancePeriod, parkingTime_1, parkingPrice_1,
                                                    parkingTime_2, parkingPrice_2, additionalTime_1, additionalPrice_1, additionalTime_2, additionalPrice_2, daily) 
                                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $registerTimeAndPrices->bindValue(1, NULL);
                $registerTimeAndPrices->bindValue(2, $idParking);
                $registerTimeAndPrices->bindValue(3, $idParkingBranch);

                $registerTimeAndPrices->bindValue(4, $tolerancePeriod);
                $registerTimeAndPrices->bindValue(5, $parkingTime_1);
                $registerTimeAndPrices->bindValue(6, $parkingPrice_1);
                $registerTimeAndPrices->bindValue(7, $parkingTime_2);
                $registerTimeAndPrices->bindValue(8, $parkingPrice_2);

                $registerTimeAndPrices->bindValue(9, $additionalTime_1);
                $registerTimeAndPrices->bindValue(10, $additionalPrice_1);
                $registerTimeAndPrices->bindValue(11, $additionalTime_2);
                $registerTimeAndPrices->bindValue(12, $additionalPrice_2);
                $registerTimeAndPrices->bindValue(13, $daily);
                $registerTimeAndPrices->execute();

                $msg = "Dados cadastrados com sucesso!";
                
                $return = array(
                    'msg' => $msg
                );

                echo json_encode($return);

            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get parking time and prices':
        // print_r($data);
        $idParking = $_GET['id_parking'];
        $idSubparking = $_GET['idParkingBranch'];

        try {
            $getParkingTimeAndPrices=$pdo->prepare("SELECT * FROM timeAndPrices 
                                                WHERE idParking=:idParking 
                                                AND idSubparking=:idSubparking");
            $getParkingTimeAndPrices->bindValue(":idParking", $idParking);                
            $getParkingTimeAndPrices->bindValue(":idSubparking", $idSubparking);
            $getParkingTimeAndPrices->execute();

            // $getParkingTimeAndPrices->rowCount();

            while ($line=$getParkingTimeAndPrices->fetch(PDO::FETCH_ASSOC)) {

                $idParkingTimeAndPrices = $line['idParkingTimeAndPrices'];
                $idSubparking = $line['idSubparking'];

                $tolerancePeriod = $line['tolerancePeriod'];
                $parkingTime_1 = $line['parkingTime_1'];
                $parkingPrice_1 = $line['parkingPrice_1'];
                $parkingPrice_1 = str_replace(".",",",$parkingPrice_1);

                $parkingTime_2 = $line['parkingTime_2'];
                $parkingPrice_2 = $line['parkingPrice_2'];
                $parkingPrice_2 = str_replace(".",",",$parkingPrice_2);

                $additionalTime_1 = $line['additionalTime_1'];
                $additionalPrice_1 = $line['additionalPrice_1'];
                $additionalPrice_1 = str_replace(".",",",$additionalPrice_1);

                $additionalTime_2 = $line['additionalTime_2'];
                $additionalPrice_2 = $line['additionalPrice_2'];
                $additionalPrice_2 = str_replace(".",",",$additionalPrice_2);

                $daily = $line['daily'];
                $daily = str_replace(".",",",$daily);
                
                $return = array(
                    'idParkingTimeAndPrices' => $idParkingTimeAndPrices,
                    'idSubparking' => $idSubparking,
                    'tolerancePeriod' => $tolerancePeriod,
                    'parkingTime_1' => $parkingTime_1,
                    'parkingPrice_1' => $parkingPrice_1,
                    'parkingTime_2' => $parkingTime_2,
                    'parkingPrice_2' => $parkingPrice_2,
                    'additionalTime_1' => $additionalTime_1,
                    'additionalPrice_1' => $additionalPrice_1,
                    'additionalTime_2' => $additionalTime_2,
                    'additionalPrice_2' => $additionalPrice_2,
                    'daily' => $daily
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