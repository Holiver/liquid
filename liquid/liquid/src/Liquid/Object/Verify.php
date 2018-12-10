<?php
/**
 * Created by IntelliJ IDEA.
 * User: heliwen
 * Date: 2018/3/29
 * Time: 上午11:57
 */

namespace Liquid\Object;

use Liquid\Drop;

/**
 * 验证码相关
 * Class Verify
 * @package Liquid\Object
 */
class Verify extends Drop {

    private $code;
    private $name;

    public function __construct($code, $name = '') {
        $this->code = $code;
        $this->name = $name;
    }

    public function code() {
        return $this->code;
    }

    public function name() {
        return $this->name;
    }
}