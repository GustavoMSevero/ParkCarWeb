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

        $parkingTime_3 = $data->parkingTime_3;
        $parkingPrice_3 = $data->parkingPrice_3;

        $parkingTime_4 = $data->parkingTime_4;
        $parkingPrice_4 = $data->parkingPrice_4;

        $daily = $data->daily;

        $toleranceDay = $data->toleranceDay;
        $addPeriod = $data->addPeriod;
        $addValue = $data->addValue;
        $dailyPeriod = $data->dailyPeriod;

        $idParking = $data->idParking;
        $idParkingBranch = $data->idParkingBranch;

        $parkingPrice_1 = str_replace(",",".",$parkingPrice_1);
        $parkingPrice_2 = str_replace(",",".",$parkingPrice_2);
        $parkingPrice_3 = str_replace(",",".",$parkingPrice_3);
        $parkingPrice_4 = str_replace(",",".",$parkingPrice_4);
        $daily = str_replace(",",".",$daily);

        try {
            $checkParkingTimeNadPricesExists=$pdo->prepare("SELECT * FROM timeAndPrices WHERE idParking=:idParking AND idSubparking=:idSubparking");
            $checkParkingTimeNadPricesExists->bindValue(":idParking", $idParking);
            $checkParkingTimeNadPricesExists->bindValue(":idSubparking", $idSubparking);
            $checkParkingTimeNadPricesExists->execute();
            $qtd = $checkParkingTimeNadPricesExists->rowCount();

            if ($qtd != 0) {

                $updateTimeAndPrices=$pdo->prepare("UPDATE timeAndPrices SET toleranceDay=:toleranceDay, tolerancePeriod=:tolerancePeriod, parkingTime_1=:parkingTime_1, parkingPrice_1=:parkingPrice_1,
                                                    parkingTime_2=:parkingTime_2, parkingPrice_2=:parkingPrice_2, parkingTime_3=:parkingTime_3, parkingPrice_3=:parkingPrice_3,
                                                    parkingTime_4=:parkingTime_4, parkingPrice_4=:parkingPrice_4, addPeriod=:addPeriod, addValue=:addValue, dailyPeriod=:dailyPeriod, daily=:daily
                                                    WHERE idParkingTimeAndPrices=:idParkingTimeAndPrices");
                $updateTimeAndPrices->bindValue(":toleranceDay", $toleranceDay); 
                $updateTimeAndPrices->bindValue(":tolerancePeriod", $tolerancePeriod); 
                $updateTimeAndPrices->bindValue(":parkingTime_1", $parkingTime_1); 
                $updateTimeAndPrices->bindValue(":parkingPrice_1", $parkingPrice_1); 
                $updateTimeAndPrices->bindValue(":parkingTime_2", $parkingTime_2); 
                $updateTimeAndPrices->bindValue(":parkingPrice_2", $parkingPrice_2); 
                $updateTimeAndPrices->bindValue(":parkingTime_3", $parkingTime_3); 
                $updateTimeAndPrices->bindValue(":parkingPrice_3", $parkingPrice_3); 
                $updateTimeAndPrices->bindValue(":parkingTime_4", $parkingTime_4); 
                $updateTimeAndPrices->bindValue(":parkingPrice_4", $parkingPrice_4); 
                $updateTimeAndPrices->bindValue(":addPeriod", $addPeriod);
                $updateTimeAndPrices->bindValue(":addValue", $addValue); 
                $updateTimeAndPrices->bindValue(":dailyPeriod", $dailyPeriod); 
                $updateTimeAndPrices->bindValue(":daily", $daily); 
                $updateTimeAndPrices->bindValue(":idParkingTimeAndPrices", $idParkingTimeAndPrices); 
                $updateTimeAndPrices->execute();

                $msg = "Dados atualizados com sucesso!";
                
                $return = array(
                    'msg' => $msg
                );

                echo json_encode($return);

            } else {

                $registerTimeAndPrices=$pdo->prepare("INSERT INTO timeAndPrices (idParkingTimeAndPrices, idParking, idSubparking, toleranceDay, tolerancePeriod, parkingTime_1, parkingPrice_1,
                                                    parkingTime_2, parkingPrice_2, parkingTime_3, parkingPrice_3, parkingTime_4, parkingPrice_4, addPariod, addValue, dailyPeriod, daily) 
                                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $registerTimeAndPrices->bindValue(1, NULL);
                $registerTimeAndPrices->bindValue(2, $idParking);
                $registerTimeAndPrices->bindValue(3, $idParkingBranch);

                $registerTimeAndPrices->bindValue(4, $toleranceDay);
                $registerTimeAndPrices->bindValue(5, $tolerancePeriod);
                $registerTimeAndPrices->bindValue(6, $parkingTime_1);
                $registerTimeAndPrices->bindValue(7, $parkingPrice_1);
                $registerTimeAndPrices->bindValue(8, $parkingTime_2);
                $registerTimeAndPrices->bindValue(9, $parkingPrice_2);

                $registerTimeAndPrices->bindValue(10, $parkingTime_3);
                $registerTimeAndPrices->bindValue(11, $parkingPrice_3);
                $registerTimeAndPrices->bindValue(12, $parkingTime_4);
                $registerTimeAndPrices->bindValue(13, $parkingPrice_4);
                $registerTimeAndPrices->bindValue(14, $addPeriod);
                $registerTimeAndPrices->bindValue(15, $addValue);
                $registerTimeAndPrices->bindValue(16, $dailyPeriod);
                $registerTimeAndPrices->bindValue(17, $daily);
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

                $toleranceDay = $line['toleranceDay'];
                $tolerancePeriod = $line['tolerancePeriod'];
                $parkingTime_1 = $line['parkingTime_1'];
                $parkingPrice_1 = $line['parkingPrice_1'];
                $parkingPrice_1 = str_replace(".",",",$parkingPrice_1);

                $parkingTime_2 = $line['parkingTime_2'];
                $parkingPrice_2 = $line['parkingPrice_2'];
                $parkingPrice_2 = str_replace(".",",",$parkingPrice_2);

                $parkingTime_3 = $line['parkingTime_3'];
                $parkingPrice_3 = $line['parkingPrice_3'];
                $parkingPrice_3 = str_replace(".",",",$parkingPrice_3);

                $parkingTime_4 = $line['parkingTime_4'];
                $parkingPrice_4 = $line['parkingPrice_4'];
                $parkingPrice_4 = str_replace(".",",",$parkingPrice_4);

                $addPeriod = $line['addPeriod'];
                $addValue = $line['addValue'];
                $addValue = str_replace(".",",",$addValue);

                $dailyPeriod = $line['dailyPeriod'];
                $daily = $line['daily'];
                $daily = str_replace(".",",",$daily);
                
                $return = array(
                    'idParkingTimeAndPrices' => $idParkingTimeAndPrices,
                    'idSubparking' => $idSubparking,
                    'toleranceDay' => $toleranceDay,
                    'tolerancePeriod' => $tolerancePeriod,
                    'parkingTime_1' => $parkingTime_1,
                    'parkingPrice_1' => $parkingPrice_1,
                    'parkingTime_2' => $parkingTime_2,
                    'parkingPrice_2' => $parkingPrice_2,
                    'parkingTime_3' => $parkingTime_3,
                    'parkingPrice_3' => $parkingPrice_3,
                    'parkingTime_4' => $parkingTime_4,
                    'parkingPrice_4' => $parkingPrice_4,
                    'addPeriod' => $addPeriod,
                    'addValue' => $addValue,
                    'dailyPeriod' => $dailyPeriod,
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