<?php

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class DBjson {

    public $config;
    public $dbjson_dir;
    public $database_dir;
    public $collection_dir;
    //public $document_id;
    public $debug = array();

    public function __construct() {
        $this->config = $config;
    }

    public function testFilesystem() {
        $fs = new Filesystem();
        try {
            echo $fs->mkdir($file = 'dbdata/mydata/mycollection/' . mt_rand());
            //$fs->remove($file);
        } catch (IOExceptionInterface $e) {
            echo 'An error occurred while creating your directory at ' . $e->getPath();
        }
    }

    public function testFinder() {
        $finder = new Finder();
        $finder->files()->in(__DIR__);

        //Helper::print_pre($finder);
        foreach ($finder as $file) {
            // Dump the absolute path
            var_dump($file->getRealPath());

            // Dump the relative path to the file, omitting the filename
            var_dump($file->getRelativePath());

            // Dump the relative path to the file
            var_dump($file->getRelativePathname());
        }
    }

    public function installDB($dbjson_dir) {
        $this->dbjson_dir = $dbjson_dir;
        if (!file_exists($this->dbjson_dir)) {
            if (!mkdir($this->dbjson_dir, 0777, true)) {
                $this->debug['fail'][] = 'Failed to create default DBjson directory';
            } else {
                $this->debug['success'][] = 'Success creating directory: ' . $this->dbjson_dir;
            }
        } else {
            $this->debug['warning'][] = 'DBjson is already installed!';
        }
    }

    public function createDB($database_name) {
        $this->database_dir = $this->dbjson_dir . DIRECTORY_SEPARATOR . $database_name;
        if (!file_exists($this->database_dir)) {
            if (!mkdir($this->dbjson_dir . DIRECTORY_SEPARATOR . $database_name, 0777, true)) {
                $this->debug['fail'][] = 'Failed to create database directory';
            } else {
                $this->debug['success'][] = 'Database: ' . $database_name . ' created successfully!';
            }
        } else {
            $this->debug['warning'][] = 'Database ' . $database_name . ' already exists!';
        }
    }

    public function dropDB($database_name) {
        if (is_dir($this->dbjson_dir . DIRECTORY_SEPARATOR . $database_name)) {
            if (!Helper::rmdir_recursive($this->dbjson_dir . DIRECTORY_SEPARATOR . $database_name)) {
                $this->debug['fail'][] = 'Failed to drop database!';
            } else {
                $this->debug['success'][] = 'Database: ' . $database_name . ' dropped!';
            }
        } else {
            $this->debug['warning'][] = 'Database: ' . $database_name . ' does not exsist!';
        }
    }

    public function newCollection($collection_name) {
        $this->collection_dir = $this->database_dir . DIRECTORY_SEPARATOR . $collection_name;
        if (!file_exists($this->collection_dir)) {
            if (!mkdir($this->collection_dir, 0777, true)) {
                $this->debug['fail'][] = 'Failed to create collection directory';
            } else {
                $this->debug['success'][] = 'Collection: ' . $collection_name . ' created successfully!';
            }
        } else {
            $this->debug['warning'][] = 'Collection ' . $collection_name . ' already exists!';
        }
    }

    public function removeCollection($collection_name) {
        if (is_dir($this->database_dir . DIRECTORY_SEPARATOR . $collection_name)) {
            if (!Helper::rmdir_recursive($this->database_dir . DIRECTORY_SEPARATOR . $collection_name)) {
                $this->debug['fail'][] = 'Failed to remove collection!';
            } else {
                $this->debug['success'][] = 'Collection: ' . $collection_name . ' removed!';
            }
        } else {
            $this->debug['warning'][] = 'Collection: ' . $collection_name . ' does not exsist!';
        }
    }

    public function insertDocument($data) {
        $dir_array = $this->getCollection('list_array'); //array_diff(scandir($this->collection_dir, SCANDIR_SORT_DESCENDING), array('.', '..'));
        //Helper::print_pre($dir_array);
        $document_id = uniqid(microtime(), true);
        if (!count($dir_array)) {
            $this->debug['notice']['initial_document'][] = $document_id;
        } else {
            $this->debug['notice']['adding_document'][] = $document_id;
        }

        if (!file_exists($this->collection_dir . DIRECTORY_SEPARATOR . $document_id)) {
            if (!$filesize = file_put_contents($this->collection_dir . DIRECTORY_SEPARATOR . $document_id, $data)) {
                $this->debug['fail']['write_error'][] = $document_id;
            } else {
                $this->debug['success']['unique_id'][] = $document_id;
                //return $filesize;
            }
        } else {
            $this->debug['warning']['duplicat_id'][] = $document_id;
            $this->debug['fixing']['duplicat_id'][] = $this->insertDocument($data); // If duplicat document_id then loop back until unique
        }
        return $document_id;
    }

    public function deleteDocument($document_id) {
        if (is_file($this->collection_dir . DIRECTORY_SEPARATOR . $document_id)) {
            if (!unlink($this->collection_dir . DIRECTORY_SEPARATOR . $document_id)) {
                $this->debug['fail'][] = 'Failed to delete document!';
            } else {
                $this->debug['success'][] = 'Document: ' . $document_id . ' deleted!';
            }
        } else {
            $this->debug['warning'][] = 'Document: ' . $document_id . ' does not exsist!';
        }
    }

    public function updateDocument($id, $json) {

    }

    public function getDocument($document_id) {
        $filepath = $this->collection_dir . DIRECTORY_SEPARATOR . $document_id;
        if (file_exists($filepath)) {
            if (!$content = file_get_contents($filepath)) {
                $this->debug['warning'][] = 'Document: ' . $document_id . ' is empty!';
            } else {
                $this->debug['success'][] = 'Document: ' . $document_id . ' retrieved!';

                return $content;
            }
        } else {
            $this->debug['fail'][] = 'Could not find document: ' . $document_id;
        }
    }

    public function getCollection($datatype = 'list_array') {
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
                    $dir_array = $this->getCollection('list_array');

                    foreach ($dir_array as $key => $value) {
                        $data_array[] = $this->getDocument($value);
                    }

                    return $data_array;
                    break;
                }
            case 'data_json': {
                    $dir_array = $this->getCollection();

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

        $collection_array['info']['count'] = $iterator->count();

        foreach ($iterator as $file) {
            $collection_array['files']['realpath'][] = $file->getRealpath();
            $collection_array['files']['filename'][] = $file->getFilename();
            $collection_array['files']['pathname'][] = $file->getPathname();
            $collection_array['files']['path'][] = $file->getPath();
            $collection_array['files']['size'][] = $file->getSize();

            //Helper::print_pre($file->getSize());
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

        //Helper::print_pre($iterator);

        foreach ($iterator as $collection) {
            $size += $this->getCollectionSize($collection->getRealPath());
            //echo '<br>' . $collection->getBasename();
            //echo '<br>' . Helper::getNiceFileSize($size = $collection->getSize());
            //Helper::print_pre($collection);
        }
        return $size;
    }

}
