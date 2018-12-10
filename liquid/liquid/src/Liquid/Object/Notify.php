<?php
/**
 * Created by PhpStorm.
 * User: XiaXiang
 * CreateTime: 2018/10/3 下午5:15
 * Description：
 */

namespace Liquid\Object;

use Liquid\Drop;

class Notify extends Drop
{

    private $type;
    private $data;

    public function __construct($notifyType, $data = '')
    {
        $this->type = $notifyType;
        $this->data = $data;
    }

    public function type()
    {
        return $this->type;
    }

    public function link()
    {
        return $this->data['link'] ?? '';
    }

    public function name()
    {
        return $this->data['name'] ?? '';
    }

    public function date()
    {
        return $this->data['date'] ?? '';
    }
}