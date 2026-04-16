<?php

namespace wwaz\Components\Helper\Strings;

class Json
{
    public static function isJson($str)
    {
        // if( !is_string($str) ){
        //   return false;
        // }
        $json = json_decode($str);
        return $json && $str != $json;
    }

    /**
     * Converts json to object.
     *
     * @param string
     * @return object
     */
    public static function toObj($str)
    {
        return json_decode($str);
    }

    /**
     * Converts json to array.
     *
     * @param string
     * @return array
     */
    public static function toArray($str)
    {
        return json_decode($str, true);
    }

    /**
     * Converts array or object to json.
     *
     * @param object|array
     * @return string
     */
    public static function toJson($obj, $pretty = true)
    {
        if ($pretty) {
            return json_encode($str, JSON_PRETTY_PRINT);
        }
        json_encode($str);
    }

}
