<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * A generic wrapper block that renders its children and supports a few parameters of the wrapper HTML-element
 *
 * @package    Mage_Page
 *
 * @method string getElementClass()
 * @method string getElementId()
 * @method string getOtherParams()
 * @method bool   hasElementClass()
 * @method bool   hasElementId()
 * @method bool   hasMayBeInvisible()
 * @method bool   hasOtherParams()
 */
class Mage_Page_Block_Html_Wrapper extends Mage_Core_Block_Abstract
{
    /**
     * Whether block should render its content if there are no children (no)
     * @var bool
     */
    protected $_dependsOnChildren = true;

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
        $html = empty($this->_children) ? '' : trim($this->getChildHtml('', true, true));
        if ($this->_dependsOnChildren && empty($html)) {
            return '';
        }

        if ($this->_isInvisible()) {
            return $html;
        }

        $id          = $this->hasElementId() ? sprintf(' id="%s"', $this->getElementId()) : '';
        $class       = $this->hasElementClass() ? sprintf(' class="%s"', $this->getElementClass()) : '';
        $otherParams = $this->hasOtherParams() ? ' ' . $this->getOtherParams() : '';
        return sprintf('<%1$s%2$s%3$s%4$s>%5$s</%1$s>', $this->getElementTagName(), $id, $class, $otherParams, $html);
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

    /**
     * Setter whether this block depends on children
     * @param  string $depends
     * @return $this
     */
    public function dependsOnChildren($depends = '0')
    {
        $this->_dependsOnChildren = (bool) (int) $depends;
        return $this;
    }

    /**
     * Whether the wrapper element should be eventually rendered
     * If it becomes "invisible", the behaviour will be somewhat similar to core/text_list
     *
     * @return bool
     */
    protected function _isInvisible()
    {
        if (!$this->hasMayBeInvisible()) {
            return false;
        }

        foreach ($this->_children as $child) {
            if ($child->hasWrapperMustBeVisible()) {
                return false;
            }
        }

        return true;
    }
}
