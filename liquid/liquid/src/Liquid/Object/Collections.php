<?php
namespace Liquid\Object;

use common\components\internal\ProductApi;
use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/collection
 * Class Collections
 * @package Liquid\Object
 */

class Collections extends Drop implements \Iterator {

    private $position = 0;
    private $array;
    private $map = [];
    private $total = -1;

    private $page = 0;
    private $limit = 100;
    private $need_paginate = true; // 迭代时是否需要翻页

    public function __construct() {
    }

    // 生成迭代数据
    private function _genArrayByPage($page=0, $limit=100) {
        // $collections = CollectionModel::getCollectionList($page, $limit, null, true,true, true);
        $params=array('page'=>$page,'per_page'=>$limit);
        $collectionList=ProductApi::getCollections($params);
        $collections=$collectionList['collections'];
        foreach ($collections as $collection) {
            $collection['collection_id']=$collection['id'];
            $this->array[] = new Collection($collection['collection_id'], $collection);
        }
    }

    // 生成迭代数据
    public function genArrayByPage($page=0, $limit=100) {
        $this->page = $page;
        $this->limit = $limit;
        $this->need_paginate = false;
        $this->_genArrayByPage($page, $limit);
    }

    // 统计总数
    public function count() {
        if ($this->total == -1) {
            // $this->total = CollectionModel::getTotal();
            $params=array('per_page'=>1);
            $collectionList=ProductApi::getCollections($params);
            $this->total=$collectionList['count'];
        }
        return $this->total;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->array[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        if ($this->need_paginate) {
            if ($this->position >= $this->count()) {
                return false;
            }
            if (!isset($this->array[$this->position])) {
                $this->_genArrayByPage($this->page, $this->limit);
                $this->page += 1;
            }
        }
        return isset($this->array[$this->position]);
    }

    public function invokeDrop($method) {
        if (!isset($this->map[$method])) {
            $this->map[$method] = new Collection($method);
        }
        return $this->map[$method];
    }

}
