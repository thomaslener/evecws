<?php
/*
#EVE Corp Wallet Script - evecws.php
Run this script to transfer data from API to database
*/
$today = date("Y-m-d H:i:s");
$today2 = date("Y-m-d");

// Set Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// [1]
// Create config.ini array
// [database]'host','name','user','pass' [api]'id','code'
$config = parse_ini_file("config.ini");
echo "\n".$today."\n";
// [2]
// Establish database connection

$conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
if ($conn->connect_error) {
  die("[2] Database connection failed: " . $conn->connect_error);
}
echo "[2] Database connected successfully\n";

// configure desired wallet journal reference types:
$referencetypes = array(46, 2, 97, 56, 120);
$sums = array();
$sum = 0;
foreach ($referencetypes as $no) {
// query RAW database
$sql = "SELECT * FROM `walletjournal` WHERE refTypeID=$no AND YEAR(dateJ) = YEAR(NOW()) AND MONTH(dateJ) = MONTH(NOW()) AND DAY(dateJ) = DAY(NOW())";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
//	echo "$row[weekno]".":"."$row[amount]".":"."$row[refTypeID]"."\n";
	$sum = $sum + $row['amount'];
	
	}
}
$doublecheck = mysqli_query($conn, "SELECT * FROM daily WHERE dateD='$today2' AND refTypeID='$no'");
// if row is in database, print x
  if(mysqli_num_rows($doublecheck) > 0) {
	$sum = 0;
    	echo "Values for the date ".$today2." are already in database"."\n";
  } else {
	$todb = "INSERT INTO daily (dateD, refTypeID, amount) VALUES ('$today2', $no, $sum)";
	if ($conn->query($todb) === TRUE) {
    	echo "New record created successfully"."\n";
  } else {
    	echo "Error: " . $todb . "\n" . $conn->error;
  }
$sum = 0;
}
}
echo "------"."\n\n";
?>
