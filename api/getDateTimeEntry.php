<?php
    header("Access-Control-Allow-Origin: *");
    ini_set('display_errors', true);
    error_reporting(E_ALL);
    
    include_once("con.php");

    $pdo = conectar();

    $getDateTimeEntry=$pdo->prepare("SELECT * FROM date_time_entry");
    $getDateTimeEntry->execute();

    while ($line=$getDateTimeEntry->fetch(PDO::FETCH_ASSOC)) {

        $date_entry = $line['date_entry'];
        $time_entry = $line['time_entry'];

        $return = array(
            'date_entry' => $date_entry,
            'time_entry' => $time_entry,
        );

    }

    $deleteDateTimeEntry=$pdo->prepare("DELETE FROM date_time_entry");
    $deleteDateTimeEntry->execute();

    echo json_encode($return);

?>