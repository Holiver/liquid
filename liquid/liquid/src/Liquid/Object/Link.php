<?php

namespace Liquid\Object;

use Liquid\Drop;

/**
 * refernece: https://help.shopify.com/themes/liquid/objects/link
 * Class Link
 * @package Liquid\Object
 */
class Link extends Drop
{

    private $active = true;
    private $title;
    /** Home page
     * Search page
     * Collection
     * All collections
     * Product
     * All products
     * Page
     * Blog
     * Web address
     **/
    private $type;
    private $url;
    private $id;
    private $is_active;

    public function __construct($title, $type, $url, $id, $is_active) {
        $this->title = $title;
        $this->type = $type;
        $this->url = $url;
        $this->id = $id;
        $this->is_active = $is_active;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
		$this->title = $title;
	}

	public function setLinks($links) {
	    $this->links = $links;
    }

    public function active() {
        return $this->is_active;
    }

    public function id() {
        return $this->id;
    }

    public function title() {
	    return $this->title;
    }

    public function url() {
        return $this->url;
    }

    public function type() {
        return $this->type;
    }

    public function object() {

    }

}
