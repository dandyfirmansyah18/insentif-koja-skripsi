<?php
phpinfo();
// echo $date = date ( "d M Y", strToTime ( "1 days" ));

// $date = "2015-05-28 14:40:43";
// $newdate = strtotime ( '+2 hours' , strtotime ( $date ) );
// $newdate = date ( 'Y-m-d H:i:s' , $newdate );

// echo $newdate;

$now = strtotime(date("Y-m-d")); // or your date as well
$your_date = strtotime("2015-06-24");
$datediff =  $your_date - $now;
echo floor($datediff/(60*60*24));
