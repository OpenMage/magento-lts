<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Accordion item
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Accordion_Item extends Mage_Adminhtml_Block_Widget
{
    protected $_accordion;

    public function setAccordion($accordion)
    {
        $this->_accordion = $accordion;
        return $this;
    }

    public function getTarget()
    {
        return ($this->getAjax()) ? 'ajax' : '';
    }

    public function getTitle()
    {
        $title  = $this->getData('title');
        $url    = $this->getContentUrl() ? $this->getContentUrl() : '#';

        return '<a href="' . $url . '" class="' . $this->getTarget() . '">' . $title . '</a>';
    }

    public function getContent()
    {
        $content = $this->getData('content');
        if (is_string($content)) {
            return $content;
        }

        if ($content instanceof Mage_Core_Block_Abstract) {
            return $content->toHtml();
        }

        return null;
    }

    public function getClass()
    {
        $class = $this->getData('class');
        if ($this->getOpen()) {
            $class .= ' open';
        }

        return $class;
    }

    protected function _toHtml()
    {
        $content = $this->getContent();
        $html = '<dt id="dt-' . $this->getHtmlId() . '" class="' . $this->getClass() . '">';
        $html .= $this->getTitle();
        $html .= '</dt>';
        $html .= '<dd id="dd-' . $this->getHtmlId() . '" class="' . $this->getClass() . '">';
        $html .= $content;
        return $html . '</dd>';
    }
}
