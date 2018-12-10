<?php
namespace Liquid\Object;

use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/collection
 * Class Collections
 * @package Liquid\Object
 */

class Blogs extends Drop implements \Iterator  {

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
    public function _genArrayByPage($page=0, $limit=15) {
//        $blogs = BlogModel::getBlogs(['page' => $page, 'limit' => $limit]);
//        foreach ($blogs as $blog) {
//            $this->array[] = new Blog($blog['id']);
//        }
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
//            $this->total = BlogModel::getTotalBlogs();
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
            $this->map[$method] = new Blog(-1, $method);
        }
        return $this->map[$method];
    }

}