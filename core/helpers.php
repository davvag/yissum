<?php

class Helpers {

    public static function getValueByPath($array, $path,$default=null){
        $tPath = trim($path);

        if ($tPath === "")
            return $default;
        
        $currentObj = $array;
        $pathParts = explode(".", $path);
        
        foreach ($pathParts as $pathPart) {
            if (isset($currentObj[$pathPart])){
                $currentObj = $currentObj[$pathPart];
            }else {
                return $default;
            }
        }

        return $currentObj;
    }

    public static function deepCopy($arr1, $arr2){
        return Helpers::deepCopyLogic(Helpers::deepCopyLogic(array(), $arr1, $arr2), $arr2, $arr1);
    }

    private static function deepCopyLogic($mainArray, $arr1, $arr2){
        foreach ($arr1 as $key => $value) {
            if (isset($arr2[$key])){
                if (is_array($arr1[$key]) && is_array($arr2[$key])){
                    $mainArray[$key] = Helpers::deepCopy($arr1[$key], $arr2[$key]);
                }else {
                    $mainArray[$key] = $arr1[$key];
                }

            }else {
                $mainArray[$key] = $value;
            }
        }

        return $mainArray;
    }
}