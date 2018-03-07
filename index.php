<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<?php
require __DIR__ . '/vendor/autoload.php';
require '../../3rdparty/kint/Kint.class.php';
require 'classes/Helper.php';

use DBjson\Connector\Connector as Connector;
use DBjson\DBEngine\DBEngine as DBEngine;
use DBjson\Builder\Builder as Builder;

use Symfony\Component\Finder\Finder;

ini_set('max_execution_time', 1800); //300 seconds = 5 minutes


try {
// ------------------------------------------- Start connecting  ----------------------------------------
// Connection data
$json_connect = '{
  "username" : "archyboy",
  "password" : "xxxxx",
  "database" : "mydatabase"
}';
// New instance of Connector
$connector = new Connector($json_connect);
$dbjson = new DBEngine($connector);
$dbjson->dropDB($database_name);

// ------------------------------------------- Start builder init ----------------------------------------
$builder = new Builder($connector);

Kint::dump($builder);
// ------------------------------------------- Start builder query ----------------------------------------
// Query data
$json_query = '{
  "collection" : "mycollection",
  "lastname" : "Davidson"
}';
$result_query = $builder->query($json_query);

// ------------------------------------------- Start local init ----------------------------------------


$dbjson = $builder->getDatabase();

//$dbjson = new DBjson($connector);


// ------------------------------------------- Start install root data directory ----------------------------------------
    //$dbjson->install('mydata');

// ------------------------------------------- Start local install ----------------------------------------

    //$dbjson->createDB('mydatabase');
//    $dbjson->createDB('mydatabase2');
    $dbjson->newCollection('mycollection');
//    $dbjson->newCollection('mycollection3');
//    $dbjson->newCollection('mycollection4');


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
//die();
//$test_dbjson = new Test_DBjson('dbdata');

$dummy_user_json_template = file_get_contents('templates/json/dummydata.json') . PHP_EOL;
$dummy_user_json_data = file_get_contents('https://randomuser.me/api/');
$dummy_user_object = json_decode($dummy_user_json_template);

for ($i = 0; $i < 1; $i++) {
    $dummy_objects_array[] = $dummy_user_object;
}

//Helper::print_pre($dummy_objects_array);
$time_start = microtime(true);
$dummy_json = json_encode($dummy_user_object);
//echo $dummy_json;
$dbjson->index_max = 10000;
$insert_dummy_json = $dbjson->insert_test_data($dummy_json, 1000);
$time_finish = microtime(true);
$time_total = ($time_finish - $time_start);

$database_collection_info = $dbjson->getCollectionInfo($dbjson->collection_dir);

echo '<br>Insert time: ' . number_format($time_total, 5) . ' seconds';
echo '<br>Inserted: ' . Helper::getNiceFileSize($insert_dummy_json['size']) . ' into collection';
echo '<br>' . $insert_dummy_json['last'] . ' new documents added to collection';
echo '<br>';

echo '<br>Active collection size: ' . Helper::getNiceFileSize($dbjson->getCollectionSize($dbjson->collection_dir));
echo '<br>Active collection total documents: ' . $database_collection_info['info']['count'];

echo '<br>';
echo '<br>Total database size: ' . Helper::getNiceFileSize($dbjson->getAllCollectionsSize($dbjson->database_dir));
echo '<br><br>';

//echo $dummy_template;
//Helper::print_pre($dummy_object);
//Helper::print_pre($database_insert_array);
//$dbjson->insertDocument($lorum_content_json);
$document_random_array = $dbjson->getDocumentRandom();
//Helper::print_pre($document_random_array);
$document_random_id = key($document_random_array);
//echo '<br>ID: ' . $document_random_id;
$document_random_json = $document_random_array[$document_random_id];
//echo '<br>JSON: ' . $document_random_json;
//$document_json_contact = $dbjson->getDocumentRandom();
//$document_json_contact = $dbjson->getDocument('0.14178800 1484745687587f6bd7229e32.06633735');
$document_object = json_decode($document_random_json);
//echo $document_json_contact;
//echo $objact_name = get_object($document_object->results[0]);

function objects2html($object_vars) {
    echo '<dl>';
    foreach ($object_vars as $key => $value) {
        if(is_array($value)) {
            $value = (object)$value;
        }
        echo '<dt>';
        if(is_object($value)) {
            echo '<b>' . $key . '</b>';
            echo '<dd>';
            objects2html($value);
            echo '</dd>';
        } else {
            echo '<div>';
            echo $key;
            echo '<input type="text" name="' . $key . '" value=" ' . $value . '">';
            echo '</div>';
        }
        echo '<dt>';
    }
    echo '</dl>';
}

$object_vars = get_object_vars($document_object);
objects2html($object_vars);

} catch (Exception $e) {
    echo $e->getMessage();
}





Kint::dump($dbjson);
//Kint::trace();
//Kint::dump( $_SERVER );

//Helper::print_pre(get_included_files());

//Helper::print_pre($document_object);
/* From json template
echo '<br>Firstname: ' . $document_object->results[0]->gender;
echo '<br>Lastname: ' . $document_object->results[0]->lastname;
echo '<br>Street: ' . $document_object->results[0]->adress->street;
echo '<br>Postcode: ' . $document_object->results[0]->adress->postcode;
echo '<br>State: ' . $document_object->results[0]->adress->state;
echo '<br>Country: ' . $document_object->results[0]->adress->country;
*/

//$collection_array = $dbjson->getCollectionData();
//Helper::print_pre($collection_array);
//$collection_files = $dbjson->getCollectionData('list_array');
//Helper::print_pre($collection_files);
//$collection_data_array = $dbjson->getCollectionData('data_array');
//Helper::print_pre($collection_data_array);
//$collection_data_json = $dbjson->getCollectionData('data_json');
//Helper::print_pre($collection_data_json);
//$collection_data_json_decoded = json_decode($dbjson->getCollectionData('data_json'));
//Helper::print_pre($collection_data_json_decoded);
//Helper::print_pre($lorum_content);
//Helper::print_pre($dbjson);
//Helper::print_pre($dbjson);
//Helper::print_pre($dbjson->debug);


//Helper::print_pre(get_defined_vars());
//Helper::print_pre(get_defined_functions()['user']);
