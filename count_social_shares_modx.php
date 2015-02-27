<?php
/**
 * Social Share Buttons 1.0
 * Copyright 2015, Sebastian Apprecht (sebastian@coding-pioneers.com)
 * 
 * @autor Sebastian Apprecht (sebastian@coding-pioneers.com)
 * @version 1.0
 * 
 * Verarbeitet die übergebene Domain und gibt die Ergebnisse zurück.
 * 
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

//header('Access-Control-Allow-Origin: *');

$url = "http://www.coding-pioneers.com/";

if(isset($_POST['url'])) 
	$url = $_POST['url'];

/**
 * Liefert die Anzahl der tweets über die übergebene URL
 * @param  string $url URL, die überprüft werden soll
 * @return int         Anzahl, der Shares der URL oder 0 bei Fehler
 */
function twitter($url='') {

   $json_string = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url='.$url);
   $json=json_decode($json_string,true);

   if($json['count'])
      return intval($json['count']);
   
   return 0;
}

/**
 * Liefert die Anzahl der "+1" die auf die überegebene URL
 * @param  string $url URL, die überprüft werden soll
 * @return int         Anzahl, der Shares der URL oder 0 bei Fehler
 */
function google($url='') {

   // CURL initiieren & Parameter Setzen
   $ch=curl_init();
   curl_setopt($ch,CURLOPT_URL,'https://clients6.google.com/rpc');
   curl_setopt($ch,CURLOPT_POST,true);
   curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
   curl_setopt($ch,CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
   curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/json'));
   
   // CURL Resultate in String schreiben und schließen
   $curl_results = curl_exec($ch);
   curl_close($ch);

   // JSON Parsen
   $json = json_decode($curl_results,true);
   
   // Rückgabe vorbereiten
   if($json[0]['result']['metadata']['globalCounts']['count'])
      return intval($json[0]['result']['metadata']['globalCounts']['count']);
   
   return 0;
}

/**
 * Liefert die Anzahl der Shares die auf die überegebene URL
 * @param  string $url URL, die überprüft werden soll
 * @return int         Anzahl, der Shares der URL oder 0 bei Fehler
 */
function facebook($url='') {

   // Auslesen über Facebook Graph
   $source='http://graph.facebook.com/?id='.$url;
   $result=json_decode(file_get_contents($source));

   // Rückgabe vorbereiten
   if($result->shares)
      return intval($result->shares);

   return 0;
}

/**
 * Liefert die Anzahl der "+1" die auf die überegebene URL
 * @param  string $url URL, die überprüft werden soll
 * @return int         Anzahl, der Shares der URL oder 0 bei Fehler
 */
function xing($url='') {

   // CURL initiieren & Parameter Setzen
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, 'https://www.xing-share.com/spi/shares/statistics');
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS, 'url='.$url);
   curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

   // CURL Resultate in String schreiben und schließen
	$curl_results  = curl_exec($ch);
	curl_close($ch);

   // JSON Parsen
   $json = json_decode($curl_results,true);

   // Rückgabe vorbereiten
   if($json['share_counter'])
      return intval($json['share_counter']);

   return 0;
}

// Einzelne Interaktionen
$interaktionen['twitter']  = twitter($url);
$interaktionen['google']   = google($url);
$interaktionen['facebook'] = facebook($url);
$interaktionen['xing']     = xing($url);
// Summe der Interaktionen
$interaktionen['all'] = array_sum($interaktionen);


// Modx Platzhalter vorbereiten für das Template
$modx->toPlaceholders(array(
   'all' => $interaktionen['all'],
   'twitter' => $interaktionen['twitter'],
   'google' => $interaktionen['google'],
   'xing' => $interaktionen['xing'],
   'facebook' => $interaktionen['facebook'],
   'url' => $url,
),'counter');

return '';