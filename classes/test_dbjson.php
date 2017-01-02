<?php

namespace Classes\Test_DBjson;

use Classes\DBjson\DBjson;

class Test_DBjson extends DBjson {

    public function __construct($dbjson_dir) {
        parent::__construct($dbjson_dir);
    }

    public function insert_test_data($dbjson, $data, $bytes) {
        //Helper::print_pre($dummy_json_objects_array);
        do {
            $insert_array = $dbjson->insertDocument($data);

            $database_insert_size_bytes += $insert_array['size'];
            $database_insert_documents++;
            //echo '<br>Database size: ' . number_format($database_insert_size_bytes, 2) . ' bytes<br>';
        } while ($database_insert_size_bytes < $bytes);

        return array(
            'size' => $database_insert_size_bytes,
            'last' => $database_insert_documents
        );
    }

}
