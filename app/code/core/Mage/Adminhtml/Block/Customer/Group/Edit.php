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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer group edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_group';

        $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Customer Group'));
        $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Customer Group'));

        if (!Mage::registry('current_group')->getId() || Mage::registry('current_group')->usesAsDefault()) {
            $this->_removeButton('delete');
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDeleteUrl()
    {
        if (!Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            return $this->getUrl('*/*/delete', [
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            ]);
        } else {
            return parent::getDeleteUrl();
        }
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (!is_null(Mage::registry('current_group')->getId())) {
            return Mage::helper('customer')->__('Edit Customer Group "%s"', $this->escapeHtml(Mage::registry('current_group')->getCustomerGroupCode()));
        }
        return Mage::helper('customer')->__('New Customer Group');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
