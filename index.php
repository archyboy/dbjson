<?php

set_include_path('classes/');
spl_autoload($class);
spl_autoload_extensions('.php');
spl_autoload_register();



require_once 'interfaces/dbjson.php';
require_once 'classes/dbjson.php';
require_once 'classes/test_dbjson.php';
require_once 'vendor/autoload.php';

use Illuminate\Filesystem\Filesystem as SomeFS;
use Symfony\Component\Finder\Finder;
use Classes\DBjson\DBjson;
use Classes\Test_DBjson\Test_DBjson;

ini_set('max_execution_time', 1800); //300 seconds = 5 minutes

$dbjson = new Classes\DBjson\DBjson($dbjson_dir);
$dbjson->chgrp = 'archy';
$dbjson->chown = 'archy';


$fs = new SomeFS;


try {

    $dbjson->install('dbdata');
    $dbjson->createDB('mydatabase');
    $dbjson->newCollection('mycollection');
//    $dbjson->newCollection('mycollection3');
//    $dbjson->newCollection('mycollection4');
} catch (Exception $e) {
    echo $e->getMessage();
}

//$dbjson->setDB('mydata');
//$dbjson->getDocument('1234');
//echo $dbjson->collection_dir;
//$dbjson->install('dbdata3');
//echo 'Final';
//$dbjson->newCollection('sweetercollection');
//$dbjson->dropDB('mydata');
//$dbjson->removeCollection('sweetercollection');
//$dbjson->createDB('mydata');
//$dbjson->newCollection('sweetercollection');
//$dbjson->deleteDocument(rand(1,500));
//
// Setting up a test environment

Helper::print_pre($dbjson->debug);
//die();
$test_dbjson = new Test_DBjson('dbdata');

$dummy_template = file_get_contents('templates/json/dummydata.json') . PHP_EOL;
$dummy_object = json_decode($dummy_template);

echo $dummy_template;
Helper::print_pre($dummy_object);

for ($i = 0; $i < 5; $i++) {
    $dummy_objects_array[] = $dummy_object;
}

//Helper::print_pre($dummy_objects_array);

$time_start = microtime(true);
$dummy_json = json_encode($dummy_objects_array);
$insert_dummy_json = $test_dbjson->insert_test_data($dbjson, $dummy_json, 3000);
$database_collection_info = $dbjson->getCollectionInfo($dbjson->collection_dir);

$time_finish = microtime(true);
$time_total = ($time_finish - $time_start) / 60;
echo '<br>Insert time: ' . number_format($time_total, 2) . ' minutes';


echo '<br>Inserted: ' . Helper::getNiceFileSize($insert_dummy_json['size']) . ' into collection';
echo '<br>' . $insert_dummy_json['last'] . ' new documents added to collection';
echo '<br><br>';
echo '<br>Total documents in collection: ' . $database_collection_info['info']['count'];
echo '<br>Active collection size: ' . Helper::getNiceFileSize($dbjson->getCollectionSize($dbjson->collection_dir));
echo '<br><br>';
echo '<br>All collections size: ' . Helper::getNiceFileSize($dbjson->getAllCollectionsSize($dbjson->database_dir));

//Helper::print_pre($database_insert_array);
//$dbjson->insertDocument($lorum_content_json);

$document_json_contact = $dbjson->getDocument('1234');
$document_object = json_decode($document_json_contact);
//echo $document_json_contact;
//Helper::print_pre($document_object);
echo '<p>';
echo '<br>Firstname: ' . $document_object[0]->firstname;
echo '<br>Lastname: ' . $document_object[0]->lastname;
echo '<br>Street: ' . $document_object[0]->adress->street;
echo '<br>Postcode: ' . $document_object[0]->adress->postcode;
echo '<br>State: ' . $document_object[0]->adress->state;
echo '<br>Country: ' . $document_object[0]->adress->country;
echo '</p>';

//$collection_array = $dbjson->getCollection();
//Helper::print_pre($collection_array);
//$collection_files = $dbjson->getCollection('list_array');
//Helper::print_pre($collection_files);
//$collection_data_array = $dbjson->getCollection('data_array');
//Helper::print_pre($collection_data_array);
//$collection_data_json = $dbjson->getCollection('data_json');
//Helper::print_pre($collection_data_json);
//$collection_data_json_decoded = json_decode($dbjson->getCollection('data_json'));
//Helper::print_pre($collection_data_json_decoded);
//Helper::print_pre($lorum_content);
//Helper::print_pre($dbjson);

Helper::print_pre($dbjson);
