<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'save agreement':

        $idParking = $data->idParking;
        $title = $data->title;
        $type = $data->type;
        $value = $data->value;

        try {
            $saveAgreement=$pdo->prepare("INSERT INTO agreements (idagreement, idParking, agreement, agreementType, agreementValue) VALUES(?,?,?,?,?)");
            $saveAgreement->bindValue(1, NULL);
            $saveAgreement->bindValue(2, $idParking);
            $saveAgreement->bindValue(3, $title);
            $saveAgreement->bindValue(4, $type);
            $saveAgreement->bindValue(5, $value);
            $saveAgreement->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get agreements':

        $idParking = $_GET['idParking'];

        try {
            $getAgreemets=$pdo->prepare("SELECT * FROM agreements WHERE idParking=:idParking");
            $getAgreemets->bindValue(':idParking', $idParking);
            $getAgreemets->execute();

            while ($line=$getAgreemets->fetch(PDO::FETCH_ASSOC)) {

                $idagreement = $line['idagreement'];
                $agreement = $line['agreement'];
                $agreementType = $line['agreementType'];
                $agreementValue = $line['agreementValue'];

                $return[] = array(
                    'idagreement' => $idagreement,
                    'agreement' => $agreement,
                    'agreementType' => $agreementType,
                    'agreementValue' => $agreementValue
                );

            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get agreement to edit':

        $idParking = $_GET['idParking'];
        $idAgreement = $_GET['idAgreement'];

        try {
            $getAgreemetToEdit=$pdo->prepare("SELECT * FROM agreements WHERE idParking=:idParking AND idAgreement=:idAgreement");
            $getAgreemetToEdit->bindValue(':idParking', $idParking);
            $getAgreemetToEdit->bindValue(':idAgreement', $idAgreement);
            $getAgreemetToEdit->execute();

            while ($line=$getAgreemetToEdit->fetch(PDO::FETCH_ASSOC)) {

                $idagreement = $line['idagreement'];
                $agreement = $line['agreement'];
                $agreementType = $line['agreementType'];
                $agreementValue = $line['agreementValue'];

                $return = array(
                    'title' => $agreement,
                    'type' => $agreementType,
                    'value' => $agreementValue
                );

            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;


    case 'update agreement':

        $agreement = $data->title;
        $type = $data->type;
        $value = $data->value;
        $idParking = $data->idParking;
        $idAgreement = $data->idAgreement;

        try {
            $updateAgreemet=$pdo->prepare("UPDATE agreements SET agreement=:agreement, agreementType=:agreementType,  agreementValue=:agreementValue
                                        WHERE idParking=:idParking AND idAgreement=:idAgreement");
            $updateAgreemet->bindValue(':agreement', $agreement);
            $updateAgreemet->bindValue(':agreementType', $type);
            $updateAgreemet->bindValue(':agreementValue', $value);
            $updateAgreemet->bindValue(':idParking', $idParking);
            $updateAgreemet->bindValue(':idAgreement', $idAgreement);
            $updateAgreemet->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;
    
    default:
        # code...
        break;
}


?>