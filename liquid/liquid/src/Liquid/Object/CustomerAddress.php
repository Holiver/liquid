<?php
namespace Liquid\Object;

use Liquid\Drop;

class CustomerAddress extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow customer_address');
    }
}