<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

header("Access-Control-Allow-Methods", "POST, PUT, OPTIONS");
header("Access-Control-Allow-Origin", "*.*");
header("Access-Control-Allow-Headers", "Content-Type");

include_once("../con.php");

$pdo = conectar();

$uploadDir = "uploadLogoParking/";

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'upload logo':

        $uploadfile = $uploadDir . $_FILES['file_jpg']['name'];
        $idParking = $_GET['idParking'];//id do curso no banco, não id de cadastro no banco

        $qryCheckLogo=$pdo->prepare('SELECT * FROM parkingLogo WHERE idParking=:idParking');
        $qryCheckLogo->bindValue('idParking', $idParking);
        $qryCheckLogo->execute();

        $qtd = $qryCheckLogo->rowCount();

        if($qtd == 0){

            if(move_uploaded_file($_FILES['file_jpg']['tmp_name'], $uploadfile)) {

                $logoName = $_FILES[ 'file_jpg' ][ 'name' ];
                $extension = $_FILES[ 'file_jpg' ][ 'type' ];
                $local = $_FILES[ 'file_jpg' ][ 'tmp_name' ];
                $size = $_FILES[ 'file_jpg' ][ 'size' ];

                $qryUploadImage=$pdo->prepare('INSERT INTO parkingLogo (idLogo, idParking, logoName, local, size, extension) VALUES (?,?,?,?,?,?)');
                $qryUploadImage->bindValue(1, NULL);
                $qryUploadImage->bindValue(2, $idParking);
                $qryUploadImage->bindValue(3, $logoName);
                $qryUploadImage->bindValue(4, $local);
                $qryUploadImage->bindValue(5, $size);
                $qryUploadImage->bindValue(6, $extension);
                $qryUploadImage->execute();

            }

        } else {

            while ($linha=$qryCheckLogo->fetch(PDO::FETCH_ASSOC)) {

                $idLogo = $linha['idLogo'];
                $idParking = $linha['idParking'];
                $logoName = $linha['logoName'];

                if(move_uploaded_file($_FILES['file_jpg']['tmp_name'], $uploadfile)) {

                    $logoName = $_FILES[ 'file_jpg' ][ 'name' ];
                    $extension = $_FILES[ 'file_jpg' ][ 'type' ];
                    $local = $_FILES[ 'file_jpg' ][ 'tmp_name' ];
                    $size = $_FILES[ 'file_jpg' ][ 'size' ];

                    try {
                        $qryUpdateImage=$pdo->prepare('UPDATE parkingLogo SET logoName=:logoName WHERE idLogo=:idLogo');
                        $qryUpdateImage->bindValue(':logoName', $logoName);
                        $qryUpdateImage->bindValue(':idLogo', $idLogo);
                        $qryUpdateImage->execute();

                    } catch (Exception $e) {
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }

                }

            }

        }

        break;

    case 'get parking logo':

        $idParking = $_GET['idParking'];

        try {
            
            $getParkingLogo=$pdo->prepare("SELECT * FROM parkingLogo WHERE idParking=:idParking");
            $getParkingLogo->bindvalue(":idParking", $idParking);
            $getParkingLogo->execute();

            while ($line=$getParkingLogo->fetch(PDO::FETCH_ASSOC)) {

                $idLogo = $line['idLogo'];
                $logoName = utf8_encode($line['logoName']);;

                $local = 'http://localhost:8888/Projects/web/ParkCarWeb/api/parking/uploadLogoParking/'.$logoName;

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

        break;
    
    default:
        # code...
        break;
}



?>
