<?php
header("Access-Control-Allow-Origin: *");
// header('Content-Type: application/json');
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
    case 'register customer':
        print_r($data);
        $idParking = $data->idParking;
        $firstname = $data->firstname;
        $secondname = $data->secondname;
        $licensePlate = $data->licensePlate;
        $contract = $data->contract;
        $floor = $data->floor;
        $contractStartDate = $data->contractStartDate;
        $contractEndDate = $data->contractEndDate;

        $contractStartDateP = explode('T', $contractStartDate);
        $contractStartDate = $contractStartDateP[0];

        $contractEndDateP = explode('T', $contractEndDate);
        $contractEndDate = $contractEndDateP[0];

        @$codFloor = $data->codFloor;
        @$customerBox = $data->customerBox;
        @$contractValue = $data->contractValue;


        $insertParkingCustomer=$pdo->prepare("INSERT INTO parkingCustomer (idParkingCustomer, idParking, firstname, secondname, licensePlate, contractType,
                                            contractStartDate, contractEndDate, parkingFloor, codFloor, box, value) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $insertParkingCustomer->bindValue(1, NULL);
        $insertParkingCustomer->bindValue(2, $idParking);
        $insertParkingCustomer->bindValue(3, $firstname);
        $insertParkingCustomer->bindValue(4, $secondname);
        $insertParkingCustomer->bindValue(5, $licensePlate);
        $insertParkingCustomer->bindValue(6, $contract);
        $insertParkingCustomer->bindValue(7, $contractStartDate);
        $insertParkingCustomer->bindValue(8, $contractEndDate);
        $insertParkingCustomer->bindValue(9, $floor);
        $insertParkingCustomer->bindValue(10, $codFloor);
        $insertParkingCustomer->bindValue(11, $customerBox);
        $insertParkingCustomer->bindValue(12, $contractValue);
        $insertParkingCustomer->execute();

        break;

    case 'get parking customers':
    
        $idParking = $_GET["idParking"];

        $getParkingCustomers=$pdo->prepare("SELECT pc.*, cc.contractType, cc.idParking FROM parkingCustomer pc, customerContract cc 
                                            WHERE pc.idParking=:idParking 
                                            AND cc.idCustomerContract=pc.contractType 
                                            AND cc.idParking=:idParking");
        $getParkingCustomers->bindValue(":idParking", $idParking);
        $getParkingCustomers->execute();

        while ($line=$getParkingCustomers->fetch(PDO::FETCH_ASSOC)) {
            $idParkingCustomer = $line['idParkingCustomer'];
            $firstname = $line['firstname'];
            $secondname = $line['secondname'];
            $licensePlate = $line['licensePlate'];
            $contractType = $line['contractType'];
            $contractStartDate = $line['contractStartDate'];
            $contractEndDate = $line['contractEndDate'];
            $parkingFloor = $line['parkingFloor'];
            $codFloor = $line['codFloor'];
            $box = $line['box'];
            $value = $line['value'];

            if ($secondname == '' || $secondname == null) {

            }

            $return[] = array(
                'idParkingCustomer' => $idParkingCustomer,
                'firstname' => $firstname,
                'secondname' => $secondname,
                'licensePlate' => $licensePlate,
                'contractType' => $contractType,
                'contractStartDate' => $contractStartDate,
                'contractEndDate' => $contractEndDate,
                'parkingFloor' => $parkingFloor,
                'codFloor' => $codFloor,
                'box' => $box,
                'value' => $value
            );
        }

        echo json_encode($return);

        break;

    case 'get parking customer to edit':

        $idParking = $_GET["idParking"];
        $idParkingCustomer = $_GET["idParkingCustomer"];

        $getParkingCustomer=$pdo->prepare("SELECT * FROM parkingCustomer WHERE idParking=:idParking 
                                            AND idParkingCustomer=:idParkingCustomer");
        $getParkingCustomer->bindValue(":idParking", $idParking);
        $getParkingCustomer->bindValue(":idParkingCustomer", $idParkingCustomer);
        $getParkingCustomer->execute();

        while ($line=$getParkingCustomer->fetch(PDO::FETCH_ASSOC)) {
            $idParkingCustomer = $line['idParkingCustomer'];
            $firstname = $line['firstname'];
            $secondname = $line['secondname'];
            $licensePlate = $line['licensePlate'];
            $contractType = $line['contractType'];
            $contractStartDate = $line['contractStartDate'];
            $contractEndDate = $line['contractEndDate'];
            $parkingFloor = $line['parkingFloor'];
            $codFloor = $line['codFloor'];
            $box = $line['box'];
            $value = $line['value'];

            $contractStartDateP = explode('-', $contractStartDate);
            $contractStartDate = $contractStartDateP[2].'/'.$contractStartDateP[1].'/'.$contractStartDateP[0];

            $contractEndDate = explode('-', $contractEndDate);
            $contractEndDate = $contractEndDate[2].'/'.$contractEndDate[1].'/'.$contractEndDate[0];


            $value = str_replace(".",",",$value);

            $return = array(
                'idParkingCustomer' => $idParkingCustomer,
                'firstname' => $firstname,
                'secondname' => $secondname,
                'licensePlate' => $licensePlate,
                'idCustomerContract' => $contractType,
                'contractStartDate' => $contractStartDate,
                'contractEndDate' => $contractEndDate,
                'parkingFloor' => $parkingFloor,
                'codFloor' => $codFloor,
                'box' => $box,
                'value' => $value
            );
        }

        echo json_encode($return);

        break;

    case 'update customer':
        // print_r($data);
        $idParkingCustomer = $data->idParkingCustomer;
        $idParking = $data->idParking;
        $firstname = $data->firstname;
        $secondname = $data->secondname;
        $licensePlate = $data->licensePlate;
        $idContract = $data->idCustomerContract;
        $contractStartDate = $data->contractStartDate;
        $contractEndDate = $data->contractEndDate;
        $parkingFloor = $data->parkingFloor;

        $contractStartDateP = explode("/", $contractStartDate);
        $contractStartDate = $contractStartDateP[2].'-'.$contractStartDateP[1].'-'.$contractStartDateP[0];

        $contractEndDateP = explode("/", $contractEndDate);
        $contractEndDate = $contractEndDateP[2].'-'.$contractEndDateP[1].'-'.$contractEndDateP[0];

        @$codFloor = $data->codFloor;
        @$box = $data->box;
        @$value = $data->value;

        $value = str_replace(",",".",$value);
        // echo $contractEndDate;


        $updateParkingCustomer=$pdo->prepare("UPDATE parkingCustomer SET firstname=:firstname, secondname=:secondname, licensePlate=:licensePlate,
                                            contractType=:contractType, contractStartDate=:contractStartDate, contractEndDate=:contractEndDate, 
                                            parkingFloor=:parkingFloor, codFloor=:codFloor, box=:box, value=:value
                                            WHERE idParkingCustomer=:idParkingCustomer
                                            AND idParking=:idParking");
        $updateParkingCustomer->bindValue(":firstname", $firstname);
        $updateParkingCustomer->bindValue(":secondname", $secondname);
        $updateParkingCustomer->bindValue(":licensePlate", $licensePlate);
        $updateParkingCustomer->bindValue(":contractType", $idContract);
        $updateParkingCustomer->bindValue(":contractStartDate", $contractStartDate);
        $updateParkingCustomer->bindValue(":contractEndDate", $contractEndDate);
        $updateParkingCustomer->bindValue(":parkingFloor", $parkingFloor);
        $updateParkingCustomer->bindValue(":codFloor", $codFloor);
        $updateParkingCustomer->bindValue(":box", $box);
        $updateParkingCustomer->bindValue(":value", $value);
        $updateParkingCustomer->bindValue(":idParkingCustomer", $idParkingCustomer);
        $updateParkingCustomer->bindValue(":idParking", $idParking);
        $updateParkingCustomer->execute();

        break;
    
    default:
        # code...
        break;
}


?>