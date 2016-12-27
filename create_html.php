<?php

/****************************
This script will create a monthly report, where you can see daily income. Output is static HTML page.
You'll need to copy provided css file also to the output directory.

The data which this script reads is from database table "daily". So you WILL need to modify this script to match your data!

This is far from optimal coding, so I bet there's a lot for me to learn about coding. 

Why static HTML page? The reasoning is that this way we don't need to pay so much attention to security in public web interface.
With the data stored to database, you can ofc create nice dynamic pages... this is just for my personal usage.

****************************/

// Set Error Reporting

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US.UTF-8');

// Prepare the static HTML content
$start_html = "<html>
<head>
<link rel='stylesheet' type='text/css' href='bpc.css'>
</head>
<div align='center' id='main'>

<div class='caption'>Daily Income</div>	
<div id='table'>
	<div class='header-row row'>
    <span class='cell primary'>Date</span>
    <span class='cell'>Broker Fee</span>
	<span class='cell'>Market</span>
    <span class='cell'>POCO Tax</span>
    <span class='cell'>Manufacturing</span>
    <span class='cell'>Industry Fee</span>
	<span class='cell'><b>DAY TOTAL</b></span>
  </div>";

$end_html = "  </div>
</div>
</div>
</html>";

// Create config.ini array
// [database]'host','name','user','pass' [api]'id','code'
$config = parse_ini_file("config.ini");


// Establish database connection
$conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
echo "Database connected successfully\n";

// Date configuration
$today = date("Y-m-d H:i:s");
$yourdate = date("Y-m-d");
$yourdate2 = date("Y-m");
$week = date("Weeknumber: W", strtotime($yourdate));

$week = explode(" ", $week);

// path where your html file will be located
define('CSV_PATH','/var/www/html/eve/');

// Name of your HTML file. Each month there will be new HTML file
$csv_file = CSV_PATH . "wallet_".$yourdate2.".html";

// Open the file for writing and write the static HTML content (header)
$myfile = fopen($csv_file, "w+") or die("Unable to open file!");
fwrite($myfile, $start_html);

// Get dates from daily table and store them to an array
$date_array = array();
$dates = "SELECT DISTINCT dateD FROM `daily` WHERE 1 ORDER by dateD DESC";
$result = $conn->query($dates);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
		//echo "$row[dateD]"."\n";
		array_push($date_array, $row['dateD']);
	}
}	
// If you wish to ignore some income from Daily Total, add columns to array. Clear the array if you don't need to ignore.
$skip_money = array(2,4);

$monthly_money = 0;

// Loop date_array and get data based on the date
foreach ($date_array as $daa) {
	$sql = "SELECT * FROM `daily` where dateD='$daa' ORDER by dateD DESC";
	//echo $sql."\n";

// push date to date_row array
$date_row = array();
array_push($date_row, $daa);

$result = $conn->query($sql);

$rowcount = 0;
$money_total = 0;
$count_money = 0;

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
//	echo "$row[dateD]".":"."$row[amount]".":"."$row[refTypeID]"."\n";

	$count_money++;

	// Change amount formating to USD Money
	$money = money_format('%.2n', $row['amount']);

	// Store the amount to date_row array
	array_push($date_row,$money);

	// Lets check if you want to skip some income from total income
	if (in_array($count_money, $skip_money)) {
		//echo "This will be skiped ".$row['amount']."\n";
 } else {
		// Here we store the amount to total income 
		//echo $row['amount']."\n";
		$money_total = $money_total + $row['amount'];
	}
	
	}
}

// Sum daily income to monthly income
$monthly_money = $monthly_money + $money_total;

// Lets change the daily total income format to USD
$money_total = money_format('%.2n', $money_total);

// Write the daily income rows to HTML file
$na = "<div class=\"row\"> <span class=\"cell primary\" data-label=\"location\">$date_row[0]</span><span class=\"cell\" data-label=\"name\">$date_row[1]</span><span class=\"cell\" data-label=\"type\">$date_row[2]</span><span class=\"cell\" data-label=\"owner\">$date_row[3]</span><span class=\"cell\" data-label=\"time\">$date_row[4]</span><span class=\"cell\" data-label=\"owner\">$date_row[5]</span><span class=\"cell\" data-label=\"owner\">$money_total</span></div>";
fwrite($myfile, $na);
fwrite($myfile, "\n");

}
//$days = $rowcount / 5;

// Lets change the monthly total income format to USD
$monthly_money = money_format('%.2n', $monthly_money);

// Write the monthly income total to HTML file
$write_total = "<div class=\"row\"> <span class=\"cell primary\" data-label=\"location\"></span><span class=\"cell\" data-label=\"name\"></span><span class=\"cell\" data-label=\"type\"></span><span class=\"cell\" data-label=\"owner\"></span><span class=\"cell\" data-label=\"time\"></span><span class=\"cell\" data-label=\"owner\"><b>Monthly Total</b></span><span class=\"cell\" data-label=\"owner\"><b>$monthly_money</b></span></div>";
fwrite($myfile, $write_total);

// Write closing HTML tags to HTML file
fwrite($myfile, $end_html);

// AND WE'RE DONE!!!




