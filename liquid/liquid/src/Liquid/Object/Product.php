<?php

namespace Liquid\Object;

use common\models\price_rule\DiscountFlashsaleCalculationModel;
use common\models\product\ProductModel;
use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/image
 * Class Image
 * @package Liquid\Object
 */
class Product extends Drop
{
    private $id;
    private $handle;
    private $data;
    private $discount_data;

    public function __construct($id = -1, $handle = '', $data = null) {
        $this->id = $id;
        $this->handle = $handle;
        $this->data = $data;
    }

    private function _loadData() {
        if ($this->data === null) {
            if ($this->id != -1) {
                $this->data = ProductModel::getCProduct($this->id, true);
            } elseif ($this->handle) {
                $this->data = ProductModel::getProductByUrl($this->handle);
            }
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    private function _loadDiscountData() {
        $this->_loadData();
        if ($this->discount_data === null) {
            $this->_loadData();
            $spids = array_column($this->data['variants'], 'id');
            $discount_flashsale_calculation_model = new DiscountFlashsaleCalculationModel();
            $this->discount_data = $discount_flashsale_calculation_model->getDiscountFlashsale($spids);
        }
    }

    public function beforeMethod($method) {
        $this->_loadData();
        if ($this->data && isset($this->data[$method])) {
            return $this->data[$method];
        }
        return null;
    }

    public function src() {
        $this->_loadData();
        if ($this->data && $this->data['image'] && $this->data['image'] instanceof Drop) {
            return $this->data['image']->src();
        }
        return '';
    }

    public function object_type() {
        return 'product';
    }

    public function discount() {
        $this->_loadDiscountData();
        return $this->discount_data;
    }

    public function __toJson() {
        $this->_loadData();
        if ($this->data) {
            $json = $this->_recJson($this->data);
            if ($json) {
                $json['discount'] = $this->discount();
            }
            return $json;
        }
        return [];
    }

    private function _recJson($json) {
        if (is_array($json)) {
            foreach ($json as $k => $v) {
                $json[$k] = $this->_recJson($v);
            }
        } elseif (is_object($json)) {
            if (method_exists($json, '__toJson')) {
                return $json->__toJson();
            }
            return $json->__toString();
        }
        return $json;
    }

}
