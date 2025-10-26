<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml store content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Store extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        $this->_controller  = 'system_store';
        $this->_headerText  = Mage::helper('adminhtml')->__('Manage Stores');
        $this->setTemplate('system/store/container.phtml');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /* Add website button */
        $this->_addButton('add', [
            'label'     => Mage::helper('core')->__('Create Website'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/newWebsite')),
            'class'     => 'add website',
        ]);

        /* Add Store Group button */
        $this->_addButton('add_group', [
            'label'     => Mage::helper('core')->__('Create Store'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/newGroup')),
            'class'     => 'add store',
        ]);

        /* Add Store button */
        $this->_addButton('add_store', [
            'label'     => Mage::helper('core')->__('Create Store View'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/newStore')),
            'class'     => 'add storeview',
        ]);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/system_store_tree')->toHtml();
    }

    /**
     * Retrieve buttons
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return implode(' ', [
            $this->getChildHtml('add_new_website'),
            $this->getChildHtml('add_new_group'),
            $this->getChildHtml('add_new_store'),
        ]);
    }
}
