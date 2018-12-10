<?php

namespace Liquid\Object;

use common\models\MenusModel;
use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/linklists
 * Class Linklist
 * @package Liquid\Object
 *
 */
class Linklists extends Drop implements \Iterator
{
    private $array;
    private $userid;
    private $request_url;

    public function __construct($request_url) {
        $request_url = $request_url ? $request_url : '/';
        $this->request_url = $request_url[0] == '/' ? $request_url : '/' . $request_url;
    }

    public function rewind() {
        reset($this->array);
    }

    public function current() {
        return current($this->array);
    }

    public function key() {
        return key($this->array);
    }

    public function next() {
        next($this->array);
    }

    public function valid() {
        return false !== $this->current();
    }

    private function _loadData($method) {
        if (isset($this->array[$method])) {
            return;
        }

        // 加载导航数据
        $menu_tree = MenusModel::detail($method);
        // 构造平级菜单数据
        $items = $this->deMenus([$menu_tree]);
        foreach ($items as $item) {
            $this->array[$item['id']] = new Linklist($menu_tree['id'], '', $item, $this->request_url);
        }
    }

    public function invokeDrop($method) {
        $this->_loadData($method);
        
        return $this->array[$method] ? $this->array[$method] : [];
    }

    private function deMenus($menus)
    {
        $result = [];
        if ($menus) {
            foreach ($menus as $menu) {
                if (isset($menu['is_hide']) && $menu['is_hide']) {
                    continue;
                }
                if (isset($menu['children']) && $menu['children']) {
                    $sub_menu = self::deMenus($menu['children']);
                    $result = array_merge($result, $sub_menu);
                    foreach ($menu['children'] as $k => &$child) {
                        if (isset($child['is_hide']) && $child['is_hide']) {
                            unset($menu['children'][$k]);
                        }
                        if (isset($child['children']) && $child['children']) {
                            unset($child['children']);
                        }
                    }
                }
                $result[] = $menu;
            }
        }
        return $result;
    }
}
