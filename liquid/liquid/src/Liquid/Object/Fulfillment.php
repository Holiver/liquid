<?php
namespace Liquid\Object;

use Liquid\Drop;

class Fulfillment extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow fulfillment');
    }
}