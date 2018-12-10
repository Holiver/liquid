<?php
namespace Liquid\Filter;

use Liquid\SvgLib;

class AdditionalFilters {

    private $path;
    private $theme_id;

    public function __construct($path, $theme_id) {
        $this->path = $path;
        $this->theme_id = $theme_id;
    }

    private static function rec($arr) {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                $arr[$k] = self::rec($v);
            }
            return $arr;
        } elseif (is_object($arr)) {
            if (method_exists($arr, '__toJson')) {
                return $arr->__toJson();
            }
            return $arr->__toString();
        } else {
            return $arr;
        }
    }

    public function json($input) {
        $input = self::rec($input);
        return json_encode($input);
    }

    public function time_tag($input, $format) {
        $fdate = "";
        if (!is_numeric($input)) {
            if ($input == 'now' || $input == 'today') {
                $input = time();
            } else {
                $input = strtotime($input);
            }
        }

        if (is_array($format)) {
            if (isset($format['format'])) { //  format: 'month_day_year'
                $new_format = $format['format'];
                $new_format = str_replace('year', '%Y', $new_format);
                $new_format = str_replace('month', '%m', $new_format);
                $new_format = str_replace('day', '%d', $new_format);
                $new_format = str_replace('hour', '%H', $new_format);
                $new_format = str_replace('minutes', '%M', $new_format);
                $new_format = str_replace('seconds', '%S', $new_format);
                $format = $new_format;
            }
        }

        if ($format == 'r') {
            $fdate = date($format, $input);
        } else {
            $fdate = strftime($format, $input);
        }
        return "<time datetime=\"$fdate\">$fdate</time>";
    }

    public function default_val($input, $value) {
        if (empty($input) || !$input) {
            return $value;
        } else {
            return $input;
        }
    }

    public function weight_with_unit($input, $unit = "kg") {
        return "$input $unit";
    }

    public function highlight($input, $items) {
        $hl =  "<strong class=\"highlight\">$items</strong>";
        return str_replace($items, $hl, $input);
    }

    public function highlight_active_tag($input, $items) {
        $hl =  "<strong class=\"highlight\">$items</strong>";
        return str_replace($items, $hl, $input);
    }

    public function default_errors($input, $error = "") {
        return $input ? $input : $error;
    }

    public function default_pagination($paginate) {
        $html = array();
        $url_filters = new UrlFilters($this->path, $this->theme_id);
        if ($paginate['previous']) {
            $html[] = "<span class=\"prev\">" . $url_filters->link_to($paginate['previous']['title'], $paginate['previous']['url']) . "</span>";
        }
        foreach ($paginate['parts'] as $part) {
            if ($part['is_link']) {
                $html[] = "<span class=\"page\">" . $url_filters->link_to($part['title'], $part['url']) . "</span>";
            } elseif (intval($part['title']) == intval($part['current_page'])) {
                $html[] = "<span class=\"page current\">" . $part['title'] . "</span>";
            } else {
                $html[] = "<span class=\"deco\">" . $part['title'] . "</span>";
            }
        }
        if ($paginate['next']) {
            $html[] = "<span class=\"next\">" . $url_filters->link_to($paginate['next']['title'], $paginate['next']['url']) . "</span>";
        }
        return implode(" ", $html);
    }

    public static function format_address($input) {
        return $input;
    }

    public static function placeholder_svg_tag($input, $tag) {
        $content = '';
        $svg_dic = SvgLib::dic;
        if ($input && isset($svg_dic[$input])) {
            $svg = $svg_dic[$input];
        }
        if ($input) {
            return '<svg class=' . $tag . ' xmlns="http://www.w3.org/2000/svg" viewBox="' . $svg['viewBox'] . '">' . $svg['path'] . '</svg>';
        } else {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 525.5 525.5"></svg>';
        }
    }

    // float补零，如果是整数不补
    public static function zerofill($input, $num_decimals=2) {
        if (!is_numeric($input)) {
            return $input;
        }
        $result = number_format($input, $num_decimals, '.', '');
        if (intval($result) == $result) {
            return intval($result);
        }
        return $result;
    }

    /**
     * 计算高宽比
     * @param $height
     * @param $width
     * @param string $origin 是否限时宽高
     * @return string
     */
    public static function image_padding_bottom($height, $width, $origin='limit') {
        if ($width && $height) {
            $hw_ratio = floatval($height) / floatval($width);
            if ($origin == 'limit') {
                if ($hw_ratio < 0.62) {
                    return '62%';
                } elseif ($hw_ratio > 1.6) {
                    return '160%';
                }
            }
            return intval($hw_ratio * 100) . '%';
        }
        return '100%';
    }

}