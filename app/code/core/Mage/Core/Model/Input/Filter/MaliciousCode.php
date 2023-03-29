<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Filter for removing malicious code from HTML
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Input_Filter_MaliciousCode implements Zend_Filter_Interface
{
    /**
     * Regular expressions for cutting malicious code
     *
     * @var array
     */
    protected $_expressions = [
        //comments, must be first
        '/(\/\*.*\*\/)/Us',
        //tabs
        '/(\t)/',
        //javasript prefix
        '/(javascript\s*:)/Usi',
        //import styles
        '/(@import)/Usi',
        //js in the style attribute
        '/style=[^<]*((expression\s*?\([^<]*?\))|(behavior\s*:))[^<]*(?=\>)/Uis',
        //js attributes
        '/(ondblclick|onclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onload|onunload|onerror|onanimationstart|onfocus|onloadstart|ontoggle)\s*=[^>]*(?=\>)/Uis',
        //tags
        '/<\/?(script|meta|link|frame|iframe|object).*>/Uis',
        //base64 usage
        '/src\s*=[^<]*base64[^<]*(?=\>)/Uis',
        //data attribute
        '/(data(\\\\x3a|:|%3A)(.+?(?=")|.+?(?=\')))/is',
    ];

    /**
     * Filter value
     *
     * @param string|array $value
     * @return string|array
     */
    public function filter($value)
    {
        do {
            $value = preg_replace($this->_expressions, '', $value, -1, $count);
        } while ($count !== 0);

        return $value;
    }

    /**
     * Add expression
     *
     * @param string $expression
     * @return $this
     */
    public function addExpression($expression)
    {
        if (!in_array($expression, $this->_expressions)) {
            $this->_expressions[] = $expression;
        }
        return $this;
    }

    /**
     * Set expressions
     *
     * @param array $expressions
     * @return $this
     */
    public function setExpressions(array $expressions)
    {
        $this->_expressions = $expressions;
        return $this;
    }

    /**
     * The filter adds safe attributes to the link
     *
     * @param string $html
     * @param bool $removeWrapper flag for remove wrapper tags: Doctype, html, body
     * @return string
     * @throws Mage_Core_Exception
     */
    public function linkFilter($html, $removeWrapper = true)
    {
        if (stristr($html, '<a ') === false) {
            return $html;
        }

        $libXmlErrorsState = libxml_use_internal_errors(true);
        $dom = $this->_initDOMDocument();
        if (!$dom->loadHTML($html)) {
            Mage::throwException(Mage::helper('core')->__('HTML filtration has failed.'));
        }

        $relAttributeDefaultItems = ['noopener', 'noreferrer'];
        /** @var DOMElement $linkItem */
        foreach ($dom->getElementsByTagName('a') as $linkItem) {
            $relAttributeItems = [];
            $relAttributeCurrentValue = $linkItem->getAttribute('rel');
            if (!empty($relAttributeCurrentValue)) {
                $relAttributeItems = explode(' ', $relAttributeCurrentValue);
            }
            $relAttributeItems = array_unique(array_merge($relAttributeItems, $relAttributeDefaultItems));
            $linkItem->setAttribute('rel', implode(' ', $relAttributeItems));
            $linkItem->setAttribute('target', '_blank');
        }

        if (!$html = $dom->saveHTML()) {
            Mage::throwException(Mage::helper('core')->__('HTML filtration has failed.'));
        }

        if ($removeWrapper) {
            $html = preg_replace('/<(?:!DOCTYPE|\/?(?:html|body))[^>]*>\s*/i', '', $html);
        }

        libxml_use_internal_errors($libXmlErrorsState);

        return $html;
    }

    /**
     * Initialize built-in DOM parser instance
     *
     * @return DOMDocument
     */
    protected function _initDOMDocument()
    {
        $dom = new DOMDocument();
        $dom->strictErrorChecking = false;
        $dom->recover = false;

        return $dom;
    }
}
