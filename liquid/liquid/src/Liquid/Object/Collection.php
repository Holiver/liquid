<?php
namespace Liquid\Object;

use common\components\internal\ProductApi;
use common\models\product\CollectionModel;
use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/collection
 * Class Collection
 * @package Liquid\Object
 */

class Collection extends Drop {

    public $id;
    public $handle;
    public $data;
    public $max_show_count = 4;

    public function __construct($id = -1, $handle = '', $data = null) {
        $this->id = $id;
        $this->handle = $handle;
        $this->data = $data;
    }

    public function _loadData() {
        if ($this->data === null) {
            if ($this->id != -1) {  // 通过id获取专辑信息
                $this->data = ProductApi::getCollectionById($this->id);
            } elseif ($this->handle !== null) {  // 通过handle获取专辑信息
                $this->data = CollectionModel::getCollectionBySlug($this->handle);
                $this->id = $this->data['id'];
            }
        }
        if ($this->data === null) {
            $this->data = array();
        }
    }

    public function _getValue($key) {
        if ($this->data === null) {
            $this->_loadData();
        }
        if ($this->data && isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function setField($field, $value) {
        $this->data[$field] = $value;
    }

    public function all_products_count() {
        return $this->_getValue('products_count');
    }

    public function default_sort_by() {
        $default_sort_by = $this->_getValue('default_sort_by');
        return $default_sort_by ? $default_sort_by : 'manual';
    }

    public function products() {
        $products = CollectionModel::getCollectionProduct($this->id);
        return $products['products'];
    }

    public function published_at() {
        return $this->_getValue('created_at');
    }

    public function beforeMethod($method) {
        $this->_loadData();
        if ($this->data && isset($this->data[$method])) {
            return $this->data[$method];
        }
        return null;
    }

}