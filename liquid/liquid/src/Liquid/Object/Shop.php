<?php

namespace Liquid\Object;

use common\components\internal\TigerApi;
use common\components\internal\TortoiseApi;
use common\models\GlobalConfig;
use common\models\PaymentModel;
use common\parser\util;
use Liquid\Drop;

/**
 * Reference: https://help.shopify.com/themes/liquid/objects/shop
 * Class Shop
 * @package Liquid\Object
 */
class Shop extends Drop
{

    private $data;
    private $url;
    private $enabled_payment_types = null;
    private $register_info;
    private $domain_info;
    private $shopid;
    private $env = 0;
    private $global_configs;
    private $finance;
    private $skin;
    private $cdn_domain;
    private $enable_payment_list;

    public function __construct($url = '', $shopid = -1, $env = 0, $cdn_domain = '')
    {
        $this->url = $url;
        $this->shopid = $shopid;
        $this->env = $env;
        $this->cdn_domain = $cdn_domain;
    }

    public function _loadData()
    {
        if ($this->data === null) {
            $this->data = TigerApi::getStoreInfo();
        }
        if ($this->data === null) {
            $this->data = array();
        }
    }

    private function _getRegisterInfo()
    {
        if ($this->register_info === null) {
            $this->register_info = \Yii::$app->shoplazaService->getUserInfo($this->shopid);;
        }
        if ($this->register_info === null) {
            $this->register_info = [];
        }
    }

    private function _getDomainInfo()
    {
        if ($this->domain_info === null) {
            $this->domain_info = $this->_getPrimaryDomain($this->shopid);
        }
        if ($this->domain_info === null) {
            $this->domain_info = [];
        }
    }

    private function _getPrimaryDomain($store_id)
    {
        $domain_list = \Yii::$app->shoplazaService->getDomainList($store_id);
        $default_domain = '';
        if (isset($domain_list['data'])) {
            foreach ($domain_list['data'] as $domain) {
                if ($domain['is_primary']) {
                    return $domain['domain'];
                }
                $default_domain = $domain['domain'];
            }
        }
        return $default_domain;
    }

