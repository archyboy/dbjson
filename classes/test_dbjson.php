<?php

class Test_DBjson extends DBjson {

    public function __construct() {
        parent::__construct();
    }

    public function insert_test_data($dbjson, $data, $bytes) {
        //Helper::print_pre($dummy_json_objects_array);
        do {
            $database_insert_size_bytes += $dbjson->insertDocument($data);
            $database_insert_documents++;
            //echo '<br>Database size: ' . number_format($database_insert_size_bytes, 2) . ' bytes<br>';
        } while ($database_insert_size_bytes < $bytes);

        return array(
            'size' => $database_insert_size_bytes,
            'documents' => $database_insert_documents
        );
    }

}
