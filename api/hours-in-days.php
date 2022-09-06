<?php

$dayAndTimeEntry = date("2022-08-31 16:27:00");
$dayAndTimeEntrey_DateTime = new DateTime($dayAndTimeEntry);


$departureDayAndTime = date("Y-m-d H:i:s");
$departureDayAndTime_DateTime = new DateTime($departureDayAndTime);

// print_r($dayAndTimeEntrey_DateTime);
// echo "<br>";
// print_r($departureDayAndTime_DateTime);
// echo "<br>";

echo $dayAndTimeEntrey_DateTime->format('Y-m-d H:i:s');
echo "<br>";
echo $departureDayAndTime_DateTime->format('Y-m-d H:i:s');

$diff = $departureDayAndTime_DateTime->diff($dayAndTimeEntrey_DateTime);

$hours = $diff->h;
$hours = $hours + ($diff->days*24);

echo "<br>";

echo $hours."<br>";

?>