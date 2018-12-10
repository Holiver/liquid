<?php
namespace Liquid\Filter;

class LinkFilters {

    private $request_url;

    public function __construct($url) {
        $this->request_url = $url[0] === '/' ? $url : '/' . $url;
    }

    public function url_active($url) {
        $active = false;
        if ($this->request_url == '/') {
            $active = true;
        } elseif ($this->request_url === $url) {
            $active = true;
        }
        return $active;
    }
}