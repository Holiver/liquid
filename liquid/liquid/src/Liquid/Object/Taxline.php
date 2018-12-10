<?php
namespace Liquid\Object;

use Liquid\Drop;

class Taxline extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow tax_line');
    }
}