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
use Liquid\Template;

/**
 *Sections can bundle their own script and style assets using the javascript and stylesheet tags.
 *Like the schema tag, javascript and stylesheet tags do not output anything, and any Liquid inside them will not be executed.
 */

class TagForm extends AbstractBlock{
    private $name;
    private $key;

    private static $formMarkup = array(
        'activate_customer_password' => '<form accept-charset="UTF-8" action="/account/activate" method="post">
<input name="form_type" type="hidden" value="activate_customer_password" />
<input name="utf8" type="hidden" value="1" />',
        'contact' => '<form accept-charset="UTF-8" action="/contact#contact_form" class="contact-form" method="post">
<input name="form_type" type="hidden" value="contact" />
<input name="utf8" type="hidden" value="1" />',
        'customer' => '<form method="post" action="/contact" id="contact_form" class="contact-form" accept-charset="UTF-8">
<input type="hidden" value="customer" name="form_type">
<input type="hidden" name="utf8" value="1">',
        'create_customer' => '<form accept-charset="UTF-8" action="/account" id="create_customer" method="post">
<input name="form_type" type="hidden" value="create_customer" />
<input name="utf8" type="hidden" value="1" />',
        'customer_login' => '<form accept-charset="UTF-8" action="/account/login" id="customer_login" method="post">
<input name="form_type" type="hidden" value="customer_login" />
<input name="utf8" type="hidden" value="1" />',
        'guest_login' => '<form method="post" action="/account/login" id="customer_login_guest" accept-charset="UTF-8">
<input type="hidden" value="guest_login" name="form_type">
<input type="hidden" name="utf8" value="1">
<input type="hidden" name="guest" value="true">
<input type="hidden" name="checkout_url" value="https://checkout.shopify.com/store-id/checkouts/session-id?step=contact_information">',
        'recover_customer_password' => '<form accept-charset="UTF-8" action="/account/recover" method="post">
<input name="form_type" type="hidden" value="recover_customer_password" />
<input name="utf8" type="hidden" value="1" />',
        'reset_customer_password' => '<form method="post" action="/account/reset" accept-charset="UTF-8">
<input type="hidden" value="reset_customer_password" name="form_type" />
<input name="utf8" type="hidden" value="1" />',
        'storefront_password' => '<form method="post" action="/password" id="login_form" class="storefront-password-form" accept-charset="UTF-8">
<input type="hidden" value="storefront_password" name="form_type">
<input type="hidden" name="utf8" value="1">',

        'article' => '<form method="post" action="{{ article.url }}/comments#comment_form" id="comment_form" class="comment-form" accept-charset="UTF-8" novalidate="">
<input type="hidden" value="new_comment" name="form_type">
<input type="hidden" name="utf8" value="1">',
    );

    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null) {
        $markup = trim($markup);
        if ($markup) {
            $syntaxRegexp = new Regexp('/("[^"]+"|\'[^\']+\')/');
            if ($syntaxRegexp->match($markup)) {
                $this->name = strtolower(substr($syntaxRegexp->matches[1], 1, strlen($syntaxRegexp->matches[1]) - 2));
            } else {
                $syntaxRegexp = new Regexp('/([\w]+)/');
                if ($syntaxRegexp->match($markup)) {
                    $this->name = strtolower($syntaxRegexp->matches[1]);
                    $this->key = $this->name;
                } else {
                    throw new LiquidException("Syntax Error in 'form'");
                }
            }
        }
        parent::__construct($markup,$tokens,$fileSystem);
    }

    public function render(Context $context){

        $inner = $this->renderAll($this->nodelist, $context);

        if (TagForm::$formMarkup[$this->name]){
            if ($this->key) {
                $content = TagForm::$formMarkup[$this->name];
                $document = new Template();
                $document->parse($content);
                $raw = $document->render([$this->key => $context->get($this->key)]);
                var_dump($raw);
                return $raw . $inner . '</form>';
            } else {
                return TagForm::$formMarkup[$this->name] . $inner . '</form>';
            }

        }else{
            throw new LiquidException("form " . $this->name . " not support");
        }

    }
}
