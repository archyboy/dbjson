<?php

namespace DBjson\DBEngine;

//require('interfaces/dbjsoninterface.php');
//require_once 'classes/connector.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Filesystem\Filesystem as IFS;

use Dbjson\Connector\Connector as Connector;
use Interfaces\DBjsonInterface\DBjsonInterface as DBjsonInterface;

/**
 * @author ArchyBoy 2017
 */
class DBEngine extends Connector implements DBjsonInterface {
    protected $connector;
    public $config;
    public $data_dir;
    public $database_dir;
    public $collection_dir;
    public $database_name;
    public $collection_name;
    public $document_id;
    public $index_name = 'index.json';
    public $index_max; // 0 for unlimited
    public $chown = 'http';
    public $chgrp = 'http';
    public $check_duplicate = true;
    public $debug = array();

    public function __construct(Connector $connector) {
      $this->connector = $connector;
      $this->data_dir = $this->connector->data_dir;
      $this->database_dir = $this->connector->data_dir . DIRECTORY_SEPARATOR . $this->connector->database_name;
    }


    public function searchDocument(String $query_json) {
        $query_object = json_decode($query_json);

        //$collection_data = $this->getCollectionData();
        $result = $collection_data;
        return $result;
    }

    public function testme(int $one, int $two, int $three) {
    	return array($one, $two, $three);
    }

    public function install(string $data_dir) {
    		$this->data_dir = $data_dir;

        $fs = new IFS;

        if (!$fs->isDirectory($this->data_dir)) {
            if (!$fs->makeDirectory($this->data_dir, 0777, true)) {
                $this->debug['fail']['filesystem']['create']['directory'][] = $this->data_dir;
            } else {
                $this->debug['success']['filesystem']['create']['directory'][] = $this->data_dir;
                if ($fs->isWritable($this->data_dir)) {
                    //Helper::print_pre($fs->chmod($this->data_dir, 0777));
                }
            }
        } else {
            $this->debug['warning']['filesystem']['duplicate']['directory'][] = $this->data_dir;
        }
    }

    /**
     * [Creates the database directory]
     * @param  [type] $database_name [Name of the database]
     */
    public function createDB($database_name) {
        $this->database_name = $database_name;
        $this->database_dir = $this->data_dir . DIRECTORY_SEPARATOR . $database_name;

        $filesystem = new Filesystem;

        if (!$filesystem->exists($this->database_dir)) {
            if ($filesystem->mkdir($this->data_dir . DIRECTORY_SEPARATOR . $database_name, 0777)) {
                $this->debug['fail']['database']['directory']['write_error'][] = $database_name;
            } else {
                $this->debug['success']['database']['create']['directory'][] = $database_name;
            }
        } else {
            $this->debug['warning']['database']['duplicate']['directory'][] = $database_name;
            //throw new Exception('Database: ' . $database_name . ' already exist');
        }
    }

    public function dropDB($database_name) {
        if (is_dir($this->data_dir . DIRECTORY_SEPARATOR . $database_name)) {
            if (!Helper::rmdir_recursive($this->data_dir . DIRECTORY_SEPARATOR . $database_name)) {
                $this->debug['fail']['database']['drop']['directory'][] = $database_name;
            } else {
                $this->debug['success']['database']['drop']['directory'][] = $database_name;
            }
        } else {
            $this->debug['warning']['database']['not_found']['directory'][] = $database_name;
        }
    }

    public function setDB($database_name) {
        $this->database_dir = $this->data_dir . DIRECTORY_SEPARATOR . $database_name;
    }

    public function newCollection($collection_name) {
        $this->collection_name = $collection_name;
        $this->collection_dir = $this->database_dir . DIRECTORY_SEPARATOR . $collection_name;

        $filesystem = new Filesystem;

        if (!file_exists($this->collection_dir)) {
            if ($filesystem->mkdir($this->collection_dir, 0777, true)) {
                $this->debug['fail']['collection']['write_error']['directory'][] = $collection_name;
            } else {
                $this->debug['success']['collection']['create']['directory'][] = $collection_name;
            }
        } else {
            $this->debug['warning']['collection']['duplicate']['directory'][] = $collection_name;
        }
    }

