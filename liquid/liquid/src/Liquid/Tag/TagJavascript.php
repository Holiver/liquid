<?php

/**
 *
 * @package Shopify
 */

namespace Liquid\Tag;

use Liquid\AbstractBlock;
use Liquid\Context;
use Liquid\FileSystem;

/**
 *Sections can bundle their own script and style assets using the javascript and stylesheet tags.
 *Like the schema tag, javascript and stylesheet tags do not output anything, and any Liquid inside them will not be executed.
 */

class TagJavascript extends AbstractBlock{

//    private static $codeLeftWrapper = ";(function(){try{";
//    private static $codeRightWrapper = "}catch(e){console.log(e)}})();";
    private static $codeLeftWrapper = "";
    private static $codeRightWrapper = "";

    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null){
        parent::__construct($markup,$tokens,$fileSystem);
    }

    public function render(Context $context){
        if (!isset($context->shareData['javascript'])) {
            $context->shareData['javascript'] = [
                'hashmap' => array(),
            ];
        }

        if (!isset($context->shareData['assets'])) {
            $context->shareData['assets'] = [
                'css' => [],
                'css_name' => [],
                'js' => [],
                'js_name' => []
            ];
        }

        $codeHashMap = &$context->shareData['javascript']['hashmap'];

        //ignore any liquid tag
        $code = $this->renderAll($this->nodelist, $context);
//        $code = implode("",$this->nodelist);

        $codeHash = md5($code);

        if (!$codeHashMap[$codeHash]){
            $code = (TagJavascript::$codeLeftWrapper .
                $code .
                TagJavascript::$codeRightWrapper);
//            $context->shareData['javascript']['code'][] = $code;
            $codeHashMap[$codeHash] = 1;
            $context->shareData['assets']['js'][] = [$code, 'code'];
        }

        return ""; //output nothing
    }
}
