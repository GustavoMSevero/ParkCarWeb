<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../jwt.php");

$pdo = conectar();

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'] ?? '';
}

switch($option) {
    case 'login':
        $googleId = $data->googleId ?? null;
        $email = $data->email ?? null;
        $loginType = $data->loginType ?? 'client';

        if(!$googleId) {
            http_response_code(400);

            echo json_encode(array(
                'msg' => 'googleId is required',
                'status' => 0
            ));
            break;
        }

        if($loginType === 'client') {
            $checkIfUserExists=$pdo->prepare("SELECT * FROM client WHERE googleId=:googleId OR email=:email");
        } else {
            $checkIfUserExists=$pdo->prepare("SELECT * FROM parking WHERE googleId=:googleId OR email=:email");
        }

        $checkIfUserExists->bindValue(":googleId", $googleId);
        $checkIfUserExists->bindValue(":email", $email);
        $checkIfUserExists->execute();

        $user = $checkIfUserExists->fetch();

        if(!$user) {            
            http_response_code(202);
            echo json_encode(array(
                'msg' => 'user with this google id or email not exists, lets create one',
                'status' => 1
            ));
            break;
        } else {
            if(!$user['googleId']) {
                http_response_code(406);
                echo json_encode(array(
                    'status' => 0,
                    'msg' => 'user with this google have account with email'
                ));
                break;
            }

            if($user['googleId'] != $googleId) {
                http_response_code(401);
                echo json_encode(array(
                    'status' => 0,
                    'msg' => 'this google not vinculed with google email'
                ));
                break;
            }

            http_response_code(200);
            $id = $loginType === 'client' ? $user['idClient'] : $user['idParking'];
            $jwt = jwt($id, $loginType);
            echo json_encode(array(
                    'status' => 1,
                    'id' => $id,
                    'name' => $user['name'],
                    'token' => $jwt['token'],
                    'refreshToken' => $jwt['refreshToken']
            ), JSON_UNESCAPED_SLASHES);
        }
        break;
    case 'link':
        $googleId = $data->googleId ?? null;
        $avatar = $data->avatar ?? null;
        $email = $data->email ?? null;
        $loginType = $data->loginType ?? 'client';


        if(!$googleId) {
            http_response_code(400);

            echo json_encode(array(
                'msg' => 'googleId is required',
                'status' => 0
            ));
            break;
        }

        if(!$email) {
            http_response_code(400);

            echo json_encode(array(
                'msg' => 'email is required',
                'status' => 0
            ));
            break;
        }

        if($loginType === 'client') {
            $checkIfUserExists=$pdo->prepare("SELECT * FROM client WHERE email=:email");
            $checkIfUsergoogleExists=$pdo->prepare("SELECT * FROM client WHERE googleId=:googleId");
        } else {
            $checkIfUserExists=$pdo->prepare("SELECT * FROM parking WHERE email=:email");
            $checkIfUsergoogleExists=$pdo->prepare("SELECT * FROM parking WHERE googleId=:googleId");
        }

        $checkIfUsergoogleExists->bindValue(":googleId", $googleId);
        $checkIfUsergoogleExists->execute();

        $checkIfUserExists->bindValue(":email", $email);
        $checkIfUserExists->execute();

        $user = $checkIfUserExists->fetch();
        $google = $checkIfUsergoogleExists->fetch();

        if($google) {
            http_response_code(400);
            echo json_encode(array(
                'msg' => 'this google is already linked to some account',
                'status' => 0
            ));
            break;
        }

        if(!$user) {            
            http_response_code(404);
            echo json_encode(array(
                'msg' => 'user with this google id or email not exists',
                'status' => 0
            ));
            break;
        }
        if($user['googleId']) {
            if($user['googleId'] === $googleId) {                      
                http_response_code(200);
                echo json_encode(array(
                    'msg' => 'account already linked',
                    'status' => 0
                ));
            } else {
                http_response_code(406);
                echo json_encode(array(
                    'msg' => 'account is linked to another google account',
                    'status' => 0
                ));
            }
            break;
        } else {
            if($loginType === 'client') {
                $sqlQuery = "UPDATE client SET googleId=:googleId ";
            } else {
                $sqlQuery = "UPDATE parking SET googleId=:googleId ";
            }

            if($avatar){
                $sqlQuery .= ", avatar=:avatar";
            }
            $sqlQuery .= "WHERE email=:email";

            $updategoogleId = $pdo->prepare($sqlQuery);
            if($avatar) {
                $updategoogleId->bindValue(':avatar', $avatar);
            }
            $updategoogleId->bindValue(':googleId', $googleId);
            $updategoogleId->bindValue(':email', $email);

            if($updategoogleId->execute()) {
                http_response_code(202);
                echo json_encode(array(
                    'msg' => 'account linked succesfull',
                    'status' => 1
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    'msg' => 'something is wrong',
                    'status' => 0
                ));
            }
        }

        break;

    default: 
        http_response_code(404);
        echo 'Page not found';
        break;
}