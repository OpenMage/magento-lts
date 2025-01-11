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
 * Adminhtml sales order create sidebar compared block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Compared extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_compared');
        $this->setDataId('compared');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Products in Comparison List');
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
            if ($collection = $this->getCreateOrderModel()->getCustomerCompareList()) {
                $collection = $collection->getItemCollection()
                    ->useProductItem(true)
                    ->setStoreId($this->getQuote()->getStoreId())
                    ->addStoreFilter($this->getQuote()->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('image')
                    ->addAttributeToSelect('status')
                    ->load();
            }
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    public function getItemId($item)
    {
        return $item->getCatalogCompareItemId();
    }
}
