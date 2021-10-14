<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
// include_once("../functions/jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'buy credits':

        $idClient=$data->idClient;
        $creditValue=$data->creditValue;

        $buyCredits=$pdo->prepare("INSERT INTO credits (idCredits, idClient, creditValue) VALUES(?,?,?)");
        $buyCredits->bindValue(1, NULL);
        $buyCredits->bindValue(2, $idClient);
        $buyCredits->bindValue(3, $creditsValue);
        $buyCredits->execute();

        break;

    case 'get client credits':

        $idClient=$_GET['idClient'];

        $getClientCredits=$pdo->prepare("SELECT creditValue FROM credits WHERE idClient=:idClient");
        $getClientCredits->bindValue(":idClient", $idClient);
        $getClientCredits->execute();

        while ($line=$getClientCredits->fetch(PDO::FETCH_ASSOC)) {

            $creditValue = $line['creditValue'];

            $return = array(
                'creditValue' => $creditValue
            );

        }

        echo json_encode($return);

        break;

    case 'update credits':

        $idClient=$data->idClient;
        $creditValue=$data->creditValue;

        $getCreditBalance=$pdo->prepare("SELECT creditValue FROM credits WHERE idClient=:idClient");
        $getCreditBalance->bindValue(":idClient", $idClient);
        $getCreditBalance->execute();

        while ($line=$getCreditBalance->fetch(PDO::FETCH_ASSOC)) {
            $creditBalance = $line['creditValue'];
        }

        $newCreditBalance = $creditBalance - $creditValue;

        $updateCredits=$pdo->prepare("UPDATE credits SET creditValue=:creditValue WHERE idClient=:idClient");
        $updateCredits->bindValue(":creditValue", $newCreditBalance);
        $updateCredits->bindValue(":idClient", $idClient);
        $updateCredits->execute();

        break;

    default:
        # code...
        break;
}


?>