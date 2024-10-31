<?php
/**
 * Created by PhpStorm.
 * User: ExeQue
 * Date: 07-03-2018
 * Time: 23:20
 */

class ProdiiCache
{
    private static $_max_age = 14400; // 4 hours

    public static function get_cache_dir($type = false) {
        $cache_dir = Prodii::get_plugin_dir() . "../../cache/prodii/";
        return prodii_path_convert($cache_dir . ($type ? "$type/" : ''));
    }

    public static function save_to_cache($type, $id, $content) {
        $dir = self::get_cache_dir($type);
        $fn = "$id.json";
        self::prep_cache_dir($type);

        $result = file_put_contents(prodii_path_convert("$dir/$fn"), json_encode($content));
        if ($result === false) {
            return false;
        }

        return true;
    }

    public static function exists_in_cache($type, $id) {
        $dir = self::get_cache_dir($type);
        $fn = "$id.json";

        return file_exists(prodii_path_convert("$dir/$fn")) ? prodii_path_convert("$dir/$fn") : false;
    }

    public static function cache_entry_too_old($type, $id, $max_age = null) {
        $max_age = is_null($max_age) || !is_int($max_age) || $max_age <= 0 ? self::$_max_age : $max_age;
        if ($path = self::exists_in_cache($type, $id)) {
            $now = (int)(new DateTime())->format('U');
            $filetime = filemtime($path);

            return $now - $filetime >= $max_age;
        }

        return true;
    }

    public static function get($type, $id) {
        $dir = self::get_cache_dir($type);
        $fn = "$id.json";

        return file_exists(prodii_path_convert("$dir/$fn")) ? json_decode(file_get_contents("$dir/$fn"), true) : false;
    }

    public static function prep_cache_dir($type = null) {
        if (!is_null($type)) {
            if (is_string($type)) {
                $path = substr(self::get_cache_dir($type), 0, strlen(self::get_cache_dir($type)) - 1);
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
            }
        } else {
            foreach (Prodii::get_classes() as $class) {
                if (get_parent_class($class) === 'ProdiiBase' && $class::get_cache_dir() !== false) {
                    $path = substr(self::get_cache_dir($class::get_cache_dir()), 0, strlen(self::get_cache_dir($class::get_cache_dir())) - 1);

                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                }
            }
        }
    }

    public static function flush_cache() {
        $result = self::rmdir(self::get_cache_dir());
        if ($result) {
            self::prep_cache_dir();

            return true;
        }

        return false;
    }

    private static function rmdir($dir) {
        foreach (glob($dir . '/' . '*') as $file) {
            if (is_dir($file)) {
                self::rmdir($file);
            } else {
                unlink($file);
            }
        }

        return rmdir($dir);
    }

    public static function get_cache_size() {
        $types = array();

        foreach (glob(self::get_cache_dir() . '*', GLOB_ONLYDIR) as $type) {
            $type = basename($type);

            $files = array();
            foreach (glob(self::get_cache_dir($type) . '*') as $file) {
                $files[self::format_size(filesize($file))] = $file;
            }

            $types[$type] = $files;
        }

        return $types;
    }

    public static function get_total_size() {
        $size = 0;

        foreach (glob(self::get_cache_dir() . '*', GLOB_ONLYDIR) as $type) {
            $type = basename($type);
            foreach (glob(self::get_cache_dir($type) . '*') as $file) {
                $size += filesize($file);
            }
        }

        return self::format_size($size);
    }

    public static function format_size($size) {
        $suffixes = array('B&nbsp;', 'Kb', 'Mb', 'Gb', 'Tb');
        foreach ($suffixes as $i => $suffix) {
            $delimiter = pow(1024, $i);
            $num = $size / $delimiter;
            if ($num < 1000) {
                return number_format($num, 2) . " $suffix";
            }
        }

        return $size;
    }
}