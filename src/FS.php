<?php
namespace Simcify;

use Exception;

class FS {

    /**
     * The default working directory
     * 
     * @var string
     */
    protected static $dir;

    /**
     * The constructor
     */
    public function __construct() {
        // Nothing
    }

    /**
     * Assign $dir if unassigned
     * 
     * @return void
     */
    protected static function dirize() {
        if(empty(static::$dir)) {
            $default_disk = config('filesystem.default');
            static::$dir = config("filesystem.disks.{$default_disk}");
        }
    }
    /**
     * Download file
     * 
     * @return void
     */
    public static function download($file_path) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);     
    }
    /**
     * Delete a file
     * 
     * @param   string  $file_path
     * @return  boolean
     * @throws File
     */
    public static function delete($file_path) {
        static::dirize();
        if (static::exists($file_path)) {
            unlink(static::path($file_path));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Change the working directory
     * 
     * @param   string  $disk_name
     * @return  \Simcyfy\FS
     */
    public static function disk($disk_name) {
        static::$dir = config("filesystem.disks.{$disk_name}");
        return new static();
    }

    /**
     * Check if a file exists
     * 
     * @param   string  $file_path
     * @return  boolean
     */
    public static function exists($file_path) {
        return file_exists(static::$dir . "/{$file_path}");
    }

    /**
     * List all files in a directory
     * 
     * @return  array
     */
    public static function ls() {
        static::dirize();
        return array_diff(scandir(static::$dir), ['.', '..']);
    }

    /**
     * Generate a full path to a file
     * 
     * @param   string  $disk_name
     * @return  string
     */
    public static function path($file_path) {
        static::dirize();
        return static::$dir . "/{$file_path}";
    }

    /**
     * Write a file to disk
     * 
     * @param   string  $disk_name
     * @return  string
     */
    public static function save($file_path, $data) {
        static::dirize();
        try {
            $file = fopen(static::$dir . "/{$file_path}", 'w+');
            fwrite($file, $data);
            fclose($file);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
