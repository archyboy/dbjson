<?php

namespace DBjson\Builder;

use DBjson\DBEngine\DBEngine as DBjson;
use DBjson\Connector\Connector as Connector;

class Builder extends DBjson {
  protected $connector;
  protected $database;


  public function __construct(Connector $connector) {
    if($connector->connect()) {
      $this->connector = $connector;
      $this->database = new DBjson($connector);
    }
  }

  public function getDatabase() {
    return $this->database;
  }

  public function query($query) {
    $result = $this->database->searchDocument($query);

    return $result;
  }
}
