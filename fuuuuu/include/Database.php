<?php

if (!defined('INCLUDE_GUARD')) { header("HTTP/1.0 403 Forbidden"); die(); }

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
  
  public function exists($table, $field, $value) {
    $value = ($field == 'id') ? intval($value) : $this->sanitizeString($value);
    $sql = "SELECT COUNT(*) AS count FROM $table WHERE `$field`='$value'";
    if ($result = $this->query($sql)) {
      $row = $result->fetch_assoc();
      return intval($row['count']) > 0;
    }
    return false;
  }
  
  public function insert($object) {
    $table = $object->get('table');
    $fields = $object->getFields();
    $names = array();
    $values = array();
    foreach ($fields as $name=>$arr) {
      if ($arr['type'] != 'virtual') {
        $names[] = $name;
        $values[] = ($arr['type'] == 'int') ? 
          intval($arr['value']) : $this->sanitizeString($arr['value']);
      }
    }
    $fields = '(`'.implode('`,`', $names).'`)';
    $values = '(\''.implode('\',\'', $values).'\')';
    $sql = "INSERT INTO $table $fields VALUES $values;";
    if ($result = $this->query($sql)) {
      return $this->mysqli->insert_id;
    } else {
      throw new Exception("Failed to execute query <pre>$sql</pre>");
    }
  }
  
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
      }
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
   * Gets rows from database table (paginated)
   *
   * @param string $table name of database table
   * @param int $page page number
   * @return array rows
   * @author Chi Feng
   */
  public function getPaginated($table, $page) {
    $numPerPage = 10; 
    $lower = ($page - 1) * $numPerPage;
    $sql = "SELECT * FROM $table WHERE 1 LIMIT $lower, $numPerPage";
    if ($table == 'numbers') { 
      $sql = "SELECT numbers.*, users.username FROM numbers 
              LEFT JOIN users ON numbers.user_id=users.id
              WHERE 1 LIMIT $lower, $numPerPage";
    }      
    elseif ($table == 'requests') { 
      $sql = "SELECT requests.*, users.username FROM requests
              LEFT JOIN users ON requests.user_id=users.id
              WHERE 1 LIMIT $lower, $numPerPage";
    }      
    $arr = array();
    if ($result = $this->query($sql)) {
      while ($row = $result->fetch_assoc()) {
        $arr[] = $row;
      }
      $result->close(); 
    }
    return $arr;
  }
  
  public function getLocationID($location) { 
    $location = $this->sanitizeString($location);
    $sql = "SELECT `zip` as `id` from `locations` WHERE `city`='$location'";
    if ($result = $this->query($sql)) {
      $arr = $result->fetch_assoc();
      $result->close(); 
      return $arr['id'];
    } else {
      throw new Exception("Failed to execute query <pre>$sql</pre>");
    }
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
