<?php


namespace App\Includes;


use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\Input;

class Helper
{
    public static function download($path, $filename, $type){
        if (env('APP_ON_SERVER')) {
            $file_path = "/home/x/public_html" . "/storage/public/" . $path;
        } else {
            $file_path = public_path() . "/storage/" . $path;
        }

        $headers = array(
            'Content-Type' => 'application/image',
            'Content-Disposition: attachment; filename=' . $filename . $type,
        );
        if (file_exists($file_path)) {
            // Send Download
            return \Response::download($file_path, $filename . $type, $headers);
        } else {
            // Error
            exit('Requested file does not exist on our server!');
        }
    }


    public static function uploadFileToDisk($action, $model, $attr, $disk, $destination_path, $type, $file)
    {
        if ($action != 'create') {
            \Storage::disk($disk)->delete($model[$attr]);

            if($action == 'delete'){
                $model[$attr] = null;
                $model->save();
                return;
            }
        }

        // 1. Generate a new file name
        $new_file_name = md5($file->getClientOriginalName() . time()) . $type;

        // 2. Move the new file to the correct path
        $path = Storage::disk($disk)->putFileAs($destination_path, $file, $new_file_name);

        // 3. Save the complete path to the database
        $model[$attr] = $path;
        $model->save();

        return $path;
    }

    public static function getIntersect($a1, $a2){
        $r = [];
        foreach ($a1 as $o1){
            foreach ($a2 as $o2) {
                if ($o1['id'] == $o2['id']) {
                    array_push($r, $o1);
                }
            }
        }
        return $r;
    }

    public static function removeSimilarObjects($array, $keep_key_assoc = false){
        $duplicate_keys = array();
        $tmp = array();

        foreach ($array as $key => $val){
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val))
                $val = (array)$val;

            if (!in_array($val, $tmp))
                $tmp[] = $val;
            else
                $duplicate_keys[] = $key;
        }

        foreach ($duplicate_keys as $key)
            unset($array[$key]);

        return $keep_key_assoc ? $array : array_values($array);
    }
    public static function convertPersianToEnglish($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($persian, $english, $string);
    }

    public static function convertEnglishToPersian($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english, $persian, $string);
    }

    public static function truncate($i) {
        return floor($i*100) / 100.0;
    }

}
