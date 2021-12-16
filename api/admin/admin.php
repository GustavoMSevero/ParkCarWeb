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

    case 'get data parking by id':

        $idParking = $_GET['idParking'];

        try {
            $getParkingById=$pdo->prepare("SELECT p.parkingName, p.city, p.state, p.vaccantNumber, p.idParking, 
                                            tap.idParking, tap.tolerancePeriod, tap.parkingTime_1, tap.parkingPrice_1, 
                                            tap.parkingTime_2, tap.parkingPrice_2, tap.adicionalTime_1, 
                                            tap.adicionalPrice_1, tap.adicionalTime_2, tap.adicionalPrice_2, tap.daily 
                                            FROM `parking` p, timeAndPrices_1 tap 
                                            WHERE p.idParking=tap.idParking 
                                            AND p.idParking=:idParking ");
            $getParkingById->bindValue(":idParking", $idParking);
            $getParkingById->execute();

            while ($line=$getParkingById->fetch(PDO::FETCH_ASSOC)) {
                    $idParking = $line['idParking'];
                    $parkingName = $line['parkingName'];
                    $city = $line['city'];
                    $state = $line['state'];
                    $vaccantNumber = $line['vaccantNumber'];

                    $tolerancePeriod = $line['tolerancePeriod'];
                    $parkingTime_1 = $line['parkingTime_1'];
                    $parkingPrice_1 = $line['parkingPrice_1'];

                    $parkingTime_2 = $line['parkingTime_2'];
                    $parkingPrice_2 = $line['parkingPrice_2'];

                    $adicionalTime_1 = $line['adicionalTime_1'];
                    $adicionalPrice_1 = $line['adicionalPrice_1'];

                    $adicionalTime_2 = $line['adicionalTime_2'];
                    $adicionalPrice_2 = $line['adicionalPrice_2'];

                    $daily = $line['daily'];

                    $return[] = array(
                        'idParking' => $idParking,
                        'parkingName' => $parkingName,
                        'city' => $city,
                        'state' => $state,
                        'vaccantNumber' => $vaccantNumber,
                        'tolerancePeriod' => $tolerancePeriod,
                        'parkingTime_1' => $parkingTime_1,
                        'parkingPrice_1' => $parkingPrice_1,
                        'parkingTime_2' => $parkingTime_2,
                        'parkingPrice_2' => $parkingPrice_2,
                        'adicionalTime_1' => $adicionalTime_1,
                        'adicionalPrice_1' => $adicionalPrice_1,
                        'adicionalTime_2' => $adicionalTime_2,
                        'adicionalPrice_2' => $adicionalPrice_2,
                        'daily' => $daily
                    );
            }

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'save fee':
        // print_r($data);
        $fee = $data->fee;
        $idParking = $data->idParking;

        $feePrice_1 = 0;
        $feePrice_2 = 0;
        $feeAditional_1 = 0; 
        $feeAditional_2 = 0;
        $feeDaily = 0;

        try {

            $checkExistingFee=$pdo->prepare("SELECT * FROM fee WHERE idParking=:idParking");
            $checkExistingFee->bindvalue(":idParking", $idParking);
            $checkExistingFee->execute();

            $exists = $checkExistingFee->rowCount();

            // SELECT TO GET TIME AND PRICES
            $getTimeAndPricesParking=$pdo->prepare("SELECT parkingPrice_1, parkingPrice_2, adicionalPrice_1, adicionalPrice_2, daily 
                                                    FROM timeAndPrices_1 
                                                    WHERE idParking=:idParking ");
            $getTimeAndPricesParking->bindValue(":idParking", $idParking);
            $getTimeAndPricesParking->execute();

            while ($line=$getTimeAndPricesParking->fetch(PDO::FETCH_ASSOC)) {
                
                // PARKING PRICES
                $parkingPrice_1 = $line['parkingPrice_1'];
                $parkingPrice_2 = $line['parkingPrice_2'];
                $aditionalPrice_1 = $line['adicionalPrice_1'];
                $aditionalPrice_2 = $line['adicionalPrice_2'];
                $daily = $line['daily'];

                // FEES CHARGED BY PARKCAR
                $feePrice_1 = $parkingPrice_1 * $fee;
                $feePrice_2 = $parkingPrice_2 * $fee;
                $feeAditional_1 = $aditionalPrice_1 * $fee;
                $feeAditional_2 = $aditionalPrice_2 * $fee;
                $feeDaily = $daily * $fee;

                if ($exists != 0) {
                    // UPDATE
                    $updateFeeParking=$pdo->prepare("UPDATE fee SET fee=:fee, price_1=:feePrice_1, price_2=:feePrice_2, 
                                                    aditionalPrice_1=:feeAditional_1, aditionalPrice_2=:feeAditional_2,
                                                    daily=:feeDaily
                                                    WHERE idParking=:idParking");
                    $updateFeeParking->bindValue(":fee", $fee);
                    $updateFeeParking->bindValue(":feePrice_1", $feePrice_1);
                    $updateFeeParking->bindValue(":feePrice_2", $feePrice_2);
                    $updateFeeParking->bindValue(":feeAditional_1", $feeAditional_1);
                    $updateFeeParking->bindValue(":feeAditional_2", $feeAditional_2);
                    $updateFeeParking->bindValue(":feeDaily", $feeDaily);
                    $updateFeeParking->bindValue(":idParking", $idParking);
                    $updateFeeParking->execute();


                } else {
                    // INSERT
                    $saveFeeParking=$pdo->prepare("INSERT INTO fee (idFee, idParking, fee, price_1, price_2, aditionalPrice_1,
                                                        aditionalPrice_2, daily) VALUES(?,?,?,?,?,?,?,?)");
                    $saveFeeParking->bindValue(1, NULL);
                    $saveFeeParking->bindValue(2, $idParking);
                    $saveFeeParking->bindValue(3, $fee);
                    $saveFeeParking->bindValue(4, $feePrice_1);
                    $saveFeeParking->bindValue(5, $feePrice_2);
                    $saveFeeParking->bindValue(6, $feeAditional_1);
                    $saveFeeParking->bindValue(7, $feeAditional_2);
                    $saveFeeParking->bindValue(8, $feeDaily);
                    $saveFeeParking->execute();

                }

            }



        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get fee parking':

        $idParking = $_GET['idParking'];

        try {
            $getFeeParking=$pdo->prepare("SELECT * FROM fee WHERE idParking=:idParking");
            $getFeeParking->bindValue(":idParking", $idParking);
            $getFeeParking->execute();

            while ($line=$getFeeParking->fetch(PDO::FETCH_ASSOC)) {
                $idFee = $line['idFee'];
                $fee = $line['fee'];
                $price_1 = $line['price_1'];
                $price_2 = $line['price_2'];
                $aditionalPrice_1 = $line['aditionalPrice_1'];
                $aditionalPrice_2 = $line['aditionalPrice_2'];
                $daily = $line['daily'];

                $return[] = array(
                    'idFee' => $idFee,
                    'fee' => $fee,
                    'price_1' => $price_1,
                    'price_2' => $price_2,
                    'aditionalPrice_1' => $aditionalPrice_1,
                    'aditionalPrice_2' => $aditionalPrice_2,
                    'daily' => $daily
    
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