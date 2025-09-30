<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Form element widget block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Form_Element extends Mage_Adminhtml_Block_Template
{
    protected $_element;
    protected $_form;
    protected $_formBlock;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/form/element.phtml');
    }

    public function setElement($element)
    {
        $this->_element = $element;
        return $this;
    }

    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    public function setFormBlock($formBlock)
    {
        $this->_formBlock = $formBlock;
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->assign('form', $this->_form);
        $this->assign('element', $this->_element);
        $this->assign('formBlock', $this->_formBlock);

        return parent::_beforeToHtml();
    }
}
