<?php
namespace Liquid\Object;

use common\models\FlashsaleModel;
use Liquid\Drop;

class Flashsales extends Drop implements \Iterator {

    private $data;

    public function __construct()
    {
    }

    public function _loadData() {
        if ($this->data === null) {
            $this->data = FlashsaleModel::getCurrentPublished();
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    public function beforeMethod($method) {
        $this->_loadData();
        if ($this->data && isset($this->data[$method])) {
            return $this->data[$method];
        }
        return null;
    }

    public function __toJson() {
        $this->_loadData();
        if ($this->data) {
            return $this->data;
        }
        return [];
    }

    public function count() {
        return count($this->data);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        if ($this->position >= $this->count()) {
            return false;
        }
        return isset($this->data[$this->position]);
    }

}