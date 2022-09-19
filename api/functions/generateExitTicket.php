<?php
    header("Access-Control-Allow-Origin: *");
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    include_once("../con.php");

    function generateExitTicket($licensePlate) {

        $pdo = conectar();

        date_default_timezone_set('America/Sao_Paulo');
        $departureTime = date("Y-m-d H:i:s");
        $statusTicket = 1;

        try {

            // SEARCH VEHICLE BY LICENSEPLATE AND STATUS TO GET TICKET INFORMATIONS
            $getVehicleToExitTicket=$pdo->prepare("SELECT * FROM ticket WHERE licensePlate=:licensePlate 
                                                AND statusTicket=:statusTicket");
            $getVehicleToExitTicket->bindValue(":licensePlate", $licensePlate);
            $getVehicleToExitTicket->bindValue(":statusTicket", $statusTicket);
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

            $date1 = date_create($entryDateAndTime);
            $date2 = date_create($departureTime);
            $diff = date_diff($date1,$date2);
            $diff = $diff->format("%H:%I:%S");

            $paymentType = "DEBITO";

            $status = 0;
            // $amountPaid = 0;
        
            // $getAmountPaid=$pdo->prepare("SELECT * FROM parkedVehicles WHERE licensePlate=:licensePlate 
            //                             AND departureTime=:departureTime");
            // $getAmountPaid->bindValue(":licensePlate", $licensePlate);
            // $getAmountPaid->bindValue(":departureTime", $departureTime);
            // $getAmountPaid->execute();

            // while ($line=$getAmountPaid->fetch(PDO::FETCH_ASSOC)) {
            //     $amountPaid = $line['valuePaid'];
            // }

            // UPDATE TICKET
            $updateStatusTicketEntry=$pdo->prepare("UPDATE ticket SET statusTicket=:statusTicket, exitDate=:exitDate, exitTime=:exitTime,
                                            stayOfTime=:stayOfTime, paymentType=:paymentType WHERE licensePlate=:licensePlate 
                                            AND entryDate=:entryDate 
                                            AND entryTime=:entryTime");
            $updateStatusTicketEntry->bindValue(":statusTicket", $status);
            $updateStatusTicketEntry->bindValue(":exitDate", $exitDate);
            $updateStatusTicketEntry->bindValue(":exitTime", $exitTime);
            $updateStatusTicketEntry->bindValue(":stayOfTime", $diff);
            $updateStatusTicketEntry->bindValue(":paymentType", $paymentType);
            $updateStatusTicketEntry->bindValue(":licensePlate", $licensePlate);
            $updateStatusTicketEntry->bindValue(":entryDate", $entryDate);
            $updateStatusTicketEntry->bindValue(":entryTime", $entryTime);
            $updateStatusTicketEntry->execute();


        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    // $licensePlate = "IXW3620";
    // generateExitTicket($licensePlate);

?>