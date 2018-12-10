<?php
namespace Liquid\Filter;

class I18nFilters {

    public $default;
    public $locales = array();

    public function __construct($default='en', $locales=[]) {
        $this->default = $default;
        $this->locales = $locales;
    }

    public function t($input, $args = array()) {
        if (!$input) {
            return '';
        }

        $vars = explode('.', $input);
        $value = $this->locales[$this->default];

        foreach ($vars as $var) {
            if (!isset($value[$var])) {
                return '';
            }
            $value = $value[$var];
        }
        if (is_string($value)) {
            $m = preg_match_all("/({{\s*([\w\-\_]+)\s*}})/", $value, $matches);
            if ($m && count($matches) > 2) {
                for ($i = 0; $i < count($matches[1]); $i += 1) {
                    if (isset($args[$matches[2][$i]])) {
                        $value = str_replace($matches[1][$i], $args[$matches[2][$i]], $value);
                    }
                }
            }
        }
        return $value;
    }
}
//
//$i18nFilters = new I18nFilters(__DIR__.'/../../themes/debut/locales/');
//echo json_encode($i18nFilters::t('general.accessibility'));