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

use common\template\VideoUrlParser;
use Liquid\AbstractTag;
use Liquid\Context;
use Liquid\Document;
use Liquid\FileSystem;
use Liquid\Liquid;
use Liquid\LiquidException;
use Liquid\Regexp;
use Liquid\Template;

/**
 * Includes another, partial, template
 *
 * Example:
 *
 *     {% include 'foo' %}
 *
 *     Will include the template called 'foo'
 *
 *     {% include 'foo' with 'bar' %}
 *
 *     Will include the template called 'foo', with a variable called foo that will have the value of 'bar'
 *
 *     {% include 'foo' for 'bar' %}
 *
 *     Will loop over all the values of bar, including the template foo, passing a variable called foo
 *     with each value of bar
 */
class TagBlock extends AbstractTag
{
    /**
     * @var string The name of the template
     */
    private $templateName;

    /**
     * @var templateName is a variable or string
     */
    private $isVar = false;

    /**
     * @var bool True if the variable is a collection
     */
    private $collection;

    /**
     * @var mixed The value to pass to the child template as the template name
     */
    private $variable;

    /**
     * @var Document The Document that represents the included template
     */
    private $document;

    /**
     * @var string The Source Hash
     */
    protected $hash;
    private $baseTemplateName;
    private $basePath = "";
    private $section_file = 'index';

    private function setTemplateName($templateName) {
        if (substr($this->templateName, 0, strlen($this->basePath)) != $this->basePath) {
            $this->templateName = $this->basePath . $templateName;
        }
    }

    /**
     * Constructor
     *
     * @param string $markup
     * @param array $tokens
     * @param FileSystem $fileSystem
     *
     * @throws \Liquid\LiquidException
     */
    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null) {
        $regex = new Regexp('/("[^"]+"|\'[^\']+\'|' . Liquid::get('QUOTED_FRAGMENT') . ')(\s+(with|for)\s+(' . Liquid::get('QUOTED_FRAGMENT') . '+))?/');

        if ($regex->match($markup)) {
            if (preg_match('/^[\'|"].*/', $regex->matches[1])) {
                $this->templateName = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);
                $this->isVar = false;
            } else {
                $this->templateName = trim($regex->matches[1]);
                $this->isVar = true;
            }
            $this->baseTemplateName = $this->templateName;
            // $this->templateName = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);

            if (isset($regex->matches[1])) {
                $this->collection = (isset($regex->matches[3])) ? ($regex->matches[3] == "for") : null;
                $this->variable = (isset($regex->matches[4])) ? $regex->matches[4] : null;
            }

            $this->extractAttributes($markup);
        } else {
            throw new LiquidException("Error in tag 'section' - Valid syntax: include '[template]' (with|for) [object|collection]");
        }

        parent::__construct($markup, $tokens, $fileSystem);
    }

    /**
     * Parses the tokens
     *
     * @param array $tokens
     *
     * @throws \Liquid\LiquidException
     */
    public function parseInclude() {
        if ($this->fileSystem === null) {
            throw new LiquidException("No file system");
        }

        // read the source of the template and create a new sub document
        try {
            $source = $this->fileSystem->readTemplateFile($this->section_file);
        } catch (LiquidException $e) {
            return '';
        }

        $this->hash = md5($source);

        $cache = Template::getCache();

        if (isset($cache)) {
            if (($this->document = $cache->read($this->hash)) != false && $this->document->checkIncludes() != true) {
            } else {
                $templateTokens = Template::tokenize($source);
                $this->document = new Document($templateTokens, $this->fileSystem);
                $cache->write($this->hash, $this->document);
            }
        } else {
            $templateTokens = Template::tokenize($source);
            $this->document = new Document($templateTokens, $this->fileSystem);
        }

        return $source;
    }

    /**
     * check for cached includes
     *
     * @return boolean
     */
    public function checkIncludes() {
        $cache = Template::getCache();

        if ($this->document->checkIncludes() == true) {
            return true;
        }

        $source = $this->fileSystem->readTemplateFile($this->section_file);

        if ($cache->exists(md5($source)) && $this->hash == md5($source)) {
            return false;
        }

        return true;
    }

    private function renderVideo($section_data) {
        if (isset($section_data['settings']['video_url'])) {
            $video_url = $section_data['settings']['video_url'];
            $info = VideoUrlParser::getUrlInfo($video_url);
            if ($info[0]) {
                $video = [
                    'type' => $info[0],
                    'id' => $info[1]
                ];
                $section_data['settings']['video_url'] = $video;
            }
        }
        return $section_data;
    }

    /**
     * Renders the node
     *
     * @param Context $context
     *
     * @return string
     */
    public function render(Context $context) {
        $result = '';
        $variable = $context->get($this->variable);

        $context->push();

        foreach ($this->attributes as $key => $value) {
            $context->set($key, $context->get($value));
        }

        if ($this->isVar) {
            $this->baseTemplateName = $context->get($this->baseTemplateName);
        }

        $templateName = $this->baseTemplateName;
        if ($this->isVar) {
            $templateName = $context->get($templateName);
        }
        $this->templateName = $templateName;

        $context->set('section_tpl_name', $templateName);
        $section_data =  $context->get("settings.sections." . $templateName);
        $section_type = isset($section_data['type']) ? $section_data['type'] : null;
//        $section_type = $context->get("settings.sections." . $this->baseTemplateName . ".type");
        $need_default = false;
        if ($section_type) {
            $this->templateName = $section_type;
            if (!isset($section_data['settings']) || empty($section_data['settings'])) {
                $need_default = true;
            }
        } else {
            $this->templateName = $templateName;
            $need_default = true;
        }
        $context->set('section_name', $this->templateName);

        $this->setTemplateName($this->templateName);

        $source = $this->parseInclude();
        if (!$source) {
            return '';
        }

        // 设置默认section数据
        if (!$need_default) {
            if ($section_type === 'video') {    // special block
                $section_data = $this->renderVideo($section_data);
            }
            if ($section_data['blocks']) {
                foreach ($section_data['blocks'] as $k => $block) {
                    if ($block['type'] === 'video') {
                        $section_data['blocks'][$k] = $this->renderVideo($block);
                    }
                }
            }
        }

        // 设置新settings数据
        $settings = $context->get('settings');
        if (isset($settings['sections'])) {
            $settings['sections'][$templateName] = $section_data;
        } else {
            $settings = [
                'sections' => [
                    $templateName => $section_data
                ]
            ];
        }
        $context->set('settings', $settings);

        if ($this->collection) {
            foreach ($variable as $item) {
                $context->set($templateName, $item);
                $result .= $this->document->render($context);
            }
        } else {
            if (!is_null($this->variable)) {
                $context->set($templateName, $variable);
            }

            $result .= $this->document->render($context);
        }

        $class = self::SECTION_CLASS_NAME;
        if (isset($context->get('schema')['class'])) {
            $class = $class . " " . $context->get('schema')['class'];
        }

        $section_id = $context->get('section_tpl_name');        // 卡片id
        $section_style = $section_data['settings']['style'];    // 卡片风格id

        $result = '<div id="' . self::SECTION_CLASS_NAME . '-' . $section_id . '" class="' . $class . '" data-section-id="' . $section_id . '"' .
            ' data-section-type="' . $section_type . '" data-section-style="' . $section_style . '">' . $result . '</div>';

        $context->pop();

        return $result;
    }
}