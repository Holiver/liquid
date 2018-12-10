<?php

/**
 *
 * @package Shopify
 */

namespace Liquid\Tag;

use Liquid\AbstractBlock;
use Liquid\Context;
use Liquid\FileSystem;
use Liquid\LiquidException;
use Liquid\Regexp;

/**
 *Sections can bundle their own script and style assets using the javascript and stylesheet tags.
 *Like the schema tag, javascript and stylesheet tags do not output anything, and any Liquid inside them will not be executed.
 */

class TagStylesheet extends AbstractBlock{

    private $cssLang;

    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null){
        $markup = trim($markup);
        if ($markup){
            $syntaxRegexp = new Regexp('/"([^"]*)"|\'([^\']*)\'/');
            if ($syntaxRegexp->match($markup)){
                $this->cssLang = strtolower($syntaxRegexp->matches[1]);
            }else{
                throw new LiquidException("Syntax Error in 'stylesheet'");
            }
        }
        parent::__construct($markup,$tokens,$fileSystem);
    }

    private function renderCss($code){
        if (!$this->cssLang){
            return $code;
        }

        if ($this->cssLang == "scss"){
            $compiler = new \Leafo\ScssPhp\Compiler();
            try{
                return $compiler->compile($code);
            }catch(Exception $e){
                throw $e;
            }
        }

        throw new LiquidException($this->cssLang . "is not supported");
    }

    public function render(Context $context){
        if ( !isset($context->shareData['css'])){
            $context->shareData['css'] = array(
                'hashmap' => array(),
                'code' => array(),
            );
        }

        $codeHashMap = &$context->shareData['css']['hashmap'];

//        $code = implode("",$this->nodelist);
        //ignore any liquid tag
        $code = $this->renderAll($this->nodelist, $context);

        $code = $this->renderCss($code);

        $codeHash = md5($code);

        if (!$codeHashMap[$codeHash]){
            $context->shareData['css']['code'][] = $code;
            $codeHashMap[$codeHash] = 1;
        }

        return ""; //output nothing
    }
}