<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("con.php");

function stayCount($idClient, $licensePlate, $idParking) {

    $pdo = conectar();
    date_default_timezone_set('America/Sao_Paulo');
    $weekDay = date('l'); // day of week
    
    $idClient;
    $licensePlate;
    $idParking;

    try {
        // GET ENTRANCE DATA TO CALCULATE LENGTH OF STAY
        $getVehicleEntranceTime=$pdo->prepare("SELECT entrance FROM parkedVehicles WHERE licensePlate=:licensePlate");
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

        $getParkingTimeAndPrices=$pdo->prepare("SELECT * FROM parkingTimeAndPrices 
                                                WHERE idParking=:idParking");
        $getParkingTimeAndPrices->bindValue(":idParking", $idParking);
        $getParkingTimeAndPrices->execute();
        
        while ($line=$getParkingTimeAndPrices->fetch(PDO::FETCH_ASSOC)) {
            $tolerancePeriod = $line['tolerancePeriod'];
            $parkingTime_01 = $line['parkingTime_01'];
            $parkingTime_02 = $line['parkingTime_02'];
            $parkingTime_03 = $line['parkingTime_03'];
            $parkingTime_04 = $line['parkingTime_04'];
            $parkingTime_05 = $line['parkingTime_05'];
            $parkingPrice_01 = $line['parkingPrice_01'];
            $parkingPrice_02 = $line['parkingPrice_02'];
            $parkingPrice_03 = $line['parkingPrice_03'];
            $parkingPrice_04 = $line['parkingPrice_04'];
            $parkingPrice_05 = $line['parkingPrice_05'];
            $adicionalPrice = $line['adicionalPrice'];
            $toleranceDay = $line['toleranceDay'];
            $toleranceTimeDayOfWeek = $line['toleranceTimeDayOfWeek'];
        }
        
        $departureTime = date("Y-m-d H:i:s");
        $start_t = new DateTime($entrance);
        $current_t = new DateTime($departureTime);
        $difference = $start_t ->diff($current_t );
        $return_time = $difference ->format('%H:%I:%S');
    
        $dayOfWeek = ['Monday'=>'Segunda','Tuesday'=> 'Terça','Wednesday'=> 'Quarta','Thursday'=> 'Quinta',
            'Friday'=> 'Sexta','Saturday'=> 'Sábado','Sunday'=> 'Domingo'];
        
        // $entrance = '2021-08-26 10:00:00'; // teste
        // $start_t = new DateTime($entrance); // teste
        // $departureTime = '2021-08-26 12:00:00'; // teste
        // $current_t = new DateTime($departureTime); // teste
        // $difference = $start_t ->diff($current_t ); // teste
        // $return_time = $difference ->format('%H:%I:%S'); // teste

        $toleranceDay = 'Quinta'; // Dia da Semana de tolerância teste
        $toleranceTimeDayOfWeek = '01:00'; //Tempo de peranência em dia de tolerancia tese

        echo "Tolerância ".$tolerancePeriod."<br><br>";
        
        echo "Time_01 ..... ".$parkingTime_01." ";
        echo "Price_01 ..... ".$parkingPrice_01."<br>";
        
        echo "Time_02 ..... ".$parkingTime_02." ";
        echo "Price_02 ..... ".$parkingPrice_02."<br><br>";
        
        if ($parkingTime_03 != null) {
            echo "Time_03 ..... ".$parkingTime_03." ";
            echo "Price_03 ..... ".$parkingPrice_03."<br>";
        }
        
        if ($parkingTime_04 != null) {
            echo "Time_04 ..... ".$parkingTime_04." ";
            echo "Price_04 ..... ".$parkingPrice_04."<br>";
        }
        
        if ($parkingTime_05 != null) {
            echo "Time_05 ..... ".$parkingTime_05." ";
            echo "Price_05 ..... ".$parkingPrice_05."<br><br>";
        }

        echo "Hora adicional ..... ".$adicionalPrice."<br>";
        echo "Dia de tolerância ..... ".$toleranceDay."<br>";
        echo "Tempo de tolerância no dia de tolerância ..... ".$toleranceTimeDayOfWeek."<br></br>";

        $min_15 = '00:15';
        $min_16 = '00:16';
        $min_30 = '00:30';
        $min_31 = '00:31';
        $min_45 = '00:45';
        $min_46 = '00:46';
        $min_60 = '01:00';
        $min_61 = '01:01';
        $min_75 = '01:15';
        $min_76 = '01:16';
        $min_90 = '01:30';
        $min_91 = '01:31';
        $min_105 = '01:45';
        $min_106 = '01:46';
        $min_120 = '02:00';
        $min_121 = '02:01';
        $min_135 = '02:15';
        $min_136 = '02:16';
        $min_150 = '02:30';
        $min_151 = '02:31';
        $min_165 = '02:45';
        $min_166 = '02:46';
        $min_180 = '03:00';
        $min_181 = '03:01';
        $min_720 = '12:00';
        $min_1440 = '24:00';

        $lenghtOfstay = '02:59'; // Tempo, teste, de permanencia do veículo

        // CHECK IF DAY O TOLERANCE
        $weekDay = $dayOfWeek[$weekDay];

        if ($weekDay == $toleranceDay) {
            // DAY OF TOLERANCE
            $priceToPay = "0.00";
            echo $weekDay.' '.$tolerancePeriod."</br></br>";
            echo "Tempo de permanência de teste ".$lenghtOfstay."<br></br>";
            echo "Tempo permanencia dia de tolerancia ".$toleranceTimeDayOfWeek."<br></br>";

            echo 'validação de tempo'."<br></br>";
            if ($lenghtOfstay <= $toleranceTimeDayOfWeek ) { // 1 hora de tolerancia
                echo '1o. IF'."</br>";
                echo 'paga '.$priceToPay."</br>";
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";

            } elseif ($lenghtOfstay > $toleranceTimeDayOfWeek && $lenghtOfstay <= '02:00' ) {
                echo '2o. IF'."</br>";
                echo 'paga '.$adicionalPrice."</br>";
                $priceToPay = $adicionalPrice;
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";

            } elseif ($lenghtOfstay > '01:01' && $lenghtOfstay <= '01:30') {
                // $time = '01:30';
                echo '3o. IF'."</br>";
                echo 'adicionalPrice '.$adicionalPrice."</br>";
                if ($weekDay == $toleranceDay) {
                    $priceToPay = $adicionalPrice;
                } else {
                    $priceToPay = $adicionalPrice;
                }
                echo 'paga '.$priceToPay."</br>";
            } elseif ($lenghtOfstay > '01:30' && $lenghtOfstay <= '02:00') {
                // $time = '01:30';
                echo '4o. IF'."</br>";
                echo 'adicionalPrice '.$adicionalPrice."</br>";
                if ($weekDay == $toleranceDay) {
                    $priceToPay = ($adicionalPrice*2);
                } else {
                    $priceToPay = $adicionalPrice;
                }
                echo 'paga '.$priceToPay."</br>";
            } elseif ($lenghtOfstay > '02:00' && $lenghtOfstay <= '02:30') {
                // $time = '01:30';
                echo '5o. IF'."</br>";
                echo 'adicionalPrice '.$adicionalPrice."</br>";
                if ($weekDay == $toleranceDay) {
                    $priceToPay = ($adicionalPrice*3);
                } else {
                    $priceToPay = $adicionalPrice;
                }
                echo 'paga '.$priceToPay."</br>";
            }
            echo 'fim da validação'."<br></br>";

            $return = array(
                'parkingName' => $parkingName,
                'licensePlate' => $licensePlate,
                'lenghtOfStay' => $lenghtOfstay,
                'priceToPay' => $priceToPay,
                'dif' => $return_time

            );

            echo json_encode($return);
            
        } else {
            $priceToPay = "0.00";
            echo $weekDay.' '.$tolerancePeriod."</br></br>";
            echo "Tempo de permanência de teste ".$lenghtOfstay."<br></br>";
            if ($lenghtOfstay < $tolerancePeriod ) {
                echo '1o. IF'."</br>";
                echo 'paga_ '.$parkingPrice_01."</br>";
                $priceToPay = "0.00";
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";
            } elseif ($lenghtOfstay > $tolerancePeriod && $lenghtOfstay <= $parkingTime_01) {
                echo '2o. IF'."</br>";
                echo 'paga '.$parkingPrice_01."</br>";
                $priceToPay = $parkingPrice_01;
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";
            } elseif ($lenghtOfstay > $parkingTime_01 && $lenghtOfstay <= $parkingTime_02) {
                echo '3o. IF'."</br>";
                echo 'paga '.$parkingPrice_02."</br>";
                $priceToPay = $parkingPrice_02;
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";
            } elseif ($lenghtOfstay > $parkingTime_02 && $lenghtOfstay <= $parkingTime_03) {
                echo '4o. IF'."</br>";
                echo 'paga '.($parkingPrice_03+$adicionalPrice)."</br>";
                $priceToPay = $parkingPrice_03;
                echo 'tempo de permanencia '.$time."</br>";
            } elseif ($lenghtOfstay > $parkingTime_03 && $lenghtOfstay <= $parkingTime_04) {
                echo '5o. IF'."</br>";
                echo 'paga '.($parkingPrice_04+(2*$adicionalPrice))."</br>";
                $priceToPay = $parkingPrice_04;
                echo 'tempo de permanencia '.$lenghtOfstay."</br>";
            }

            $return = array(
                'parkingName' => $parkingName,
                'licensePlate' => $licensePlate,
                'lenghtOfStay' => $lenghtOfstay,
                'priceToPay' => $priceToPay,
                'dif' => $return_time

            );

            echo json_encode($return);
        }
    
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

}
        


?>