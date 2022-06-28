<?php
    header("Access-Control-Allow-Origin: *");
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    include_once("../con.php");

    function generateExitTicket($licensePlate) {

        $pdo = conectar();

        date_default_timezone_set('America/Sao_Paulo');
        $departureTime = date("Y-m-d H:i:s");
        $statusForSearch = 1;

        try {

            // SEARCH VEHICLE BY LICENSEPLATE AND STATUS TO GET TICKET INFORMATIONS
            $getVehicleToExitTicket=$pdo->prepare("SELECT * FROM entry_ticket WHERE licensePlate=:licensePlate 
                                                AND statusTicket=:status");
            $getVehicleToExitTicket->bindValue(":licensePlate", $licensePlate);
            $getVehicleToExitTicket->bindValue(":status", $statusForSearch);
            $getVehicleToExitTicket->execute();

            // DATE FROM ENTRY TICKET
            while ($line=$getVehicleToExitTicket->fetch(PDO::FETCH_ASSOC)) {
                $parkingName = $line['parkingName'];
                $parkingAddress = $line['parkingAddress'];
                $cnpj = $line['CNPJ'];
                $parkingPhone = $line['parkingPhone'];
                $entryDate = $line['entryDate'];
                $entryTime = $line['entryTime'];
            }

                // CALCULATION OF STAY
                $eD = $entryDate;
                $eT = $entryTime;
                $entryDateAndTime = $entryDate." ".$entryTime;

                $departureTimeP = explode(" ", $departureTime);
                $exitDate = $departureTimeP[0];
                $exitTime = $departureTimeP[1];

                $date1= date_create($entryDateAndTime);
                $date2= date_create($departureTime);
                $diff=date_diff($date1,$date2);
                $diff = $diff->format("%H:%I:%S");

                $paymentType = "DEBITO";

                $status = 0;

            // SAVE DEPARTURE DATA TICKET
            $insertDepartureData=$pdo->prepare("INSERT INTO exit_ticket (id_exit_ticket, parkingName, parkingAddress, CNPJ, parkingPhone, licensePlate, entryDate, 
                    entryTime, exitDate, exitTime, stayOfTime, paymentType, statusTicket) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $insertDepartureData->bindValue(1, NULL);
            $insertDepartureData->bindValue(2, $parkingName);
            $insertDepartureData->bindValue(3, $parkingAddress);
            $insertDepartureData->bindValue(4, $cnpj);
            $insertDepartureData->bindValue(5, $parkingPhone);
            $insertDepartureData->bindValue(6, $licensePlate);
            $insertDepartureData->bindValue(7, $entryDate);
            $insertDepartureData->bindValue(8, $entryTime);
            $insertDepartureData->bindValue(9, $exitDate);
            $insertDepartureData->bindValue(10, $exitTime);
            $insertDepartureData->bindValue(11, $diff);
            $insertDepartureData->bindValue(12, $paymentType);
            $insertDepartureData->bindValue(13, $status);
            $insertDepartureData->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    // $licensePlate = "IXW3620";
    // generateExitTicket($licensePlate);

?>