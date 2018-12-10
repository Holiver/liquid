<?php

namespace Liquid\Object;

use common\models\FilesModel;
use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/image
 * Class Image
 * @package Liquid\Object
 */
class Image extends Drop
{
    private $path;
    private $data;
    private $type;  // 0普通图片 1关联产品的图片
    private $product_id;
    private $position;
    private $variant_ids=[];
    private $alt;
    private $image_url_prefix;

    public function __construct($path, $image_url_prefix, $type=0, $product_id=0, $position=0, $variant_ids=[], $alt='') {
        $this->path = $path;
        $this->type = $type;
        $this->product_id = $product_id;
        $this->position = $position;
        $this->variant_ids = $variant_ids;
        $this->alt = $alt;
        $this->image_url_prefix = $image_url_prefix;
    }

    public function setAlt($alt) {
        $this->alt = $alt;
    }

    private function _loadData() {
        $this->data = FilesModel::getFile($this->path);
        if ($this->data === null) {
            $this->data = array();
        }
    }

    private function _getValue($key) {
        if ($this->data === null) {
            $this->_loadData();
        }
        if ($this->data && isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function id() {
        try {
            return explode('.', $this->path)[0];
        } catch (\Exception $e) {
            return $this->path;
        }
    }

    public function src() {
        $val = $this->_getValue('url');
        if (!$val && $this->path) {
            $val = $this->path;
        }
        if (is_string($val) && strpos($val, 'http://') === false && strpos($val, 'https://') === false) {
            $val = $this->image_url_prefix . $val;
        }
        return $val ? $val : '';
    }

    public function alt() {
        $val = $this->_getValue('desc');
        if ($val) {
            return $val;
        }
        return $this->alt ? $this->alt : '';
    }

    public function height() {
        $val = $this->_getValue('height');
        return $val ? $val : 0;
    }

    public function width() {
        $val = $this->_getValue('width');
        return $val ? $val : 0;
    }

    public function aspect_ratio() {
        $val = $this->_getValue('aspect_ratio');
        return $val ? $val : 1;
    }

    public function product_id() {
        return $this->product_id;
    }

    public function attached_to_variant() {
        return false;
    }

    public function variant_ids() {
        return $this->variant_ids;
    }

    public function __toString() {
        return $this->src();
    }

    public function __toJson() {
        if ($this->type === 0) {
            return $this->src();
        } else {
            return [
                'id' => $this->id(),
                'width' => $this->width(),
                'height' => $this->height(),
                'aspect_ratio' => $this->aspect_ratio(),
                'product_id' => $this->product_id(),
                'position' => $this->position,
                'src' => $this->src(),
                'variant_ids' => $this->variant_ids()
            ];
        }
    }
}