    public function removeCollection($collection_name) {
        if (is_dir($this->database_dir . DIRECTORY_SEPARATOR . $collection_name)) {
            if (!Helper::rmdir_recursive($this->database_dir . DIRECTORY_SEPARATOR . $collection_name)) {
                $this->debug['fail']['collection']['remove']['directory'][] = $collection_name;
            } else {
                $this->debug['success']['collection']['remove']['directory'][] = $collection_name;
                $filesystem->chown($this->document_id, $this->chown);
                $filesystem->chgrp($this->document_id, $this->chgrp);
            }
        } else {
            $this->debug['warning']['collection']['not_found']['directory'][] = $collection_name;
        }
    }

    public function setCollection($collection_name) {
        $this->collection_dir = $this->database_dir . DIRECTORY_SEPARATOR . $collection_name;
    }

    public function getIndexObject() {
        $fs = new IFS;

        $index_file_path = $this->database_dir . DIRECTORY_SEPARATOR . $this->collection_name . '_' . $this->index_name;

        if ($fs->isFile($index_file_path)) {
            return file_get_contents($index_file_path);
        } else {
            echo '<br>json file not found!';
        }
    }

    public function getLastIndexID() {
        $index_array = json_decode($this->getIndexObject(), true);
        return array_last($index_array);
    }

    public function updateIndex($document_id) {
        $fs = new \Illuminate\Filesystem\Filesystem;
        $index_file_path = $this->database_dir . DIRECTORY_SEPARATOR . $this->collection_name . '_' . $this->index_name;
        $last_index_id = $this->getLastIndexID();
        $index_array[] = $document_id;
        $index_json = json_encode($index_array, JSON_PRETTY_PRINT);

        if ($fs->isFile($index_file_path)) {
            $fs->append($this->database_dir . DIRECTORY_SEPARATOR . $this->collection_name . '_' . $this->index_name, $index_json);
        } else {
            $fs->put($index_file_path, $index_json);
        }
    }

    public function insertDocument($data) {
        $collection_array = $this->getCollectionData('list_array'); //array_diff(scandir($this->collection_dir, SCANDIR_SORT_DESCENDING), array('.', '..'));
        //Kint::dump($collection_array);
        //$document_id = uniqid(microtime(), true);
        $document_id = rand(1, $this->index_max);

        $documents_in_collection = count($collection_array);

        if (!$documents_in_collection) {
            $this->debug['notice']['document']['initial'][] = $document_id;
        }

        if($documents_in_collection <=> $this->index_max) {
            $this->debug['notice']['document']['new'][] = $document_id;
        } else {
          $this->debug['error']['document']['new']['id'] = $document_id;
          $this->debug['error']['document']['new']['message'] = 'No more options for unique filename';
          throw new Exception("Oooobs...Could not write unique filename for document!!");
        }

        if($this->check_duplicate == true) {
          $file_exists = file_exists($this->collection_dir . DIRECTORY_SEPARATOR . $document_id);
        }

        if (!$file_exists) {
            if (!$filesize = file_put_contents($this->collection_dir . DIRECTORY_SEPARATOR . $document_id, $data)) {
                $this->debug['fail']['document']['write'][] = $document_id;
            } else {
              if($this->check_duplicate) {
                  $this->debug['success']['document']['unique'][] = $document_id;
                } else {
                  $this->debug['warning']['document']['unchecked'][] = $document_id;
                }
                $this->debug['success']['document']['write'][] = $document_id;
                $this->updateIndex($document_id);
            }
        } else {
            $this->debug['warning']['document']['duplicate'][] = $document_id;
            $this->debug['fixing']['document']['duplicated'][] = $this->insertDocument($data); // If duplicat document_id then loop back until unique
        }

        $insert_info = array(
            'size' => $filesize,
            'last' => $document_id
        );
        return $insert_info;
    }

