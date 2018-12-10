<?php
namespace Liquid\Object;

use Liquid\Drop;

class Discount extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow discount');
    }
}