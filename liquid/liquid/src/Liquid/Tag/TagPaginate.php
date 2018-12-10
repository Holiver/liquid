<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid\Tag;

use Liquid\AbstractBlock;
use Liquid\Context;
use Liquid\FileSystem;
use Liquid\Liquid;
use Liquid\LiquidException;
use Liquid\Regexp;

/**
 * The paginate tag works in conjunction with the for tag to split content into numerous pages. 
 *
 * Example:
 *
 *	{% paginate collection.products by 5 %}  
 * 		{% for product in collection.products %}
 * 			<!--show product details here -->
 * 		{% endfor %}
 * 	{% endpaginate %}
 *
 */

class TagPaginate extends AbstractBlock
{
	/**
     * @var array The collection to paginate
     */
    private $collectionName;

    /**
     * @var array The collection object
     */
    private $collection;
    
    /**
     *
     * @var int The size of the collection
     */
    private $collectionSize;

	/**
     * @var int The number of items to paginate by
     */
    private $numberItems;
    
    /**
     * @var int The current page
     */
    private $currentPage;
    
    /**
     * @var int The current offset (no of pages times no of items)
     */
    private $currentOffset;
    
    /**
     * @var int Total pages
     */
    private $totalPages;

    /**
     * @var 当前页前后显示页数
     */
    private static $SIDE_SIZE = 2;
    /**
     * Constructor
     *
     * @param string $markup
     * @param array $tokens
	 * @param FileSystem $fileSystem
     *
	 * @throws \Liquid\LiquidException
     *
     */
	public function __construct($markup, array &$tokens, FileSystem $fileSystem = null) {
       
        parent::__construct($markup, $tokens, $fileSystem);

//        $syntax = new Regexp('/(' . Liquid::get('ALLOWED_VARIABLE_CHARS') . '+)\s+by\s+(\w+)/');
        $syntax = new Regexp('/(' . Liquid::get('ALLOWED_VARIABLE_CHARS') . '+)\s+by\s+([a-zA-Z0-9\.\-\_]+)/');

        if ($syntax->match($markup)) {
            $this->collectionName = $syntax->matches[1];
            $this->numberItems = $syntax->matches[2];
            $this->extractAttributes($markup);
        } else {
            throw new LiquidException("Syntax Error - Valid syntax: paginate [collection] by [items]");
        }
        
    }

