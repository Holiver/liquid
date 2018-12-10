<?php
namespace Liquid\Object;

use Liquid\Drop;

class Script extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow script');
    }
}