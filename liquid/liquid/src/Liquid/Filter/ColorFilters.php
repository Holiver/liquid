<?php
namespace Liquid\Filter;

class ColorFilters {

    public static function rgbToHsl($r, $g, $b) {
        $oldR = $r;
        $oldG = $g;
        $oldB = $b;
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / (1 - abs(2 * $l - 1));
            switch($max){
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;
                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }
        return array(round($h, 2), round($s, 2), round($l, 2));
    }

    public static function hsl_to_rgb($h, $s, $l) {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs( fmod(($h / 60), 2) - 1));
        $m = $l - ($c / 2);
        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }
        $r = intval(($r + $m) * 255);
        $g = intval(($g + $m) * 255);
        $b = intval(($b + $m) * 255);
        return array($r, $g, $b);
    }

    public static function color_to_rgb($input) {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            $r = hexdec($arr[1]);
            $g = hexdec($arr[2]);
            $b = hexdec($arr[3]);
            return "rgb($r, $g, $b)";
        }
        preg_match('/hsla\((.+),\s*([^%]+)%?,\s*([^%]+)%?,\s*(.+)\)/', $input, $arr);
        if (count($arr) > 0) {
            $h = floatval($arr[1]);
            $s = floatval($arr[2]) / 100.;
            $l = floatval($arr[3]) / 100.;
            $rgb = self::hsl_to_rgb($h, $s, $l);
            return "rgba(" . intval($rgb[0]) . ", " . intval($rgb[1]) . ", " . intval($rgb[2]) . ", $arr[4])";
        }
    }

    public static function color_to_hsl($input) {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            $r = hexdec($arr[1]);
            $g = hexdec($arr[2]);
            $b = hexdec($arr[3]);
            $hsl = self::rgbToHsl($r, $g, $b);
            return "hsl(" . intval($hsl[0]) . ", " . intval($hsl[1] * 100) . "%, " . intval($hsl[2] * 100) . "%)";
        }
        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([0-9.]+)\)/', $input, $arr);
        if (count($arr) > 0) {
            $hsl = self::rgbToHsl($arr[1], $arr[2], $arr[3]);
            return "hsla(". intval($hsl[0]) . ", " . intval($hsl[1] * 100) . "%, " . intval($hsl[2] * 100) . "%, " . "$arr[4])";
        }
    }

    public static function color_to_hex($input) {
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $input, $arr);
        if (count($arr) > 0) {
            $r = dechex($arr[1]);
            if ($arr[1] < 16) {
                $r = '0' . $r;
            }
            $g = dechex($arr[2]);
            if ($arr[2] < 16) {
                $g = '0' . $g;
            }
            $b = dechex($arr[3]);
            if ($arr[3] < 16) {
                $b = '0' . $b;
            }
            return "#${r}${g}${b}";
        }
    }

    public static function color_extract($input, $component) {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            switch ($component) {
                case 'red':
                    return hexdec($arr[1]);
                case 'green':
                    return hexdec($arr[2]);
                case 'blue':
                    return hexdec($arr[3]);
            }
        }
    }

    public static function color_brightness($input) {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            return sprintf("%.2f", (hexdec($arr[1]) * 299 + hexdec($arr[2]) * 587 + hexdec($arr[3]) * 114) / 1000.);
        }
    }

    public static function color_modify($input, $modify, $value='') {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            switch ($modify) {
                case 'red':
                    $r = $value < 16 ? '0' . dechex($value) : dechex($value);
                    return "#${r}${arr[2]}${arr[3]}";
                case 'green':
                    $g = $value < 16 ? '0' . dechex($value) : dechex($value);
                    return "#${arr[1]}${g}${arr[3]}";
                case 'blue':
                    $b = $value < 16 ? '0' . dechex($value) : dechex($value);
                    return "#${arr[1]}${arr[2]}${b}";
                case 'alpha':
                    return "rgba(" . hexdec($arr[1]) . ", " . hexdec($arr[2]) . ", " . hexdec($arr[3]) . ", $value)";
            }
        }
    }

    public static function change_color_hsl($input, $percent, $pos, $order) {
        if ($percent > 100 || $percent < 0) {
            return $input;
        }
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            $hsl = self::rgbToHsl(hexdec($arr[1]), hexdec($arr[2]), hexdec($arr[3]));
            if ($order > 0) {
                if ($pos == 0) {
                    $hsl[$pos] += $percent * 360;
                    if ($hsl[$pos] > 360) {
                        $hsl[$pos] = 360;
                    }
                } else {
                    $hsl[$pos] += $percent / 100.;
                    if ($hsl[$pos] > 1) {
                        $hsl[$pos] = 1;
                    }
                }
            } else {
                if ($pos == 0) {
                    $hsl[$pos] -= $percent * 360;
                    if ($hsl[$pos] < 0) {
                        $hsl[$pos] = 0;
                    }
                } else {
                    $hsl[$pos] -= $percent / 100.;
                    if ($hsl[$pos] < 0) {
                        $hsl[$pos] = 0;
                    }
                }
            }
            $rgb = self::hsl_to_rgb($hsl[0], $hsl[1], $hsl[2]);
            $r = $rgb[0] < 16 ? '0' . dechex($rgb[0]) : dechex($rgb[0]);
            $g = $rgb[1] < 16 ? '0' . dechex($rgb[1]) : dechex($rgb[1]);
            $b = $rgb[2] < 16 ? '0' . dechex($rgb[2]) : dechex($rgb[2]);
            return "#${r}${g}${b}";
        }
        return $input;
    }

    public static function color_lighten($input, $percent) {
        return self::change_color_hsl($input, $percent, 2, 1);
    }

    public static function color_darken($input, $percent) {
        return self::change_color_hsl($input, $percent, 2, -1);
    }

    public static function color_saturate($input, $percent) {
        return self::change_color_hsl($input, $percent, 1, 1);
    }

    public static function color_desaturate($input, $percent) {
        return self::change_color_hsl($input, $percent, 1, -1);
    }

    public static function getRGBA($input) {
        preg_match('/#(\w{2})(\w{2})(\w{2})/', $input, $arr);
        if (count($arr) > 0) {
            return array(
                hexdec($arr[1]),
                hexdec($arr[2]),
                hexdec($arr[3]),
                1,
            );
        }
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $input, $arr);
        if (count($arr) > 0) {
            return array(
                $arr[1],
                $arr[2],
                $arr[3],
                1
            );
        }
        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([0-9.]+)\)/', $input, $arr);
        if (count($arr) > 0) {
            return array(
                $arr[1],
                $arr[2],
                $arr[3],
                $arr[4],
            );
        }
        return array();
    }
    public static function color_mix($input, $mix, $percent) {
        if ($percent > 100 || $percent < 0) {
            return $input;
        }
        $rgba = self::getRGBA($input);
        $rgba1 = self::getRGBA($mix);
        if (count($rgba) && count($rgba1)) {
            $r = intval(ceil(($rgba[0] * (100 - $percent) + $rgba1[0] * $percent) / 100.));
            $g = intval(ceil(($rgba[1] * (100 - $percent) + $rgba1[1] * $percent) / 100.));
            $b = intval(ceil(($rgba[2] * (100 - $percent) + $rgba1[2] * $percent) / 100.));
            $a = ($rgba[3] * (100 - $percent) + $rgba1[3] * $percent) / 100.;
            if ($a == 1) {
                $r = $r < 16 ? '0' . dechex($r) : dechex($r);
                $g = $g < 16 ? '0' . dechex($g) : dechex($g);
                $b = $b < 16 ? '0' . dechex($b) : dechex($b);
                return "#${r}${g}${b}";
            } else {
                return sprintf("rgba(%d, %d, %d, %.3f)", $r, $g, $b, $a);
            }
        }
        return $input;
    }

    public static function hex_to_rgba($input, $a = 1) {
        $rgba = self::getRGBA($input);
        if (count($rgba)) {
            return sprintf("rgba(%d, %d, %d, %.2f)", $rgba[0], $rgba[1], $rgba[2], $a);
        }
        return $input;
    }
}

