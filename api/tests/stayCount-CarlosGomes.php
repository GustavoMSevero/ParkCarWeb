<?php
// EXEMPLO GUSTAVO
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");

include_once("timeToMinutes.php");

// function calc($veiculoValor, $valorHora) {
//     return $veiculoValor + $valorHora;
// }

// $veiculo = ''; // P=pequeno - M=moto - G=grande
// $veiculoValor;

// switch ($veiculo) { 
//     case 'G':
//         $veiculoValor = 2.50;
//         break;
//     case 'M':
//         $veiculoValor = 0.50;
//         break;
//     case 'P':
//         $veiculoValor = 1.50;
//         break;
//     default:
//         $veiculoValor = 0.0;
//         break;
// }

function stayCount($idClient, $licensePlate, $idParking) {

    $pdo = conectar();
    date_default_timezone_set('America/Sao_Paulo');
    // $weekDay = date('l'); // day of week
    
    // $idClient = 29;
    // $licensePlate = 'IXW3620';
    // $idParking = 15;
    $vehicleParkStatus = 1;

    try {
        // GET ENTRANCE DATA TO CALCULATE LENGTH OF STAY
        $getVehicleEntranceTime=$pdo->prepare("SELECT entrance FROM parkedVehicles WHERE licensePlate=:licensePlate AND vehicleParkStatus=:vehicleParkStatus");
        $getVehicleEntranceTime->bindValue(":licensePlate", $licensePlate);
        $getVehicleEntranceTime->bindValue(":vehicleParkStatus", $vehicleParkStatus);
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

        // // $dayAndTimeEntry = date("2021-08-30 16:00:00");
        // $dayAndTimeEntry = $entrance;
        // $dayAndTimeEntrey_DateTime = new DateTime($dayAndTimeEntry);

        // // $departureDayAndTime = date("2021-08-30 18:00:00");
        // $departureDayAndTime = date("Y-m-d H:i:s");
        // $departureDayAndTime_DateTime = new DateTime($departureDayAndTime);

        // $difference = $dayAndTimeEntrey_DateTime ->diff($departureDayAndTime_DateTime );
        // $return_time = $difference ->format('%H:%I:%S');

        // $dayAndTimeEntryInMinutes = dateAndTimeToNumber($dayAndTimeEntry);
        // $departureDayAndTimeMinutes = dateAndTimeToNumber($departureDayAndTime);

        // $permanenceInMinutes = ($dayAndTimeEntryInMinutes - $departureDayAndTimeMinutes) * -1;

        $dayAndTimeEntry = date("2021-08-30 07:00:00");
        // $dayAndTimeEntry = $entrance;
        $dayAndTimeEntrey_DateTime = new DateTime($dayAndTimeEntry);


        $departureDayAndTime = date("2021-08-30 10:30:00");
        // $departureDayAndTime = date("Y-m-d H:i:s");
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
        // elseif ($permanenceInMinutes > 210 && $permanenceInMinutes <= 240) {
        //     // echo 'Pagar 4:00 horas '.'<br>'; 42,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 3);
        // } elseif ($permanenceInMinutes > 240 && $permanenceInMinutes <= 270) {
        //     // echo 'Pagar 4:30 horas '.'<br>'; 47,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 3) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 270 && $permanenceInMinutes <= 300) {
        //     // echo 'Pagar 5:00 horas '.'<br>'; 52,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 4);
        // } elseif ($permanenceInMinutes > 300 && $permanenceInMinutes <= 330) {
        //     // echo 'Pagar 5:30 horas '.'<br>'; 57,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 4) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 330 && $permanenceInMinutes <= 360) {
        //     // echo 'Pagar 6:00 horas '.'<br>'; 62,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 5);
        // } elseif ($permanenceInMinutes > 360 && $permanenceInMinutes <= 390) {
        //     // echo 'Pagar 6:30 horas '.'<br>'; 67,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 5) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 390 && $permanenceInMinutes <= 420) {
        //     // echo 'Pagar 7:00 horas '.'<br>'; 72,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 6);
        // } elseif ($permanenceInMinutes > 420 && $permanenceInMinutes <= 450) {
        //     // echo 'Pagar 7:30 horas '.'<br>'; 77,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 6) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 450 && $permanenceInMinutes <= 480) {
        //     // echo 'Pagar 8:00 horas '.'<br>'; 82,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 7);
        // } elseif ($permanenceInMinutes > 480 && $permanenceInMinutes <= 510) {
        //     // echo 'Pagar 8:30 horas '.'<br>'; 87,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 7) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 510 && $permanenceInMinutes <= 540) {
        //     // echo 'Pagar 9:00 horas '.'<br>'; 92,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 8);
        // } elseif ($permanenceInMinutes > 540 && $permanenceInMinutes <= 570) {
        //     // echo 'Pagar 9:30 horas '.'<br>'; 97,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 8) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 570 && $permanenceInMinutes <= 600) {
        //     // echo 'Pagar 10:00 horas '.'<br>'; 102,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 9);
        // } elseif ($permanenceInMinutes > 600 && $permanenceInMinutes <= 630) {
        //     // echo 'Pagar 10:30 horas '.'<br>'; 107,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 9) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 630 && $permanenceInMinutes <= 660) {
        //     // echo 'Pagar 11:00 horas '.'<br>'; 112,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 10);
        // } elseif ($permanenceInMinutes > 660 && $permanenceInMinutes <= 690) {
        //     // echo 'Pagar 11:30 horas '.'<br>'; 117,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 10) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 690 && $permanenceInMinutes <= 720) {
        //     // echo 'Pagar 12:00 horas '.'<br>'; 122,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 11);
        // } elseif ($permanenceInMinutes > 720 && $permanenceInMinutes <= 750) {
        //     // echo 'Pagar 12:30 horas '.'<br>'; 127,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 11) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 750 && $permanenceInMinutes <= 780) {
        //     // echo 'Pagar 13:00 horas '.'<br>'; 132,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 12);
        // } elseif ($permanenceInMinutes > 780 && $permanenceInMinutes <= 810) {
        //     // echo 'Pagar 13:30 horas '.'<br>'; 137,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 12) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 810 && $permanenceInMinutes <= 840) {
        //     // echo 'Pagar 14:00 horas '.'<br>'; 142,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 13);
        // } elseif ($permanenceInMinutes > 840 && $permanenceInMinutes <= 870) {
        //     // echo 'Pagar 14:30 horas '.'<br>'; 147,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 13) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 870 && $permanenceInMinutes <= 900) {
        //     // echo 'Pagar 15:00 horas '.'<br>'; 152,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 14);
        // } elseif ($permanenceInMinutes > 900 && $permanenceInMinutes <= 930) {
        //     // echo 'Pagar 15:30 horas '.'<br>'; 157,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 14) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 930 && $permanenceInMinutes <= 960) {
        //     // echo 'Pagar 16:00 horas '.'<br>'; 162,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 15);
        // } elseif ($permanenceInMinutes > 960 && $permanenceInMinutes <= 990) {
        //     // echo 'Pagar 16:30 horas '.'<br>'; 167,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 15) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 990 && $permanenceInMinutes <= 1020) {
        //     // echo 'Pagar 17:00 horas '.'<br>'; 172,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 16);
        // } elseif ($permanenceInMinutes > 1020 && $permanenceInMinutes <= 1050) {
        //     // echo 'Pagar 17:30 horas '.'<br>'; 177,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 16) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1050 && $permanenceInMinutes <= 1080) {
        //     // echo 'Pagar 18:00 horas '.'<br>'; 182,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 17);
        // } elseif ($permanenceInMinutes > 1080 && $permanenceInMinutes <= 1110) {
        //     // echo 'Pagar 18:30 horas '.'<br>'; 187,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 17) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1110 && $permanenceInMinutes <= 1140) {
        //     // echo 'Pagar 19:00 horas '.'<br>'; 192,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 18);
        // } elseif ($permanenceInMinutes > 1140 && $permanenceInMinutes <= 1170) {
        //     // echo 'Pagar 19:30 horas '.'<br>'; 197,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 18) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1170 && $permanenceInMinutes <= 1200) {
        //     // echo 'Pagar 20:00 horas '.'<br>'; 202,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 19);
        // } elseif ($permanenceInMinutes > 1200 && $permanenceInMinutes <= 1230) {
        //     // echo 'Pagar 20:30 horas '.'<br>'; 207,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 19) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1230 && $permanenceInMinutes <= 1260) {
        //     // echo 'Pagar 21:00 horas '.'<br>'; 212,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 20);
        // } elseif ($permanenceInMinutes > 1260 && $permanenceInMinutes <= 1290) {
        //     // echo 'Pagar 21:30 horas '.'<br>'; 217,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 20) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1290 && $permanenceInMinutes <= 1320) {
        //     // echo 'Pagar 22:00 horas '.'<br>'; 222,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 21);
        // } elseif ($permanenceInMinutes > 1320 && $permanenceInMinutes <= 1350) {
        //     // echo 'Pagar 22:30 horas '.'<br>'; 227,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 21) + $adicionalPrice_1;
        // } elseif ($permanenceInMinutes > 1350 && $permanenceInMinutes <= 1380) {
        //     // echo 'Pagar 23:00 horas '.'<br>'; 232,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 22);
        // } elseif ($permanenceInMinutes > 1380 && $permanenceInMinutes <= 1410) {
        //     // echo 'Pagar 23:30 horas '.'<br>'; 237,00
        //     $valueToPay = $parkingPrice_2 + ($adicionalPrice_2 * 22) + $adicionalPrice_1;
        // } 
        // echo 'valueToPay '.$valueToPay.'<br>';

        $return = array(
            'entraceInMinutes' => $dayAndTimeEntryInMinutes,
            'departureDayAndTimeMinutes' => $departureDayAndTimeMinutes,
            'permanenceInMinutes' => $permanenceInMinutes,
            'permanenceFormated' => $return_time,
            'valueToPay' => $valueToPay,
            'idClient' => $idClient,
            'licensePlate' => $licensePlate,
            'idParking' => $idParking,
            'entraceDateAndTimeFormated' => $entrance,
            'departureDateAndTimeFormated' => $departureDayAndTime
        );

        echo json_encode($return);

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

}

?>