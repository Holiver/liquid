<?php
namespace Liquid\Object;

use Liquid\Drop;

class Search extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow search');
    }
}