<?php
// EXEMPLO GUSTAVO
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");

include_once("functions/timeToMinutes.php");


$pdo = conectar();
date_default_timezone_set('America/Sao_Paulo');
$dayOfTheWeek = date('l'); // day of week

$daysOfTheWeek = array(
    "Monday"=>2, 
    "Tuesday"=>3, 
    "Wednesday"=>4, 
    "Thursday"=>5, 
    "Friday"=>6, 
    "Saturday"=>7, 
    "Sunday"=>1
);

$daysOfTheWeekInvert = array(
    1=>"Sunday",
    2=>"Monday",
    3=>"Tuesday",
    4=>"Wednesday",
    5=>"Thursday",
    6=>"Friday",
    7=>"Saturday",
);

echo "Hoje é ".$dayOfTheWeek;
echo "<br>";
$numberOfTheWeek = $daysOfTheWeek[$dayOfTheWeek];
echo $numberOfTheWeek;
echo "<br>";

$licensePlate = "BBB1111";
$idParking = 15;

// $toleranceTime = 16; // minutes

// Part 1: identify client by license plate
//         check if client is diarist or monthly payer
$search_licensePlate_in_parking_database=$pdo->prepare("SELECT * FROM parkingCustomer WHERE licensePlate=:licensePlate AND idParking=:idParking");
$search_licensePlate_in_parking_database->bindValue(":licensePlate", $licensePlate);
$search_licensePlate_in_parking_database->bindValue(":idParking", $idParking);
$search_licensePlate_in_parking_database->execute();

$qtd = $search_licensePlate_in_parking_database->rowCount();

if ($qtd > 0) {
    echo "É cliente do estacionameto";
    echo "<br>";
    while ($line=$search_licensePlate_in_parking_database->fetch(PDO::FETCH_ASSOC)) {
        $licensePlate = $line['licensePlate'];
        $contractType = $line['contractType'];
        $contractEndDate = $line['contractEndDate'];
        $parkingFloor = $line['parkingFloor'];
        $codFloor = $line['codFloor'];
        $box = $line['box'];
        $value = $line['value'];
    }

    // Check if payment is up to date
} else {
    echo "Não é cliente do estacionamento";
    echo "<br>";

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

    echo "Tolerância ".$tolerancePeriod ." min";
    echo "<br>";
    
    echo $parkingTime_1." - ".$parkingPrice_1;
    echo "<br>";
    
    echo $parkingTime_2." - ".$parkingPrice_2;
    echo "<br>";
    
    echo $parkingTime_3." - ".$parkingPrice_3;
    echo "<br>";
    
    echo $parkingTime_4." - ".$parkingPrice_4;
    echo "<br>";

    echo $addPeriod." - ".$addValue;
    echo "<br>";

    $addValue = intval($addValue);
    $parkingTime_4 = intval($parkingTime_4);
    $addPeriod = intval($addPeriod);

    $excedente = $parkingTime_4 + $addPeriod;

    echo $excedente." - ".$parkingPrice_4+$addValue;
    echo "<br>";
    
    echo $dailyPeriod." - ".$daily;
    echo "<br>";

    $dayOfTheWeek;
    $numberOfTheWeek;
    
    $tD = (int)$toleranceDay;
    $numberOfToleranceDayParking = $daysOfTheWeekInvert[$tD];
    echo "Dia de tolerância é ".$numberOfToleranceDayParking;
    echo "<br>";
    
    // Part 2: check if it's tolerance day
    if ($toleranceDay == $numberOfTheWeek) {
        echo 'É dia de tolerância';
    } else {
        echo 'Não é dia de tolerância';
        echo "<br>";
    }

    
    $dayAndTimeEntry = date("2022-08-31 13:00:00");
    // $dayAndTimeEntry = $entrance;
    echo "<br>";
    echo "entrada ".$dayAndTimeEntry."<br>";
    $dayAndTimeEntrey_DateTime = new DateTime($dayAndTimeEntry);
    
    
    $departureDayAndTime = date("2021-08-30 18:00:00");
    // $departureDayAndTime = date("Y-m-d H:i:s");
    echo "saída ".$departureDayAndTime."<br>";
    echo "<br>";
    $departureDayAndTime_DateTime = new DateTime($departureDayAndTime);
    
    $difference = $dayAndTimeEntrey_DateTime ->diff($departureDayAndTime_DateTime );
    $return_time = $difference ->format('%H:%I:%S');
    
    $dayAndTimeEntryInMinutes = dateAndTimeToNumber($dayAndTimeEntry);
    $departureDayAndTimeMinutes = dateAndTimeToNumber($departureDayAndTime);
    
    $permanenceInMinutes = ($dayAndTimeEntryInMinutes - $departureDayAndTimeMinutes) * -1;

    echo "Permanência ".$permanenceInMinutes." min";
    echo "<br>";
    
    $valueToPay = 0;

    $parkingTime_4 = intval($parkingTime_4);
    $addPeriod = intval($addPeriod);
    $excedente = $parkingTime_4 + $addPeriod;

    if ($permanenceInMinutes <= $tolerancePeriod) {
        echo "Dentro da tolerância. Isento de pagamento";
    //                                                                              60
    } elseif ($permanenceInMinutes > $tolerancePeriod && $permanenceInMinutes < $parkingTime_2) { 
        // se o tempo de tolerância acabou, paga
        echo $parkingTime_1."<br>";
        echo "Paga ".$parkingPrice_1;
    //                                        60                                    120
    } elseif ($permanenceInMinutes >= $parkingTime_2 && $permanenceInMinutes < $parkingTime_3) {
        echo $parkingTime_2."<br>";
        echo "Paga ".$parkingPrice_2;
    //                                      120                                 180 
    } elseif ($permanenceInMinutes >= $parkingTime_3 && $permanenceInMinutes < $parkingTime_4) {
        echo $parkingTime_3."<br>";
        echo "Paga ".$parkingPrice_3;
    //                                     240                                  300
    } elseif ($permanenceInMinutes >= $parkingTime_4 && $permanenceInMinutes < $excedente) {
        echo $parkingTime_4."<br>";
        echo "Paga ".$parkingPrice_4;
    //                                     240                                  300
    } elseif ($permanenceInMinutes >= $excedente && $permanenceInMinutes < $dailyPeriod) {
        echo $parkingTime_4." e ".$addPeriod."<br>";
        echo "Paga ".$parkingPrice_4 + $addValue;
        
    } elseif ($permanenceInMinutes >= $dailyPeriod) {
        echo "Cobrar diária ".$daily;
    }

}







?>