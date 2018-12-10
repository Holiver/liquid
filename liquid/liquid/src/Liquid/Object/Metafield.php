<?php
namespace Liquid\Object;

use Liquid\Drop;

class Metafield extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow metafield');
    }
}