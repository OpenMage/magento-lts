<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml store content block
 *
 * @category   Mage
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
            'class'     => 'add',
        ]);

        /* Add Store Group button */
        $this->_addButton('add_group', [
            'label'     => Mage::helper('core')->__('Create Store'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/newGroup')),
            'class'     => 'add',
        ]);

        /* Add Store button */
        $this->_addButton('add_store', [
            'label'     => Mage::helper('core')->__('Create Store View'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/newStore')),
            'class'     => 'add',
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
            $this->getChildHtml('add_new_store')
        ]);
    }
}
