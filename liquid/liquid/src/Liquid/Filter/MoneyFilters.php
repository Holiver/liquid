<?php
namespace Liquid\Filter;

class MoneyFilters {

    private $currency = 'USD';
    private $symbol = '$';
    private $finance_currency = 'USD';
    private $finance_symbol = '$';

    public function __construct($currency = 'USD', $symbol = '$', $finance_currency = 'USD', $finance_symbol = '$') {
        $this->currency = $currency;
        $this->symbol = $symbol;
        $this->finance_currency = $finance_currency;
        $this->finance_symbol = $finance_symbol;
    }

    public function money_with_currency($money) {
        if (is_numeric($money)) {
            return sprintf("%.2f " . $this->currency, $money);
        }
        return "";
    }

    public function money($money) {
        if (is_numeric($money)) {
            $money = floatval($money);
//            $num_decimals = (intval($money) == $money) ? 0 : 2;
            $num_decimals = 2;
            return number_format($money, $num_decimals) . ' ' . $this->currency;
        }
        return "";
    }

    public function money_without_currency($money) {
        if (is_numeric($money)) {
            return sprintf("%.2f", $money);
        }
        return "";
    }

    public function money_without_trailing_zeros($money) {
        if (is_numeric($money)) {
            $money = floatval($money);
            $num_decimals = (intval($money) == $money) ? 0 : 2;
            return number_format($money, $num_decimals);
        }
        return "";
    }

    /**
     * @param $money 数额
     * @param int $decimals 小数点后几位
     * @return string
     */
    public function money_with_symbol($money, $decimals=2) {
        if (is_numeric($money)) {
            $money = floatval($money);
//            $num_decimals = (intval($money) == $money) ? 0 : 2;
//            $num_decimals = 2;
            return $this->symbol . number_format($money, $decimals);
        }
        return '';
    }


    /**
     * 兑算汇率后的价格
     *
     * @param $money 数额
     * @param int $decimals 小数点后几位
     *
     * @return string
     */
    public function finance_money_with_symbol($money, $finance, $decimals = 2){
        if (is_numeric($money)) {
            $money = floatval($money * $finance);
            return $this->finance_symbol . number_format($money, $decimals);
        }

        return '';
    }

    /**
     * 兑算汇率后的价格
     * 带货币符号，整数不显示末尾0
     * example 1.1 => 1.10 1 => 1 1.11 => 1.11 1.111 => 1.11
     * @param $money 数额
     * @param int $decimals 小数点后几位
     *
     * @return string
     */
    public function finance_money_with_symbol_without_trailing_zeros($money, $finance, $decimals = 2){
        if (is_numeric($money)) {
            $money = floatval($money * $finance);
            $decimals = (intval($money) == $money) ? 0 : $decimals;
            return $this->finance_symbol . number_format($money, $decimals);
        }

        return '';
    }
}
