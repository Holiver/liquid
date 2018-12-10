<?php
namespace Liquid\Filter;

class StringFilters {

    /** md5
     * @param $input
     * @return string
     */
    public static function md5($input) {
        return md5($input);
    }

    /** sha1
     * @param $input
     * @return string
     */
    public static function sha1($input) {
        return sha1($input);
    }

    /** sha256
     * @param $input
     * @return string
     */
    public static function sha256($input) {
        return hash("sha256", $input);
    }

    /** sha1 with mac
     * @param $input
     * @param $mac
     * @return string
     */
    public static function hmac_sha1($input, $mac) {
        return hash_hmac("sha1", $input, $mac);
    }

    /** sha256 with mac
     * @param $input
     * @param $mac
     * @return string
     */
    public static function hmac_sha256($input, $mac) {
        return hash_hmac("sha256", $input, $mac);
    }

    /** camelcase
     * @param $input
     * @return mixed
     */
    public static function camelcase($input) {
        // return ucwords(strtolower($input));
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $input);
        // uppercase the first character of each word
        return str_replace(" ", "", ucwords(strtolower(trim($str))));
    }

    /** Accepts a number, and two words - one for singular, one for plural
     * Returns the singular word if input equals 1, otherwise plurals
     * @param $input
     * @param $singular
     * @param $plural
     * @return mixed
     */
    public static function pluralize($input, $singular, $plural) {
        return $input > 1 ? $plural : $singular;
    }

    /**
     * @param $input
     * @return string
     */
    public static function handle($input) {
        if (is_array($input)) {
            return '';
        }
        $input = strtolower($input);
        $to_replace = [' ', '?', '%', '#', '"', "'", "\\", "(", ")", "[", "]", '!', '/', '&', '*', '+'];
        foreach ($to_replace as $item) {
            $input = str_replace($item, '-', $input);
        }
        $result = '';
        for ($i = 0; $i < strlen($input); $i++) {
            if ($i > 0 && $input[$i-1] === '-' && $input[$i] === '-') {
                continue;
            }
            $result .= $input[$i];
        }
        $result = trim($result, '-');
        return $result;
//        $str = preg_replace('/^([a-z0-9]+.*[a-z0-9]+)/i', '$1', $input);
//        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $str), '-'));
    }

    public static function handleize($input) {
        return self::handle($input);
    }

    public static function url_escape($input) {
        return str_replace('%26', '&', rawurlencode($input));
    }

    public static function url_param_escape($input) {
        return rawurlencode($input);
    }

    public static function reverse($input) {
        return strrev($input);
    }

}

$filters = new StringFilters();
//echo $filters::md5("abc"), PHP_EOL;
//echo $filters::sha1("abc"), PHP_EOL;
//echo $filters::sha256("ShopifyIsAwesome!"), PHP_EOL;
//echo $filters::hmac_sha1("ShopifyIsAwesome!", "secret_key"), PHP_EOL;
//echo $filters::hmac_sha256("ShopifyIsAwesome!", "secret_key"), PHP_EOL;
//echo $filters::camelcase("coming soon-LATER"), PHP_EOL;
//echo $filters::pluralize(2, "item", "items"), PHP_EOL;
//echo $filters::handle("100% M & Ms!!!"), PHP_EOL;
//echo $filters::handle("coming soon-LATER"), PHP_EOL;
//echo $filters::url_escape("<hello> & <shopify>"), PHP_EOL;
//echo $filters::url_param_escape("<hello> & <shopify>"), PHP_EOL;
//echo $filters::reverse('Ground control to Major Tom.'), PHP_EOL;
//echo $filters::handle('100% M & Ms!!!');
//echo $filters::handle('100-m-ms%');