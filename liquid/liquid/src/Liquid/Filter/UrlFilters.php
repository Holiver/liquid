<?php
namespace Liquid\Filter;

use Liquid\Drop;

class UrlFilters {

    private $path;
    private $theme_id;
    private $asset_domain;
    private $cdn_domain;
    private $common_asset_path; // common asset base path

    public function __construct($path, $theme_id, $asset_domain='', $cdn_domain='', $common_asset_path='') {
        $this->path = $path ? (strrpos($path, '/') === 0 ? $path : $path . '/') : '';
        $this->theme_id = $theme_id;
        $this->asset_domain = $asset_domain;
        $this->cdn_domain = $cdn_domain;
        $this->common_asset_path = $common_asset_path;
    }

    // 计算文件时间戳
    private function _file_timestamp($filepath) {
        $timestamp = '';
        if (file_exists($filepath)) {
            $timestamp = filemtime($filepath);
        }
        return $timestamp;
    }

    public function asset_url($input) {
        if (strpos($input, 'http://') === 0
            || strpos($input, 'https://') === 0
            || strpos($input, '//') === 0) {
            return $input;
        }
        $abspath = $this->theme_id . "/assets/$input";
        $url = "/themes/" . $abspath;
        return $url;
    }

    public function global_asset_url($input) {
        return "//cdn.shopify.com/s/global/$input?1";
    }

    public function shopify_asset_url($input) {
        return "/assets/s/shopify/" . $input;
    }

    public function asset_img_url($input, $format) {
        return "/themes/" . $this->theme_id . "/assets/${input}_${format}";
    }

    public function file_url($input) {
        return $input;
    }

    public function file_img_url($input, $format) {
        return "/files/${input}_${format}";
    }

    public function customer_login_link($input) {
        return "<a href=\"/account/login\" id=\"customer_login_link\">$input</a>";
    }

    public function img_url($input, $args, $opt=null) {
        $image = $input;
        if (is_array($input)) {
            if (isset($input['image'])) {
                $image = $input['image'];
            }
            if (is_object($image)) {
                $image = $image->invokeDrop('src');
            }
            if (isset($image['src'])) {
                $image = $image['src'];
            }
        } elseif ($input instanceof Drop) {
            $image = $input->invokeDrop('src');
        }

        if (is_numeric($args)) {
            $args = strval($args);
        }

        if (!is_string($image)) {
            return "";
        }
        if ($args && is_string($args)) {
            switch ($args) {
                case 'pico':
                    $args = '16x16';
                    break;
                case 'icon':
                    $args = '32x32';
                    break;
                case 'thumb':
                    $args = '50x50';
                    break;
                case 'small':
                    $args = '100x100';
                    break;
                case 'compact':
                    $args = '160x160';
                    break;
                case 'medium':
                    $args = '240x240';
                    break;
                case 'large':
                    $args = '480x480';
                    break;
                case 'grande':
                    $args = '600x600';
                    break;
                case 'original':
                    $args = '1024x1024';
                    break;
                case 'master':
                    $args = '';
            }

            if ($opt && is_array($opt) && isset($opt['scale'])) {
                $scale = (int)$opt['scale'];
                if ($scale > 1) {
                    $params = explode('x', $args);
                    foreach ($params as &$param) {
                        if (is_numeric($param)) {
                            $param = (int)$param * $scale;
                        }
                    }
                    $args = implode('x', $params);
                }
            }

            if ($args && is_string($args)){
                if (trim($args) !== 'x') {
                    $pos = strrpos($image, '.');
                    if ($pos != false) {
                        $pre_path = substr($image, 0, $pos);
                        $post_path = substr($image, $pos);
                        $image = $pre_path . '_' . $args . $post_path;
                    }
                }
            }

        }

        // 不支持webp格式的图片显示的图片地址加_nw.jpg后缀
        if (!self::isSupportWebP()) {
            $pos = strrpos($image, '.');
            if ($pos != false) {
                $pre_path = substr($image, 0, $pos);
                $post_path = substr($image, $pos);
                $image = $pre_path . '_nw' . $post_path;
            }
        }

        return $image;
    }

