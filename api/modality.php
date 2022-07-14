<?php
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');
    ini_set('display_errors', true);
    error_reporting(E_ALL);
    
    include_once("con.php");

    $pdo = conectar();

    $data = file_get_contents("php://input");
    $data = json_decode($data);

    if($data){
        $option = $data->option;
    }else{
        $option = $_GET['option'];
    }

    switch ($option) {
        case 'register modality':
            
            print_r($data);

            break;
        
        default:
            # code...
            break;
    }
?>