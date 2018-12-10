<?php
namespace Liquid\Object;

use Liquid\Drop;

class Transaction extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow transaction');
    }
}