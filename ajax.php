<?php

require_once('db.php');
$db = new Database();
$db->connect('localhost', 'mpower', '!QAZ@WSX#EDC', 'mpower');

ob_start();
var_dump($_REQUEST);
$data = ob_get_clean();
$fp = fopen("ajax-log.txt", "a");
fwrite($fp, $data);
fclose($fp);  

// get id and name from authentication step
// if the user doesn't exist, insert into users table
if ($_REQUEST['action'] == 'user_checkin') {
  $db->user_checkin($_REQUEST['id'], $_REQUEST['name']);
}

// get the list of friends that use the app
// update friend network table 
// recompute score using new friend network 
if ($_REQUEST['action'] == 'user_checkin_friends') {
  if(isset($_REQUEST['app_friends'])){
    $results=$db->user_checkin_friends($_REQUEST['id'], $_REQUEST['num_friends'], $_REQUEST['app_friends']);
  } else {
    $results=$db->user_checkin_friends($_REQUEST['id'], $_REQUEST['num_friends'], 0);
  }
  header('Content-Type: application/json');
  echo json_encode(array('score' =>   $results['score'],
                         'friendcount' => $results['friendcount'],
                         'namelist' => $results['namelist'],
                         'idlist' => $results['idlist'],
                         'newuser' => $results['newuser']));   
}

if ($_REQUEST['action'] == 'phone_alert') {
  $results = $db->user_phone_alert($_REQUEST['id'], $_REQUEST['phone']);
  header('Content-Type: application/json');
  if($results != -1){
    echo json_encode(array('message' => 'Phone alert sign up success!'));
  } else {
    echo json_encode(array('message' => 'Phone alert sign up failure!'));
  }
}

if ($_REQUEST['action'] == 'email_alert') {
  $results = $db->user_email_alert($_REQUEST['id'], $_REQUEST['email']);
  header('Content-Type: application/json');
  if($results != -1){
    echo json_encode(array('message' => 'Email alert sign up success!'));
  } else {
    echo json_encode(array('message' => 'Email alert sign up failure!'));
  }
}


if ($_REQUEST['action'] == 'vaccine_checkin') {
  $results = $db->user_new_vaccination($_REQUEST['id'], $_REQUEST['date'], $_REQUEST['location']);
  header('Content-Type: application/json');
  if($results != -1){
    echo json_encode(array('valid' => 1, 'message' => 'Vaccination success! Thanks for being a flu hero!'));
  } else {
    echo json_encode(array('valid' => 0, 'message' => "It's been less than six months since your last flu shot, so we don't count this one. Sorry!"));
  }
}
// using friend network table, return scores of all friends
// also return overall top scoring individuals
if ($_REQUEST['action'] == 'scoreboard') { 
  $scoreboardarray = $db->get_scoreboard($_REQUEST['id']);
  header('Content-Type: application/json');
  echo json_encode(array('global' => $scoreboardarray[0], 'friends' => $scoreboardarray[1]));
}

if ($_REQUEST['action'] == 'getscore') {
  $results = $db->user_getscore($_REQUEST['id']);
  echo json_encode(array(
    'basescore' => $results['basescore'], 
    'friendscore' => $results['friendscore'], 
    'sharescore' => $results['sharescore'], 
    'vacscore' => $results['vacscore']));
}


$allowed_states = array(
  'United-States', 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado',
  'Connecticut', 'Delaware', 'District-of-Columbia', 'Florida', 'Georgia', 'Hawaii',
  'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine',
  'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
  'Montana', 'Nebraska', 'Nevada', 'New-Hampshire', 'New-Jersey', 'New-Mexico', 'New-York',
  'North-Carolina', 'North-Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
  'Rhode-Island', 'South-Carolina', 'South-Dakota', 'Tennessee', 'Texas', 'Utah',
  'Vermont', 'Virginia', 'Washington', 'West-Virginia', 'Wisconsin'
);

$states_short = array(
  'AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA',
  'KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NV','NH','NJ','NM','NY',
  'NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI'
);

function process_trend($state) {
  global $allowed_states, $states_short;
  $data = array();
  if (in_array($state, $allowed_states)) {
    $file = file('trends/'.$state.'.txt');
    $max = 0.0;
    $i = 0;
    foreach ($file as $line) {
      $tokens = explode(' ', $line);
      $timestamp = $tokens[0];
      $value = floatval(trim($tokens[1]));
      if ($value > $max) {
        $max = $value;
      }
      $data[$i] = array($timestamp, $value);
      $i++;
    }
    for ($i = 0; $i < count($data); $i++) {
      $data[$i][1] = round($data[$i][1] / $max, 2);
    }
  }
  
  return $data;
}

if ($_REQUEST['action'] == 'trend') {

  $state = $_REQUEST['state'];
  
  $data = process_trend($state); 
  $national = process_trend('United-States');
  
  if (time() < strtotime(date('Y').'-07')) {
    $start = strtotime((date('Y')-1).'-07');
    $end = strtotime((date('Y')).'-07');
  } else {
    $start = strtotime((date('Y')).'-07');
    $end = strtotime((date('Y')+1).'-07');
  }
    
  echo json_encode(array(
    'message'=>'Loaded '.$state.' flu trend data',
    'trends' => $data,
    'national' => $national,
    'start' => $start,
    'end' => $end));
  
}

if ($_REQUEST['action'] == 'user_location') {
  $results = $db->user_location($_REQUEST['id'], $_REQUEST['city'], $_REQUEST['state']);
  // echo json_encode(array('message' => 'You are checked into ' . $_REQUEST['city'] . ', ' . $_REQUEST['state']));
}

$db->disconnect();
exit();
?>
