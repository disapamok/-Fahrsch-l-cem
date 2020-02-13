<?php
namespace Simcify;

use \Suin\ImageResizer\ImageResizer;
use \Gumlet\ImageResize;
use Simcify\Str;

class File {
    
    /**
     * Delete file
     * 
     * @param   array|string $file
     * @return  true
     */
    public static function delete($file, $folder) {

        if (is_array($file)) {
            foreach ($file as $filePath) {
                $filePath = config("app.storage").$folder."/".$filePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }else{
            $file = config("app.storage").$folder."/".$file;
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return true;

    }
    
    /**
     * Upload file
     * 
     * @param   string $file
     * @param   string $storage
     * @param   array $options
     * @return  array
     */
    public static function upload($file, $storage = "general", array $options = array()) {
        if (!isset($options['source'])) {
            $options['source'] = "form";
        }

        $storage = config("app.storage").$storage;

        if (!is_dir($storage)) {
            return array(
                "status" => "error",
                "title" => sch_translate("folder_not_folder"),
                "message" => sch_translate("storage_folder_is_not_found").$storage
            );
        }

        if (!is_writable($storage)) {
            return array(
                "status" => "error",
                "title" => sch_translate("folder_not_write_able"),
                "message" => sch_translate("You don't have permission to storage folder.")
            );
        }

        $forbiddenExtension = array("exe", "php", "js", "html", "py");

        if (!isset($options["extension"]) && $options['source'] == "form") {
            $options["extension"] = Str::lower(pathinfo(basename($file['name']),PATHINFO_EXTENSION));
        }elseif (!isset($options["extension"])) {
            $options["extension"] = Str::lower(pathinfo(basename($file),PATHINFO_EXTENSION));
        }

        if (in_array($options["extension"], $forbiddenExtension)) {
            return array(
                "status" => "error",
                "title" => sch_translate("forbidden_file_type"),
                "message" => sch_translate("this_file_type_is_forbidden")
            );
        }

        if (isset($options["allowedExtensions"])) {
            $allowedExtensions = explode(",", $options["allowedExtensions"]);

            if (!in_array($options["extension"], $allowedExtensions)) {
                return array(
                    "status" => "error",
                    "title" => sch_translate("file_type_not_allowed"),
                    "message" => sch_translate("this_file_type_is_allowed")
                );
            }
        }

        $fileName = Str::random(32).".".$options["extension"];
        $outputFile = $storage."/".$fileName;

        if ($options['source'] == "form") {
            if (isset($file["tmp_name"])) {
                $temporaryFile = $file["tmp_name"];
            }else{
                $temporaryFile = $file;
            }
            if(!move_uploaded_file($temporaryFile, $outputFile)){
                return array(
                    "status" => "error",
                    "title" => sch_translate("upload_failed"),
                    "message" => sch_translate("something_went_wront_uploading_file")
                );
            }
        }elseif ($options['source'] == "url") {
            $fileBody = file_get_contents($file);
            $f = fopen($outputFile, 'w');
            fwrite($f, $fileBody);
            fclose($f);
        }elseif ($options['source'] == "base64") {
            $data = explode( ',', $file );
            if(!file_put_contents($outputFile, base64_decode($data[1]))){
                return array(
                    "status" => "error",
                    "title" => sch_translate("upload_failed"),
                    "message" => sch_translate("something_went_wront_uploading_file")
                );
            }
        }

        if (!file_exists($outputFile)) {
            return array(
                "status" => "error",
                "title" => sch_translate("upload_failed"),
                "message" => sch_translate("something_went_wront_uploading_file")
            );
        }

        if (isset($options["resize"])) {
            $resizer = new ImageResizer($outputFile);
            if (isset($options["resize"]["maxWidth"]) && isset($options["resize"]["maxHeight"])) {
                $resizer->maxWidth($options["resize"]["maxWidth"])->maxHeight($options["resize"]["maxHeight"])->resize();
            }elseif (isset($options["resize"]["maxWidth"])) {
                $resizer->maxWidth($options["resize"]["maxWidth"])->resize();
            }elseif (isset($options["resize"]["maxHeight"])) {
                $resizer->maxHeight($options["resize"]["maxHeight"])->resize();
            }
        }

        if (isset($options["crop"])) {
            $image = new ImageResize($outputFile);
            $image->crop($options["crop"]["width"], $options["crop"]["height"]);
            $image->save($outputFile);
        }

        $fileSize = self::FileSizeConvert(filesize($outputFile));

        return array(
            "status" => "success",
            "title" => sch_translate("file_uploaded"),
            "message" => sch_translate("file_successfully_uploaded"),
            "info" => array(
                                "name" => $fileName,
                                "path" => $outputFile,
                                "extension" => $options['extension'],
                                "size" => $fileSize,
                            )
        );

    }

    /** 
    * Converts bytes into human readable file size. 
    * 
    * @param string $bytes 
    * @return string human readable file size (2,87 Мб)
    * @author Mogilev Arseny 
    */ 
    public static function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}