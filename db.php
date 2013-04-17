<?php

# constants
$USERSIGNUP = 10;
$FRIENDSIGNUP = 10;
$VACCINATION = 100;
$FRIENDVACCINATION = 100;
$SHARE = 20;


class Database {

  private $mysqli;

  public function __construct() {
    $this->mysqli = NULL; 
  }
  
  public function connect($host, $username, $password, $database) {
    $this->mysqli = new mysqli($host, $username, $password, $database);
    if ($this->mysqli->connect_errno) {
      throw new Exception($this->mysqli->connect_error);
    }
  }
  
  public function disconnect() {
    if (!is_null($this->mysqli)) {
      $this->mysqli->close();
    }
  }
  
  private function sanitizeString($value) {
    return $this->mysqli->real_escape_string(trim($value));
  }
  
  private function query($sql) {
    $timestamp = date('Y-m-d H:i:s');
    $entry = $timestamp . ' ' . str_replace("\n", " ", $sql);
    $entry = str_replace("  ", " ", $entry);
    $entry = str_replace("  ", " ", $entry);
    $entry = str_replace("  ", " ", $entry);
    file_put_contents('queries.txt', $entry."\n", FILE_APPEND); 
    return $this->mysqli->query($sql);
  }
  
  public function user_exists($id) {
    $id = $this->sanitizeString($id);
    $sql = "SELECT COUNT(*) as count from users WHERE `id`='$id'";
    if ($result = $this->query($sql)) {
      $row = $result->fetch_assoc();
      return intval($row['count']) > 0;
    }
    return false;
  }
  
  # returns insert ID if success, -1 if error
  public function user_create($id, $name) {
    $id = $this->sanitizeString($id);
    $name = $this->sanitizeString($name);
    $timestamp = time();
    global $USERSIGNUP;
    $sql = "INSERT INTO users (`id`, `name`, `timestamp`, `friends`, `basescore`) 
            VALUES ('$id', '$name', '$timestamp', '0', '$USERSIGNUP');";
    if ($result = $this->query($sql)) {
      return $this->mysqli->insert_id;
    } else {
      return -1;
    }
  }
  # returns insert ID if success, -1 if error
  public function user_phone_alert($id, $phone) {
    require "Services/Twilio.php";
    $phone = $this->sanitizeString($phone);
    $phone = preg_replace("/[^0-9]/","",$phone);
    $accountSid = 'ACdf39e8a1bcd42b2d079508446e48ad02';
    $authToken = 'd48f45477df89da4af67870e53e3c598';
    $client = new Services_Twilio($accountSid, $authToken);
    $from = '6578889671';
    $body = "Thank you for signing up for flu alerts! We'll keep you informed. --Flu-Trackr";
    $client->account->sms_messages->create($from, $phone, $body);

    $sql = "UPDATE users SET phone = '$phone' WHERE id='$id'";
    if ($result = $this->query($sql)) {
      return $this->mysqli->insert_id;
    } else {
      return -1;
    }
  }

  # returns insert ID if success, -1 if error
  public function user_email_alert($id, $email) {
    $headers = 'From: webmaster@flu-trackr.com' . "\r\n"; 
    $headers .= 'Reply-To: webmaster@flu-trackr.com' . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $message = "Hello,<br><br>Thank you for signing up for Flu-Trackr's flu information alerts and being a Flu Hero! We'll 
keep you informed on vaccinations availability in your area as well as any outbreak information.<br><br>The Flu-Trackr Team<br><br>
If you would like to unsubscribe to flu alerts, click <a href='http://flu-trackr.com/ajax.php?action=unsubscribe&id=$id'>here</a>.";

    mail($email, 'Flu Alerts from Flu-Trackr Sign Up',$message, $headers);	
    $sql = "UPDATE users SET email = '$email' WHERE id='$id'";
    if ($result = $this->query($sql)) {
      return $this->mysqli->insert_id;
    } else {
      return -1;
    }
  }
  
  # returns insert ID if success, -1 if error
  public function user_checkin($id, $name) {
    if ($this->user_exists($id)) {
      return 0;
    } else {
      return $this->user_create($id, $name);
    }
  }
  
  public function user_getscore($id) {
    $sql = "SELECT basescore, vacscore, sharescore, friendscore from users where id = '$id'";
    if ($result = $this->query($sql)){
    $row = $result->fetch_assoc();
    return($row);}
    else { return -1; }
  }
  
