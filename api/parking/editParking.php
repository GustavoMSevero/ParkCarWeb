<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
    case 'get local parkings lots':

        try {
            $getLocalParkingsLots=$pdo->prepare("SELECT * FROM parking");
            $getLocalParkingsLots->execute();
                
            while ($line=$getLocalParkingsLots->fetch(PDO::FETCH_ASSOC)) {

                $idParking = $line['idParking'];
                $parkingName = $line['parkingName'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $city = $line['city'];
                $state = $line['state'];
                $latitude = $line['latitude'];
                $longitude = $line['longitude'];
                $vaccantNumber = $line['vaccantNumber'];

                $status = 1;
                $return[] = array(
                    'idParking' => $idParking,
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'city' => $city,
                    'state' => $state,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'get parking to update':  // get parking address to edit in app
        
        $idParking = $_GET['idParking'];

        try {
            $getParkingToUpdate=$pdo->prepare("SELECT * FROM parking WHERE idParking=:idParking");
            $getParkingToUpdate->bindValue(":idParking", $idParking);
            $getParkingToUpdate->execute();
                
            while ($line=$getParkingToUpdate->fetch(PDO::FETCH_ASSOC)) {

                $zipcode = $line['zipcode'];
                $parkingName = $line['parkingName'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $neighborhood = $line['neighborhood'];
                $city = $line['city'];
                $state = $line['state'];
                $vaccantNumber = $line['vaccantNumber'];
                
                // @$toleranceTime = $line['toleranceTime'];
                // @$timeOne = $line['timeOne'];
                // @$priceOne = $line['priceOne'];
                // @$timeTwo = $line['timeTwo'];
                // @$priceTwo = $line['priceTwo'];
                // @$timeThree = $line['timeThree'];
                // @$priceThree = $line['priceThree'];
                // @$adicionalPrice = $line['adicionalPrice'];

                $return = array(
                    'cep' => $zipcode,
                    'newParkingName' => $parkingName,
                    'logradouro' => utf8_encode($address),
                    'newAddressNumber' => $addressNumber,
                    'bairro' => $neighborhood,
                    'localidade' => $city,
                    'uf' => $state,

                    'newVaccantNumber' => $vaccantNumber,
                    // 'newToleranceTime' => $toleranceTime,
                    // 'newTimeOne' => $timeOne,
                    // 'newPriceOne' => $priceOne,
                    // 'newTimeTwo' => $timeTwo,
                    // 'newPriceTwo' => $priceTwo,
                    // 'newTimeThree' => $timeThree,
                    // 'newPriceThree' => $priceThree,
                    // 'newAdicionalPrice' => $adicionalPrice
                );
    
            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'update parking':
        // print_r($data);
        $zipcode = $data->cep;
        $parkingName = $data->newParkingName;
        $address = $data->logradouro;
        $addressNumber = $data->newAddressNumber;
        $neighborhood = $data->bairro;
        $city = $data->localidade;
        $state = $data->uf;
        $newVaccantNumber = $data->newVaccantNumber;
        $idParking = $data->idParking;

        try {
            
            $encode = urlencode("$address $addressNumber $city $neighborhood $state");

            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$encode&key=AIzaSyByiGVOIjoR_NWHz9TpYtDixGx7GZuxoEU";
            // get the json response
            $resp_json = file_get_contents($url);
            // decode the json
            $resp = json_decode($resp_json, true);

            $latitude = $resp['results'][0]['geometry']['location']['lat'];
            $longitude = $resp['results'][0]['geometry']['location']['lng'];

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    

        try {
            $address = utf8_decode($address);
            $updateParking=$pdo->prepare("UPDATE parking 
                                            SET parkingName=:parkingName, zipcode=:zipcode, address=:address, addressNumber=:addressNumber, neighborhood=:neighborhood, city=:city, state=:state, latitude=:latitude, longitude=:longitude
                                            WHERE idParking=:idParking");
            $updateParking->bindValue(":idParking", $idParking);
            $updateParking->bindValue(":parkingName", $parkingName);
            $updateParking->bindValue(":zipcode", $zipcode);
            $updateParking->bindValue(":address", $address);
            $updateParking->bindValue(":addressNumber", $addressNumber);
            $updateParking->bindValue(":neighborhood", $neighborhood);
            $updateParking->bindValue(":city", $city);
            $updateParking->bindValue(":state", $state);
            $updateParking->bindValue(":latitude", $latitude);
            $updateParking->bindValue(":longitude", $longitude);
            $updateParking->execute();

            // $toleranceTime = $data->newToleranceTime;
            // $timeOne = $data->newTimeOne;
            // $priceOne = $data->newPriceOne;
            // $timeTwo = $data->newTimeTwo;
            // $priceTwo = $data->newPriceTwo;
            // $timeThree = $data->newTimeThree;
            // $priceThree = $data->newPriceThree;
            // $adicionalPrice = $data->newAdicionalPrice;
            

            // $toleranceTimeP = explode(':', $toleranceTime);
            // $toleranceTime = $toleranceTimeP[0];

            // $timeOneP = explode(':', $timeOne);
            // $timeOne = $timeOneP[0];

            // $timeTwoP = explode(':', $timeTwo);
            // $timeTwo = $timeTwoP[0];

            // $timeThreeP = explode(':', $timeThree);
            // $timeThree = $timeThreeP[0];

            // $priceOneP = explode(',', $priceOne);
            // $priceOne = $priceOneP[0];

            // $priceTwoP = explode(',', $priceTwo);
            // $priceTwo = $priceTwoP[0];
            
            // $priceThreeP = explode(',', $priceThree);
            // $priceThree = $priceThreeP[0];

            // $adicionalPriceP = explode(',', $adicionalPrice);
            // $adicionalPrice = $adicionalPriceP[0];

            // $updateTimeAndPrices=$pdo->prepare("UPDATE parkingTimeAndPrices
            //                                     SET toleranceTime=:toleranceTime, timeOne=:timeOne, priceOne=:priceOne, timeTwo=:timeTwo, priceTwo=:priceTwo, timeThree=:timeThree, priceThree=:priceThree, adicionalPrice=:adicionalPrice
            //                                     WHERE idParking=:idParking");
            // $updateTimeAndPrices->bindValue(":toleranceTime", $toleranceTime);
            // $updateTimeAndPrices->bindValue(":timeOne", $timeOne);
            // $updateTimeAndPrices->bindValue(":priceOne", $priceOne);
            // $updateTimeAndPrices->bindValue(":timeTwo", $timeTwo);
            // $updateTimeAndPrices->bindValue(":priceTwo", $priceTwo);
            // $updateTimeAndPrices->bindValue(":timeThree", $timeThree);
            // $updateTimeAndPrices->bindValue(":priceThree", $priceThree);
            // $updateTimeAndPrices->bindValue(":adicionalPrice", $adicionalPrice);
            // $updateTimeAndPrices->bindValue(":idParking", $idParking);
            // $updateTimeAndPrices->execute();

            $status = 1;
            $msg = 'Dados atualizados com sucesso';

            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        break;

    case 'get email from parking lot to change':  // get parking address to edit in app
    
        $idParking = $_GET['idParking'];

        try {
            $getEmailFromParkingToLotToChnage=$pdo->prepare("SELECT * FROM parking WHERE idParking=:idParking");
            $getEmailFromParkingToLotToChnage->bindValue(":idParking", $idParking);
            $getEmailFromParkingToLotToChnage->execute();
                
            while ($line=$getEmailFromParkingToLotToChnage->fetch(PDO::FETCH_ASSOC)) {

                $email = $line['email'];

                $return = array(
                    'email' => $email
                );
    
            }

            echo json_encode($return);

            
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        break;

    case 'update email and password': // email and password to access the app

        $email = $data->email;
        @$password = $data->password;
        $idParking = $data->idParking;

        if ($password == '') {
            // echo 'Senha não será alterada';
            $email = $data->email;
            $idParking = $data->idParking;

            try {
                $updateEmailAndPassword=$pdo->prepare("UPDATE parking SET email=:email WHERE idParking=:idParking");
                $updateEmailAndPassword->bindValue(":email", $email);
                $updateEmailAndPassword->bindValue(":idParking", $idParking);
                $updateEmailAndPassword->execute();

                $msg = 'E-mail atualizado com sucesso';
                $return = array(
                    'msg' => $msg
                );

                echo json_encode($return);
    
                
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            
            
        } else {
            // echo 'Senha alterada';
            $email = $data->email;
            $password = md5($data->password);
            $idParking = $data->idParking;

            try {
                $updateEmailAndPassword=$pdo->prepare("UPDATE parking SET email=:email, password=:password 
                                                    WHERE idParking=:idParking");
                $updateEmailAndPassword->bindValue(":email", $email);
                $updateEmailAndPassword->bindValue(":password", $password);
                $updateEmailAndPassword->bindValue(":idParking", $idParking);
                $updateEmailAndPassword->execute();

                $msg = 'E-mail e senha atualizados com sucesso';
                $return = array(
                    'msg' => $msg
                );

                echo json_encode($return);
    
                
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }


        break;
    
    default:
        # code...
        break;
}


?>