    /**{{ 'Shopify' | link_to: 'https://www.shopify.com','A link to Shopify' }}
     * <a href="https://www.shopify.com" title="A link to Shopify">Shopify</a>
     * @param $input
     * @param $url
     * @param string $title
     * @return string
     */
    public function link_to($input, $url, $title = "") {
        return "<a href=\"$url\" title=\"$title\">$input</a>";
    }

    public function link_to_vendor($vendor) {
        return self::link_to($vendor, self::url_for_vendor($vendor), $vendor);
    }

    public function link_to_type($type) {
        return self::link_to($type, self::url_for_type($type), $type);
    }

    public function link_to_tag($tag) {
        return "<a title=\"Show products matching tag $tag\" href=\"/collections/frontpage/$tag\">$tag</a>";
    }

    public function link_to_add_tag($tag, $btag) {
        if ($tag == $btag) {
            return "<a title=\"Show products matching tag $tag\" href=\"/collections/frontpage/$tag\">$tag</a>";
        } else {
            return "<a title=\"Show products matching tag $tag\" href=\"/collections/frontpage/${tag}+${btag}\">$tag</a>";
        }
    }

    public function link_to_remove_tag($tag, $btag) {
        if ($tag == $btag) {
            return "<a title=\"Show products matching tag $tag\" href=\"/collections/frontpage/$tag\">$tag</a>";
        }
    }

    public function url_for_vendor($vendor) {
        return "/collections/vendors?q=$vendor";
    }

    public function url_for_type($type) {
        return "/collections/types?q=$type";
    }

    public function payment_type_img_url($input) {
        return $input;
//        return "//cdn.shopify.com/s/global/payment_types/$input.svg?3cdcd185ab8e442b12edc11c2cd13655f56b0bb1";
    }

    public function product_img_url($url, $style = "small") {
        return $this->img_url($url, $style);
    }

    public function collection_img_url($input, $type = "small") {
        return $this->img_url($input, $type);
    }

    public function within($input, $collection) {
        $handle = '';
        if (is_object($collection)) {
            $handle = $collection->url();
        } elseif (is_array($collection) && isset($collection['url'])) {
            $handle = $collection['url'];
        }
        return $handle . $input;
    }

    // 静态资源目录
    public function asset_abs_url($input) {
        $abspath = $this->theme_id . "/assets/$input";
        $url = $this->asset_domain . "/themes/" . $abspath;

        $real_path = $this->path . "assets/$input";
        $timestamp = $this->_file_timestamp($real_path);

        return $timestamp ? "$url?$timestamp" : $url;
    }

    // 公共资源目录
    public function common_asset_abs_url($input) {
        $abspath = "assets/$input";
        $url = $this->cdn_domain . $abspath;
        return $url;
    }

    /**
     * 链接加上cdn域名 //cdn.shoplaza.com/123.jpg
     * @param $input
     * @param string $http 带上http的标示（http或者https）
     * @return string
     */
    public function shoplaza_asset_url($input, $http='') {
        if ($http) {
            return $http . ':' . $this->cdn_domain . $input;
        }
        return $this->cdn_domain . $input;
    }

    public function srcset($input, $width, $opt=[2,3]) {
        if ($width) {
            $result = '';
            foreach ($opt as $o) {
                if ($result) {
                    $result .= ', ';
                }
                $result .= $this->img_url($input, $width, ['scale' => $o]) . " {$o}x";
            }
            return $result;
        }
        return $input;
    }

    /**
     * 是否支持webptup
     * @return bool
     */
    public static function isSupportWebP()
    {
        return (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false);
    }

}

//$urlFilters = new UrlFilters();
//echo $urlFilters::asset_img_url("abc", "300x") . PHP_EOL;