<?php
namespace Liquid\Object;

use Liquid\Drop;

class Comment extends Drop {

    public function beforeMethod($method) {
        throw new \Exception('unknow comment');
    }
}