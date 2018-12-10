<?php
namespace Liquid\Object;

use Liquid\Drop;

class Theme extends Drop {

    private $id;
    private $data;

    public function __construct($id) {
        $this->id = $id;
    }

    public function id() {
        return $this->id;
    }

    private function _loadData() {
        if ($this->data === null) {
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    private function _get($key, $default='') {
        if ($this->data === null) {
            $this->_loadData();
        }
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function name() {
        return $this->_get('name', '');
    }

    public function theme_store_id() {
        return $this->_get('tpl_id', 0);
    }

    public function role() {
        $status = $this->_get('status', 0);
        if ($status) {
            return 'main';
        } else {
            return 'unpublished';
        }
    }

    public function __toString() {
        return $this->name();
    }

    public function __toJson() {
        return [
            "name" => $this->name(),
            "id" => $this->id,
            "theme_store_id" => $this->theme_store_id(),
            "role" => $this->role()
        ];
    }
}