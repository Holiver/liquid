<?php

/**
 *
 * @package Shopify
 */

namespace Liquid\Tag;

use common\util\Common;
use Liquid\AbstractBlock;
use Liquid\Context;
use Liquid\Document;
use Liquid\FileSystem;
use Liquid\Liquid;
use Liquid\LiquidException;
use Liquid\Regexp;
use Liquid\Template;

/**
 *Sections can bundle their own script and style assets using the javascript and stylesheet tags.
 *Like the schema tag, javascript and stylesheet tags do not output anything, and any Liquid inside them will not be executed.
 */

class TagUse extends AbstractBlock {

    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null) {
        $syntax = new Regexp('/(' . Liquid::get('TAG_VARIABLE_NAME') . ')/');

        if ($syntax->match($markup)) {
            $this->assets = $syntax->matches[0];
        } else {
            throw new LiquidException("Syntax Error in 'use' - Valid syntax: use [var]");
        }
    }

    public function render(Context $context) {
        if ($this->assets) {
            $asset_key = 'assets';    // assets私有 common_assets公共
            if ($this->assets[0] === '<' && $this->assets[strlen($this->assets)-1] == '>') {    // common asset
                $asset_key = 'common_assets';
                $this->assets = substr($this->assets, 1, strlen($this->assets) - 2);
            }
            if (!isset($context->shareData[$asset_key])){
                $context->shareData[$asset_key] = [
                    'css' => [],
                    'css_name' => [],
                    'js' => [],
                    'js_name' => []
                ];
            }
            $assets_map = &$context->shareData[$asset_key];

            $type = '';
            $type_name = '';
            if (Common::endsWith($this->assets, '.css')) {
                $type = 'css';
                $type_name = 'css_name';
            } elseif (Common::endsWith($this->assets, '.js')) {
                $type = 'js';
                $type_name = 'js_name';
            }

            if ($type) {
                if (!in_array($this->assets, $assets_map[$type_name])) {
                    $asset_tag = '';
                    if ($type === 'css') {
                        if ($asset_key == 'assets') {
                            $asset_tag = "{{ '" . $this->assets . "' | asset_abs_url | stylesheet_tag }}";
                        } else {
                            $asset_tag = "{{ '" . $this->assets . "' | common_asset_abs_url | stylesheet_tag }}";
                        }
                    } elseif ($type === 'js') {
                        if ($asset_key == 'assets') {
                            $asset_tag = "{{ '" . $this->assets . "' | asset_abs_url | script_tag }}";
                        } else {
                            $asset_tag = "{{ '" . $this->assets . "' | common_asset_abs_url | script_tag }}";
                        }
                    }
                    $templateTokens = Template::tokenize($asset_tag);
                    $this->document = new Document($templateTokens, $this->fileSystem);
                    $asset_tag = $this->document->render($context);

                    $context->shareData[$asset_key][$type_name][] = $this->assets;
                    if ($type === 'js' && $asset_key === 'assets') {
                        $context->shareData[$asset_key][$type][] = [$asset_tag, 'js'];
                    } else {
                        $context->shareData[$asset_key][$type][] = $asset_tag;
                    }

                }
            }
        }

        return ""; //output nothing
    }

}
