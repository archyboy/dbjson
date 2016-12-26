<?php

$obj = new stdObject();
$obj->name = "Nick";

$obj->getInfo = function($stdObject) { // $stdObject referred to this object (stdObject).
    echo $stdObject->name . " " . $stdObject->surname . " have " . $stdObject->age . " yrs old. And live in " . $stdObject->adresse;
};
