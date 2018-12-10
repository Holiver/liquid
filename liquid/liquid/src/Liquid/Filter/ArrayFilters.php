<?php
namespace Liquid\Filter;

class ArrayFilters {

    /** concat two array
     * @param $input
     * @param array $array
     * @return array
     */
    public static function concat(array $input, array $array) {
        return array_merge($input, $array);
    }

    public static function index() {

    }

    public static function parse_json(string $input) {
        $de = json_decode($input, true);
        return $de ? $de : $input;
    }

}

//$arr1 = array("apples", "oranges", "peaches", "tomatoes");
//$arr2 = array("broccoli", "carrots", "lettuce", "tomatoes");
//$res = ArrayFilters::concat($arr1, $arr2);
//var_dump($res);