    public function deleteDocument($document_id) {
        if (is_file($this->collection_dir . DIRECTORY_SEPARATOR . $document_id)) {
            if (!unlink($this->collection_dir . DIRECTORY_SEPARATOR . $document_id)) {
                $this->debug['fail']['document']['delete'][] = $document_id;
            } else {
                $this->debug['success']['document']['delete'][] = $document_id;
            }
        } else {
            $this->debug['fail']['document']['not_found'][] = $document_id;
        }
    }

    public function updateDocument($id, $json) {

    }

    public function getDocument($document_id) {
        $filepath = $this->collection_dir . DIRECTORY_SEPARATOR . $document_id;
        if (file_exists($filepath)) {
            if (!$content = file_get_contents($filepath)) {
                $this->debug['warning']['document']['empty'] = $document_id;
            } else {
                $this->debug['success']['document']['retrieved'] = $document_id;
                return array( $document_id => $content );
            }
        } else {
            $this->debug['fail']['document']['not_found'][] = $document_id;
        }
    }

	public function getDocumentRandom() {
		$collection_array = $this->getCollectionData('list_array');
		$random_key = array_rand($collection_array);
		$random_id = $collection_array[$random_key];
		return $this->getDocument($random_id);
	}

    public function getCollectionData($datatype = 'list_array') {
        switch ($datatype) {
            case 'list_array': {
                    $finder = new Finder();
                    $finder->files()->in($this->collection_dir);

                    foreach ($finder as $files) {
                        //echo $files->getRelativePathname();
                        $files_array[] = $files->getRelativePathname();
                    }
                    //Helper::print_pre($finder);
                    //$array = array_diff(scandir($this->collection_dir, SCANDIR_SORT_DESCENDING), array('.', '..'));
                    return $files_array;
                    break;
                }
            case 'data_array': {
                    $dir_array = $this->getCollectionData('list_array');
                    foreach ($dir_array as $key => $value) {
                        $data_array[] = $this->getDocument($value);
                    }
                    return $data_array;
                    break;
                }
            case 'data_json': {
                    $dir_array = $this->getCollectionData();

                    foreach ($dir_array as $key => $value) {
                        $data_array[] = $this->getDocument($value);
                    }
                    return json_encode($data_array);
                    break;
                }
        }
    }

    public function getCollectionInfo($collection_dir) {
        $finder = new Finder;
        $iterator = $finder->files()->in($collection_dir);
        $collection_array['info']['count'] = $iterator->count() - 1;

        foreach ($iterator as $file) {
            $collection_array['files']['realpath'][] = $file->getRealpath();
            $collection_array['files']['filename'][] = $file->getFilename();
            $collection_array['files']['pathname'][] = $file->getPathname();
            $collection_array['files']['path'][] = $file->getPath();
            $collection_array['files']['size'][] = $file->getSize();
        }

        //Helper::print_pre($collection_array);
        return $collection_array;
    }

    public function getCollectionSize($collection_dir) {
        $finder = new Finder;
        $iterator = $finder->files()->in($collection_dir);

        foreach ($iterator as $file) {
            $collection_size += $file->getSize();

            //Helper::print_pre($file->getSize());
        }
        return $collection_size;
    }

    public function getAllCollectionsSize($database_dir) {
        $finder = new Finder;
        $iterator = $finder->directories()->in($database_dir);

        foreach ($iterator as $collection) {
            $size += $this->getCollectionSize($collection->getRealPath());
            //echo '<br>' . $collection->getBasename();
            //echo '<br>' . Helper::getNiceFileSize($size = $collection->getSize());
            //Helper::print_pre($collection);
        }
        return $size;
    }
    public function insert_test_data($data, $bytes) {
        //Helper::print_pre($dummy_json_objects_array);
        do {
            $insert_array = $this->insertDocument($data);
            $database_insert_size_bytes += $insert_array['size'];
            $database_insert_documents++;
        } while ($database_insert_size_bytes < $bytes);

        return array(
            'size' => $database_insert_size_bytes,
            'last' => $database_insert_documents
        );
    }
}
