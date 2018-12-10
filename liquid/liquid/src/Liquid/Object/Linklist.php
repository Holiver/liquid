<?php

namespace Liquid\Object;

use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/linklist
 * Class Linklist
 * @package Liquid\Object
 *
 * 1 frontpage 2 collection 3 collections 4 product 5 catalog 6 page 7 blog 8 search 9 http
 */
class Linklist extends Drop
{

    public static $TYPE_COLLECTION = 'Collection';
    public static $TYPE_PRODUCT = 'Product';

    private $request_url;

    private $data;

    public function __construct($id, $type='', $data=array(), $request_url='') {
        $this->data = $data;
        $this->request_url = $request_url;
    }

	public function id() {
        if ($this->data && isset($this->data['id'])) {
            return $this->data['id'];
        }
        return null;
    }

    public function setLinks() {
        $this->data['links'] = null;
        if ($this->data && (isset($this->data['sub']) || isset($this->data['children']))) {
            $sub_menus = isset($this->data['sub']) ? $this->data['sub'] : $this->data['children'];
            if (count($sub_menus) > 0) {
                $this->data['links'] = [];
                foreach ($sub_menus as $sub_menu) {
                    $active = false;
                    if ($this->request_url == $sub_menu['url']) {
                        $active = true;
                    }
                    $this->data['links'][] = new Link($sub_menu['title'], $sub_menu['type'], $sub_menu['url'],
                        $sub_menu['id'], $active);
                }
            }
        }
    }

	public function links() {

        if ($this->data && !isset($this->data['links'])) {
            $this->setLinks();
        }
        if ($this->data && isset($this->data['links'])) {
            return $this->data['links'];
        }
        return null;
    }

    public function title() {
        if ($this->data && isset($this->data['title'])) {
            return $this->data['title'];
        }
        return '';
    }

    public function handle() {
        if ($this->data && isset($this->data['handle'])) {
            return $this->data['handle'];
        }
        return '';
    }

    public function url() {
        if ($this->data && isset($this->data['url'])) {
            return $this->data['url'];
        }
        return '';
    }

    public function is_home() {
        if ($this->data && isset($this->data['is_home'])) {
            return $this->data['is_home'];
        }
        return '';
    }

    public function active() {
        $active = false;
        if ($this->request_url === $this->url()) {
            $active = true;
        }
        return $active;
    }

}
