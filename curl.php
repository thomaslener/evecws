<?php
/*
#EVE Corp Wallet Script - functions.php
Required by evecws.php
*/

// Get Data from API via curl

function makeApiRequest($url) {

  $ch = curl_init($url);

  curl_setopt_array($ch, array(
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false));
  $data = curl_exec($ch);
  curl_close($ch);
  return new SimpleXMLElement($data);
}

?>