  # returns user's new score, list of friend vaccinations since last login if no error, -1 otherwise
  public function user_checkin_friends($id, $num_friends, $app_friends) {
    if ($this->user_exists($id)) {
      $sql = "SELECT last_login, basescore, vacscore, sharescore from users WHERE id = '$id'";
      $result = $this->query($sql);
      $row = $result->fetch_assoc();
      $last_login = $row['last_login'];
      $basescore = count($app_friends)*$GLOBALS['FRIENDSIGNUP'];
      $vacscore = $row['vacscore'];
      $sharescore = $row['sharescore'];
      $new_user = 0;
      
      $sql = "UPDATE users SET basescore = '$basescore' WHERE id = '$id'";
      if(!$result = $this->query($sql)) return -1;
      
      
      if($last_login == 0)
        $new_user = 1;
      
      # delete all current friend entries
      $sql = "DELETE FROM friends WHERE user_id1 = '$id' OR user_id2 = '$id'";  
      $result = $this->query($sql);  
      $newscore = 0;
      $count = 0;
      $namelist = array();
      $idlist = array();
      
      foreach ($app_friends as $friendid) {
        $newscore = $newscore;
        $sql = "SELECT DISTINCT vaccinations.user_id, users.name FROM vaccinations INNER JOIN users ON vaccinations.user_id = users.id WHERE vaccinations.user_id = '$friendid' AND vaccinations.timestamp > '$last_login'";
        if ($result = $this->query($sql)) {
          $count = $count + ( $result->num_rows > 0 ) ? 1 : 0;
          if ($result->num_rows > 0){
          while ($row = $result->fetch_assoc()){
            $namelist[] = $row['name'];
            $idlist[] = $friendid;
          }}
        } else { return -1; }
          
        if($friendid < $id) {
          $sql = "INSERT INTO friends (`user_id1`,`user_id2`) VALUES ('$friendid', '$id')";
        } else {
          $sql = "INSERT INTO friends (`user_id1`,`user_id2`) VALUES ('$id', '$friendid')";
        }
        if(!$result = $this->query($sql)) return -1;
        
        # Recompute friends's scores
        $sql = "SELECT IFNULL(SUM(points), 0) FROM vaccinations WHERE user_id = '$friendid'";
        if ($result = $this->query($sql)) {
          $arr = $result->fetch_array();
          $newscore = $arr[0] + $newscore;
        } else {
          return -1;
        }
      }
     
      
      $sql = "UPDATE users SET friendscore = '$newscore' WHERE id = '$id'";
      if ($result = $this->query($sql)){
        $timestamp = time();
        $sql = "UPDATE users SET friends = '$num_friends', last_login = 
              '$timestamp' WHERE id = '$id'";
        if (!$result = $this->query($sql)) 
          return -1;
        return(array('score' => $basescore + $sharescore + $vacscore + $newscore, 'namelist' => $namelist, 'idlist' => $idlist, 'friendcount' => $count, 'newuser' => $new_user));
      } else {
        return -1;  
      }
      
    } else {
      return -1;
    }
    
    
  }
    
  
  # returns insert ID if success, 0 if no action (if no location is given) and -1 if error  
  public function user_location($id, $city, $state){
    if (($city != 'none') && ($state != 'United States')){
      $sql = "UPDATE users SET `city` = '$city', `state` = '$state' WHERE id ='$id'";
      if ($result = $this->query($sql)) {
        return($this->mysqli->affected_rows > 0);
      } else {
        return -1;
      }
    }
    return 0;
  }
  
  # returns insertion ID if success and -1 if error
  public function user_share($id, $points){
    $sql = "UPDATE users SET sharescore = sharescore + '$points' WHERE $id = '$id'";
    if ($result = $this->query($sql)) {
    return($this->mysqli->insert_id);
    } else {
      return -1;
    }
  }
  
