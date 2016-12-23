<?php

class Helper {

    public static function print_pre($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';

    }

    public static function print_array($data) {
        echo '<pre>';
        foreach ($data as $key => $value) {

            echo PHP_EOL .  $key . '=>' ;
            foreach($value as $key => $value) {
                echo PHP_EOL . $key . '=>';
                foreach($value as $key => $value) {
                echo  PHP_EOL . '=>' . $value;
            }
            }

        }
        echo '</pre>';
    }

    public static function myecho ($data) {
        $view = new \System\View(parent::$config->TEMPLATE_SYSTEM_DIRECTORY . 't_output.php');
        $view->add("DATA", $data);
        $view->execute();
    }

    public static function rmdir_recursive($dir) {
        $it = new RecursiveDirectoryIterator($dir);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($it as $file) {
            if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
            if ($file->isDir()) rmdir($file->getPathname());
            else unlink($file->getPathname());
        }
        return rmdir($dir);

    }

    public static function dir_size($directory) {
      $size = 0;
      foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
        $size += $file->getSize();
        }
      return $size;
    }
}
