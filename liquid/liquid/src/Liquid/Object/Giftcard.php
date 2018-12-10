<?php
namespace Liquid\Object;

use Liquid\Drop;

class Giftcard extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow giftcard');
    }
}