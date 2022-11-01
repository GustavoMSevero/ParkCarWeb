<?php

include_once("../con.php");

$pdo = conectar();

$idParking = $_GET['idParking'];

try {
    
    $getParkingLogo=$pdo->prepare("SELECT * FROM parkingLogo WHERE idParking=:idParking");
    $getParkingLogo->bindvalue(":idParking", $idParking);
    $getParkingLogo->execute();

    while ($line=$getParkingLogo->fetch(PDO::FETCH_ASSOC)) {

        $idLogo = $line['idLogo'];
        $logoName = $line['logoName'];

        $local = 'api/parking/uploadLogoParking/'.$logoName;

        $return = array(
            'idLogo' => $idLogo,
            'logoName' => $logoName,
            'local' => $local
        );

    }

    echo json_encode($return);

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}




?>
