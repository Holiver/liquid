<?php

namespace Liquid\Object;

use Liquid\Drop;

/**
 * Reference: https://help.shopify.com/themes/liquid/objects/section
 * Class Section
 * @package Liquid\Object
 */
class Section extends Drop
{

	public function hasKey($name) {
		return true;
	}

	public function id() {
        $section = $this->context->get('section_tpl_name');
        if ($section) {
            return $section;
        }
        return $this->context->get('section_name');
    }

	public function settings() {
        return $this->context->get("settings.sections." . $this->context->get('section_tpl_name') . ".settings");
    }

    public function blocks() {
	    $arr = array();
        $bs = $this->context->get("settings.sections." . $this->context->get('section_tpl_name') . ".blocks");
        if (!$bs) {return $arr;}
        foreach ($bs as $k => $b) {
            $block = new Block();
            $block->setKey($k);
            $block->setValue($b);
            $arr[] = $block;
        }
        return $arr;
    }

}
