<?php
namespace Liquid\Object;

use Liquid\Drop;

class Article extends Drop {

    private $name;
    private $data;

    public function __construct($name='') {
        $this->name = $name;
    }

    private function _loadData() {
        if ($this->data === null) {
            // load data
            if ($this->name) {
//                $this->data = ArticleModel::getArticleByUrl($this->name, true);
            }
//            else {
//                $this->data = ArticleModel::getArticleByMinId(true);
//            }
        }
        if ($this->data === null) {
            $this->data = [];
        }
    }

    private function _get($key, $default='') {
        if ($this->data === null) {
            $this->_loadData();
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    public function author() {
        return $this->_get('author');
    }

    public function comments() {
        return $this->_get('comments');
    }

    public function comments_count() {
        return $this->_get('comments_count');
    }

    public function comments_enabled() {
        return $this->_get('comments_enabled');
    }

    public function comment_post_url() {
        return $this->_get('comment_post_url');
    }

    public function content() {
        return $this->_get('content');
    }

    public function created_at() {
        return $this->_get('created_at');
    }

    public function excerpt() {
        return $this->_get('excerpt');
    }

    public function excerpt_or_content() {
        return $this->_get('excerpt_or_content');
    }

    public function id() {
        return $this->_get('id');
    }

    public function handle() {
        return $this->_get('handle');
    }

    public function image() {
        return $this->_get('image');
    }

    public function moderated() {
        return $this->_get('moderated');
    }

    public function published_at() {
        return $this->_get('published_at');
    }

    public function tags() {
        return $this->_get('tags');
    }

    public function title() {
        return $this->_get('title');
    }

    public function url() {
        return $this->_get('url');
    }

    public function user() {
        return $this->_get('user');
    }

    public function src() {
        return '';
    }

    public function __toJson() {
        $this->_loadData();
        if ($this->data) {
            return $this->_recJson($this->data);
        }
        return [];
    }

    private function _recJson($json) {
        if (is_array($json)) {
            foreach ($json as $k => $v) {
                $json[$k] = $this->_recJson($v);
            }
        } elseif (is_object($json)) {
            if (method_exists($json, '__toJson')) {
                return $json->__toJson();
            }
            return $json->__toString();
        }
        return $json;
    }
}