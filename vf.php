<?php 

header('Content-Type: application/json');

/* gets the data from a URL */
function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

$url = 'http://flushot.healthmap.org/getMarkers.php?lat='.$_REQUEST['lat'].'&lon='.$_REQUEST['lng'];

echo get_data($url);

?>