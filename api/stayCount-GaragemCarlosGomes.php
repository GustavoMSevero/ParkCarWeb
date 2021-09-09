<?php
// EXEMPLO GUSTAVO
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");

include_once("timeToMinutes.php");


function stayCount($idParkedVehicle, $licensePlate, $idParking) {

    $pdo = conectar();
    date_default_timezone_set('America/Sao_Paulo');
    // $weekDay = date('l'); // day of week

    try {
        // GET ENTRANCE DATA TO CALCULATE LENGTH OF STAY
        $getVehicleEntranceTime=$pdo->prepare("SELECT entrance FROM parkedVehicles WHERE licensePlate=:licensePlate AND vehicleParkStatus=1");
        $getVehicleEntranceTime->bindValue(":licensePlate", $licensePlate);
        $getVehicleEntranceTime->execute();
    
        while ($line=$getVehicleEntranceTime->fetch(PDO::FETCH_ASSOC)) {
            $entrance = $line['entrance'];
        }

        $getParkingName=$pdo->prepare("SELECT parkingName FROM parking 
                                        WHERE idParking=:idParking");
        $getParkingName->bindValue(":idParking", $idParking);
        $getParkingName->execute();

        while ($line=$getParkingName->fetch(PDO::FETCH_ASSOC)) {
            $parkingName = $line['parkingName'];
        }

        $getParkingTimeAndPrices=$pdo->prepare("SELECT * FROM timeAndPrices_1 
                                                WHERE idParking=:idParking");
        $getParkingTimeAndPrices->bindValue(":idParking", $idParking);
        $getParkingTimeAndPrices->execute();
        
        while ($line=$getParkingTimeAndPrices->fetch(PDO::FETCH_ASSOC)) {
            $parkingTime_1 = $line['parkingTime_1']; // 30 min
            $parkingPrice_1 = $line['parkingPrice_1']; // 6,00
            $parkingTime_2 = $line['parkingTime_2']; // 1 hora
            $parkingPrice_2 = $line['parkingPrice_2']; // 12,00
            $adicionalTime_1 = $line['adicionalTime_1']; // 30 min adicional
            $adicionalPrice_1 = $line['adicionalPrice_1']; // 5,00
            $adicionalTime_2 = $line['adicionalTime_2']; // 1 hora adicional
            $adicionalPrice_2 = $line['adicionalPrice_2']; // 10,00
            $daily = $line['daily'];
        }

        // $dayAndTimeEntry = date("2021-08-30 07:00:00");
        $dayAndTimeEntry = $entrance;
        $dayAndTimeEntrey_DateTime = new DateTime($dayAndTimeEntry);


        // $departureDayAndTime = date("2021-08-30 10:30:00");
        $departureDayAndTime = date("Y-m-d H:i:s");
        $departureDayAndTime_DateTime = new DateTime($departureDayAndTime);

        $difference = $dayAndTimeEntrey_DateTime ->diff($departureDayAndTime_DateTime );
        $return_time = $difference ->format('%H:%I:%S');

        $dayAndTimeEntryInMinutes = dateAndTimeToNumber($dayAndTimeEntry);
        $departureDayAndTimeMinutes = dateAndTimeToNumber($departureDayAndTime);

        $permanenceInMinutes = ($dayAndTimeEntryInMinutes - $departureDayAndTimeMinutes) * -1;

        $valueToPay = 0;

        // echo 'permanenceInMinutes '.$permanenceInMinutes.'<br>';
        if ($permanenceInMinutes <= 30) {
            // echo 'Pagar 30 min'.'<br>'; 6,00
            $valueToPay = $parkingPrice_1;
        } elseif ($permanenceInMinutes > 30 && $permanenceInMinutes <= 60) {
            // echo 'Pagar 1 hora'.'<br>'; 12,00
            $valueToPay = $parkingPrice_2;
        } elseif ($permanenceInMinutes > 60 && $permanenceInMinutes <= 90) {
            // echo 'Pagar 1:30'.'<br>'; 17,00
            $valueToPay = ($parkingPrice_2 + $adicionalPrice_1);
        } elseif ($permanenceInMinutes > 90 && $permanenceInMinutes <= 120) {
            // echo 'Pagar 2:00 horas'.'<br>'; 22,00
            $valueToPay = ($parkingPrice_2 + $adicionalPrice_2);
        } elseif ($permanenceInMinutes > 120 && $permanenceInMinutes <= 150) {
            // echo 'Pagar 2:30 horas '.'<br>'; 27,00
            $valueToPay = ($parkingPrice_2 + $adicionalPrice_2 + $adicionalPrice_1);
        } elseif ($permanenceInMinutes > 150 && $permanenceInMinutes <= 180) {
            // echo 'Pagar 3:00 horas '.'<br>'; 32,00
            $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 2);
        } elseif ($permanenceInMinutes > 180 && $permanenceInMinutes <= 210) {
            // echo 'Pagar 3:30 horas '.'<br>'; 37,00
            $valueToPay = $daily;
        }

        $updateLenghtAndValue=$pdo->prepare("UPDATE parkedVehicles SET lenghtOfStay=:lenghtOfStay, valuePaid=:valueToPay
                                        WHERE id=:idParkedVehicle");
        $updateLenghtAndValue->bindValue(":idParkedVehicle", $idParkedVehicle);
        $updateLenghtAndValue->bindValue(":lenghtOfStay", $return_time);
        $updateLenghtAndValue->bindValue(":valueToPay", $valueToPay);
        $updateLenghtAndValue->execute();
        

        $return = array(
            'entraceInMinutes' => $dayAndTimeEntryInMinutes,
            'departureDayAndTimeMinutes' => $departureDayAndTimeMinutes,
            'permanenceInMinutes' => $permanenceInMinutes, // Tempo de permanencia em minutos
            'permanenceFormated' => $return_time, // Tempo de permanencia formatado (00:00:00)
            'valueToPay' => $valueToPay, // Valor a ser pago
            'licensePlate' => $licensePlate,
            'idParking' => $idParking,
            'entraceDateAndTimeFormated' => $entrance, // Horário de entrada 00:00:00
            'departureDateAndTimeFormated' => $departureDayAndTime // Horário de saída 00:00:00
        );

        echo json_encode($return);

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

}

// $idParkedVehicle = 71;
// $licensePlate = 'IXW3620';
// $idParking = 15;

// stayCount($idParkedVehicle, $licensePlate, $idParking)

?>