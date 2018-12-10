<?php
/**
 * Created by IntelliJ IDEA.
 * User: heliwen
 * Date: 2017/12/26
 * Time: 下午8:27
 */

namespace Liquid\Object;


use Liquid\Drop;

class Errors extends Drop implements \Iterator
{
    private $values;

    public function __construct($values)
    {
        $this->values = $values;
    }

    public function rewind()
    {
        reset($this->values);
    }

    public function valid()
    {
        return false !== $this->current();
    }

    public function next()
    {
        next($this->values);
    }

    public function current()
    {
        return current($this->values);
    }

    public function key()
    {
        return key($this->values);
    }

    public function messages() {
        return $this->values;
    }

    public function __toString()
    {
        if ($this->values) {
            foreach ($this->values as $key => $value) {
                return $value;
            }
        }
        return "";
    }
}