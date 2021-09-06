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
        
        $toleranceDay = 'Segunda'; // Dia da Semana de tolerância
        $toleranceTimeToleranceDay = '01:00'; //Tempo de peranência em dia de tolerancia

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
        
        $weekDay = $dayOfWeek[$weekDay];
        // CHECK IF DAY O TOLERANCE
        if ($weekDay == $toleranceDay) {
            // DAY OF TOLERANCE
            $priceToPay = "0.00";
            echo $weekDay.' '.$tolerancePeriod."</br></br>";
            echo "Tempo de permanência de teste ".$return_time."<br></br>";
            
            if ($return_time < $tolerancePeriod ) {
                echo 'paga '.$parkingPrice_01."</br>";
                $priceToPay = "0.00";
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $tolerancePeriod && $return_time <= $parkingTime_01) {
                echo 'paga '.$parkingPrice_01."</br>";
                $priceToPay = $parkingPrice_01;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_01 && $return_time <= $parkingTime_02) {
                echo 'paga '.$parkingPrice_02."</br>";
                $priceToPay = $parkingPrice_02;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_02 && $return_time <= $parkingTime_03) {
                echo 'paga '.($parkingPrice_03+$adicionalPrice)."</br>";
                $priceToPay = $parkingPrice_03;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_03 && $return_time <= $parkingTime_04) {
                echo 'paga '.($parkingPrice_04+(2*$adicionalPrice))."</br>";
                $priceToPay = $parkingPrice_04;
                echo 'tempo de permanencia '.$return_time."</br>";
            }

            $return = array(
                'parkingName' => $parkingName,
                'licensePlate' => $licensePlate,
                'stayTime' => $time,
                'priceToPay' => $priceToPay,
                'dif' => $return_time

            );

            echo json_encode($return);
            
        } else {
            $priceToPay = "0.00";
            echo $weekDay.' '.$tolerancePeriod."</br></br>";
            echo "Tempo de permanência de teste ".$return_time."<br></br>";
            if ($return_time < $tolerancePeriod ) {
                echo 'paga '.$parkingPrice_01."</br>";
                $priceToPay = "0.00";
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $tolerancePeriod && $return_time <= $parkingTime_01) {
                echo 'paga '.$parkingPrice_01."</br>";
                $priceToPay = $parkingPrice_01;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_01 && $return_time <= $parkingTime_02) {
                echo 'paga '.$parkingPrice_02."</br>";
                $priceToPay = $parkingPrice_02;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_02 && $return_time <= $parkingTime_03) {
                echo 'paga '.($parkingPrice_03+$adicionalPrice)."</br>";
                $priceToPay = $parkingPrice_03;
                echo 'tempo de permanencia '.$return_time."</br>";
            } elseif ($return_time > $parkingTime_03 && $return_time <= $parkingTime_04) {
                echo 'paga '.($parkingPrice_04+(2*$adicionalPrice))."</br>";
                $priceToPay = $parkingPrice_04;
                echo 'tempo de permanencia '.$return_time."</br>";
            }

            $return = array(
                'parkingName' => $parkingName,
                'licensePlate' => $licensePlate,
                'stayTime' => $time,
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