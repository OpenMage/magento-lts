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
 */
class Mage_Adminhtml_Block_Tag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Add and update buttons
     */
    public function __construct()
    {
        $this->_objectId   = 'tag_id';
        $this->_controller = 'tag';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('tag')->__('Save Tag'));
        $this->_updateButton('delete', 'label', Mage::helper('tag')->__('Delete Tag'));

        $this->addButton('save_and_edit_button', [
            'label'   => Mage::helper('tag')->__('Save and Continue Edit'),
            'onclick' => Mage::helper('core/js')->getSaveAndContinueEditJs($this->getSaveAndContinueUrl()),
            'class'   => 'save',
        ], 1);
    }

    /**
     * Add child HTML to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/tag_store_switcher'))
             ->setChild('tag_assign_accordion', $this->getLayout()->createBlock('adminhtml/tag_edit_assigned'))
             ->setChild('accordion', $this->getLayout()->createBlock('adminhtml/tag_edit_accordion'));

        return $this;
    }

    /**
     * Retrieve Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_tag')->getId()) {
            return Mage::helper('tag')->__(
                "Edit Tag '%s'",
                $this->escapeHtml(Mage::registry('current_tag')->getName()),
            );
        }
        return Mage::helper('tag')->__('New Tag');
    }

    /**
     * Retrieve Accordions HTML
     *
     * @return string
     */
    public function getAcordionsHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * Retrieve Tag Delete URL
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete',
            [
                'tag_id' => $this->getRequest()->getParam($this->_objectId),
                'ret' => $this->getRequest()->getParam(
                    'ret',
                    'index',
                ),
            ],
        );
    }

    /**
     * Retrieve Assigned Tags Accordion HTML
     *
     * @return string
     */
    public function getTagAssignAccordionHtml()
    {
        return $this->getChildHtml('tag_assign_accordion');
    }

    /**
     * Retrieve Store Switcher HTML
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Retrieve Tag Save URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true]);
    }

    /**
     * Retrieve Tag SaveAndContinue URL
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current'  => true,
                'ret'       => 'edit',
                'continue'  => $this->getRequest()->getParam('ret', 'index'),
                'store'     => Mage::registry('current_tag')->getStoreId(),
            ],
        );
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }
}
