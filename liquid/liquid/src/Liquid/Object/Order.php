<?php
namespace Liquid\Object;

use common\components\internal\TortoiseApi;
use common\dao\order\OrderService;
use Liquid\Drop;

class Order extends Drop
{

    private $id;
    private $data;
    private $fillData = false;

    public function __construct($id, $data = [])
    {
//        var_dump($id['line_items']);exit;
        $this->id = $id;
        $this->data = $data;
    }

    private function _loadData()
    {
        if (($this->data === null || count($this->data) < 10) && $this->id != -1) {
            $order = OrderService::getOrder($this->id);
            if (!empty($order)) {
                $this->fillData = true;
                $this->data = $order;
            }
        }

        if (!empty($this->data) && !isset($this->data['prices'])) {
            $this->data['prices'] = [
                'total_price' => $this->data['total'] ?? 0,
                'discount_price' => $this->data['discount_total'] ?? 0,
                'tax_price' => $this->data['tax_total'] ?? 0,
                'shipping_price' => $this->data['shipping_total'] ?? 0,
                'subtotal_price' => $this->data['sub_total'] ?? 0,
                'discount_code_price' => $this->data['code_discount_total'] ?? 0,
                'discount_line_item_price' => $this->data['line_item_discount_total'] ?? 0,
            ];
            $currency = TortoiseApi::getCurrencyByCode($this->data['currency_code']);
            $this->data['currency_symbol'] = (empty($currency) || empty($currency['symbol_left'])) ? '' : $currency['symbol_left'];
            $this->data['order_path'] = '/order/' . $this->id;
            $this->data['create_time'] = strtotime($this->data['create_time']);
            $tmpLineItems = [];
            foreach ($this->data['line_items'] as &$value) {
                $value['name']  = !empty($value['variant_title']) ? $value['title'] . ' ' . $value['variant_title'] : $value['title'];
                $tmpLineItems[$value['id']] = $value;
            }
            if (!isset($this->data['fulfillment']) && !empty(\Yii::$app->request->post('fulfillment_id'))) {
                $this->data['fulfillment'] = OrderService::getOrderFulfillment($this->id, \Yii::$app->request->post('fulfillment_id'));
            }
            if (!empty($this->data['fulfillment'])) {
                foreach ($this->data['fulfillment']['line_items'] as &$item) {
                    $item = $tmpLineItems[$item['id']];
                }
            }
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    public function beforeMethod($method)
    {
        if ($this->fillData == false) {
            $this->_loadData();
        }
        if ($this->data && isset($this->data[$method])) {
            return $this->data[$method];
        }
        return null;
    }

    public function __toJson()
    {
        $this->_loadData();
        if ($this->data) {
            return $this->data;
        }
        return [];
    }

}