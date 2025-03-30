<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Nominal shipping total
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Shipping extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
    /**
     * Don't add/set amounts
     * @var bool
     */
    protected $_canAddAmountToAddress = false;
    protected $_canSetAddressAmount   = false;

    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'shipping_amount';

    /**
     * Whether to get all address items when collecting them
     *
     * @var bool
     */
    protected $_shouldGetAllItems = false;

    /**
     * Collect shipping amount individually for each item
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $items = $address->getAllNominalItems();
        if (!count($items)) {
            return $this;
        }

        // estimate quote with all address items to get their row weights
        $this->_shouldGetAllItems = true;
        parent::collect($address);
        $address->setCollectShippingRates(true);
        $this->_shouldGetAllItems = false;
        // now $items contains row weight information

        // collect shipping rates for each item individually
        foreach ($items as $item) {
            if (!$item->getProduct()->isVirtual()) {
                $address->requestShippingRates($item);
                $baseAmount = $item->getBaseShippingAmount();
                if ($baseAmount) {
                    $item->setShippingAmount($address->getQuote()->getStore()->convertPrice($baseAmount, false));
                }
            }
        }

        return $this;
    }

    /**
     * Don't fetch anything
     *
     * @return array|Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

    /**
     * Get nominal items only or indeed get all items, depending on current logic requirements
     *
     * @return array
     */
    protected function _getAddressItems(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->_shouldGetAllItems) {
            return $address->getAllItems();
        }
        return $address->getAllNominalItems();
    }
}
