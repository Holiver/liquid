<?php
namespace Liquid\Object;

use common\models\checkout\CartModel;
use Liquid\Drop;

class Cart extends Drop
{

    private $user_sign = false;
    private $cart = null;

    public function __construct($user_sign)
    {
        $this->user_sign = $user_sign;
    }

    private function _loadData()
    {
        if ($this->cart === null) {
            $this->cart = (new CartModel())->getCarts([]);
            foreach ($this->cart as $k => $v) {
                $this->$k = $v;
            }
        }

        if ($this->cart === null) {
            $this->cart = [];
        }
    }

    public function beforeMethod($method) {
        $this->_loadData();
        if ($this->cart && isset($this->cart[$method])) {
            return $this->cart[$method];
        }
        return null;
    }

    public function __toJson()
    {
        $this->_loadData();
        if ($this->cart) {
            return $this->cart;
        }
        return [];
    }
}