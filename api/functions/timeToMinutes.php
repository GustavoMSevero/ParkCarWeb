<?php

function dateAndTimeToNumber($time) {
    $timeP = explode(' ', $time);
    $time = $timeP[1];

    // echo $time."<br>";
    $timeP = explode(':', $time);
    $hour = $timeP[0];
    $hourToMin = $timeP[0] * 60;
    // echo $hourToMin. ' minutos'.'<br>';
    $min = $timeP[1];
    // echo $min. ' minutos'."<br>";
    // echo 'total '.intval($hourToMin+$min);
    $minutesTotal = intval($hourToMin+$min);
    return $minutesTotal;
}

function timeStrigToTimeNumber($time) {
    $timeP = explode(':', $time);
    $hour = $timeP[0];
    $hourToMin = $timeP[0] * 60;
    // echo $hourToMin. ' minutos'.'<br>';
    $min = $timeP[1];
    // echo $min. ' minutos'."<br>";
    // echo 'total '.intval($hourToMin+$min);
    $minutesTotal = intval($hourToMin+$min);
    return $minutesTotal;
}

?>