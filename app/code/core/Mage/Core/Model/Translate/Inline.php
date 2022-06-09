<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Inline Translations PHP part
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Translate_Inline
{
    /**
     * Regular Expression for detected and replace translate
     *
     * @var string
     */
    protected $_tokenRegex = '\{\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\}';

    /**
     * Response body or JSON content string
     *
     * @var string
     */
    protected $_content;

    /**
     * Is enabled and allowed inline translates flags
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted    = false;

    /**
     * Current content is JSON or Response body
     *
     * @var bool
     */
    protected $_isJson              = false;

    /**
     * Get max translate block in same tag
     *
     * @var int
     */
    protected $_maxTranslateBlocks    = 7;

    /**
     * List of global tags
     *
     * @var array
     */
    protected $_allowedTagsGlobal = array(
        'script'    => 'String in Javascript',
        'title'     => 'Page title',
    );

    /**
     * List of simple tags
     *
     * @var array
     */
    protected $_allowedTagsSimple = array(
        'legend'        => 'Caption for the fieldset element',
        'label'         => 'Label for an input element.',
        'button'        => 'Push button',
        'a'             => 'Link label',
        'b'             => 'Bold text',
        'strong'        => 'Strong emphasized text',
        'i'             => 'Italic text',
        'em'            => 'Emphasized text',
        'u'             => 'Underlined text',
        'sup'           => 'Superscript text',
        'sub'           => 'Subscript text',
        'span'          => 'Span element',
        'small'         => 'Smaller text',
        'big'           => 'Bigger text',
        'address'       => 'Contact information',
        'blockquote'    => 'Long quotation',
        'q'             => 'Short quotation',
        'cite'          => 'Citation',
        'caption'       => 'Table caption',
        'abbr'          => 'Abbreviated phrase',
        'acronym'       => 'An acronym',
        'var'           => 'Variable part of a text',
        'dfn'           => 'Term',
        'strike'        => 'Strikethrough text',
        'del'           => 'Deleted text',
        'ins'           => 'Inserted text',
        'h1'            => 'Heading level 1',
        'h2'            => 'Heading level 2',
        'h3'            => 'Heading level 3',
        'h4'            => 'Heading level 4',
        'h5'            => 'Heading level 5',
        'h6'            => 'Heading level 6',
        'center'        => 'Centered text',
        'select'        => 'List options',
        'img'           => 'Image',
        'input'         => 'Form element',
    );

    /**
     * Is enabled and allowed Inline Translates
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if (is_null($store)) {
            $store = Mage::app()->getStore();
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }

        if (is_null($this->_isAllowed)) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active_admin', $store);
            } else {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active', $store);
            }

            $this->_isAllowed = $active && Mage::helper('core')->isDevAllowed($store);
        }

        /* @var Mage_Core_Model_Translate $translate */
        $translate = Mage::getSingleton('core/translate');

        return $translate->getTranslateInline() && $this->_isAllowed;
    }

    /**
     * Parse and save edited translate
     *
     * @param array $translate
     * @return $this
     */
    public function processAjaxPost($translate)
    {
        if (!$this->isAllowed()) {
            return $this;
        }

        /* @var Mage_Core_Model_Mysql4_Translate_String $resource */
        $resource = Mage::getResourceModel('core/translate_string');
        foreach ($translate as $t) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $storeId = 0;
            } elseif (empty($t['perstore'])) {
                $resource->deleteTranslate($t['original'], null, false);
                $storeId = 0;
            } else {
                $storeId = Mage::app()->getStore()->getId();
            }

            $resource->saveTranslate($t['original'], $t['custom'], null, $storeId);
        }

        return $this;
    }

    /**
     * Strip inline translations from text
     *
     * @param array|string $body
     * @return $this
     */
    public function stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->stripInlineTranslations($part);
            }
        } elseif (is_string($body)) {
            $body = preg_replace('#' . $this->_tokenRegex . '#', '$1', $body);
        }
        return $this;
    }

    /**
     * Replace translate templates to HTML fragments
     *
     * @param array|string $body
     * @return $this
     */
    public function processResponseBody(&$body)
    {
        if (!$this->isAllowed()) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $this->stripInlineTranslations($body);
            }
            return $this;
        }

        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part);
            }
        } elseif (is_string($body)) {
            $this->_content = $body;

            $this->_specialTags();
            $this->_tagAttributes();
            $this->_otherText();
            $this->_insertInlineScriptsHtml();

            $body = $this->_content;
        }

        return $this;
    }

    /**
     * Add translate js to body
     */
    protected function _insertInlineScriptsHtml()
    {
        if ($this->_isScriptInserted || stripos($this->_content, '</body>')===false) {
            return;
        }

        $baseJsUrl = Mage::getBaseUrl('js');
        $url_prefix = Mage::app()->getStore()->isAdmin() ? 'adminhtml' : 'core';
        $ajaxUrl = Mage::getUrl(
            $url_prefix . '/ajax/translate',
            array('_secure'=>Mage::app()->getStore()->isCurrentlySecure())
        );
        $trigImg = Mage::getDesign()->getSkinUrl('images/fam_book_open.png');

        ob_start();
        $magentoSkinUrl = Mage::getDesign()->getSkinUrl('lib/prototype/windows/themes/magento.css');
?>
<!-- script type="text/javascript" src="<?php echo $baseJsUrl ?>prototype/effects.js"></script -->
<script type="text/javascript" src="<?php echo $baseJsUrl ?>prototype/window.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $baseJsUrl ?>prototype/windows/themes/default.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $magentoSkinUrl; ?>"/>

<script type="text/javascript" src="<?php echo $baseJsUrl ?>mage/translate_inline.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $baseJsUrl ?>mage/translate_inline.css"/>

<div id="translate-inline-trig"><img src="<?php echo $trigImg ?>" alt="[TR]"/></div>
<script type="text/javascript">
    new TranslateInline('translate-inline-trig', '<?php echo $ajaxUrl ?>', '<?php
        echo Mage::getDesign()->getArea() ?>');
</script>
<?php
        $html = ob_get_clean();

        $this->_content = str_ireplace('</body>', $html . '</body>', $this->_content);

        $this->_isScriptInserted = true;
    }

    /**
     * Escape Translate data
     *
     * @param string $string
     * @return string
     */
    protected function _escape($string)
    {
        return str_replace("'", "\\'", htmlspecialchars($string));
    }

    /**
     * Get attribute location
     *
     * @param array $matches
     * @param array $options
     * @return string
     */
    protected function _getAttributeLocation($matches, $options)
    {
        return 'Tag attribute (ALT, TITLE, etc.)';
    }

    /**
     * Get tag location
     *
     * @param array $matches
     * @param array $options
     * @return string
     */
    protected function _getTagLocation($matches, $options)
    {
        $tagName = strtolower($options['tagName']);

        if (isset($options['tagList'][$tagName])) {
            return $options['tagList'][$tagName];
        }

        return ucfirst($tagName) . ' Text';
    }

    /**
     * Get translate data by regexp
     *
     * @param string $regexp
     * @param string $text
     * @param string|array $locationCallback
     * @param array $options
     * @return array
     */
    protected function _getTranslateData($regexp, &$text, $locationCallback, $options = array())
    {
        $trArr = array();
        $next = 0;
        while (preg_match($regexp, $text, $m, PREG_OFFSET_CAPTURE, $next)) {
            $trArr[] = json_encode(array(
                'shown' => $m[1][0],
                'translated' => $m[2][0],
                'original' => $m[3][0],
                'location' => call_user_func($locationCallback, $m, $options),
                'scope' => $m[4][0],
            ));
            $text = substr_replace($text, $m[1][0], $m[0][1], strlen($m[0][0]));
            $next = $m[0][1];
        }
        return $trArr;
    }


    /**
     * Prepare tags inline translates
     *
     */
    protected function _tagAttributes()
    {
        $this->_prepareTagAttributesForContent($this->_content);
    }

    /**
     * Prepare tags inline translates for the content
     *
     * @param string $content
     * @return void
     */
    protected function _prepareTagAttributesForContent(&$content)
    {
        if ($this->getIsJson()) {
            $quoteHtml   = '\"';
        } else {
            $quoteHtml   = '"';
        }

        $tagMatch   = array();
        $nextTag    = 0;
        $tagRegExp = '#<([a-z]+)\s*?[^>]+?((' . $this->_tokenRegex . ')[^>]*?)+\\\\?/?>#iS';
        while (preg_match($tagRegExp, $content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
            $tagHtml    = $tagMatch[0][0];
            $m          = array();
            $attrRegExp = '#' . $this->_tokenRegex . '#S';
            $trArr = $this->_getTranslateData($attrRegExp, $tagHtml, array($this, '_getAttributeLocation'));
            if ($trArr) {
                $transRegExp = '# data-translate=' . $quoteHtml . '\[([^'.preg_quote($quoteHtml).']*)]' . $quoteHtml . '#i';
                if (preg_match($transRegExp, $tagHtml, $m)) {
                    $tagHtml = str_replace($m[0], '', $tagHtml); //remove tra
                    $trAttr = ' data-translate=' . $quoteHtml
                        . htmlspecialchars('[' . $m[1] . ',' . implode(',', $trArr) . ']') . $quoteHtml;
                } else {
                    $trAttr = ' data-translate=' . $quoteHtml
                        . htmlspecialchars('[' . implode(',', $trArr) . ']') . $quoteHtml;
                }
                $tagHtml = substr_replace($tagHtml, $trAttr, strlen($tagMatch[1][0])+1, 1);
                $content = substr_replace($content, $tagHtml, $tagMatch[0][1], strlen($tagMatch[0][0]));
            }
            $nextTag = $tagMatch[0][1] + strlen($tagHtml);
        }
    }

    /**
     * Get html quote symbol
     *
     * @return string
     */
    protected function _getHtmlQuote()
    {
        if ($this->getIsJson()) {
            return '\"';
        } else {
            return '"';
        }
    }

    /**
     * Prepare special tags
     */
    protected function _specialTags()
    {
        $this->_translateTags($this->_content, $this->_allowedTagsGlobal, '_applySpecialTagsFormat', false);
        $this->_translateTags($this->_content, $this->_allowedTagsSimple, '_applySimpleTagsFormat', true);
    }

    /**
     * Format translate for special tags
     *
     * @param string $tagHtml
     * @param string  $tagName
     * @param array $trArr
     * @return string
     */
    protected function _applySpecialTagsFormat($tagHtml, $tagName, $trArr)
    {
        return $tagHtml . '<span class="translate-inline-' . $tagName
            . '" data-translate='
            . $this->_getHtmlQuote()
            . htmlspecialchars('[' . implode(',', $trArr) . ']')
            . $this->_getHtmlQuote() . '>'
            . strtoupper($tagName) . '</span>';
    }

    /**
     * Format translate for simple tags
     *
     * @param string $tagHtml
     * @param string  $tagName
     * @param array $trArr
     * @return string
     */
    protected function _applySimpleTagsFormat($tagHtml, $tagName, $trArr)
    {
        return substr($tagHtml, 0, strlen($tagName) + 1)
            . ' data-translate='
            . $this->_getHtmlQuote() . htmlspecialchars('[' . implode(',', $trArr) . ']')
            . $this->_getHtmlQuote()
            . substr($tagHtml, strlen($tagName) + 1);
    }

    /**
     * Prepare simple tags
     *
     * @param string $content
     * @param array $tagsList
     * @param string|array $formatCallback
     * @param bool $isNeedTranslateAttributes
     */
    protected function _translateTags(&$content, $tagsList, $formatCallback, $isNeedTranslateAttributes)
    {
        $nextTag = 0;

        $tags = implode('|', array_keys($tagsList));
        $tagRegExp  = '#<(' . $tags . ')(/?>| \s*[^>]*+/?>)#iSU';
        $tagMatch = array();
        while (preg_match($tagRegExp, $content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
            $tagName  = strtolower($tagMatch[1][0]);
            if (substr($tagMatch[0][0], -2) == '/>') {
                $tagClosurePos = $tagMatch[0][1] + strlen($tagMatch[0][0]);
            } else {
                $tagClosurePos = $this->findEndOfTag($content, $tagName, $tagMatch[0][1]);
            }

            if ($tagClosurePos === false) {
                $nextTag += strlen($tagMatch[0][0]);
                continue;
            }

            $tagLength = $tagClosurePos - $tagMatch[0][1];

            $tagStartLength = strlen($tagMatch[0][0]);

            $tagHtml = $tagMatch[0][0]
                . substr($content, $tagMatch[0][1] + $tagStartLength, $tagLength - $tagStartLength);
            $tagClosurePos = $tagMatch[0][1] + strlen($tagHtml);

            $trArr = $this->_getTranslateData(
                '#' . $this->_tokenRegex . '#iS',
                $tagHtml,
                array($this, '_getTagLocation'),
                array(
                    'tagName' => $tagName,
                    'tagList' => $tagsList
                )
            );

            if (!empty($trArr)) {
                $trArr = array_unique($trArr);
                $tagHtml = call_user_func(array($this, $formatCallback), $tagHtml, $tagName, $trArr);
                $tagClosurePos = $tagMatch[0][1] + strlen($tagHtml);
                $content = substr_replace($content, $tagHtml, $tagMatch[0][1], $tagLength);
            }
            $nextTag = $tagClosurePos;
        }
    }

    /**
     * Find end of tag
     *
     * @param string $body
     * @param string $tagName
     * @param int $from
     * @return false|int return false if end of tag is not found
     */
    private function findEndOfTag($body, $tagName, $from)
    {
        $openTag = '<' . $tagName;
        $closeTag =  ($this->getIsJson() ? '<\\/' : '</') . $tagName;
        $tagLength = strlen($tagName);
        $length = $tagLength + 1;
        $end = $from + 1;
        while (substr_count($body, $openTag, $from, $length) != substr_count($body, $closeTag, $from, $length)) {
            $end = strpos($body, $closeTag, $end + $tagLength + 1);
            if ($end === false) {
                return false;
            }
            $length = $end - $from  + $tagLength + 3;
        }
        if (preg_match('#<\\\\?\/' . $tagName .'\s*?>#i', $body, $tagMatch, null, $end)) {
            return $end + strlen($tagMatch[0]);
        } else {
            return false;
        }
    }

    /**
     * Prepare other text inline translates
     */
    protected function _otherText()
    {
        if ($this->getIsJson()) {
            $quoteHtml = '\"';
        } else {
            $quoteHtml = '"';
        }

        $next = 0;
        $m    = array();
        while (preg_match('#' . $this->_tokenRegex . '#', $this->_content, $m, PREG_OFFSET_CAPTURE, $next)) {
            $tr = json_encode(array(
                'shown' => $m[1][0],
                'translated' => $m[2][0],
                'original' => $m[3][0],
                'location' => 'Text',
                'scope' => $m[4][0],
            ));

            $spanHtml = '<span data-translate=' . $quoteHtml . htmlspecialchars('[' . $tr . ']') . $quoteHtml
                . '>' . $m[1][0] . '</span>';
            $this->_content = substr_replace($this->_content, $spanHtml, $m[0][1], strlen($m[0][0]));
            $next = $m[0][1] + strlen($spanHtml) - 1;
        }
    }

    /**
     * Check is a Request contain Json flag
     *
     * @deprecated 1.3.2.2
     * @return bool
     */
    public function getIsAjaxRequest()
    {
        return (bool)Mage::app()->getRequest()->getQuery('isAjax');
    }

    /**
     * Set is a Request contain Json flag
     *
     * @param bool $flag
     * @deprecated 1.3.2.2
     * @return $this
     */
    public function setIsAjaxRequest($flag)
    {
        Mage::app()->getRequest()->setQuery('isAjax', intval((bool)$flag));
        return $this;
    }

    /**
     * Retrieve flag about parsed content is Json
     *
     * @return bool
     */
    public function getIsJson()
    {
        return $this->_isJson;
    }

    /**
     * Set flag about parsed content is Json
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsJson($flag)
    {
        $this->_isJson = (bool)$flag;
        return $this;
    }
}
