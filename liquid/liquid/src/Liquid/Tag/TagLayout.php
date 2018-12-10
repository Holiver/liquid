<?php

/**
 *
 * @package Shopify
 */

namespace Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Context;
use Liquid\FileSystem;
use Liquid\LiquidException;
use Liquid\Regexp;

/**
 *
 */

class TagLayout extends AbstractTag{

    private $name = 'theme';

    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null){
        $syntaxRegexp = new Regexp('/"([^"]*)"|\'([^\']*)\'/');
        if ($syntaxRegexp->match($markup)){
            $this->name = $syntaxRegexp->matches[1];
            parent::__construct($markup, $tokens, $fileSystem);
        }else if (strtolower(trim($markup)) == 'none'){
            $this->name = null;
        }else{
            throw new LiquidException("Syntax Error in 'layout'");
        }
    }

    public function render(Context $context){
        $context->shareData['layout'] = $this->name;
        return "";
    }
}
