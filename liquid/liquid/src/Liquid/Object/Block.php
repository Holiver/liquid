<?php

namespace Liquid\Object;

use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/block
 * Class Block
 * @package Liquid\Object
 */
class Block extends Drop
{
    private $key;
    private $value;

    public function setKey($k) {
        $this->key = $k;
    }

    public function setValue($v) {
        $this->value = $v;
    }

    public function hasKey($name) {
        return true;
    }

    public function id() {
        return $this->key;
    }

    public function settings() {
        return $this->value['settings'];
    }

    public function type() {
        return $this->value['type'];
    }

}
