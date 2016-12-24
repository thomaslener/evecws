<?php
/*
#EVE Corp Wallet Script - evecws.php
Run this script to transfer data from API to database
*/

$today = date("Y-m-d H:i:s");

// Set Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include curl.php
require_once 'curl.php';

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

// [3] Check if Curl is enabled:
echo '[3] Curl ', function_exists('curl_version') ? 'is working'."\n" : 'Disabled: please enable / install php curl extension.\n';

// [4] Get API Data
$url="https://api.eveonline.com/corp/WalletJournal.xml.aspx?keyID=".$config['id']."&vCode=".$config['code']."&accountKey=".$config['accountkey'];
$xml = makeApiRequest($url);

// check if API Data was received successfully
if ($xml->error) {
  $msg = "[4] Error receiving API Data\n";
}
elseif ($xml->result->rowset->row[0]) {
  $msg = '[4] API Data received'."\n";
 }
echo $msg;

// filter the retrieved data for desired values as specified in $referencetypes
// and save it in a new array called $filteredAssets

$filteredAssets = array();

// configure desired wallet journal reference types:
$referencetypes = array(46, 2, 97, 56, 120);

// loop the xml
foreach ($xml->xpath('//row') as $key => $value) {

// save refTypeID to variable
$refTypeID = (string)$value['refTypeID'];

// if refTypeID is in array, go on, else go back to the beginning of loop
if (!in_array($refTypeID, $referencetypes)) {
  continue;
}
// save variables
$date = (string)$value['date'];
$ownerName1 = (string)$value['ownerName1'];
$ownerID1 = (string)$value['ownerID1'];
$ownerName2 = (string)$value['ownerName2'];
$ownerID2 = (string)$value['ownerID2'];
$argName1 = (string)$value['argName1'];
$argID1 = (string)$value['argID1'];
$balance = (string)$value['balance'];
$reason = (string)$value['reason'];
$owner1TypeID = (string)$value['owner1TypeID'];
$owner2TypeID = (string)$value['owner2TypeID'];
$refID = (string)$value['refID'];
$amount = (string)$value['amount'];


// put variables into array
$filteredAssets[$key] = array(
  'date'=>$date,
  'refID'=>$refID,
  'refTypeID'=>$refTypeID,
  'ownerName1'=>$ownerName1,
  'ownerID1'=>$ownerID1,
  'ownerName2'=>$ownerName2,
  'ownerID2'=>$ownerID2,
  'argName1'=>$argName1,
  'argID1'=>$argID1,
  'balance'=>$balance,
  'reason'=>$reason,
  'owner1TypeID'=>$owner1TypeID,
  'owner2TypeID'=>$owner2TypeID,
  'amount'=>$amount);
}

/* // For debugging / testing: print filtered Assets
print_r($filteredAssets);
echo $filteredAssets[0]['refID'];
echo "<br><br>";
foreach ($filteredAssets as $key => $asset) {
  echo $asset['refID']."<br>";
  echo $asset['refTypeID']."<br>";
  echo $asset['amount']."<br>";
  echo "<br><br>";
}*/

// check if the new filtered array is populated
if ($filteredAssets) {echo "[5] Data successfully stored in array\n\n";} else {die("[5] Error storing data in array\n");}

//Save the filtered array in the database
echo "Transfer to Database (x=doubles, #=new entry):\n";

// loop the filtered array
foreach ($filteredAssets as $key => $asset) {

// query if refID is already in database
$doublecheck = mysqli_query($conn, "SELECT * FROM walletjournal WHERE refID='".$asset['refID']."'");

// if refID is in database, print x
  if(mysqli_num_rows($doublecheck) > 0) {
    echo "x";
  } else {

// if refID is not in database, save it to database and print #

$stmt = $conn->prepare("INSERT INTO walletjournal (date, refID, refTypeID, ownerName1, ownerID1, ownerName2, ownerID2, argName1, argID1, balance, reason, owner1TypeID, owner2TypeID, amount)
                                              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssssssssss", $date, $refID, $refTypeID, $ownerName1, $ownerID1, $ownerName2, $ownerID2, $argName1, $argID1, $balance, $reason, $owner1TypeID, $owner2TypeID, $amount);

$date=$asset['date'];
$refID=$asset['refID'];
$refTypeID=$asset['refTypeID'];
$ownerName1=$asset['ownerName1'];
$ownerID1=$asset['ownerID1'];
$ownerName2=$asset['ownerName2'];
$ownerID2=$asset['ownerID2'];
$argName1=$asset['argName1'];
$argID1=$asset['argID1'];
$balance=$asset['balance'];
$reason=$asset['reason'];
$owner1TypeID=$asset['owner1TypeID'];
$owner2TypeID=$asset['owner2TypeID'];
$amount=$asset['amount'];

if (!$stmt->execute()) {printf("Erromessage: %s\n", mysqli_error($conn));
} else { echo "#";}
}
}

echo "\n\nDone. Fly safe."."\n";
echo "--------------------------"."\n";
?>