    /**
     * Renders the tag
     *
     * @param Context $context
     *
     * @return string
     *
     */
    public function render(Context $context) {
	    if (!is_numeric($this->numberItems)) {
	        $this->numberItems = intval($context->get($this->numberItems));
	        if ($this->numberItems == 0) {
	            $this->numberItems = 20;
            }
        }
        $this->currentPage = ( is_numeric($context->get('page')) ) ? $context->get('page') : 1;
        $this->currentOffset = ($this->currentPage - 1) * $this->numberItems;
    	$this->collection = $context->get($this->collectionName);

    	$is_all_data = true; // 标示数据是不是所有还是部分
        $this->collectionSize = -1;
        if ($this->collection instanceof \Traversable) {
            if (method_exists($this->collection, 'genArrayByPage')) {
                $this->collection->genArrayByPage($this->currentPage - 1, $this->numberItems);
                $this->collectionSize = $this->collection->count();
                $is_all_data = false;
            }
            $this->collection = iterator_to_array($this->collection);
        }

        if ($this->collectionSize === -1) {
            $this->collectionSize = count($this->collection);
        }

        $this->totalPages = 0;
        if (intval($this->numberItems) !== 0) {
            $this->totalPages = intval(ceil($this->collectionSize / $this->numberItems));
        }

    	if (is_array($this->collection)) {
            if ($is_all_data) {
                $paginatedCollection = array_slice($this->collection, $this->currentOffset, $this->numberItems);
            } else {
                $paginatedCollection = $this->collection;
            }

            // Sets the collection if it's a key of another collection (ie search.results, collection.products, blog.articles)
            $segments = explode('.', $this->collectionName);
            if (count($segments) == 2) {
                $top = $context->get($segments[0]);
                if (is_object($top)) {
                    $top->setField($segments[1], $paginatedCollection);
                } else {
                    $top[$segments[1]] = $paginatedCollection;
                }

                $context->set($segments[0], $top);
//            $context->set($segments[0], array($segments[1] => $paginatedCollection));
            } else {
                $context->set($this->collectionName, $paginatedCollection);
            }
        }
    	
    	$paginate = array(
    		'page_size' => $this->numberItems,
    		'current_page' => $this->currentPage,
    		'current_offset' => $this->currentOffset,
    		'pages' => $this->totalPages,
    		'items' => $this->collectionSize
    	);

    	if ( $this->currentPage != 1 ) {
	    	$paginate['previous']['title'] = 'Previous';
	    	$paginate['previous']['url'] = $this->currentUrl($context) . 'page=' . ($this->currentPage - 1);
            $paginate['previous']['is_link'] = true;
//            $paginate['previous']['url'] = $this->replaceUrl($context, $this->currentPage - 1);
    	}
    	
    	if ( $this->currentPage != $this->totalPages ) {
	    	$paginate['next']['title'] = 'Next';
	    	$paginate['next']['url'] = $this->currentUrl($context) . 'page=' . ($this->currentPage + 1);
	    	$paginate['next']['is_link'] = true;
//            $paginate['next']['url'] = $this->replaceUrl($context, $this->currentPage + 1);
    	}

    	$paginate['parts'] = array();
    	if ($this->currentPage > self::$SIDE_SIZE + 1) {
    	    $paginate['parts'][] = array(
    	        'is_link' => false,
                'title' => '...',
                'url' => ''
            );
        }
        $start = max($this->currentPage - self::$SIDE_SIZE, 1);
    	$end = min($this->currentPage + self::$SIDE_SIZE, $this->totalPages);
    	for ($i = $start; $i <= $end; $i += 1) {
    	    if ($i == $this->currentPage) {
                $paginate['parts'][] = array(
                    'is_link' => false,
                    'title' => strval($i),
                    'url' => ''
                );
            } else {
                $paginate['parts'][] = array(
                    'is_link' => true,
                    'title' => strval($i),
                    'url' => $this->currentUrl($context) . 'page=' . $i
                );
            }
        }
        if ($this->currentPage + self::$SIDE_SIZE < $this->totalPages) {
            $paginate['parts'][] = array(
                'is_link' => false,
                'title' => '...',
                'url' => ''
            );
        }

        $context->set('paginate', $paginate);

        return parent::render($context);
        
    }
    
    /**
     * Returns the current page URL
     *
     * @param Context $context
     *
     * @return string
     *
     */
    public function currentUrl($context) {
	    $request_uri = $context->get('request_uri') ? $context->get('request_uri') : $context->get('REQUEST_URI');
	    $uri = explode('?', $request_uri);

//	    $url = 'http';
//		if ($context->get('HTTPS') == 'on') $url .= 's';
//		$url .= '://' . $context->get('HTTP_HOST') . reset($uri);

        $url = reset($uri);
		$url .= '?';
		if (count($uri) > 1) {
            $params = explode('&', $uri[1]);
            $q = '';
            foreach ($params as $param) {
                if (strpos($param, 'page=') !== 0) {
                    if ($q !== '') {
                        $q .= '&';
                    }
                    $q .= $param;
                }
            }
            if ($q && $q !== '') {
                $url .= $q . '&';
            }

        }

		return $url;
		
    }

    public function replaceUrl($context, $page) {
        $request_uri = $context->get('request_uri') ? $context->get('request_uri') : $context->get('REQUEST_URI');
        $uri = explode('?', $request_uri);
        $url = 'http';
        if ($context->get('HTTPS') == 'on') $url .= 's';
        $url .= '://' . $context->get('HTTP_HOST') . reset($uri);
        $queryParts = explode('&', $context->get('QUERY_STRING'));
        $params = array();
        $exist = false;
        foreach ($queryParts as $param) {
//            $item = explode('=', $param);
            $pos = strpos($param, '=');
            if ($pos === false) {
                continue;
            }
            $key = substr($param, 0, $pos);
            $value = substr($param, $pos + 1, strlen($param) - $pos);
            if ($key == 'page') {
                $value = $page;
                $exist = true;
            }
            $params[] = $key . '=' . $value;
        }
        if (!$exist) {
            $params[] = 'page=' . $page;
        }

        $url .= '?' . implode('&', $params);
        return $url;
    }
    
}
