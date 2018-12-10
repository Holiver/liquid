<?php
namespace Liquid\Object;

use Liquid\Drop;

class Checkouts extends Drop
{

    private $order_token; // 订单token
    private $data; // 同步渲染数据
    private $page_step;  // 当前加载的checkout页面step

    public $exception_error = false;    // checkout是否异常
    public $exception_error_code;       // 相应异常code

    public function __construct($order_token = 0, $data = null, $step = '')
    {
        $this->order_token = $order_token;
        $this->data = $data;
        $this->page_step = $step;
    }

    public function step()
    {
        return $this->page_step;
    }

    public function order_token()
    {
        return $this->_get(__FUNCTION__);
    }

    public function order_no()
    {
        return $this->_get(__FUNCTION__);
    }

    public function line_items()
    {
        return $this->_get(__FUNCTION__);
    }

    public function shipping_country()
    {
        return $this->_get(__FUNCTION__);
    }

    public function checkout_settings()
    {
        return $this->_get(__FUNCTION__);
    }

    public function prices()
    {
        return $this->_get(__FUNCTION__);
    }

    public function currency()
    {
        return $this->_get(__FUNCTION__);
    }

    public function shipping_address()
    {
        return $this->_get(__FUNCTION__);
    }

    public function order_info()
    {
        return $this->_get(__FUNCTION__);
    }

    public function payment_lines()
    {
        return $this->_get(__FUNCTION__);
    }

    public function customer_info()
    {
        return $this->_get(__FUNCTION__);
    }

    public function phone_areas()
    {
        return $this->_get(__FUNCTION__);
    }

    public function checkout_status()
    {
        return $this->_get(__FUNCTION__);
    }

    public function policies()
    {
        return $this->_get(__FUNCTION__);
    }

    public function checkout_token()
    {
        return $this->_get(__FUNCTION__);
    }

    public function shipping_line()
    {
        return $this->_get(__FUNCTION__);
    }

    public function can_change_shipping()
    {
        return $this->_get(__FUNCTION__);
    }

    public function pop_ups()
    {
        return $this->_get(__FUNCTION__);
    }

    private function _get($key)
    {
        $info = [];
        if ($this->data != null) {
            $info = $this->data[$key] ?? [];
        }
        return $info;
    }

}
