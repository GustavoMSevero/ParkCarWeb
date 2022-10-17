<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
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
    case 'save avatar picture':

        $idClient = $data->idClient;
        $image = $data->image;

        try {

            $CheckIfExistsAvatarPicture=$pdo->prepare('SELECT * FROM avatarPicture WHERE idClient=:idClient');
            $CheckIfExistsAvatarPicture->bindValue('idClient', $idClient);
            $CheckIfExistsAvatarPicture->execute();

            $qtd = $CheckIfExistsAvatarPicture->rowCount();

            if($qtd == 0){

                $uploadAvatarPicture=$pdo->prepare('INSERT INTO avatarPicture (idAvatarPicture, idClient, urlImage) VALUES (?,?,?)');
                $uploadAvatarPicture->bindValue(1, NULL);
                $uploadAvatarPicture->bindValue(2, $idClient);
                $uploadAvatarPicture->bindValue(3, $image);
                $uploadAvatarPicture->execute();

            } else {

                while ($linha=$CheckIfExistsAvatarPicture->fetch(PDO::FETCH_ASSOC)) {

                    $idClient = $linha['idClient'];
                    $urlImage = $linha['urlImage'];

                    $imagem = 'https://www.parkcar.app.br/web/api/client/avatarPicture/'.$urlImage;

                    unlink($imagem);

                    }


                    $idClient = $linha['idClient'];
                    $urlImage = $linha['urlImage'];

                    $uploadAvatarPicture=$pdo->prepare('UPDATE avatarPicture SET urlImage=:urlImage WHERE idClient=:idClient');
                    $uploadAvatarPicture->bindValue('urlImage', $urlImage);
                    $uploadAvatarPicture->bindValue('idClient', $idClient);
                    $uploadAvatarPicture->execute();

                }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;


    case 'load avatar picture':

        $idClient = $_GET['idClient'];

        try {

            $loadAvatarImage=$pdo->prepare('SELECT * FROM avatarPicture WHERE idClient=:idClient');
            $loadAvatarImage->bindValue('idClient', $idClient);
            $loadAvatarImage->execute();

            while ($linha=$loadAvatarImage->fetch(PDO::FETCH_ASSOC)) {

                $urlImage = $linha['urlImage'];

            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;
    
    default:
        # code...
        break;
}


?>