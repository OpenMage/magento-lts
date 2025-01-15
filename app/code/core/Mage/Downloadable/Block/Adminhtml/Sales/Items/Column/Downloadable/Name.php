<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order downloadable items name column renderer
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Adminhtml_Sales_Items_Column_Downloadable_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
    /**
     * @var Mage_Downloadable_Model_Link_Purchased
     */
    protected $_purchased = null;

    /**
     * @return Mage_Downloadable_Model_Link_Purchased
     */
    public function getLinks()
    {
        $this->_purchased = Mage::getModel('downloadable/link_purchased')
            ->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedItem = Mage::getModel('downloadable/link_purchased_item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedItem);
        return $this->_purchased;
    }

    /**
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->_purchased && $this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
