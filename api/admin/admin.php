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
    case 'register new user admin':
        $name = $data->name;
        $email = $data->email;
        $password = md5($data->password);
        $typeUser = 'admin';

        $svr = $_SERVER['REMOTE_ADDR'];
        if ($svr == '2804:14d:4c81:89dd:4012:192a:575:5c91') {
            $name = utf8_decode($name);
        }

        try {
            $registerNewUserAdmin=$pdo->prepare("INSERT INTO usersAdmin (idUserAdmin, name, typeUser, email, password) VALUES(?,?,?,?,?)");
            $registerNewUserAdmin->bindValue(1, NULL);
            $registerNewUserAdmin->bindValue(2, $name);
            $registerNewUserAdmin->bindValue(3, $typeUser);
            $registerNewUserAdmin->bindValue(4, $email);
            $registerNewUserAdmin->bindValue(5, $password);
            $registerNewUserAdmin->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    case 'login':
        $email = $data->email;
        $password = $data->password;
        $password = md5($data->password);

        try {
            $loginUserAdmin=$pdo->prepare("SELECT * FROM usersAdmin WHERE email=:email AND password=:password");
            $loginUserAdmin->bindValue(":email", $email);
            $loginUserAdmin->bindValue(":password", $password);
            $loginUserAdmin->execute();

            $exists = $loginUserAdmin->rowCount();

            if($exists == 0) {

                $status = 0;
                $msg = 'Email ou senha inválido';

                $return = array(
                    'status' => $status,
                    'msg' => utf8_encode($msg)
                );

                echo json_encode($return);

            } else if($exists == 1){

                while ($line=$loginUserAdmin->fetch(PDO::FETCH_ASSOC)) {

                    $idUserAdmin = $line['idUserAdmin'];
                    $name = $line['name'];
                    $typeUser = $line['typeUser'];

                    $svr = $_SERVER['REMOTE_ADDR'];
                    if ($svr == '2804:14d:4c81:89dd:4012:192a:575:5c91') {
                        $name = utf8_encode($line['name']);
                    }
    
                    $status = 1;
                    $return = array(
                        'idUserAdmin' => $idUserAdmin,
                        'name' => $name,
                        'typeUser' => $typeUser
                    );
    
                }
    
                echo json_encode($return);
            }

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get admin users':

        try {
            $getAdminUsers=$pdo->prepare("SELECT * FROM usersAdmin");
            $getAdminUsers->execute();

            while ($line=$getAdminUsers->fetch(PDO::FETCH_ASSOC)) {

                    $idUserAdmin = $line['idUserAdmin'];
                    $name = $line['name'];
                    $email = $line['email'];

                    $svr = $_SERVER['REMOTE_ADDR'];
                    if ($svr == '2804:14d:4c81:89dd:4012:192a:575:5c91') {
                        $name = utf8_encode($line['name']);
                    }
    
                    $return[] = array(
                        'idUserAdmin' => $idUserAdmin,
                        'name' => $name,
                        'email' => $email
                    );
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get user admin for edit':

        $idUserAdmin = $_GET['idUserAdmin'];

        try {
            $getUserAdminForEdit=$pdo->prepare("SELECT * FROM usersAdmin WHERE idUserAdmin=:idUserAdmin");
            $getUserAdminForEdit->bindValue(':idUserAdmin', $idUserAdmin);
            $getUserAdminForEdit->execute();

            // $getUserAdminForEdit->rowCount();

            while ($line=$getUserAdminForEdit->fetch(PDO::FETCH_ASSOC)) {

                    $name = $line['name'];
                    $email = $line['email'];
                    $password = $line['password'];
    
                    $return = array(
                        'name' => $name,
                        'email' => $email,
                        'password' => $password
                    );
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'update user admin':
        // print_r($data);
        // [password] => userAdmin@2021
        $idUserAdmin = $data->idUserAdmin;
        $name = $data->name;
        $email = $data->email;
        $password = $data->password;
        $password = md5($password);

        try {
            $updateUserAdmin=$pdo->prepare("UPDATE usersAdmin SET name=:name, email=:email, password=:password
                                            WHERE idUserAdmin=:idUserAdmin");
            $updateUserAdmin->bindValue(':name', $name);
            $updateUserAdmin->bindValue(':email', $email);
            $updateUserAdmin->bindValue(':password', $password);
            $updateUserAdmin->bindValue(':idUserAdmin', $idUserAdmin);
            $updateUserAdmin->execute();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get quantity parkings':

        try {
            $getQuantityParkings=$pdo->prepare("SELECT COUNT(idParking) as parkingsQuantity FROM parking");
            $getQuantityParkings->execute();

            while ($line=$getQuantityParkings->fetch(PDO::FETCH_ASSOC)) {

                    $parkingsQuantity = $line['parkingsQuantity'];
    
                    $return = array(
                        'parkingsQuantity' => $parkingsQuantity
                    );
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get users quantity':

        try {
            $getUsersQuantity=$pdo->prepare("SELECT  ( SELECT COUNT(*) FROM   client ) AS qttClient,
                ( SELECT COUNT(*) FROM parking ) AS qttParking,
                ( SELECT COUNT(*) FROM clientVehicle ) AS qttClientVehicle
                FROM dual");
            $getUsersQuantity->execute();

            while ($line=$getUsersQuantity->fetch(PDO::FETCH_ASSOC)) {
                    $qttClient = $line['qttClient'];
                    $qttParking = $line['qttParking'];
                    $qttClientVehicle = $line['qttClientVehicle'];

                    $return = array(
                        'parkingQuantity' => $qttParking,
                        'clientQuantity' => $qttClient,
                        'vehicleQuantity' => $qttClientVehicle,
                    );
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get parkings':

        try {
            $getParkings=$pdo->prepare("SELECT * FROM parking");
            $getParkings->execute();

            while ($line=$getParkings->fetch(PDO::FETCH_ASSOC)) {
                    $idParking = $line['idParking'];
                    $parkingName = $line['parkingName'];
                    $city = $line['city'];
                    $state = $line['state'];
                    $vaccantNumber = $line['vaccantNumber'];

                    $svr = $_SERVER['REMOTE_ADDR'];
                    if ($svr == '2804:14d:4c81:89dd:4012:192a:575:5c91') {
                        $parkingName = utf8_encode($parkingName);
                        $city = utf8_encode($city);
                    }

                    $return[] = array(
                        'idParking' => $idParking,
                        'parkingName' => $parkingName,
                        'city' => $city,
                        'state' => $state,
                        'vaccantNumber' => $vaccantNumber
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