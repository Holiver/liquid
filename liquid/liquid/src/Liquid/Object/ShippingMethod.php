<?php
namespace Liquid\Object;

use Liquid\Drop;

class ShippingMethod extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow shipping_method');
    }
}