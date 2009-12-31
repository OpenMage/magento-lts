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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * A generic wrapper block that renders its children and supports a few parameters of the wrapper HTML-element
 */
class Mage_Page_Block_Html_Wrapper extends Mage_Core_Block_Abstract
{
    /**
     * Render the wrapper element html
     * Supports different optional parameters, set in data by keys:
     * - element_tag_name (div by default)
     * - element_id
     * - element_class
     * - element_other_attributes
     *
     * Renders all children inside the element.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $id          = $this->hasElementId() ? sprintf(' id="%s"', $this->getElementId()) : '';
        $class       = $this->hasElementClass() ? sprintf(' class="%s"', $this->getElementClass()) : '';
        $otherParams = $this->hasOtherParams() ? ' ' . $this->getOtherParams() : '';
        return sprintf('<%1$s%2$s%3$s%4$s>%5$s</%1$s>',
            $this->getElementTagName(), $id, $class, $otherParams, $this->getChildHtml()
        );
    }

    /**
     * Wrapper element tag name getter
     * @return string
     */
    public function getElementTagName()
    {
        $tagName = $this->_getData('html_tag_name');
        return $tagName ? $tagName : 'div';
    }
}
