<?php
    header("Access-Control-Allow-Origin: *");
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    include_once("../con.php");


    function generateEntryTicket($idParking, $licensePlate){

        $pdo = conectar();
        date_default_timezone_set('America/Sao_Paulo');
        $entrance = date("Y-m-d H:i:s");
        
        try {
            // GET ENTRY TICKET INFORMATIONS
            $getParkingData=$pdo->prepare("SELECT parkingName, address, CNPJ FROM parking WHERE idParking=:idParking");
            $getParkingData->bindValue(":idParking", $idParking);
            $getParkingData->execute();

            while ($line=$getParkingData->fetch(PDO::FETCH_ASSOC)) {
                $parkingName = $line['parkingName'];
                $parkingAddress = $line['address'];
                $cnpj = $line['CNPJ'];
            }

            $licensePlate;
            $entranceP = explode(" ", $entrance);
            $entryDate = $entranceP[0];
            $entryTime = $entranceP[1];

            $exitDate = '0000-00-00';
            $exitTime = '00:00:00';
            $stayOfTime = '00:00:00';
            $paymentType = '';
            $amountPaid = '0.0';

            $status = 1;

            // SAVE ENTRY TICKET INFO
            $insertEntrayTicketInfo=$pdo->prepare("INSERT INTO ticket (id_ticket, parkingName, parkingAddress, CNPJ, parkingPhone, licensePlate, 
                                                entryDate, entryTime, exitDate, exitTime, stayOfTime, paymentType, amountPaid, statusTicket) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $insertEntrayTicketInfo->bindValue(1, NULL);
            $insertEntrayTicketInfo->bindValue(2, $parkingName);
            $insertEntrayTicketInfo->bindValue(3, $parkingAddress);
            $insertEntrayTicketInfo->bindValue(4, @$cnpj);
            $insertEntrayTicketInfo->bindValue(5, @$parkingPhone);
            $insertEntrayTicketInfo->bindValue(6, $licensePlate);
            $insertEntrayTicketInfo->bindValue(7, $entryDate);
            $insertEntrayTicketInfo->bindValue(8, $entryTime);
            $insertEntrayTicketInfo->bindValue(9, $exitDate);
            $insertEntrayTicketInfo->bindValue(10, $exitTime);
            $insertEntrayTicketInfo->bindValue(11, $stayOfTime);
            $insertEntrayTicketInfo->bindValue(12, $paymentType);
            $insertEntrayTicketInfo->bindValue(13, $amountPaid);
            $insertEntrayTicketInfo->bindValue(14, $status);
            $insertEntrayTicketInfo->execute();

            // SAVE JUST ENTRY DATE AND ENTRY TIME TO BE LOCATED LATER TO DOWN TICKET 
            // $saveDateTimeEntry=$pdo->prepare("INSERT INTO date_time_entry (date_entry, time_entry) VALUES(?,?)");
            // $saveDateTimeEntry->bindValue(1, $entryDate);
            // $saveDateTimeEntry->bindValue(2, $entryTime);
            // $saveDateTimeEntry->execute();
            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    // $idParking = 15;
    // $licensePlate = "IXW3620";
    // generateEntryTicket($idParking, $licensePlate);
     
?>