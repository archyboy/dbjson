<?php

set_include_path('classes/');
spl_autoload($class);
spl_autoload_extensions('.php');
spl_autoload_register();

//$testdb = new Test_DBjson($config);

require 'vendor/autoload.php';

echo file_get_contents('test.html');

$dbjson = new DBjson($config);
$finder = Finder;

//$dbjson->testFinder();
//$dbjson->testFilesystem();

$dbjson->installDB('dbdata');
$dbjson->createDB('mydata');
$dbjson->newCollection('mycollection');

//$dbjson->dropDB('mydata');
//$dbjson->removeCollection('mycollection');
//$dbjson->deleteDocument(rand(1,500));

Helper::print_pre($dbjson->getCollection());

$collection_array = $dbjson->getCollection();

$filename = $collection_array[0];

echo 'Filename:'.$filename;

//insert_test_data($dbjson);
//$lorum_content_json = json_encode($content = file_get_contents('http://loripsum.net/api/1'));
//$dbjson->insertDocument($lorum_content_json);

function insert_test_data($dbjson)
{
    $database_size_bytes = Helper::dir_size('dbdata');
    while ($database_size_bytes < 100000) {
        //for($i=0; $i < 1000; $i) {
        $dbjson->insertDocument('{
      "firstname" : "Mr. Harley",
      "lastname" : "Davidson",
      "adress" : {
        "street" : "Mc Gregor St. 543",
        "postcode" : "345543",
        "state" : "WE",
        "country" : "United States Of Stupidoca"
      }
    }');
        //}
    }
}

$database_size_kilobytes = $database_size_bytes / 1024;
$database_size_megabytes = $database_size_kilobytes / 1000;
$database_size_gigabytes = $database_size_megabytes / 1000;
$database_size_terrabytes = $database_size_gigabytes / 1000;

echo '<br>Database size: '.number_format($database_size_bytes, 2).' bytes<br>';

//$document_array = json_decode($document_json, true);
//Helper::print_pre($document_array);
//$document_json_contact = $dbjson->getDocument(3);
//$document_json_lorum = $dbjson->getDocument(6);
echo $document_json_contact;
echo $document_json_lorum;

$document_object = json_decode($document_json_contact);
Helper::print_pre($document_object);
echo '<br>Firstname: '.$document_object->firstname;
echo '<br>Lastname: '.$document_object->lastname;
echo '<br>Street: '.$document_object->adress->street;
echo '<br>Postcode: '.$document_object->adress->postcode;
echo '<br>State: '.$document_object->adress->state;
echo '<br>Country: '.$document_object->adress->country;

//$collection_array = $dbjson->getCollection();
//Helper::print_pre($collection_array);
//$collection_files = $dbjson->getCollection('list_array');
//Helper::print_pre($collection_files);
//$collection_data_array = $dbjson->getCollection('data_array');
//Helper::print_pre($collection_data_array);
$collection_data_json = $dbjson->getCollection('data_json');
//Helper::print_pre($collection_data_json);
//$collection_data_json_decoded = json_decode($dbjson->getCollection('data_json'));
//Helper::print_pre($collection_data_json_decoded);
//Helper::print_pre($lorum_content);

Helper::print_pre($dbjson);
