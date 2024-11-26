<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Nominal total rendered
 * Each item is rendered as separate total with its details
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Total_Nominal extends Mage_Checkout_Block_Total_Default
{
    /**
     * Custom template
     *
     * @var string
     */
    protected $_template = 'checkout/total/nominal.phtml';

    /**
     * Getter for a quote item name
     *
     * @return string
     */
    public function getItemName(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getName();
    }

    /**
     * Getter for a quote item row total
     *
     * @return float
     */
    public function getItemRowTotal(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalRowTotal();
    }

    /**
     * Getter for nominal total item details
     *
     * @return array
     */
    public function getTotalItemDetails(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalTotalDetails();
    }

    /**
     * Getter for details row label
     *
     * @return string
     */
    public function getItemDetailsRowLabel(Varien_Object $row)
    {
        return $row->getLabel();
    }

    /**
     * Getter for details row amount
     *
     * @return string
     */
    public function getItemDetailsRowAmount(Varien_Object $row)
    {
        return $row->getAmount();
    }

    /**
     * Getter for details row compounded state
     *
     * @return bool
     */
    public function getItemDetailsRowIsCompounded(Varien_Object $row)
    {
        return $row->getIsCompounded();
    }

    /**
     * Format an amount without container
     *
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount)
    {
        return $this->_store->formatPrice($amount, false);
    }

    /**
     * Import total data into the block, if there are items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $total = $this->getTotal();
        $items = $total->getItems();
        if ($items) {
            foreach ($total->getData() as $key => $value) {
                $this->setData("total_{$key}", $value);
            }
            return parent::_toHtml();
        }
        return '';
    }
}
