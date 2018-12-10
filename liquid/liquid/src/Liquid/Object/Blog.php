<?php
namespace Liquid\Object;

use Liquid\Drop;

class Blog extends Drop {

    private $id;
    public $handle;
    private $data;

    public function __construct($id = -1, $handle = null) {
        $this->id = $id;
        $this->handle = $handle;
    }

    private function _loadData() {
        if ($this->data === null) {
            if ($this->id != -1) {
//                $this->data = BlogModel::getBlog($this->id, true);
            } elseif ($this->handle !== null) {
//                $this->data = BlogModel::getBlogByUrl($this->handle, true);
            }
//            else {
//                $this->data = BlogModel::getBlogByMinId(true);
//            }
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    public function beforeMethod($method) {
        if ($this->data === null) {
            $this->_loadData();
        }
        if ($this->data && isset($this->data[$method])) {
            return $this->data[$method];
        }
        return null;
    }

}