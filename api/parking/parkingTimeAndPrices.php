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
        @$tolerancePeriod = $data->tolerancePeriod;
        if ($tolerancePeriod == '') {
            $tolerancePeriod = null;
        }
        $parkingTime_01 = $data->parkingTime_01;
        $timeValue_01 = $data->timeValue_01;
        $parkingTime_02 = $data->parkingTime_02;
        $timeValue_02 = $data->timeValue_02;

        $addPeriod_01 = $data->addPeriod_01;
        $addValue_01 = $data->addValue_01;

        $addPeriod_02 = $data->addPeriod_02;
        $addValue_02 = $data->addValue_02;

        $daily = $data->daily;

        $idParking = $data->idParking;
        $idParkingBranch = $data->idParkingBranch;

        $timeValue_01 = str_replace(",",".",$timeValue_01);
        $timeValue_02 = str_replace(",",".",$timeValue_02);
        $addValue_01 = str_replace(",",".",$addValue_01);
        $addValue_02 = str_replace(",",".",$addValue_02);
        $daily = str_replace(",",".",$daily);

        try {
            $registerTimeAndPrices=$pdo->prepare("INSERT INTO timeAndPrices_1 (idParkingTimeAndPrices, idParking, idSubparking, tolerancePeriod, parkingTime_1, parkingPrice_1,
                                                    parkingTime_2, parkingPrice_2, adicionalTime_1, adicionalPrice_1, adicionalTime_2, adicionalPrice_2, daily) 
                                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $registerTimeAndPrices->bindValue(1, NULL);
            $registerTimeAndPrices->bindValue(2, $idParking);
            $registerTimeAndPrices->bindValue(3, $idParkingBranch);

            $registerTimeAndPrices->bindValue(4, $tolerancePeriod);
            $registerTimeAndPrices->bindValue(5, $parkingTime_01);
            $registerTimeAndPrices->bindValue(6, $timeValue_01);
            $registerTimeAndPrices->bindValue(7, $parkingTime_02);
            $registerTimeAndPrices->bindValue(8, $timeValue_02);

            $registerTimeAndPrices->bindValue(9, $addPeriod_01);
            $registerTimeAndPrices->bindValue(10, $addValue_01);
            $registerTimeAndPrices->bindValue(11, $addPeriod_02);
            $registerTimeAndPrices->bindValue(12, $addValue_02);
            $registerTimeAndPrices->bindValue(13, $daily);
            $registerTimeAndPrices->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get parking time and prices':
        // print_r($data);
        $idParking = $_GET['id_parking'];
        $idSubparking = $_GET['idParkingBranch'];

        try {
            $getParkingTimeAndPrices=$pdo->prepare("SELECT * FROM timeAndPrices_1 
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

                $adicionalTime_1 = $line['adicionalTime_1'];
                $adicionalPrice_1 = $line['adicionalPrice_1'];
                $adicionalPrice_1 = str_replace(".",",",$adicionalPrice_1);

                $adicionalTime_2 = $line['adicionalTime_2'];
                $adicionalPrice_2 = $line['adicionalPrice_2'];
                $adicionalPrice_2 = str_replace(".",",",$adicionalPrice_2);

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
                    'adicionalTime_1' => $adicionalTime_1,
                    'adicionalPrice_1' => $adicionalPrice_1,
                    'adicionalTime_2' => $adicionalTime_2,
                    'adicionalPrice_2' => $adicionalPrice_2,
                    'daily' => $daily
                );

            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get time and prices to edit':
        // print_r($data);
        $idParking = $_GET['idParking'];
        $idSubparking = $_GET['idSubparking'];

        try {
            $getParkingTimeAndPrices=$pdo->prepare("SELECT * FROM timeAndPrices_1
                                                WHERE idParking=:idParking 
                                                AND idSubparking=:idSubparking");
            $getParkingTimeAndPrices->bindValue(":idParking", $idParking);                
            $getParkingTimeAndPrices->bindValue(":idSubparking", $idSubparking);
            $getParkingTimeAndPrices->execute();

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

                $adicionalTime_1 = $line['adicionalTime_1'];
                $adicionalPrice_1 = $line['adicionalPrice_1'];
                $adicionalPrice_1 = str_replace(".",",",$adicionalPrice_1);

                $adicionalTime_2 = $line['adicionalTime_2'];
                $adicionalPrice_2 = $line['adicionalPrice_2'];
                $adicionalPrice_2 = str_replace(".",",",$adicionalPrice_2);

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
                    'adicionalTime_1' => $adicionalTime_1,
                    'adicionalPrice_1' => $adicionalPrice_1,
                    'adicionalTime_2' => $adicionalTime_2,
                    'adicionalPrice_2' => $adicionalPrice_2,
                    'daily' => $daily
                );

            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'update time and prices':
        // print_r($data);
        // [idParkingTimeAndPrices] => 1
        // [idSubparking] => 15
        // [parkingTime_1] => 30
        // [parkingPrice_1] => 6,00
        // [parkingTime_2] => 60
        // [parkingPrice_2] => 12,00
        // [adicionalTime_1] => 30
        // [adicionalPrice_1] => 5,00
        // [adicionalTime_2] => 60
        // [adicionalPrice_2] => 10,00
        // [daily] => 35,00
        // [tolerancePeriod] => 15

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

        $adicionalTime_1 = $data->adicionalTime_1;
        $adicionalPrice_1 = $data->adicionalPrice_1;

        $adicionalTime_2 = $data->adicionalTime_2;
        $adicionalPrice_2 = $data->adicionalPrice_2;

        $daily = $data->daily;
        
        $parkingPrice_1 = str_replace(",",".",$parkingPrice_1);
        $parkingPrice_2 = str_replace(",",".",$parkingPrice_2);

        $adicionalPrice_1 = str_replace(",",".",$adicionalPrice_1);
        $adicionalPrice_2 = str_replace(",",".",$adicionalPrice_2);
        $daily = str_replace(",",".",$daily);


        try {
            $updateTimeAndPrices=$pdo->prepare("UPDATE timeAndPrices_1
                                                SET parkingTime_1=:parkingTime_1, 
                                                tolerancePeriod=:tolerancePeriod,
                                                parkingPrice_1=:parkingPrice_1,
                                                parkingTime_2=:parkingTime_2, 
                                                parkingPrice_2=:parkingPrice_2,

                                                adicionalTime_1=:adicionalTime_1,
                                                adicionalPrice_1=:adicionalPrice_1,
                                                adicionalTime_2=:adicionalTime_2,
                                                adicionalPrice_2=:adicionalPrice_2,
                                                daily=:daily
                                                WHERE idParkingTimeAndPrices=:idParkingTimeAndPrices");
            $updateTimeAndPrices->bindValue(":tolerancePeriod", $tolerancePeriod);                
            $updateTimeAndPrices->bindValue(":parkingTime_1", $parkingTime_1);                
            $updateTimeAndPrices->bindValue(":parkingPrice_1", $parkingPrice_1);
            $updateTimeAndPrices->bindValue(":parkingTime_2", $parkingTime_2);
            $updateTimeAndPrices->bindValue(":parkingPrice_2", $parkingPrice_2);
            $updateTimeAndPrices->bindValue(":adicionalTime_1", $adicionalTime_1);
            $updateTimeAndPrices->bindValue(":adicionalPrice_1", $adicionalPrice_1);
            $updateTimeAndPrices->bindValue(":adicionalTime_2", $adicionalTime_2);
            $updateTimeAndPrices->bindValue(":adicionalPrice_2", $adicionalPrice_2);
            $updateTimeAndPrices->bindValue(":daily", $daily);
            $updateTimeAndPrices->bindValue(":idParkingTimeAndPrices", $idParkingTimeAndPrices);                
            $updateTimeAndPrices->execute();


        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;
    
    default:
        # code...
        break;
}


?>