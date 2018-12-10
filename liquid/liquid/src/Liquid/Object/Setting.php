<?php
/**
 * Created by PhpStorm.
 * User: XiaXiang
 * CreateTime: 2018/4/16 上午11:21
 * Description：
 */

namespace Liquid\Object;

class Setting
{
    private  $data = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    private function _get($key, $default='') {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    public function payment_setting()
    {
        return $this->_get('payment_setting');
    }

    public function shipping_setting()
    {
        return $this->_get('shipping_setting');
    }

    public function leave_out_days()
    {
        return $this->_get('leave_out_days');
    }

    public function admin_url()
    {
        return $this->_get('admin_url');
    }
}