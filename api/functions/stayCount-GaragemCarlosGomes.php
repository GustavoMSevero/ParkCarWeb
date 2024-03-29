<?php
// EXEMPLO GUSTAVO
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");

include_once("timeToMinutes.php");


function stayCount($idParkedVehicle, $licensePlate, $idParking) {

    $pdo = conectar();
    date_default_timezone_set('America/Sao_Paulo');
    $weekDay = date('l'); // day of week

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

        $get_time_and_prices_from_parking=$pdo->prepare("SELECT * FROM timeAndPrices WHERE idParking=:idParking");
        $get_time_and_prices_from_parking->bindValue(":idParking", $idParking);
        $get_time_and_prices_from_parking->execute();
        
        while ($line=$get_time_and_prices_from_parking->fetch(PDO::FETCH_ASSOC)) {
            $toleranceDay = $line['toleranceDay'];
            $tolerancePeriod = $line['tolerancePeriod'];
            
            $parkingTime_1 = $line['parkingTime_1'];
            $parkingPrice_1 = $line['parkingPrice_1'];
            
            $parkingTime_2 = $line['parkingTime_2'];
            $parkingPrice_2 = $line['parkingPrice_2'];
            
            $parkingTime_3 = $line['parkingTime_3'];
            $parkingPrice_3 = $line['parkingPrice_3'];
            
            $parkingTime_4 = $line['parkingTime_4'];
            $parkingPrice_4 = $line['parkingPrice_4'];
    
            $addPeriod = $line['addPeriod'];
            $addValue = $line['addValue'];
            
            $dailyPeriod = $line['dailyPeriod'];
            $daily = $line['daily'];
        }


        // $entrance = date("2022-09-10 10:00:00");
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

        $parkingTime_4 = intval($parkingTime_4);
        $addPeriod = intval($addPeriod);
        $excedente = $parkingTime_4 + $addPeriod;

        if ($permanenceInMinutes <= $tolerancePeriod) {
        //                                                                              60
        } elseif ($permanenceInMinutes > $tolerancePeriod && $permanenceInMinutes < $parkingTime_2) { 
            $valueToPay = $parkingPrice_1;
        //                                        60                                    120
        } elseif ($permanenceInMinutes >= $parkingTime_2 && $permanenceInMinutes < $parkingTime_3) {
           $valueToPay = $parkingPrice_2;
        //                                      120                                 180 
        } elseif ($permanenceInMinutes >= $parkingTime_3 && $permanenceInMinutes < $parkingTime_4) {
            $valueToPay = $parkingPrice_3;
        //                                     240                                  300
        } elseif ($permanenceInMinutes >= $parkingTime_4 && $permanenceInMinutes < $excedente) {
            $valueToPay = $parkingPrice_4;
        //                                     240                                  300
        } elseif ($permanenceInMinutes >= $excedente && $permanenceInMinutes < $dailyPeriod) {
            $valueToPay = $parkingPrice_4 + $addValue;
            
        } elseif ($permanenceInMinutes >= $dailyPeriod) {
            $valueToPay = $daily;
        }

        $updateLenghtAndValue=$pdo->prepare("UPDATE parkedVehicles SET lenghtOfStay=:lenghtOfStay, valuePaid=:valueToPay
                                        WHERE id=:idParkedVehicle");
        $updateLenghtAndValue->bindValue(":idParkedVehicle", $idParkedVehicle);
        $updateLenghtAndValue->bindValue(":lenghtOfStay", $return_time);
        $updateLenghtAndValue->bindValue(":valueToPay", $valueToPay);
        $updateLenghtAndValue->execute();

        //GET IDCLIENT BY LICENSEPLATE (está pegando o id do propritário do veículo)
        $getIdClient=$pdo->prepare("SELECT idClient FROM clientVehicle WHERE licensePlate=:licensePlate");
        $getIdClient->bindValue(":licensePlate", $licensePlate);
        $getIdClient->execute();

        while ($line=$getIdClient->fetch(PDO::FETCH_ASSOC)) {
            $idClient = $line['idClient'];
        }

        $getcreditsBalance=$pdo->prepare("SELECT creditValue FROM credits WHERE idClient=:idClient");
        $getcreditsBalance->bindValue(":idClient", $idClient);
        $getcreditsBalance->execute();

        while ($line=$getcreditsBalance->fetch(PDO::FETCH_ASSOC)) {
            $creditValue = $line['creditValue'];
        }

        // Debit value paid from credits
        $newCreditValue = $creditValue - $valueToPay;

        $debitValueFromCredits=$pdo->prepare("UPDATE credits SET creditValue=:creditValue WHERE idClient=:idClient");
        $debitValueFromCredits->bindValue(":creditValue", $newCreditValue);
        $debitValueFromCredits->bindValue(":idClient", $idClient);
        $debitValueFromCredits->execute();

        // UPDATE AMOUNTPAID ON TICKET TABLE
        $updateValueToPayTicket=$pdo->prepare("UPDATE ticket SET amountPaid=:valueToPay WHERE licensePlate=:licensePlate
                                            ORDER BY id_ticket DESC LIMIT 1");
        $updateValueToPayTicket->bindValue(":licensePlate", $licensePlate);
        $updateValueToPayTicket->bindValue(":valueToPay", $valueToPay);
        $updateValueToPayTicket->execute();
        

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

// $idParkedVehicle = 10;
// $licensePlate = 'IXW3620';
// $idParking = 15;

// stayCount($idParkedVehicle, $licensePlate, $idParking);

?>