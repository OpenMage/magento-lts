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
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar cart block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    public const BUTTON_EMPTY_CUSTOMER_CART = 'empty_customer_cart_button';

    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_cart_item';

    protected $_idFieldName = 'sales_order_create_sidebar_cart';

    protected function _construct()
    {
        parent::_construct();
        $this->setDataId('cart');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Shopping Cart');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        if (is_null($collection)) {
            $collection = $this->getCreateOrderModel()->getCustomerCart()->getAllVisibleItems();
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    /**
     * @return bool
     */
    public function canDisplayItemQty()
    {
        return true;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param Varien_Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getProduct()->getId();
    }

    /**
     * @codeCoverageIgnore
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_EMPTY_CUSTOMER_CART, $this->getButtonEmptyCustomerCartBlock());
    }

    public function getButtonEmptyCustomerCartBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $deleteAllConfirmString = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('sales')->__('Are you sure you want to delete all items from shopping cart?')
        );
        return parent::getButtonBlock()
            ->setLabel(Mage::helper('sales')->__('Clear Shopping Cart'))
            ->setOnClick('order.clearShoppingCart(\'' . $deleteAllConfirmString . '\')')
            ->setStyle('float: right;');
    }
}
