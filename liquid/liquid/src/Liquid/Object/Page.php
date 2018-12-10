<?php
namespace Liquid\Object;

use common\models\PagesModel;
use Liquid\Drop;

class Page extends Drop {

    private $id;
    private $data;

    public function __construct($id = -1, $data = null) {
        $this->id = $id;
        $this->data = $data;
    }

    private function _loadData() {
        if ($this->data === null) {
            if ($this->id != -1) {
                $this->data = PagesModel::getPageInfoById($this->id, true);
            }
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