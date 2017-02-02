<?php

namespace DBjson\Connector;

class Connector {
  private $username;
  private $password;
  protected $database_name;
  protected $data_dir = 'mydata';
  protected $database_dir;

  public function __construct($connect_json) {
    $connect_object = json_decode($connect_json);

    $this->password = $connect_object->password;
    $this->username = $connect_object->username;
    $this->database_name = $connect_object->database;
    return $this;
  }

  public function connect() {
    try {
      if(is_dir($database_dir = $this->data_dir . DIRECTORY_SEPARATOR . $this->database_name)) {
        if(!is_writable($database_dir)) {
          throw new Exception('Writable database directory check failed! Please inspect that you database directory folder is writable. Hint: "sudo chmod 755 ' . $database_dir . ' -R"');
        }
        $this->database_dir = $database_dir;
        return true; // Returns true if user credentials are correct
      } else {
        throw new Exception('Could not connect to database. Check your config settings! Hint: Did you miss-spell something in json data string?');
      }
    } catch (Exception $e) {
      echo $e->GetMessage();
    }
  }
}