//$colorFilters = new ColorFilters();
//echo $colorFilters::color_to_rgb('#7ab55c') . PHP_EOL;
//echo $colorFilters::color_to_rgb('#ffc0cb') . PHP_EOL;
//echo $colorFilters::color_to_rgb('#bdbb94') . PHP_EOL;
//echo $colorFilters::color_to_rgb('hsla(100, 38%, 54%, 0.5)') . PHP_EOL;
//echo $colorFilters::color_to_hsl('#7ab55c') . PHP_EOL;
//echo $colorFilters::color_to_hsl('rgba(122, 181, 92, 0.5)') . PHP_EOL;
//echo $colorFilters::color_to_hex('rgb(122, 181, 92)') . PHP_EOL;
//echo $colorFilters::color_extract('#7ab55c', 'blue') . PHP_EOL;
//echo $colorFilters::color_brightness('#7ab55c') . PHP_EOL;
//echo $colorFilters::color_modify('#7ab55c', 'red', 255) . PHP_EOL;
//echo $colorFilters::color_modify('#7ab55c', 'alpha', 0.85) . PHP_EOL;
//echo $colorFilters::color_lighten('#7ab55c', 30) . PHP_EOL;
//echo $colorFilters::color_darken('#7ab55c', 30) . PHP_EOL;
//echo $colorFilters::color_saturate('#7ab55c', 30) . PHP_EOL;
//echo $colorFilters::color_desaturate('#7ab55c', 30) . PHP_EOL;
//echo $colorFilters::color_mix('#7ab55c', '#ffc0cb', 50) . PHP_EOL;
//echo $colorFilters::color_mix('rgba(122, 181, 92, 0.75)', '#ffc0cb', 50) . PHP_EOL;
//echo $colorFilters::hex_to_rgba('#812dd3', 0.5) . PHP_EOL;