<?php

ini_set('max_execution_time', 300); //300 seconds = 5 minutes



set_include_path('classes/');
spl_autoload($class);
spl_autoload_extensions('.php');
spl_autoload_register();

require 'vendor/autoload.php';

$dbjson = new DBjson($config);


$dbjson->installDB('dbdata');
$dbjson->createDB('mydata');
$dbjson->newCollection('testcollection');

//$dbjson->dropDB('mydata');
//$dbjson->removeCollection('mycollection');
//$dbjson->deleteDocument(rand(1,500));
//Helper::print_pre($dbjson->getCollection());
//foreach ($collection_array as $key => $value) {
//    echo '<br>Filename:' . $value;
//}

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, 'http://loripsum.net/api/1');
$lorum_content_json = json_encode(curl_exec($curl));
curl_close($curl);

//Helper::print_pre($lorum_content_json);

for ($i = 0; $i < 100000; $i++) {
    $dummy_json_objects_array[] = file_get_contents('templates/json/dummydata.json');
}
$dummy_json = json_encode($dummy_json_objects_array);

//Helper::print_pre($dummy_json_objects_array);
//die();

function insert_test_data($dbjson, $data) {
    do {
        $database_insert_size_bytes += $dbjson->insertDocument(json_decode($data));
        $database_insert_documents++;
        //echo '<br>Database size: ' . number_format($database_insert_size_bytes, 2) . ' bytes<br>';
    } while ($database_insert_size_bytes < 10000000000);

    return array(
        'size' => $database_insert_size_bytes,
        'documents' => $database_insert_documents
    );
}

$database_insert_array = insert_test_data($dbjson, $dummy_json);
$database_collection_info = $dbjson->getCollectionInfo();

echo '<br>Inserted: ' . Helper::getNiceFileSize($database_insert_array['size']) . ' into collection';
echo '<br>' . $database_insert_array['documents'] . ' new documents added to collection';
echo '<br><br>';
echo '<br>Total documents in collection: ' . $database_collection_info['info']['count'];
echo '<br>Total collection size: ' . Helper::getNiceFileSize($dbjson->getCollectionSize());
echo '<br><br>';
echo '<br>All collections size: ' . Helper::getNiceFileSize($dbjson->getAllCollectionsSize());


//Helper::print_pre($database_insert_array);
//$dbjson->insertDocument($lorum_content_json);

$document_json_contact = $dbjson->getDocument('01ea601905f7df8805fa110c3611923834faec242ecfe5fb986bc26281c15bb907c99d12c89d6fb5f999bbe88f7c1cbfb93b46976c3553fa82773ed8cea87e52');


$document_object = json_decode($document_json_contact);
//Helper::print_pre($document_object);
echo '<p>';
echo '<br>Firstname: ' . $document_object->firstname;
echo '<br>Lastname: ' . $document_object->lastname;
echo '<br>Street: ' . $document_object->adress->street;
echo '<br>Postcode: ' . $document_object->adress->postcode;
echo '<br>State: ' . $document_object->adress->state;
echo '<br>Country: ' . $document_object->adress->country;
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



