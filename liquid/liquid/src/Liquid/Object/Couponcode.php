<?php
namespace Liquid\Object;

use common\models\service\DiscountCodeService;
use Liquid\Drop;

class Couponcode extends Drop {

    private $data;
    private $id;

    public function __construct($id, $code = '')
    {
        $this->id = $id;
        if (!empty($code))
            $this->data['code'] = $code;
    }

    public function _loadData() {
        if ($this->data === null) {
            $this->data = DiscountCodeService::getCouponCodeInfoById($this->id);
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    public function isEmpty()
    {
        $this->_loadData();
        return empty($this->data);
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

}