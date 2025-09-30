<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Admin tag edit block
 *
 * @package    Mage_Adminhtml
 * @deprecated after 1.3.2.3
 */
class Mage_Adminhtml_Block_Tag_Tag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @throws Mage_Core_Exception
     */
    public function __construct()
    {
        $this->_objectId = 'tag_id';
        $this->_controller = 'tag';

        parent::__construct();

        if ($this->getRequest()->getParam('product_id')) {
            $this->_updateButton(
                'back',
                'onclick',
                Mage::helper('core/js')->getSetLocationJs(
                    $this->getUrl('*/catalog_product/edit', ['id' => $this->getRequest()->getParam('product_id')]),
                ),
            );
        }

        if ($this->getRequest()->getParam('customer_id')) {
            $this->_updateButton(
                'back',
                'onclick',
                Mage::helper('core/js')->getSetLocationJs(
                    $this->getUrl('*/customer/edit', ['id' => $this->getRequest()->getParam('customer_id')]),
                ),
            );
        }

        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->_updateButton(
                'back',
                'onclick',
                Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/pending')),
            );

            $this->_updateButton(
                'delete',
                'onclick',
                Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getUrl(
                        '*/*/delete',
                        [$this->_objectId => $this->getRequest()->getParam($this->_objectId), 'ret' => 'pending'],
                    ),
                ),
            );
            Mage::register('ret', 'pending');
        }

        $this->_updateButton('save', 'label', Mage::helper('tag')->__('Save Tag'));
        $this->_updateButton('delete', 'label', Mage::helper('tag')->__('Delete Tag'));
    }

    /**
     * Add to layout accordion block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('accordion', $this->getLayout()->createBlock('adminhtml/tag_edit_accordion'));
        return $this;
    }

    /**
     * Adds to html of form html of accordion block
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html . $this->getChildHtml('accordion');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tag_tag')->getId()) {
            return Mage::helper('tag')->__("Edit Tag '%s'", $this->escapeHtml(Mage::registry('tag_tag')->getName()));
        }
        return Mage::helper('tag')->__('New Tag');
    }
}