  # returns insert ID if success, -1 if error
  public function user_new_vaccination($id, $date, $location){
    global $VACCINATION;
    $sql = "SELECT date from vaccinations WHERE user_id = '$id' ORDER BY date DESC LIMIT 1";
    if ($result = $this->query($sql)){
      if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $lastshot = $row['date'];
      }
      else{ $lastshot = 0; }
      
    } else { return -1; }
    $timestamp = time(); 
    if (strtotime($date) - $lastshot < 3600 * 24 * 180){
      return -1;
    }
    $sql = "INSERT INTO vaccinations (`user_id`,`date`,`location`,`timestamp`, 
            `points`) VALUES ('$id','" . strtotime($date) . "','$location', '$timestamp', '$VACCINATION')";
    if ($result = $this->query($sql)) {
      $sql = "UPDATE users SET vacscore = vacscore + '$VACCINATION' WHERE id = '$id'";
      if ($result = $this->query($sql)){
        return $this->mysqli->insert_id;
      } else { return -1; }
    } else {
      return -1;
    }
  }
  
  # returns score from you and friends using app, your number of vaccines, your friends getting vaccinated, 
  
  # returns IDs and scores of top 10 of global and friends, or -1 if error
  public function get_scoreboard($id){
    # global
    $sql = "SELECT id, name, `city`, `state`, (basescore + friendscore + sharescore + vacscore) as score FROM users ORDER BY (basescore + friendscore + sharescore + vacscore) DESC LIMIT 5";
    if ($result = $this->query($sql)) {
      $global=array();
      while ($row=$result->fetch_assoc()){
        $global[] = array('id' => $row['id'],'name' => $row['name'],
          'score' => $row['score'], 'city'=>$row['city'], 'state'=>$row['state']);
      }
    } else { return -1; }
    $return = array(0,0);
    # friends
    $sql = "SELECT DISTINCT users.id, users.name, users.city, users.state, (basescore + friendscore + sharescore + vacscore) as score FROM users INNER 
      JOIN friends ON (friends.user_id1 = '$id' OR friends.user_id2 = '$id' ) 
      AND (friends.user_id1 = users.id OR friends.user_id2 = users.id) ORDER BY 
      (basescore + friendscore + sharescore + vacscore) DESC LIMIT 20";
    if ($result = $this->query($sql)) {
      if($result->num_rows == 0){
        #no friends, how sad - return yourself
        $sql = "SELECT id, name, city, `state`, (basescore + friendscore + sharescore + vacscore) as score FROM users WHERE id = '$id'";
        if($result = $this->query($sql)) {
          $row = $result->fetch_assoc();
          $friends=array();
          $friends[] = array('id' => $row['id'], 'name' => $row['name'],
                          'score' => $row['score'], 'city'=>$row['city'], 'state'=>$row['state']);
        } else { return -1; }
      } else {
        $friends=array();
        while ($row=$result->fetch_assoc()){
          $friends[] = array('id' => $row['id'],'name' => $row['name'],'score' 
                              => $row['score'], 'city'=>$row['city'], 'state'=>$row['state']);
        }
      }
    } else { return -1; }
    return(array($global,$friends));
  }
     
  // legacy code below, good for reference  
    
  public function update($object) {
    $table = $object->get('table');
    $fields = $object->getFields();
    $id = $object->get('id');
    if ($this->exists($table, 'id', $id)) {
      $set = array();
      foreach ($fields as $name=>$field) {
        if ($field['type'] != 'virtual') {
        $set[] = sprintf("`%s`='%s'", (string)$name, (string)$field['value']);
        }
      }include 'vf.php';
      
      $set = implode(',', $set);
      $sql = "UPDATE $table SET $set WHERE `id`='$id' LIMIT 1;";
      if ($result = $this->query($sql)) {
        return $this->mysqli->affected_rows > 0;
      } else {
        throw new Exception("Failed to execute query <pre>$sql</pre>");
      }
    } else {
      throw new Exception('Cannot update nonexistant database entry.');
    }
    return false; 
  }
  
  /**
   * Get user from database 
   *
   * @param string $field 
   * @param string $value 
   * @return User or NULL if user does not exist
   * @author Chi Feng
   */
  public function getUser($field, $value, $options = NULL) {
    switch ($field) {
      case 'id':
        $value = intval($value);
      case 'email':
        $value = $this->sanitizeString($value);
        break;
      default:
        throw new Exception("Invalid field '$field'.");
        break;
    }
    $sql = "SELECT users.* FROM users 
            WHERE users.`$field`='$value' ORDER BY users.id LIMIT 1";
    if ($result = $this->query($sql)) {
      $arr = $result->fetch_assoc();
      $result->close(); 
      return new User($arr, $this);
    } else {
      throw new Exception("Failed to execute query <pre>$sql</pre>");
    }
    return NULL;
  }
  
  public function getAutocompleteSuggestions($field, $value) {
    $value = $this->sanitizeString($value);
    $sql = '';
    if ($field == 'username') {
      $sql = "SELECT username FROM users 
              WHERE username LIKE '$value%' LIMIT 5";
    } elseif ($field == 'location') {
      $sql = "SELECT city as location FROM locations 
              WHERE city LIKE '$value%' 
              ORDER BY population DESC LIMIT 5";
    } else {
      throw new Exception("Unknown field '$field'");
    }
    $suggestions = array();
    if ($result = $this->query($sql)) {
      while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row[$field];
      }
      $result->close(); 
    }
    return $suggestions;
  }
  
  public function get($table, $field, $value) {
    $value = $this->sanitizeString($value);
    $sql = "SELECT * FROM $table WHERE `$field`='$value';";
    $rows = array();
    if ($result = $this->query($sql)) {
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      $result->close(); 
    }
    return $rows;
  }

}

?>
