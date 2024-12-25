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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customer')->__('Customer Information'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Tabs
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        /** @var Mage_Adminhtml_Block_Customer_Edit_Tab_Account $block */
        $block = $this->getLayout()->createBlock('adminhtml/customer_edit_tab_account');
        $this->addTab('account', [
            'label'     => Mage::helper('customer')->__('Account Information'),
            'content'   => $block->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true,
        ]);

        /** @var Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses $block */
        $block = $this->getLayout()->createBlock('adminhtml/customer_edit_tab_addresses');
        $this->addTab('addresses', [
            'label'     => Mage::helper('customer')->__('Addresses'),
            'content'   => $block->initForm()->toHtml(),
        ]);

        // load: Orders, Shopping Cart, Wishlist, Product Reviews, Product Tags - with ajax

        if (Mage::registry('current_customer')->getId()) {
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                $this->addTab('orders', [
                    'label'     => Mage::helper('customer')->__('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/orders', ['_current' => true]),
                ]);
            }

            $this->addTab('cart', [
                'label'     => Mage::helper('customer')->__('Shopping Cart'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/carts', ['_current' => true]),
            ]);

            $this->addTab('wishlist', [
                'label'     => Mage::helper('customer')->__('Wishlist'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/wishlist', ['_current' => true]),
            ]);

            if ($this->isModuleOutputEnabled('Mage_Newsletter')
                && Mage::getSingleton('admin/session')->isAllowed('newsletter/subscriber')
            ) {
                /** @var Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter $block */
                $block = $this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter');
                $this->addTab('newsletter', [
                    'label'     => Mage::helper('customer')->__('Newsletter'),
                    'content'   => $block->initForm()->toHtml(),
                ]);
            }

            if ($this->isModuleOutputEnabled('Mage_Review')
                && Mage::getSingleton('admin/session')->isAllowed('catalog/reviews_ratings')
            ) {
                $this->addTab('reviews', [
                    'label'     => Mage::helper('customer')->__('Product Reviews'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productReviews', ['_current' => true]),
                ]);
            }

            if ($this->isModuleEnabled('Mage_Tag')
                && Mage::getSingleton('admin/session')->isAllowed('catalog/tag')
            ) {
                $this->addTab('tags', [
                    'label'     => Mage::helper('customer')->__('Product Tags'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productTags', ['_current' => true]),
                ]);
            }
        }

        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }

    /**
     * @throws Exception
     */
    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