    private function _get($key, $default = '')
    {
        if ($this->data === null) {
            $this->_loadData();
        }
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    private function _getGlobalConfigs()
    {
        if ($this->global_configs === null) {
            $this->global_configs = util::getGlobalConf($this->shopid);
        }

        if ($this->global_configs === null) {
            $this->global_configs = [];
        }
    }

    /**
     * 汇率
     */
    private function _getFinance()
    {
        if ($this->finance === null) {
            $currency = util::getCurrency($this->shopid);

            //是否需要转换汇率
            if ($this->currency_switch()) {
                //汇率
                $financeList = TortoiseApi::getFinance($currency['currency'], [$currency['finance_currency']]);
                foreach ($financeList as $row) {
                    if ($row['currency_code'] == $currency['finance_currency']) {
                        $finance = $row['currency_value'];
                    }

                }
            }

            $this->finance = $finance ? $finance : 1;
        }
    }

    public function global_configs()
    {
        if ($this->global_configs === null) {
            $this->_getGlobalConfigs();
        }

        return $this->global_configs;
    }

    public function currency_switch()
    {
        if ($this->global_configs === null) {
            $this->_getGlobalConfigs();
        }

        return isset($this->global_configs['currency']['switch']) ? $this->global_configs['currency']['switch'] : 0;
    }

    public function language_switch()
    {
        if ($this->global_configs === null) {
            $this->_getGlobalConfigs();
        }

        return isset($this->global_configs['language']['switch']) ? $this->global_configs['language']['switch'] : 0;
    }

    public function language_list()
    {
        if ($this->global_configs === null) {
            $this->_getGlobalConfigs();
        }

        if (isset($this->global_configs['language']['values'])) {
            return $this->global_configs['language']['values'];
        } else {
            return [];
        }
    }

    public function currency_list()
    {
        if ($this->global_configs === null) {
            $this->_getGlobalConfigs();
        }

        if (isset($this->global_configs['currency']['values'])) {
            $values = &$this->global_configs['currency']['values'];
            // 加国旗
            if (count($values) > 0 && !isset($values[0]['flag'])) {
                $currency_codes = array_column($values, 'code');

                $code_flag_map = [];
                $rows = TortoiseApi::getCurrency();
                if ($rows) {
                    foreach ($rows as $row) {
                        if (in_array($row['code'], $currency_codes)) {
                            $code_flag_map[$row['code']] = $row['flag'];
                        }
                    }
                }


                foreach ($values as &$value) {
                    $value['flag'] = isset($code_flag_map[$value['code']]) ? $code_flag_map[$value['code']] : '';
                }
            }
            return $this->global_configs['currency']['values'];
        } else {
            return [];
        }
    }

    public function finance()
    {
        if ($this->finance === null) {
            $this->_getFinance();
        }

        return $this->finance;
    }

    public function id()
    {
        return $this->shopid;
    }

    public function address()
    {
        return $this->_get('address');
    }

    public function collections_count()
    {
    }

    public function currency()
    {
        return $this->_get('currency');
    }

    public function description()
    {
        return $this->_get('description');
    }

    public function domain()
    {
        if ($this->domain_info === null) {
            $this->_getDomainInfo();
        }
        if ($this->domain_info) {
            return $this->domain_info;
        }
        return $this->_get('domain');
    }

    public function email()
    {
        if ($this->register_info === null) {
            $this->_getRegisterInfo();
        }
        if ($this->register_info && $this->register_info['email']) {
            return $this->register_info['email'];
        }
        return $this->_get('email');
    }

    public function contactEmail()
    {
        if (!empty($this->_get('service_email'))) {
            return $this->_get('service_email');
        }
        return $this->email();
    }

    public function metafields()
    {
        return $this->_get('metafields');
    }

    public function money_format()
    {
        $currency = $this->_get('currency', 'USD');
        if ($currency == 'JPY') {
            return "{{amount}} ฿";
        }
        return "{{amount}} $";
    }

    public function money_with_currency_format()
    {
        $currency = $this->_get('currency', 'USD');
        if ($currency == 'JPY') {
            return "{{amount}} ฿";
        }
        return "{{amount}} $";
    }

    public function name()
    {
        return $this->_get('name');
    }

    public function policies()
    {
        return $this->_get('password_message');
    }

    public function permanent_domain()
    {
        return $this->_get('permanent_domain');
    }

    public function products_count()
    {
    }

    public function url()
    {
        if ($this->url) {
            return $this->url;
        }
        $domain = $this->domain();
        if ($domain) {
            return 'http://' . $domain;
        }
        return $this->url;
    }

    public function secure_url()
    {
        $url = $this->url();
        if ($url) {
            return str_replace('http://', 'https://', $url);
        }
        return $this->url;
    }

    public function locale()
    {
        return 'en';
    }

    public function phone()
    {
        if ($this->register_info === null) {
            $this->_getRegisterInfo();
        }
        if ($this->register_info && $this->register_info['cell']) {
            return $this->register_info['cell'];
        }

        return $this->_get('phone');
    }

    public function logo()
    {
        $icon = $this->_get('icon');
        if (!empty($icon) && !empty($icon['src'])) {
            return $icon['src'];
        }
        return '';
    }

    // 主域名url
    public function primary_url()
    {
        $domain = $this->domain();
        if ($domain) {
            return 'http://' . $domain;
        }
        return '';
    }

    public function symbol()
    {
        return $this->_get('symbol', '$');
    }

    public function finance_symbol()
    {
        $currency = util::getCurrency($this->shopid);

        return $currency['finance_symbol'];
    }

    public function env()
    {
        return $this->env;
    }

    public function favicon()
    {
        $icon = $this->_get('icon');
        if (isset($icon['src']) && $icon['src']) {
            return $icon['src'];
        }
        return $this->cdn_domain . 'assets/favicon.ico';
    }

    // 皮肤配置
    public function skin_config()
    {
        if ($this->skin === null) {
            $skin = util::getSkin($this->shopid);
            $this->skin = [];
            if ($skin) {
                $this->skin = $skin;
            }
        }
        return $this->skin;
    }

    // 店铺部署信息
    public function deploy()
    {
        return DEPLOY;
    }

    public function customer_authority()
    {
        $checkout_setting = GlobalConfig::getEvent(GlobalConfig::EVENT_CHECKOUT_SETTING);
        if (empty($checkout_setting)) {
            return 2;
        }
        $checkout_setting = json_decode($checkout_setting, true);
        if (!isset($checkout_setting['customer_authority']))
            return 2;
        return $checkout_setting['customer_authority'];
    }

    // c端默认图
    public function default_img()
    {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
    }

    // 店铺创建时间
    public function create_time()
    {
        $create_time = $this->_get('create_time');
        $create_time = $create_time ? $create_time : time();
        return date('Y', $create_time);
    }

    public function cdn_domain()
    {
        return $this->cdn_domain;
    }

    //支付设置
    public function enabled_payment_types()
    {
        if ($this->enabled_payment_types !== null) {
            return $this->enabled_payment_types;
        }
        $result = PaymentModel::getList(['is_enable' => true, 'is_active' => true]);
        $this->enable_payment_list = $result;
        if (empty($result)) {
            $res = [];
        } else {
            $res = [];
            foreach ($result as $value) {
                if (empty($value['channel_list'])) {
                    continue;
                }
                foreach ($value['channel_list'] as $val) {
                    if (empty($val['payment_channel']))
                        continue;
                    $res[] = $val['payment_channel'];
                }
            }
        }
        $this->enabled_payment_types = $res;
        return $this->enabled_payment_types;
    }

    public function sa_server_url()
    {
        return getenv("SA_SERVER_URL");
    }

    public function sa_web_url()
    {
        $server_url = $this->sa_server_url();
        $url = '';
        if ($server_url) {
            $url_info = parse_url($server_url);
            $url = $url_info['scheme'] ? $url_info['scheme'] . '://' : '';
            $url .= $url_info['host'] ?? '';
            $url .= $url_info['port'] ? ':' . $url_info['port'] : '';
        }
        return $url ? $url . '/' : $url;
    }
}
